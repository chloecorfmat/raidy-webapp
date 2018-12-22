<?php

namespace AppBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    /**
     * Define config for AppBundle.
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('app');

        $rootNode
            ->children()
                ->arrayNode('twitter')
                        ->children()
                            ->scalarNode('account')->end()
                            ->scalarNode('oauth_access_token')->end()
                            ->scalarNode('oauth_access_token_secret')->end()
                            ->scalarNode('consumer_key')->end()
                            ->scalarNode('consumer_secret')->end()
                        ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
