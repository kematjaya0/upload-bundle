<?php

namespace Kematjaya\UploadBundle\File;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class KmjUploadedFile extends UploadedFile
{
    /**
     * 
     * @var string
     */
    private $id;
    
    /**
     * 
     * @return string|null
     */
    function getId():?string 
    {
        return $this->id;
    }

    /**
     * 
     * @param type $id
     * @return self
     */
    function setId(string $id): self 
    {
        $this->id = $id;
        
        return $this;
    }


}
