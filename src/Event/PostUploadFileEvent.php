<?php

namespace Kematjaya\UploadBundle\Event;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * @author apple
 */
class PostUploadFileEvent extends Event
{
    public const EVENT_NAME = 'kematjaya.post_upload_file';

    public function __construct(private File $file, private readonly bool $compress = true)
    {
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function setFile(File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function isCompress(): bool
    {
        return $this->compress;
    }
}
