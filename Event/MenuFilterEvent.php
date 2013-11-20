<?php


namespace Zuni\MenuBundle\Event;

/**
 * @author Fábio Lemos Elizandro 
 */
class MenuFilterEvent extends MenuEvent
{
    
    /**
     * Set menus
     * 
     * @param array $menu
     * 
     * @return MenuFilterEvent 
     */
    public function setMenu(array $menu)
    {
        $this->menu = $menu;
        
        return $this;
    }
    
}
