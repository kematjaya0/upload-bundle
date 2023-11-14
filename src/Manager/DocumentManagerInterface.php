<?php

namespace Kematjaya\UploadBundle\Manager;

use Kematjaya\UploadBundle\Uploader\UploaderInterface;
use Kematjaya\UploadBundle\Entity\DocumentInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface DocumentManagerInterface 
{
    public function upload(UploadedFile $file, string $className, string $directory = null, bool $compress=true):DocumentInterface;
    
    public function createDocument(File $file, string $className): DocumentInterface;
    
    public function getUploader():UploaderInterface;
    
    public function findById(string $uuid):?File;
    
    public function remove(string $uuid):void;
}
