<?php

namespace CanalTP\NmmPortalBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('canal_tp_nmm_portal');

        $rootNode
        ->children()
            ->arrayNode('navitia')
                ->info('NAViTiA configuration')
                ->children()
                        ->scalarNode('url')
                        ->cannotBeEmpty()
                        ->isRequired()
                    ->end()
                    ->scalarNode('format')
                        ->defaultValue('object')
                    ->end()
                    ->scalarNode('response_error')
                        ->defaultValue('exception')
                    ->end()
                    ->scalarNode('token')
                        ->cannotBeEmpty()
                        ->isRequired()
                    ->end()
                ->end()
            ->end()
            ->scalarNode('enable_logs')
                ->info('Enable logs')
                ->defaultValue(false)
            ->end()
            ->arrayNode('tyr')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('url')
                        ->defaultNull()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('fenrir')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('url')
                        ->defaultNull()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
