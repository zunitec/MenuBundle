<?php

namespace Zuni\MenuBundle\Event;

use Zuni\MenuBundle\Builder\MenuBuilderInterface;
use Knp\Menu\ItemInterface;


/**
 * @author Fábio Lemos Elizandro
 */
class MenuBuiltFilterEvent extends MenuEvent
{
    private $menuBuilt;
    
    public function __construct(MenuBuilderInterface $menuBuilder, array $menu, ItemInterface $menuBuilt)
    {
        parent::__construct($menuBuilder, $menu);
        $this->menuBuilt = $menuBuilt;
    }
    
    /**
     * Get menu built
     * 
     * @return ItemInterface
     */
    public function getMenuBuilt()
    {
        return $this->menuBuilt;
    }

    /**
     * Set menu
     * 
     * @param \Knp\Menu\ItemInterface $menuBuilt
     * @return \Zuni\MenuBundle\Event\MenuFilterBuiltEvent
     */
    public function setMenuBuilt(ItemInterface $menuBuilt)
    {
        $this->menuBuilt = $menuBuilt;
        
        return $this;
    }



}
