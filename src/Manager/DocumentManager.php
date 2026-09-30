<?php

namespace Kematjaya\UploadBundle\Manager;

use Kematjaya\UploadBundle\Entity\DocumentInterface;
use Kematjaya\UploadBundle\File\KmjUploadedFile;
use Kematjaya\UploadBundle\Repository\DocumentRepositoryInterface;
use Kematjaya\UploadBundle\Uploader\UploaderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DocumentManager implements DocumentManagerInterface
{
    public function __construct(
        private readonly UploaderInterface $uploader,
        private readonly DocumentRepositoryInterface $documentRepo,
    ) {
    }

    public function getUploader(): UploaderInterface
    {
        return $this->uploader;
    }

    public function upload(UploadedFile $file, string $className, ?string $directory = null, bool $compress = true): DocumentInterface
    {
        $uploadedFile = $this->uploader->upload($file, $directory, $compress);
        if (null === $uploadedFile) {
            throw new \RuntimeException('failed to upload file');
        }

        return $this->createDocument($uploadedFile, $className);
    }

    public function createDocument(File $file, string $className): DocumentInterface
    {
        $document = $this->documentRepo->createDocumentObject();
        $document
            ->setCreatedAt(new \DateTime())
            ->setClassName($className)
            ->setExtension($file->getExtension())
            ->setFileName($file->getFilename())
            ->setPath($file->getPath());

        $this->documentRepo->save($document);

        return $document;
    }

    public function findById(string $uuid): ?File
    {
        $document = $this->documentRepo->findOneById($uuid);
        if (null === $document) {
            return null;
        }

        // dokumen masih tercatat tetapi file-nya sudah tidak ada di disk
        $path = $document->getPath().DIRECTORY_SEPARATOR.$document->getFileName();
        if (!is_file($path)) {
            return null;
        }

        $file = new KmjUploadedFile($path, $document->getFileName());
        $file->setId($document->getId());

        return $file;
    }

    public function remove(string $uuid): void
    {
        $this->documentRepo->remove($uuid);
    }
}
