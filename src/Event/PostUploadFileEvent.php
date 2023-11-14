<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

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

    /**
     *
     * @var File
     */
    private $file;

    private bool $compress;

    public function __construct(File $file, bool $compress = true)
    {
        $this->file = $file;
        $this->compress = $compress;
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
