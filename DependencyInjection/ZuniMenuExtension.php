<?php

namespace Zuni\MenuBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ZuniMenuExtension extends Extension
{

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter("zuni_menu.cache", $config['cache']);

        foreach ($config['providers'] as $name => $menuConfig) {

            $container->setParameter("zuni_menu.menu.{$name}.resource", $menuConfig['resource']);
            $container->setParameter("zuni_menu.menu.{$name}.options", array("resource_type" => $menuConfig['type']));
            $container->setParameter("zuni_menu.menu.{$name}.name", $name);

            // Cria o service que construirá o menu
            $container->setDefinition("zuni_menu.menu_builder.{$name}", new Definition(
                    $menuConfig['builder'], array(
                $name,
                new Reference("service_container"),
                new Reference($menuConfig['factory']),
                "%zuni_menu.menu.{$name}.resource%",
                "%zuni_menu.menu.{$name}.options%"
                    )
            ));

            // Define o comportamento do builder como factory
            $container->setDefinition("zuni_menu.menu.{$name}", new Definition($menuConfig["menu_item"]))
                    ->setFactoryService("zuni_menu.menu_builder.{$name}")
                    ->setFactoryMethod("createMenu")
                    ->setArguments(array(new Reference("request")))
                    ->setScope("request")
                    ->addTag("knp_menu.menu", array("alias" => $name))
            ;

            // Cria o listener de seguranca caso a seguranca esteja habilitada 
            if ($menuConfig['security']) {
                $container->setDefinition("zuni_menu.listener.security.{$name}", new Definition(
                                "Zuni\MenuBundle\EventListener\MenuSecurityListener", array($name, new Reference("service_container"))
                                )
                        )
                        ->addTag("kernel.event_subscriber")
                ;
            }
        }
    }

    public function getAlias()
    {
        return "zuni_menu";
    }

}
