<?php

namespace Kematjaya\UploadBundle\Tests\Model;

use Kematjaya\UploadBundle\Entity\DocumentInterface;
use Kematjaya\UploadBundle\Entity\AbstractDocument;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class Document extends AbstractDocument
{
    /**
     *
     * @var \DateTimeInterface 
     */
    private $created_at;
    
    /**
     *
     * @var string
     */
    private $class_name;
    
    /**
     *
     * @var string
     */
    private $file_name;
    
    /**
     *
     * @var string
     */
    private $path;
    
    /**
     *
     * @var string
     */
    private $extension;
    
    public function __construct() 
    {
        $this->created_at = new \DateTime();
    }
    
    function getClassName():?string 
    {
        return $this->class_name;
    }

    function getFileName():?string 
    {
        return $this->file_name;
    }

    function getPath():?string 
    {
        return $this->path;
    }

    function getExtension():?string 
    {
        return $this->extension;
    }

    function setClassName($class_name): DocumentInterface 
    {
        $this->class_name = $class_name;
        
        return $this;
    }

    function setFileName($file_name): DocumentInterface 
    {
        $this->file_name = $file_name;
        
        return $this;
    }

    function setPath($path): DocumentInterface 
    {
        $this->path = $path;
        
        return $this;
    }

    function setExtension($extension): DocumentInterface 
    {
        $this->extension = $extension;
        
        return $this;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt):DocumentInterface 
    {
        $this->created_at = $createdAt;
        
        return $this;
    }
    
    public function getCreatedAt(): \DateTimeInterface 
    {
        return $this->created_at;
    }

    public function getId(): ?\Ramsey\Uuid\UuidInterface 
    {
        return \Ramsey\Uuid\Uuid::fromDateTime($this->getCreatedAt());
    }

    public static function fromFile(File $file): DocumentInterface 
    {
        return (new Document())
                ->setExtension($file->getExtension())
                ->setFileName($file->getFilename())
                ->setPath($file->getPath());
    }

}
