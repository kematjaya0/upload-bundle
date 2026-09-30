<?php

namespace Kematjaya\UploadBundle\Tests;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Kematjaya\UploadBundle\UploadBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class AppKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new DoctrineBundle(),
            new UploadBundle(),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config.yml');
    }

    public function getProjectDir(): string
    {
        return __DIR__;
    }

    public static function workDir(): string
    {
        return sys_get_temp_dir().'/kmj-upload-bundle';
    }

    public function getCacheDir(): string
    {
        return self::workDir().'/cache';
    }

    public function getLogDir(): string
    {
        return self::workDir().'/log';
    }
}
