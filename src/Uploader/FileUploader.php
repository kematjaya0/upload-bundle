<?php

namespace Kematjaya\UploadBundle\Uploader;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Kematjaya\Upload\Uploader\FileUploader as Uploader;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class FileUploader implements UploaderInterface
{
    /**
     *
     * @var ContainerInterface 
     */
    private $container;
    
    /**
     *
     * @var SluggerInterface
     */
    private $slugger;
    
    /**
     *
     * @var string
     */
    private $targetDir;
    
    public function __construct(ContainerInterface $container, SluggerInterface $slugger)
    {
        $this->container = $container;
        $this->slugger = $slugger;
        
        $this->targetDir = $container->hasParameter('upload') ? $container->getParameter('upload') : null;
    }
    
    public function setTargetDirectory(string $uploadDir):UploaderInterface
    {
        $this->targetDir = $uploadDir;
        
        return $this;
    }
    
    public function getTargetDirectory(): ?string 
    {
        return $this->targetDir;
    }

    public function upload(UploadedFile $file, string $directory = null): ?File 
    {
        $uploader = new Uploader($this->getTargetDirectory(), $this->slugger);
        return $uploader->upload($file, $directory);
    }

}
