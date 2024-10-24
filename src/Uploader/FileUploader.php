<?php

namespace Kematjaya\UploadBundle\Uploader;

use Kematjaya\UploadBundle\Event\PostUploadFileEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\File;
use Kematjaya\Upload\Uploader\FileUploader as Uploader;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class FileUploader extends Uploader implements UploaderInterface
{
    private ContainerInterface $container;
    private EventDispatcherInterface $eventDispatcher;
    private string $targetDir;

    public function __construct(EventDispatcherInterface $eventDispatcher, ContainerInterface $container, SluggerInterface $slugger)
    {
        $this->container = $container;
        $this->eventDispatcher = $eventDispatcher;

        $configs = $container->getParameter('upload');
        $this->targetDir = $configs['uploads_dir'];

        parent::__construct($this->targetDir, $slugger);
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

    public function upload(UploadedFile $file, string $directory = null, bool $compress = true): ?File
    {
        $uploadedFile = parent::upload($file, $directory);
        if (null === $uploadedFile) {

            return $uploadedFile;
        }

        $event = $this->eventDispatcher->dispatch(
            new PostUploadFileEvent($uploadedFile, $compress),
            PostUploadFileEvent::EVENT_NAME
        );

        return $event->getFile();
    }
}
