<?php

namespace Zuni\MenuBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('zuni_menu');
        $rootNode
                ->children()
                    ->arrayNode('cache')
                        ->children()
                            ->scalarNode("cache_key")->defaultValue("zuni_menu")->end()
                            ->booleanNode("enable")->defaultTrue()->end()
                        ->end()
                    ->end()
                    ->arrayNode('providers')
                        ->isRequired()
                        ->prototype('array')
                            ->children()
                                ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('file')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('path')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('translation')->isRequired()->cannotBeEmpty()->end()
                            ->end()
                    ->end()
                ->end();
        return $treeBuilder;
    }
}
