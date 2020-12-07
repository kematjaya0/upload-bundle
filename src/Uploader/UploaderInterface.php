<?php

namespace Kematjaya\UploadBundle\Uploader;

use Kematjaya\Upload\Uploader\UploaderInterface as BaseInterface;
/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface UploaderInterface extends BaseInterface
{
    public function setTargetDirectory(string $uploadDir):self;
}
