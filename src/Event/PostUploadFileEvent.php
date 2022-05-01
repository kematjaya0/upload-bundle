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
    
    public function __construct(File $file) 
    {
        $this->file = $file;
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


}
