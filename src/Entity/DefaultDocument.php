<?php

/**
 * This file is part of the upload-bundle.
 */

namespace Kematjaya\UploadBundle\Entity;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Uuid;

/**
 * @package Kematjaya\UploadBundle\Entity
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DefaultDocument extends AbstractDocument
{
    /**
     * 
     * @var string
     */
    private $className;
    
    /**
     * 
     * @var DateTimeInterface
     */
    private $createdAt;
    
    /**
     * 
     * @var string
     */
    private $extension;
    
    /**
     * 
     * @var string
     */
    private $fileName;
    
    /**
     * 
     * @var string
     */
    private $path;
    
    public function getClassName(): ?string 
    {
        return $this->className;
    }

    public function getCreatedAt(): DateTimeInterface 
    {
        return $this->createdAt;
    }

    public function getExtension(): ?string 
    {
        return $this->extension;
    }

    public function getFileName(): ?string 
    {
        return $this->fileName;
    }

    public function getId(): ?UuidInterface 
    {
        return Uuid::fromDateTime(new \DateTime());
    }

    public function getPath(): ?string 
    {
        return $this->path;
    }

    public function setClassName(string $className): DocumentInterface 
    {
        $this->className = $className;
        
        return $this;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): DocumentInterface 
    {
        $this->createdAt = $createdAt;
        
        return $this;
    }

    public function setExtension(string $extension): DocumentInterface 
    {
        $this->extension = $extension;
        
        return $this;
    }

    public function setFileName(string $fileName): DocumentInterface 
    {
        $this->fileName = $fileName;
        
        return $this;
    }

    public function setPath(string $path): DocumentInterface 
    {
        $this->path = $path;
        
        return $this;
    }

    public static function fromFile(\Symfony\Component\HttpFoundation\File\File $file): DocumentInterface 
    {
        return (new DefaultDocument())
                ->setExtension($file->getExtension())
                ->setFileName($file->getFilename())
                ->setPath($file->getPath());
    }

}
