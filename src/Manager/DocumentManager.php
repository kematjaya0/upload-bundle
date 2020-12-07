<?php

namespace Kematjaya\UploadBundle\Manager;

use Kematjaya\UploadBundle\Repository\DocumentRepositoryInterface;
use Kematjaya\UploadBundle\Entity\DocumentInterface;
use Kematjaya\UploadBundle\Uploader\UploaderInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DocumentManager implements DocumentManagerInterface
{
    /**
     *
     * @var UploaderInterface 
     */
    private $uploader;
    
    /**
     *
     * @var DocumentRepositoryInterface 
     */
    private $documentRepo;
    
    public function __construct(UploaderInterface $uploader, DocumentRepositoryInterface $documentRepo) 
    {
        $this->uploader = $uploader;
        $this->documentRepo = $documentRepo;
    }
    
    public function getUploader():UploaderInterface
    {
        return $this->uploader;
    }
    
    public function upload(UploadedFile $file, string $className, string $directory = null):DocumentInterface
    {
        $uploadedFile = $this->uploader->upload($file, $directory);
        if(!$uploadedFile)
        {
            throw new \Exception("failed to upload file");
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
    
    public function findById(string $uuid):?File
    {
        $document = $this->documentRepo->findOneById($uuid);
        if($document)
        {
            return new UploadedFile($document->getPath() . DIRECTORY_SEPARATOR . $document->getFileName(), $document->getFileName(), $document->getExtension());
        }
        
        return null;
    }
}
