<?php
namespace Ils;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface {

    /**
     * @var TreeBuilder
     */
    protected $builder;


    public function __construct(TreeBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $rootNode = $this->builder->root('backup');

        $rootNode
            ->children()
                ->arrayNode('databases')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('host')->isRequired()->end()
                            ->scalarNode('port')->end()
                            ->scalarNode('username')->isRequired()->end()
                            ->scalarNode('password')->isRequired()->end()
                            ->arrayNode('exclude')
                                ->prototype('scalar')
                                ->end()
                            ->booleanNode('files')->end()
                            ->booleanNode('gzip')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('files')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('path')->isRequired()->end()
                            ->booleanNode('gzip')->end()
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('use_nice')->end()
                ->scalarNode('time_limit')->end()
                ->arrayNode('remote')
                    ->prototype('array')
                        ->children()
                            ->arrayNode('ftp')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('host')->isRequired()->end()
                                        ->scalarNode('protocol')->isRequired()->end()
                                        ->scalarNode('port')->end()
                                        ->scalarNode('username')->isRequired()->end()
                                        ->scalarNode('password')->isRequired()->end()
                                        ->scalarNode('path')->isRequired()->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->arrayNode('email')
                                ->prototype('array')
                                    ->children()
                                        ->scalarNode('host')->isRequired()->end()
                                        ->scalarNode('protocol')->isRequired()->end()
                                        ->scalarNode('port')->end()
                                        ->scalarNode('username')->isRequired()->end()
                                        ->scalarNode('password')->isRequired()->end()
                                        ->scalarNode('path')->isRequired()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $this->builder;
    }
}