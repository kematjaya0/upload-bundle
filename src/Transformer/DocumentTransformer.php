<?php

namespace Kematjaya\UploadBundle\Transformer;

use Symfony\Component\HttpFoundation\File\File;
use Kematjaya\UploadBundle\File\KmjUploadedFile;
use Kematjaya\UploadBundle\Manager\DocumentManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DocumentTransformer implements DataTransformerInterface
{
    /**
     *
     * @var DocumentManagerInterface
     */
    private $manager;
    
    /**
     * 
     * @var string
     */
    private $className;
    
    /**
     * 
     * @var string
     */
    private $additionalPath;
    
    public function __construct(DocumentManagerInterface $manager, string $className = null,  string $additionalPath = null) 
    {
        $this->manager = $manager;
        $this->className = $className;
        $this->additionalPath = $additionalPath;
    }
    
    /**
     * form file to document
     * @param type $value
     * @return File
     */
    public function reverseTransform($value) 
    {
        if (!$value) {
            return null;
        }
        
        if (!$value instanceof File) {
            return $value;
        }
        
        if ($value->getError()) {
            return $value;
        }
        
        if (!$value instanceof KmjUploadedFile) {
            $document = $this->manager->upload($value, $this->className ? $this->className : get_class($value), $this->additionalPath);
            return $document ? $document->getId() : null;
        }
        
        return null;
    }

    /**
     * from document to file
     * @param type $value
     * @return type
     */
    public function transform($value) 
    {
        if (!$value) {
            return null;
        }
        
        return $this->manager->findById($value);
    }

}
