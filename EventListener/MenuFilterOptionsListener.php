<?php

namespace Zuni\MenuBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zuni\MenuBundle\ZuniMenuEvents;
use Zuni\MenuBundle\Event\MenuItemFilterOptionsEvent;

/**
 * Listener que cuida da seguran'ca do menu
 *
 * @author Fabio Lemos Elizandro
 */
class MenuFilterOptionsListener implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
            ZuniMenuEvents::MENU_ITEN_PRE_BUILDER => "filterOptions"
        );
    }

    /**
     * Filtra e organiza as opçoes do menu
     * antes de ser contruidos
     * 
     * @param MenuItemFilterOptionsEvent $event
     */
    public function filterOptions(MenuItemFilterOptionsEvent $event)
    {
        $options = $event->getOptions();

        if (isset($options['title'])) {
            $options['label'] = $options['title'];
        }

        if (isset($options['branches'])) {
            $options['extras']['branches'] = $options['branches'];
        }else{
            $options['extras']['branches'] = array();
        }

        if (isset($options['class'])) {
            $options['attributes']['class'] = isset($options['attributes']['class']) ? $options['attributes']['class'] . $options['class'] : $options['class'];
        } else {
            $options['attributes']['class'] = array();
        }

        if (isset($options['role'])) {
            $options['extras']['role'] = is_array($options['role']) ? $options['role'] : array($options['role']);
        } else {
            $options['extras']['role'] = array();
        }

        $event->setOptions($options);
    }

}
