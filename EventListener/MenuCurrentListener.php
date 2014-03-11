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
        $request = $this->container->get("request");
        
        //POG pra forçar selecao do menu
        $route = $request->get("_route");
        if ($request->getSession()->get('alternative_route')) {
            $route = $request->getSession()->get('alternative_route');
            $request->getSession()->remove('alternative_route');
        }

        $this->activeCurrent($event->getMenuBuilt(), $request, $route);
    }

    private function activeCurrent(ItemInterface $item, Request $request, $route)
    {
        $routes = \array_merge($item->getExtra("branches", array()), array($item->getExtra("route")));

        if (\in_array($route, $routes)) {
            $item->setCurrent(true);
        } else {
            foreach ($item->getChildren() as $child) {
                $this->activeCurrent($child, $request, $route);
            }
        }
    }

}
