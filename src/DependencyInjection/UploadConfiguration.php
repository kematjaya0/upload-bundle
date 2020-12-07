<?php

namespace Kematjaya\UploadBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class UploadConfiguration implements ConfigurationInterface
{
    
    public function getConfigTreeBuilder(): TreeBuilder 
    {
        $builder = new TreeBuilder('upload');
        $builder->getRootNode()
                ->children()
                    ->scalarNode('uploads_dir')->defaultValue('%kernel.project_dir%/docs')->end()
                ->end();
        
        return $builder;
    }

}
