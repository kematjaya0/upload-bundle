<?php

namespace Kematjaya\UploadBundle\Tests;

use Kematjaya\UploadBundle\Type\KmjFileType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class FormTest extends BundleTestCase
{
    public function testSubmitNewFile(): void
    {
        $form = $this->form(null);
        $form->submit($this->pdfFile());

        $this->assertTrue($form->isValid());
        $id = $form->getData();
        $this->assertIsString($id);

        $this->entityManager()->flush();
        $this->assertNotNull($this->manager()->findById($id));
        $this->assertSame('App\Entity\Foo', static::getContainer()->get('test.document_repository')->findOneById($id)->getClassName());
    }

    public function testSubmitWithoutFileKeepsCurrentDocument(): void
    {
        $document = $this->upload($this->pdfFile());

        $form = $this->form($document->getId());
        $form->submit(null);

        $this->assertTrue($form->isValid());
        $this->assertSame($document->getId(), $form->getData());
    }

    public function testSubmitNewFileReplacesCurrentDocument(): void
    {
        $document = $this->upload($this->pdfFile());

        $form = $this->form($document->getId());
        $form->submit($this->pdfFile('Pengganti.pdf'));

        $this->assertTrue($form->isValid());
        $this->assertNotSame($document->getId(), $form->getData());
    }

    public function testCurrentDocumentWithMissingFile(): void
    {
        $document = $this->upload($this->pdfFile());
        unlink($document->getPath() . '/' . $document->getFileName());

        $form = $this->form($document->getId());
        $form->submit(null);

        $this->assertSame($document->getId(), $form->getData());
    }

    public function testExtensionNotAllowed(): void
    {
        $form = $this->form(null, ['extensions' => ['PNG', 'jpg']]);
        $form->submit($this->pdfFile());

        $this->assertFalse($form->isValid());
        $this->assertStringContainsString('allowed extension: PNG, jpg', (string) $form->getErrors());
        $this->assertSame([], glob($this->uploadsDir() . '/*.pdf'), 'file yang ditolak dihapus');
    }

    public function testExtensionAllowedIgnoresCase(): void
    {
        $form = $this->form(null, ['extensions' => ['PDF']]);
        $form->submit($this->pdfFile());

        $this->assertTrue($form->isValid());
    }

    public function testViewHasDownloadLink(): void
    {
        $document = $this->upload($this->pdfFile());

        $view = $this->form($document->getId())->createView();
        $this->assertSame('/kmj/' . $document->getId() . '/download', $view->vars['download_url']);
        $this->assertSame($document->getFileName(), $view->vars['html_label']);

        $view = $this->form($document->getId(), ['html_label' => 'Unduh'])->createView();
        $this->assertSame('Unduh', $view->vars['html_label']);

        $this->assertArrayNotHasKey('download_url', $this->form(null)->createView()->vars);
    }

    private function form(?string $id, array $options = []): FormInterface
    {
        return static::getContainer()->get(FormFactoryInterface::class)
            ->create(KmjFileType::class, $id, $options + ['class_name' => 'App\Entity\Foo']);
    }
}
