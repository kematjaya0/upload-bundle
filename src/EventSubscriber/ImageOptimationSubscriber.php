<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Kematjaya\UploadBundle\EventSubscriber;

use Kematjaya\UploadBundle\Event\PostUploadFileEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Description of ImageOptimationSUbscriber
 *
 * @author apple
 */
class ImageOptimationSubscriber implements EventSubscriberInterface
{
    /**
     *
     * @var array
     */
    private $optimizer;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->optimizer = $parameterBag->get("upload")["optimizer"]["image"];
    }

    public static function getSubscribedEvents():array
    {
        return [
            PostUploadFileEvent::EVENT_NAME => "optimation"
        ];
    }

    public function optimation(PostUploadFileEvent $event):void
    {
        if (!$event->isCompress()) {
            return;
        }
        $uploadedFile = $event->getFile();
        $allowedImages = ['jpeg', 'gif', 'jpg', 'png'];
        if (!in_array($uploadedFile->getExtension(), $allowedImages)) {

            return;
        }

        $mimeInfo = getimagesize($uploadedFile->getRealPath());
        $imageMimeType = $mimeInfo['mime'];
        $optimizedFile = $this->compressImage($imageMimeType, $uploadedFile);

        if (true === $this->optimizer["remove_origin"]) {
            unlink($uploadedFile->getRealPath());
        }

        $event->setFile($optimizedFile);
    }

    protected function compressImage(string $mimeType, File $originalFile): File
    {
        $quality = $this->optimizer["quality"];
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
        $newImagePath = sprintf("%s/%s-optimized.%s", $originalFile->getPath(), $name, $originalFile->getExtension());
        if (false === imagejpeg($img, $newImagePath, $quality)) {
            throw new \Exception("failed to optimized image.");
        }

        return new File($newImagePath);
    }

}
