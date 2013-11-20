<?php

namespace Zuni\MenuBundle\Event;

use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * @author Fábio Lemos Elizandro 
 */
class MenuItemEvent extends Event
{
    /**
     *
     * @var ItemInterface
     */
    private $item;
    
    function __construct(ItemInterface $item)
    {
        $this->item = $item;
    }

    /**
     * Get item
     * 
     * @return ItemInterface
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * Set item 
     * 
     * @param \Knp\Menu\ItemInterface $item
     */
    public function setItem(ItemInterface $item)
    {
        $this->item = $item;
    }

}
