<?php

namespace Kematjaya\UploadBundle\Manager;

use Kematjaya\UploadBundle\Entity\DocumentInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface DocumentManagerInterface 
{
    public function upload(UploadedFile $file, string $className, string $directory = null):DocumentInterface;
    
    public function createDocument(File $file, string $className): DocumentInterface;
}
