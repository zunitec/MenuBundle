<?php

namespace Zuni\MenuBundle\Factory;

use Knp\Menu\Silex\RouterAwareFactory as BaseFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Zuni\MenuBundle\Event\MenuItemEvent;
use Zuni\MenuBundle\Event\MenuItemFilterOptionsEvent;
use Zuni\MenuBundle\ZuniMenuEvents;


/**
 * Fabrica de menu que lança evento de criação de menu
 *
 * @author Fábio Lemos Elizandro 
 */
class MenuFactory extends BaseFactory
{

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    
    public function __construct(EventDispatcherInterface $dispatcher, UrlGeneratorInterface $generator)
    {
        parent::__construct($generator);
        $this->dispatcher = $dispatcher;
    }
    
    public function createItem($name, array $options = array())
    {
        $eventFilterOptions = new MenuItemFilterOptionsEvent($name, $options);
        
        $this->dispatcher->dispatch(ZuniMenuEvents::MENU_ITEN_PRE_BUILDER, $eventFilterOptions);
        
        $item = parent::createItem($eventFilterOptions->getMenuName(), $eventFilterOptions->getOptions());
        
        $event = new MenuItemEvent($item);
        
        $this->dispatcher->dispatch(ZuniMenuEvents::MENU_ITEN_POST_BUILDER, $event);
        
        return $event->getItem();
    }
    
    public function createFromArray(array $data, $name = null)
    {
        if ($name === null) {
            $name = isset($data['name']) ? $data['name'] : \key($data);
        }
        
        if (isset($data['children'])) {
            $children = $data['children'];
            unset($data['children']);
        } else {
            $children = array();
        }
        
        $item = $this->createItem($name, $data);
        
        if (!$item) {
            return null;
        }
        
        foreach ($children as $name => $child) {
            $childBuilt = $this->createFromArray($child, $name);
            if ($childBuilt !== null) {
                $item->addChild($childBuilt);
            }
        }

        return $item;
    }
}
