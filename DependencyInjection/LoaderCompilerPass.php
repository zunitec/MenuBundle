<?php

namespace Zuni\MenuBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Fábio Lemos Elizandro
 */
class LoaderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('menu.loader.resolver')) {
            return null;
        }

        $definition = $container->getDefinition(
            'menu.loader.resolver'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'zuni_menu.loader'
        );
        
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall(
                'addLoader',
                array(new Reference($id))
            );
        }
    }
}