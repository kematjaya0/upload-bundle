<?php

namespace Kematjaya\UploadBundle\Uploader;

use Kematjaya\Upload\Uploader\FileUploader as Uploader;
use Kematjaya\UploadBundle\Event\PostUploadFileEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class FileUploader extends Uploader implements UploaderInterface
{
    private string $targetDir;

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        ParameterBagInterface $parameterBag,
        SluggerInterface $slugger,
    ) {
        $this->targetDir = $parameterBag->get('upload')['uploads_dir'];

        parent::__construct($this->targetDir, $slugger);
    }

    public function setTargetDirectory(string $uploadDir): UploaderInterface
    {
        $this->targetDir = $uploadDir;

        return $this;
    }

    public function getTargetDirectory(): ?string
    {
        return $this->targetDir;
    }

    public function upload(UploadedFile $file, ?string $directory = null, bool $compress = true): ?File
    {
        $uploadedFile = parent::upload($file, $directory);
        if (null === $uploadedFile) {
            return null;
        }

        $event = $this->eventDispatcher->dispatch(
            new PostUploadFileEvent($uploadedFile, $compress),
            PostUploadFileEvent::EVENT_NAME
        );

        return $event->getFile();
    }
}
