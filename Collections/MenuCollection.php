<?php

namespace Zuni\MenuBundle\Collections;

use ArrayObject;
use Zuni\MenuBundle\Entity\Menu;

/**
 * Coleção de class menu
 * 
 */
class MenuCollection extends ArrayObject
{
    private $translation; 
    private $breadcrumb;
    private $currentRoute;
    
    function __construct(array $menus = array(), $translationResource = "") {
        parent::__construct($menus);
        $this->setTranslation($translationResource);
    }

    
    public function getTranslation() {
        return $this->translation;
    }
    
    /**
     * Resource responsavel pela tradução do menu
     * @param string $translation
     * @return \Zuni\SuperAdminBundle\Collections\MenuCollection
     */
    public function setTranslation($translation)
    {
        $this->translation = $translation; 
        return $this;
    }
    
    /**
     * Rotorna qual menu est� selecionado com base na rota atual 
     * sempre retorna um menu Pai 
     * @param string $currentRoute
     * @return Menu|null
     */
    public function getMenuSelected($currentRoute) 
    {
        $breadcrumb = $this->getBreadcrumb($currentRoute);
        
        if (!$breadcrumb)
        {
            return null;
        }
        
        return $breadcrumb[0];
    }
    
    public function getBreadcrumb($currentRoute)
    {
        if (!$this->breadcrumb)
        {
            $this->breadcrumb = $this->createCollectionBreadcrumb($currentRoute);
        }
        
        return $this->breadcrumb;
    }
    
    
    /**
     * 
     * Retorna uma nova collection de menus com o caminho breadcrumb
     * 
     * @param type $currentRoute
     * @return \Zuni\MenuBundle\Collections\MenuCollection|null
     */
    public function createCollectionBreadcrumb($currentRoute)
    {
        $menuCurrent = $this->getCurrentMenu($this, $currentRoute);
        
        if (!$menuCurrent)
        {
            return null;
        }
        
        $menuCollectionBreadcrumb = new MenuCollection($this->createBreadcrumb($menuCurrent));
        $menuCollectionBreadcrumb->setTranslation($this->getTranslation());
        $menuCollectionBreadcrumb->setCurrentRoute($this->currentRoute);
        
        return $menuCollectionBreadcrumb;
    }
    
    /**
     * Retorna qual o nome da ação referente a rota atual
     * @return array|null Array com a rota atual
     */
    public function getCurrentRoute()
    {
        return $this->currentRoute;
    }
    
    public function setCurrentRoute($currentRoute)
    {
        $this->currentRoute = $currentRoute;
    }
    
    /**
     * Verifica se o menu está selecionado ou não
     * @param Menu $menu
     * @param string $currentRoute Nome da rota 
     * @return boolean
     */
    private function isCurrentMenu(Menu $menu , $currentRoute) {
        
        if ($menu->getRoute() === $currentRoute)
        {
            $this->setCurrentRoute(array("name" => $menu->getRoute(), "action" => $menu->getAction()));
            return true;
        }
        
        foreach ($menu->getRouteBranches() as $route)
        {
            if ($route['name'] == $currentRoute)
            {
                $this->setCurrentRoute($route);
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Retorna uma lista de menus representando o rastro do menu
     * migalhas de p�o
     * @param Menu $currentMenu
     * @return array
     */
    private function createBreadcrumb(Menu $currentMenu, array $breadcrumb = array())
    {
        
        array_unshift($breadcrumb, $currentMenu); 
        
        if($currentMenu->getParent())
        {
            $breadcrumb = $this->createBreadcrumb($currentMenu->getParent(), $breadcrumb);
        }
        
        return $breadcrumb;
    }
    
    /**
     * Retorna qual o menu est� selecionado
     * @param MenuCollection $menuCollection
     * @param string $currentRoute
     * @return Menu
     */
    private function getCurrentMenu(MenuCollection $menuCollection, $currentRoute) 
    {
        /* @var $menu Menu */
        foreach ($menuCollection as $menu) {
            
            if($this->isCurrentMenu($menu, $currentRoute)){
                return $menu;
            }
            
            if($menu->getChildren()){
                $menuSelected = $this->getCurrentMenu($menu->getChildren(), $currentRoute);
                if($menuSelected){
                    return $menuSelected;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Transforma um array em MenuCollection
     * @param array $menusFile
     * @return MenuCollection 
     */
    public static function parseMenuCollection(array $menusFile, $translation) 
    {
        
        $menuCollection = new MenuCollection();
        
        foreach ($menusFile['menus'] as $menuName => $menuFile) 
        {
            $menuCollection->append(self::createInstanceMenu($menuName, $menuFile));
        }
        
        $menuCollection->setTranslation($translation);
        
        return $menuCollection;
        
    }

    /**
     * Cuida de valores defult do array
     * @param array $array
     * @param mixed $key
     * @param mixed $defaults
     * @return mixed
     */
    private static function getValue($array , $key, $defaults)
    {
        return self::isDefalutValue($array, $key) ? $defaults : $array[$key];
    }
    
    
    /**
     * Testa o array para ver se � um valor default 
     * @param array $array
     * @param mixed $key
     * @return boolean 
     */
    private static function isDefalutValue($array , $key ) 
    {
        return (!array_key_exists($key, $array) || !$array[$key]) ;
    }
    
    /**
     * Recursive method 
     * 
     * Transforma um array de menu, em uma classe
     * @param string $name
     * @param array $menuArray
     * @return Menu
     */
    private static function createInstanceMenu($name, array $menuArray, $parent = null) 
    {
        
        $title = self::getValue($menuArray, "title", "Name Defult");
        $route = self::getValue($menuArray, "route", null);
        $icon = self::getValue($menuArray, "icon", null);
        $children = self::getValue($menuArray, "children", null);
        $active = self::getValue($menuArray, "active", true);
        $action = self::getValue($menuArray, "action", "route.index");
        
        $routeBranchesDefault = array(
            array("name" => $route."_create" , "action" => "route.new"),
            array("name" => $route."_new" , "action" => "route.new"),
            array("name" => $route."_update" , "action" => "route.edit"),
            array("name" => $route."_edit" , "action" => "route.edit"),
            array("name" => $route."_show" , "action" => "route.show"),
        );
        
        $routeBranches = self::getValue($menuArray, "branches", null);
        
        if($routeBranches)
        {
            $routeBranchesDefault = array_merge($routeBranches, $routeBranchesDefault);
        }
        
        $menu = new Menu($name, $title, $route, $icon, $action, $active, $routeBranchesDefault);
        
        if ($parent)
        {
            $menu->setParent($parent);
        }
        
        if($children){
            $childrenCollection = new MenuCollection();
            foreach ($children as $nameChild => $child) {
                $childrenCollection->append(self::createInstanceMenu($nameChild, $child, $menu));
            }
            $menu->setChildren($childrenCollection);
        }
        
        
        return $menu;
    }
    
}

