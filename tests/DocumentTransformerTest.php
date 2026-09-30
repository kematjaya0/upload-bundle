<?php

namespace Kematjaya\UploadBundle\Tests;

use Kematjaya\UploadBundle\Entity\DocumentInterface;
use Kematjaya\UploadBundle\File\KmjUploadedFile;
use Kematjaya\UploadBundle\Manager\DocumentManagerInterface;
use Kematjaya\UploadBundle\Transformer\DocumentTransformer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class DocumentTransformerTest extends TestCase
{
    public function testTransform(): void
    {
        $file = new KmjUploadedFile(__FILE__, 'a.php');
        $manager = $this->createMock(DocumentManagerInterface::class);
        $manager->expects($this->once())->method('findById')->with('doc-1')->willReturn($file);

        $transformer = new DocumentTransformer($manager);

        $this->assertNull($transformer->transform(null));
        $this->assertSame($file, $transformer->transform('doc-1'));
    }

    public function testReverseTransformWithoutUpload(): void
    {
        $manager = $this->createMock(DocumentManagerInterface::class);
        $manager->expects($this->never())->method('upload');

        $transformer = new DocumentTransformer($manager);
        $this->assertNull($transformer->reverseTransform(null));

        $transformer->transform('doc-1');
        $this->assertSame('doc-1', $transformer->reverseTransform(null));
        $this->assertSame('doc-1', $transformer->reverseTransform((new KmjUploadedFile(__FILE__, 'a.php'))->setId('doc-1')));
    }

    public function testCurrentIdKeepsItsOriginalType(): void
    {
        $uuid = Uuid::v4();
        $manager = $this->createMock(DocumentManagerInterface::class);
        $manager->expects($this->once())->method('findById')->with((string) $uuid)->willReturn(null);

        $transformer = new DocumentTransformer($manager);
        $transformer->transform($uuid);

        $this->assertSame($uuid, $transformer->reverseTransform(null));
    }

    public function testReverseTransformUploadsNewFile(): void
    {
        $file = new UploadedFile(__FILE__, 'a.php', null, null, true);
        $document = $this->createMock(DocumentInterface::class);
        $document->method('getId')->willReturn('doc-2');

        $manager = $this->createMock(DocumentManagerInterface::class);
        $manager->expects($this->once())
            ->method('upload')
            ->with($file, 'App\Entity\Foo', 'surat', false)
            ->willReturn($document);

        $transformer = new DocumentTransformer($manager, 'App\Entity\Foo', 'surat', false);
        $transformer->transform('doc-1');

        $this->assertSame('doc-2', $transformer->reverseTransform($file));
    }

    public function testReverseTransformUploadError(): void
    {
        $file = new UploadedFile(__FILE__, 'a.php', null, UPLOAD_ERR_PARTIAL, true);
        $manager = $this->createMock(DocumentManagerInterface::class);
        $manager->expects($this->never())->method('upload');

        $this->assertSame($file, (new DocumentTransformer($manager))->reverseTransform($file));
    }
}
