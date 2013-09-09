<?php

namespace Zuni\MenuBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Zuni\MenuBundle\Collections\MenuCollection;
use Zuni\MenuBundle\Config\Loader\YamlMenuLoader;
use Zuni\MenuBundle\Entity\Menu;
use Zuni\MenuBundle\Exceptions\MenuException;



/**
 * @Route("/menu")
 */
class MenuController extends Controller
{
       
    /**
     * @Route("/menu-bar/{currentRoute}", name="menu_bar")
     * @Template()
     */
    public function menuBarAction($currentRoute)
    {
        
        $menuCollection = $this->getUnserializedMenus('admin');
        $breadcrumb = $menuCollection->getBreadcrumb($currentRoute);
        
        if(!$breadcrumb){
            throw new MenuException("Menu Selecionado nao econtrado {$currentRoute}");
        }
        
        return array('menus' => $menuCollection, 'breadcrumb' => $breadcrumb);
    }
    
    /**
     * Migalhas de pão para ajudar na localização
     * 
     * @Route("/breadcrumb/{currentRoute}", name="menu_breadcrumb")
     * @Template()
     */
    public function breadcrumbAction($currentRoute)
    {
        $menuCollection = $this->getUnserializedMenus('admin');
        
        $menuBreadcrumb = $menuCollection->createCollectionBreadcrumb($currentRoute);
        
        $currentRoute = $menuBreadcrumb->getCurrentRoute();
        
        if(!$menuBreadcrumb || !$currentRoute)
        {
            throw new MenuException("Migalha de pao nao econtrado");
        }
        
        return array("menuBreadcrumb" => $menuBreadcrumb, "currentRoute" => $currentRoute);
    }
    
    
    /**
     * 
     * 
     * 
     * @param string $nameProvider
     * @return MenuCollection
     */
    public function getUnserializedMenus($nameProvider) {
        
        if ($this->isCached())
        {
            return $this->loadMenusCacheOn($nameProvider);
        }
        else
        {
            return $this->loadMenus($nameProvider);
        }
    }
    
    /**
     * 
     * Verifica se é para pegar a collection do cache
     * 
     * @return boolean
     */
    private function isCached()
    {
        $cache = $this->container->getParameter("zuni_menu.cache");
        
        return $this->get("kernel")->getEnvironment() == "prod" && $cache['enable'] == true;
    }
    
    /**
     * 
     * Carrega o menu da session, caso não tenha em cache, do do arquivo
     * o guarda na session para as próximas requisições
     * 
     * @param string $nameProvider
     * @return MenuCollection
     */
    private function loadMenusCacheOn($nameProvider)
    {
        
       $cache = $this->container->getParameter("zuni_menu.cache");
        
       $cache_key = $cache['cache_key']."_".$nameProvider ;
       
        if (!$this->getRequest()->getSession()->get($cache_key))
        {
            $menuCollection = $this->loadMenus($nameProvider);
            $this->getRequest()->getSession()->set($cache_key, $menuCollection);
        }
        
        return $this->getRequest()->getSession()->get($cache_key);
        
    }
    
    /**
     * Carrega o menu do arquivo, sem considerar cache
     * 
     * @param string $nameProvider
     * @return MenuCollection
     */
    private function loadMenus($nameProvider)
    {
        $provider = $this->container->getParameter("zuni_menu.".$nameProvider);
        
        return MenuCollection::parseMenuCollection($this->readFileMenu($provider['path'], $provider['file']), $provider['translation']);
    }
    
    /**
     * Lê o arquivo de menu, e os imports 
     * @todo Fazer imports recursivos
     * @param string $path
     * @param string $file
     * @return array
     */
    private function readFileMenu($path, $file) {
        $arrayMenu = $this->parseFileToArray($path, $file);
        
        if(array_key_exists("imports", $arrayMenu['menus']))
        {
            foreach ( $arrayMenu['menus']['imports'] as $import) {
                $arrayMenu["menus"] = array_merge($arrayMenu["menus"], $this->parseFileToArray($path, $import['resource']));;
            }
        }
        
        unset($arrayMenu['menus']['imports']);
              
        return $arrayMenu;
    }
    
    /**
     * Faz a leitra do arquivo de menu ou de um import de menus
     * e retorna um array
     * 
     * @param string $path  ZuniSuperAdminBundle/Resources/config/menu/menu.yml
     * @return array
     */
    private function parseFileToArray($path, $file) {
        
        //@ para poder inpotrar resources 
        $locator = new FileLocator(array($this->get("kernel")->locateResource("@".$path)));
        $loaderResolver = new LoaderResolver(array(new YamlMenuLoader($locator)));
        $delegatingLoader = new DelegatingLoader($loaderResolver);

        return $delegatingLoader->load($locator->locate($file, null, true));
    }
    
}
