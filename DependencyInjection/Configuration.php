<?php

namespace Becklyn\FacebookBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Validates and merges configuration from the config files
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('becklyn_facebook')
            ->children()
                ->booleanNode("add_p3p_headers")->defaultFalse()->end()
            ->end();

        return $treeBuilder;
    }
}
