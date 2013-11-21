<?php

namespace Zuni\MenuBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zuni\MenuBundle\Event\MenuBuiltFilterEvent;
use Zuni\MenuBundle\ZuniMenuEvents;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Listener que ativo qual o menu está selecionado de acordo com a rota 
 * da requisicao 
 * 
 * @author Fábio Lemos Elizandro 
 */
class MenuCurrentListener implements EventSubscriberInterface
{

    /**
     * Http request 
     * 
     * @var ContainerInterface
     */
    private $container;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array(
            ZuniMenuEvents::MENU_POST_BUILDER => "index"
        );
    }

    public function index(MenuBuiltFilterEvent $event)
    {
        $this->activeCurrent($event->getMenuBuilt(), $this->container->get("request"));
    }

    private function activeCurrent(ItemInterface $item, Request $request)
    {
        $routes = \array_merge($item->getExtra("branches", array()), array($item->getExtra("route")));

        if (\in_array($request->get("_route"), $routes)) {
            $item->setCurrent(true);
        } else {
            foreach ($item->getChildren() as $child) {
                $this->activeCurrent($child, $request);
            }
        }
    }

}
