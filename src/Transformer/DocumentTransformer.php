<?php

namespace Kematjaya\UploadBundle\Transformer;

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
    
    public function __construct(DocumentManagerInterface $manager) 
    {
        $this->manager = $manager;
    }
    
    public function reverseTransform($value) 
    {
        return $value;
    }

    public function transform($value) 
    {
        if(!$value)
        {
            return null;
        }
        
        return $this->manager->findById($value);
    }

}
