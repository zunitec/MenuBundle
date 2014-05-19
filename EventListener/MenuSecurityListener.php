<?php

namespace Zuni\MenuBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zuni\MenuBundle\Event\MenuBuiltFilterEvent;
use Zuni\MenuBundle\ZuniMenuEvents;

/**
 * Listener que cuida da seguran'ca do menu
 *
 * @author Fabio Lemos Elizandro
 */
class MenuSecurityListener implements EventSubscriberInterface
{

    /**
     * Usado para qual o menu ser'a filtrar 
     * 
     * @var string
     */
    private $menuName;

    /**
     *
     * @var ContainerInterface
     */
    private $container;

    function __construct($menuName, ContainerInterface $container)
    {
        $this->menuName = $menuName;
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array(
            ZuniMenuEvents::MENU_POST_BUILDER => "index"
        );
    }

    /**
     * Faz seguranca dos menus, bloquea o mesmo caso ele esteje bloqueado
     * 
     * @param MenuItemEvent $event
     */
    public function index(MenuBuiltFilterEvent $event)
    {
        if ($event->getBuilder()->getName() === $this->menuName) {
            $this->menuSecurity($event->getMenuBuilt());
            $this->garbageCollector($event->getMenuBuilt());
        }
    }

    private function menuSecurity(\Knp\Menu\ItemInterface $item)
    {
        $display = false;

        $rolesUser = $this->getUser()->getRoles();
        $rolesMenu = $this->getRoleFromMenuItem($item);

        if (!$rolesMenu) {
            $display = true;
        }

        foreach ($rolesMenu as $role) {
            if (\in_array(\strtoupper($role), $rolesUser, true)) {
                $display = true;
                break;
            }
        }

        $item->setDisplay($display);
        $item->setDisplayChildren($display);

        foreach ($item->getChildren() as $child) {
            $this->menuSecurity($child);
        }

        //Para corrigir problema ao montar menu que tinha sub sub menu que não estava liberado e mostrava mesmo assim
        if ($item->getChildren()) {
            $display = false;
            foreach ($item->getChildren() as $child) {
                if ($child->isDisplayed()) {
                    $display = true;
                    break;
                }
            }
            $item->setDisplay($display);
            $item->setDisplayChildren($display);
        }
    }

    /**
     * Remove os menus que n~ao est~ao sendo utilizados
     * 
     * @param \Knp\Menu\ItemInterface $item
     */
    private function garbageCollector(\Knp\Menu\ItemInterface $item)
    {
        $display = false;
        foreach ($item->getChildren() as $child) {
            /* @var $child \Knp\Menu\ItemInterface */
            if (!$display && $child->isDisplayed()) {
                $display = true;
            }
            $this->garbageCollector($child);
        }
        // se tem url n~ao 'e lixo
        if (!$item->getUri()) {
            $item->setDisplay($display);
        }
    }

    /**
     * Retorna a lista de roles do menu
     * 
     * @param \Knp\Menu\ItemInterface $menu
     * @return array lista de roles do menu
     */
    private function getRoleFromMenuItem(\Knp\Menu\ItemInterface $menu)
    {
        $role = $menu->getExtra("role", array());

        return \is_array($role) ? $role : array($role);
    }

    /**
     * Pega o usuario que est'a logado 
     * 
     * @see \Symfony\Component\Security\Core\Authentication\Token\TokenInterface::getUser() 
     * @return \Symfony\Component\Security\Core\User\UserInterface Usuar'ario da session
     * @throws LogicException Se o bundle de segunra'ca n'ao estiver habilitado
     * @throws \InvalidArgumentException Se o usuario nao for uma instancia de UserInterface
     */
    private function getUser()
    {
        if (!$this->container->has('security.context')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->container->get('security.context')->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        if (!($user instanceof \Symfony\Component\Security\Core\User\UserInterface)) {
            throw new \InvalidArgumentException(
            "O usu'ario deve ser uma instancia de \\Symfony\\Component\\Security\\Core\\User\\UserInterface"
            );
        }

        return $user;
    }

}
