<?php

namespace Kematjaya\UploadBundle\Tests;

use Kematjaya\Upload\Uploader\UploaderInterface as BaseInterface;
use Kematjaya\UploadBundle\Entity\Document;
use Kematjaya\UploadBundle\File\KmjUploadedFile;
use Kematjaya\UploadBundle\Uploader\FileUploader;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\Uuid;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class BundleTest extends BundleTestCase
{
    public function testUploaderIsSharedService(): void
    {
        $uploader = static::getContainer()->get(BaseInterface::class);

        $this->assertInstanceOf(FileUploader::class, $uploader);
        $this->assertSame($uploader, static::getContainer()->get('test.uploader'));
        $this->assertSame($uploader, $this->manager()->getUploader());
        $this->assertSame($this->uploadsDir(), $uploader->getTargetDirectory());
    }

    public function testUploadCreatesDocument(): void
    {
        $document = $this->upload($this->pdfFile());

        $this->assertInstanceOf(Document::class, $document);
        $this->assertTrue(Uuid::isValid($document->getId()));
        $this->assertSame('App\Entity\Foo', $document->getClassName());
        $this->assertSame('pdf', $document->getExtension());
        $this->assertMatchesRegularExpression('/^surat-jalan-[0-9a-f]+\.pdf$/', $document->getFileName());
        $this->assertSame(realpath($this->uploadsDir()), realpath($document->getPath()));
        $this->assertFileExists($document->getPath().'/'.$document->getFileName());

        $this->entityManager()->clear();
        $found = static::getContainer()->get('test.document_repository')->findOneById($document->getId());
        $this->assertSame($document->getFileName(), $found->getFileName());
    }

    public function testUploadToAdditionalPath(): void
    {
        $document = $this->manager()->upload($this->pdfFile(), 'Foo', 'surat/2026');

        $this->assertSame(realpath($this->uploadsDir().'/surat/2026'), realpath($document->getPath()));
    }

    public function testSetTargetDirectoryIsUsedForUpload(): void
    {
        $target = AppKernel::workDir().'/uploads/lain';
        static::getContainer()->get('test.uploader')->setTargetDirectory($target);

        $document = $this->manager()->upload($this->pdfFile(), 'Foo');

        $this->assertSame(realpath($target), realpath($document->getPath()));
    }

    public function testFindById(): void
    {
        $document = $this->upload($this->pdfFile());

        $file = $this->manager()->findById($document->getId());
        $this->assertInstanceOf(KmjUploadedFile::class, $file);
        $this->assertSame($document->getId(), $file->getId());
        $this->assertSame($document->getFileName(), $file->getClientOriginalName());

        $this->assertNull($this->manager()->findById((string) Uuid::v4()));
    }

    public function testFindByIdWhenFileIsMissingOnDisk(): void
    {
        $document = $this->upload($this->pdfFile());
        unlink($document->getPath().'/'.$document->getFileName());

        $this->assertNull($this->manager()->findById($document->getId()));
    }

    public function testRemove(): void
    {
        $document = $this->upload($this->pdfFile());
        $id = $document->getId();

        $this->manager()->remove($id);
        $this->manager()->remove((string) Uuid::v4());

        $this->entityManager()->clear();
        $this->assertNull(static::getContainer()->get('test.document_repository')->findOneById($id));
    }

    public function testImageIsCompressedInItsOwnFormat(): void
    {
        $document = $this->upload($this->pngFile());

        $this->assertStringEndsWith('-optimized.png', $document->getFileName());
        $path = $document->getPath().'/'.$document->getFileName();
        $this->assertSame('image/png', getimagesize($path)['mime']);
        $this->assertCount(1, glob($document->getPath().'/*.png'), 'file asli dihapus setelah kompresi');
    }

    public function testImageIsNotCompressedWhenDisabled(): void
    {
        $document = $this->upload($this->pngFile(), false);

        $this->assertStringNotContainsString('-optimized', $document->getFileName());
    }

    public function testDocumentFromFile(): void
    {
        $document = Document::fromFile(new File(__DIR__.'/file/test.pdf'));

        $this->assertSame('test.pdf', $document->getFileName());
        $this->assertSame('pdf', $document->getExtension());
        $this->assertSame(__DIR__.'/file', $document->getPath());
        $this->assertNull($document->getId());
    }
}
