<?php

namespace Kematjaya\UploadBundle\Entity;

use Symfony\Component\HttpFoundation\File\File;
/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
abstract class AbstractDocument implements DocumentInterface
{
    abstract public static function fromFile(File $file):DocumentInterface;
}
