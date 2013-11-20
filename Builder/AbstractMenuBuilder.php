<?php

namespace Zuni\MenuBundle\Builder;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Zuni\MenuBundle\Event\MenuBuiltFilterEvent;
use Zuni\MenuBundle\Event\MenuFilterEvent;
use Zuni\MenuBundle\Exceptions\MenuException;
use Zuni\MenuBundle\Resolver\MenuLoaderResolveInterface;
use Zuni\MenuBundle\ZuniMenuEvents;

/**
 * Construtor de menu
 */
abstract class AbstractMenuBuilder implements MenuBuilderInterface
{
    
    /**
     * Nome do menu que ser'a fabricado
     * 
     * @var string 
     */
    protected $name;

    /**
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * Recurso para cerregar o menu
     * 
     * @var string
     */
    protected $resource;

    /**
     * Opções para construir o menu
     * 
     * @var array 
     */
    protected $options;

    public function __construct($name, ContainerInterface $container, FactoryInterface $factory, $resource, array $options = array())
    {
        $this->name = $name;
        $this->container = $container;
        $this->factory = $factory;
        $this->resource = $resource;
        $this->options = $options;
    }

    /**
     * Constroi o menu 
     * 
     * @return ItemInterface
     */
    abstract public function builder(array $menus, Request $request);


    /**
     * @param Request $request
     * @return ItemInterface
     */
    public function createMenu(Request $request)
    {
        $resolver = $this->container->get("menu.loader.resolver");
        /* @var $resolver MenuLoaderResolveInterface */

        $menu = $resolver->load($this->resource, $this->options['resource_type']);

        if (!$menu) {
            throw new MenuException(sprintf(
                    "Nenhum loader cadastrado para o recurso %s", $this->resource
            ));
        }
    
        $dispacher = $this->container->get("event_dispatcher");
        /* @var $dispacher EventDispatcherInterface */

        $dispacher->dispatch(
                ZuniMenuEvents::MENU_PRE_BUILDER, new MenuFilterEvent($this, $menu)
        );
        
        $menuBuilt = $this->builder($menu, $request);

        $menuBuilt->setCurrentUri($request->getPathInfo());
        
        $dispacher->dispatch(
                ZuniMenuEvents::MENU_POST_BUILDER, new MenuBuiltFilterEvent($this, $menu, $menuBuilt)
        );

        return $menuBuilt;
    }
    
    /**
     * Nome do menu que ser'a fabricado 
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Fabrica de menu
     * 
     * @return FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Recurso de onde foi retirado o menu
     * 
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * opcoes de renderizacao do menu
     * 
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }


}
