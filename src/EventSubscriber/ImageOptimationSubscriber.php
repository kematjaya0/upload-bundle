<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\UploadBundle\EventSubscriber;

use Kematjaya\UploadBundle\Event\PostUploadFileEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Description of ImageOptimationSUbscriber
 *
 * @author apple
 */
class ImageOptimationSubscriber implements EventSubscriberInterface 
{
    //put your code here
    public static function getSubscribedEvents() 
    {
        return [
            PostUploadFileEvent::EVENT_NAME => "optimation"
        ];
    }

    public function optimation(PostUploadFileEvent $event):void
    {
        $uploadedFile = $event->getFile();
        $allowedImages = ['jpeg', 'gif', 'jpg', 'png'];
        if (!in_array($uploadedFile->getExtension(), $allowedImages)) {
            
            return;
        }
        
        $mimeInfo = getimagesize($uploadedFile->getRealPath());
        $imageMimeType = $mimeInfo['mime'];
        $optimizedFile = $this->compressImage($imageMimeType, $uploadedFile, 50);
        
        unlink($uploadedFile->getRealPath());
        
        $event->setFile($optimizedFile);
    }
    
    protected function compressImage(string $mimeType, File $originalFile, float $quality = 50): File
    {
        switch ($mimeType) {
            case 'image/png':
                $img = imagecreatefrompng($originalFile->getPathname());
                break;
            case 'image/jpeg':
                $img = imagecreatefromjpeg($originalFile->getPathname());
                break;
            case 'image/gif':
                $img = imagecreatefromgif($originalFile->getPathname());
                break;
            default:
                $img = imagecreatefromjpeg($originalFile->getPathname());
        }
        
        $name = str_replace("." . $originalFile->getExtension(), "", $originalFile->getFilename());
        $newImagePath = sprintf("%s/%s-optimized.jpg", $originalFile->getPath(), $name);
        if (false === imagejpeg($img, $newImagePath, $quality)) {
            throw new \Exception("failed to optimized image.");
        }
        
        return new File($newImagePath);
    }

}
