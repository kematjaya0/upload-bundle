<?php

namespace Kematjaya\UploadBundle\Transformer;

use Kematjaya\UploadBundle\File\KmjUploadedFile;
use Kematjaya\UploadBundle\Manager\DocumentManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DocumentTransformer implements DataTransformerInterface
{
    /**
     * id dokumen yang sedang tersimpan, dipakai lagi bila form dikirim tanpa file baru.
     * Bertipe mixed agar nilai aslinya (mis. objek Uuid) dikembalikan tanpa diubah jadi string.
     */
    private mixed $id = null;

    public function __construct(
        private readonly DocumentManagerInterface $manager,
        private readonly ?string $className = null,
        private readonly ?string $additionalPath = null,
        private readonly bool $compress = true,
    ) {}

    public function reverseTransform(mixed $value): mixed
    {
        if ($value instanceof KmjUploadedFile) {
            return $value->getId() ?? $this->id;
        }

        if (!$value instanceof UploadedFile) {
            return $this->id ?? $value;
        }

        if (UPLOAD_ERR_OK !== $value->getError()) {
            return $value;
        }

        $document = $this->manager->upload(
            $value,
            $this->className ?: $value::class,
            $this->additionalPath,
            $this->compress
        );

        return $document->getId();
    }

    public function transform(mixed $value): mixed
    {
        if (null === $value) {
            return null;
        }

        $this->id = $value;

        return $this->manager->findById($value);
    }
}
