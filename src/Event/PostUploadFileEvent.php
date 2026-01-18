<?php


namespace Kematjaya\UploadBundle\Event;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Description of PostUploadFileEvent
 *
 * @author apple
 */
class PostUploadFileEvent extends Event
{
    const EVENT_NAME = "kematjaya.post_upload_file";

    public function __construct(private File $file, private bool $compress = true)
    {
    }

    public function getFile(): File
    {
        return $this->file;
    }

    public function setFile(File $file):self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCompress(): bool
    {
        return $this->compress;
    }
}
