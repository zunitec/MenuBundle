<?php

namespace Zuni\MenuBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Zuni\MenuBundle\Builder\MenuBuilderInterface;

/**
 * Evento Principal do bundle Menu
 *
 * @author Fábio Lemos Elizandro 
 */
class MenuEvent extends Event
{

   /**
    *
    * @var MenuBuilderInterface
    */
   private $builder;

    /**
     * @var array
     */
    protected $menu;

    function __construct(MenuBuilderInterface $menuBuilder, array $menu)
    {
        $this->builder = $menuBuilder;
        $this->menu = $menu;
    }

    /**
     * Get Builder
     * 
     * @return MenuBuilderInterface
     */
    public function getBuilder()
    {
        return $this->builder;
    }

    /**
     * Get menu
     * 
     * @return array
     */
    public function getMenus()
    {
        return $this->menu;
    }


}
