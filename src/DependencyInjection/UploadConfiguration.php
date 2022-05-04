<?php

namespace Kematjaya\UploadBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class UploadConfiguration implements ConfigurationInterface
{
    
    public function getConfigTreeBuilder(): TreeBuilder 
    {
        $treeBuilder = new TreeBuilder('upload');
        $rootNode = $treeBuilder->getRootNode();
        
        $this->optimizer($rootNode->children());
        
        $rootNode
            ->children()
                ->scalarNode('uploads_dir')->defaultValue('%kernel.project_dir%/docs')->end()
            ->end();
        
        return $treeBuilder;
    }

    public function optimizer(NodeBuilder $node)
    {
        $node
            ->arrayNode('optimizer')->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('image')->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode('remove_origin')->defaultTrue()->end()
                            ->scalarNode('quality')->defaultValue(50)->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }
}
