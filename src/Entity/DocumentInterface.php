<?php

namespace Kematjaya\UploadBundle\Entity;

use Ramsey\Uuid\UuidInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface DocumentInterface 
{
    public function getId():?UuidInterface;
    
    public function setCreatedAt(\DateTimeInterface $createdAt):self;
    
    public function getCreatedAt():\DateTimeInterface;
    
    public function setClassName(string $className):self;
    
    public function getClassName():?string;
    
    public function setFileName(string $className):self;
    
    public function getFileName():?string;
    
    public function setPath(string $className):self;
    
    public function getPath():?string;
    
    public function setExtension(string $className):self;
    
    public function getExtension():?string;
}
