<?php

namespace Zuni\MenuBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * @author Fábio Lemos Elizandro 
 */
class MenuItemFilterOptionsEvent extends Event
{

    /**
     *
     * @var string
     */
    private $menuName;

    /**
     *
     * @var array
     */
    private $options;

    function __construct($menuName, array $options)
    {
        $this->menuName = $menuName;
        $this->options = $options;
    }

    public function getMenuName()
    {
        return $this->menuName;
    }

    public function setMenuName($menuName)
    {
        $this->menuName = $menuName;
    }

    public function getOptions()
    {
        return $this->options;
    }

    public function setOptions(array $options)
    {
        $this->options = $options;
    }

}
