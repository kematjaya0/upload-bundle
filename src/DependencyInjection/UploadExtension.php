<?php

namespace Kematjaya\UploadBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class UploadExtension extends Extension 
{
    public function load(array $configs, ContainerBuilder $container) :void
    {
        $loader = new YamlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('services.yml');
        
        $configuration = new UploadConfiguration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter($this->getAlias(), $config);
    }
}
