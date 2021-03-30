<?php

namespace Kematjaya\UploadBundle\Entity;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface DocumentInterface 
{
    public function getId():?string;
    
    public function setCreatedAt(\DateTimeInterface $createdAt):self;
    
    public function getCreatedAt():\DateTimeInterface;
    
    public function setClassName(string $className):self;
    
    public function getClassName():?string;
    
    public function setFileName(string $fileName):self;
    
    public function getFileName():?string;
    
    public function setPath(string $path):self;
    
    public function getPath():?string;
    
    public function setExtension(string $extension):self;
    
    public function getExtension():?string;
}
