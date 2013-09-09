<?php

namespace Zuni\MenuBundle\Entity;

use ArrayObject;
use Zuni\MenuBundle\Collections\MenuCollection;

/**
 * Usado para monta a tela do menu
 * 
 * Class persistida em arquivo menu.yml 
 */
class Menu 
{
    private $name;
    private $title;
    private $route; 
    private $icon; 
    private $children;
    private $active; 
    private $routeBranches;
    private $parent;
    private $action;
    
    function __construct($name, $title, $route, $icon, $action = null, $active = true, array $routeBranches = array(), MenuCollection $children = null) 
    {
        $this->name = $name;
        $this->title = $title;
        $this->route = $route;
        $this->icon = $icon;
        $this->children = $children;
        $this->active = $active;
        $this->routeBranches = new ArrayObject($routeBranches);
        $this->action = $action;
    }

    public function getName() 
    {
        return $this->name;
    }

    public function getTitle() 
    {
        return $this->title;
    }

    public function getRoute() 
    {
        return $this->route;
    }

    public function getIcon() 
    {
        return $this->icon;
    }
    
    public function getAction()
    {
        return $this->action;
    }
    
    /**
     * 
     * @return MenuCollection
     */
    public function getChildren() 
    {
        return $this->children;
    }
    
    public function setChildren(MenuCollection $menuCollection)
    {
        $this->children = $menuCollection;
        return $this;
    }
    
    /**
     * 
     * @return ArrayObject 
     */
    public function getRouteBranches() 
    {
        if(!$this->routeBranches){
            $this->routeBranches = new ArrayObject(array());
        }
        return $this->routeBranches;
    }
    
    
    /**
     * 
     * @return Menu|null
     */
    public function getParent()
    {
        return $this->parent;
    }
    
    /**
     * 
     * @param \Zuni\MenuBundle\Entity\Menu $parent
     * @return \Zuni\MenuBundle\Entity\Menu this 
     */
    public function setParent(Menu $parent)
    {
        $this->parent = $parent;
        return $this;
    }
    
    /**
     * 
     * Verifica se o menu está dentro da trilha de menus para saber se 
     * ele está selecionado
     * 
     * @param MenuCollection $breadcrumb
     */
    public function isSelected(MenuCollection $breadcrumb)
    {
        
        /* @var $menu Menu */
        foreach ($breadcrumb as $menu)
        {
            if ($menu->getName() == $this->getName())
            {
                return true;
            }
        }
        
        return false;
    }
    
}
