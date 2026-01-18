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

    public function __construct(private DocumentManagerInterface $manager, private ?string $className = null,  private ?string $additionalPath = null, private bool $compress = true)
    {
    }

    public function reverseTransform(mixed $value):mixed
    {
        if (null === $value and null === $this->id) {

            return null;
        }



        if (!$value instanceof File) {
            if (null !== $this->id) {

                return $this->id;
            }

            return $value;
        }

        if ($value->getError()) {

            return $value;
        }

        if (!$value instanceof KmjUploadedFile) {
            $document = $this->manager->upload($value, $this->className ? $this->className : get_class($value), $this->additionalPath, $this->compress);

            return $document ? $document->getId() : null;
        }

        return null;
    }

    public function transform(mixed $value):mixed
    {
        if (null === $value) {

            return null;
        }

        $this->id = $value;

        return $this->manager->findById($value);
    }

}
