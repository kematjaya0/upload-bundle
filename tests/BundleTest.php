<?php

namespace Kematjaya\UploadBundle\Tests;

use Kematjaya\UploadBundle\Tests\Model\Document;
use Kematjaya\UploadBundle\Tests\UploadBundleTest;
use Kematjaya\UploadBundle\Entity\DocumentInterface;
use Kematjaya\UploadBundle\Transformer\DocumentTransformer;
use Kematjaya\UploadBundle\Manager\DocumentManager;
use Kematjaya\UploadBundle\Manager\DocumentManagerInterface;
use Kematjaya\UploadBundle\Repository\DocumentRepositoryInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Kematjaya\UploadBundle\Uploader\FileUploader;
use Kematjaya\Upload\Uploader\UploaderInterface as BaseInterface;
use Kematjaya\UploadBundle\Uploader\UploaderInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class BundleTest extends WebTestCase
{
    public static function getKernelClass() :string
    {
        return UploadBundleTest::class;
    }
    
    public function testLoadBundle(): UploaderInterface
    {
        $client = parent::createClient();
        $container = $client->getContainer();
        
        $this->assertInstanceOf(FileUploader::class, $container->get(BaseInterface::class));
        
        return $container->get(BaseInterface::class);
    }
    
    /**
     * @depends testLoadBundle
     */
    public function testUploadFile(UploaderInterface $uploader):File
    {
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'file';
        $fileName = $filePath . DIRECTORY_SEPARATOR . 'test.pdf';
        $targetFile = 'test-upload.pdf';
        $fileSystem = new Filesystem();
        $fileSystem->copy($fileName, $filePath . DIRECTORY_SEPARATOR . $targetFile);
        $file = new UploadedFile( $filePath . DIRECTORY_SEPARATOR . $targetFile, $targetFile, null, null, true);
        
        $uploader->setTargetDirectory(__DIR__ . DIRECTORY_SEPARATOR . 'uploads');
        $uploadedFile = $uploader->upload($file);
        
        $this->assertInstanceOf(File::class, $uploadedFile);
        
        return $uploadedFile;
    }
    
    /**
     * 
     * @depends testLoadBundle
     * @depends testUploadFile
     */
    public function testLoadDocumentManager(UploaderInterface $uploader, File $file):DocumentManagerInterface
    {
        $documentRepo = $this->createConfiguredMock(DocumentRepositoryInterface::class, [
            'createDocumentObject' => new Document(),
            'findOneById' => Document::fromFile($file)
        ]);
        
        $this->assertTrue(true);
        return new DocumentManager($uploader, $documentRepo);
    }
    
    /**
     * @depends testUploadFile
     * @depends testLoadDocumentManager
     */
    public function testDocumentManager(File $file, DocumentManagerInterface $manager):DocumentInterface
    {
        $document = $manager->createDocument($file, "aaaa");
        $this->assertInstanceOf(DocumentInterface::class, $document);
        
        return $document;
    }
    
    /**
     * 
     * @depends testLoadDocumentManager
     */
    public function testUploadViaManager(DocumentManagerInterface $manager)
    {
        $filePath = __DIR__ . DIRECTORY_SEPARATOR . 'file';
        $fileName = $filePath . DIRECTORY_SEPARATOR . 'test.pdf';
        $targetFile = 'test-upload.pdf';
        $fileSystem = new Filesystem();
        $fileSystem->copy($fileName, $filePath . DIRECTORY_SEPARATOR . $targetFile);
        $file = new UploadedFile( $filePath . DIRECTORY_SEPARATOR . $targetFile, $targetFile, null, null, true);
        
        $document = $manager->upload($file, 'aaa');
        
        $this->assertInstanceOf(DocumentInterface::class, $document);
    }
    
    /**
     * @depends testDocumentManager
     * @depends testLoadBundle
     * @depends testUploadFile
     */
    public function testGetUploadedFile(DocumentInterface $document, UploaderInterface $uploader, File $file)
    {
        $documentRepo = $this->createConfiguredMock(DocumentRepositoryInterface::class, [
            'createDocumentObject' => new Document(),
            'findOneById' => Document::fromFile($file)
        ]);
        
        $manager = new DocumentManager($uploader, $documentRepo);
        
        $this->assertInstanceOf(File::class, $manager->findById($document->getId()));
    }
    
    /**
     * @depends testDocumentManager
     * @depends testLoadBundle
     * @depends testUploadFile
     */
    public function testTransformer(DocumentInterface $document, UploaderInterface $uploader, File $file)
    {
        $document = Document::fromFile($file);
        $documentRepo = $this->createConfiguredMock(DocumentRepositoryInterface::class, [
            'createDocumentObject' => new Document(),
            'findOneById' => $document
        ]);
        
        $manager = new DocumentManager($uploader, $documentRepo);
        
        $transformer = new DocumentTransformer($manager);
        $this->assertInstanceOf(File::class, $transformer->transform($document->getId()));
    }
}
