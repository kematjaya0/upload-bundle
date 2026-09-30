<?php

namespace Kematjaya\UploadBundle\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Kematjaya\UploadBundle\Entity\DocumentInterface;
use Kematjaya\UploadBundle\Manager\DocumentManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Kernel asli (Framework, Twig, Doctrine + sqlite) dengan schema dan folder upload yang bersih per test.
 */
abstract class BundleTestCase extends WebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        (new Filesystem())->remove([AppKernel::workDir().'/uploads', AppKernel::workDir().'/test.sqlite']);

        $this->client = static::createClient();

        $entityManager = $this->entityManager();
        (new SchemaTool($entityManager))->createSchema($entityManager->getMetadataFactory()->getAllMetadata());
    }

    protected function entityManager(): EntityManagerInterface
    {
        return static::getContainer()->get('doctrine')->getManager();
    }

    protected function manager(): DocumentManagerInterface
    {
        return static::getContainer()->get('test.document_manager');
    }

    protected function uploadsDir(): string
    {
        return static::getContainer()->getParameter('upload')['uploads_dir'];
    }

    /**
     * File upload tiruan; test: true melewati pengecekan is_uploaded_file().
     */
    protected function uploadedFile(string $name, string $content): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'kmj');
        file_put_contents($path, $content);

        return new UploadedFile($path, $name, null, null, true);
    }

    protected function pdfFile(string $name = 'Surat Jalan.pdf'): UploadedFile
    {
        return $this->uploadedFile($name, file_get_contents(__DIR__.'/file/test.pdf'));
    }

    protected function pngFile(string $name = 'logo.png'): UploadedFile
    {
        $image = imagecreatetruecolor(20, 20);
        imagefill($image, 0, 0, imagecolorallocate($image, 200, 30, 30));
        ob_start();
        imagepng($image);

        return $this->uploadedFile($name, ob_get_clean());
    }

    protected function upload(UploadedFile $file, bool $compress = true): DocumentInterface
    {
        $document = $this->manager()->upload($file, 'App\Entity\Foo', null, $compress);
        $this->entityManager()->flush();

        return $document;
    }
}
