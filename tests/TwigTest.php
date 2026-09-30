<?php

namespace Kematjaya\UploadBundle\Tests;

use Symfony\Component\Uid\Uuid;

class TwigTest extends BundleTestCase
{
    public function testDownloadLink(): void
    {
        $document = $this->upload($this->pdfFile());
        $url = '/kmj/'.$document->getId().'/download';

        $html = $this->render('{{ download_link(id) }}', ['id' => $document->getId()]);
        $this->assertStringContainsString($url, $html);
        $this->assertStringContainsString('class="btn btn-sm btn-outline-success"', $html);
        $this->assertStringContainsString('download', $html);

        $html = $this->render("{{ download_link(id, {label_type: 'filename'}) }}", ['id' => $document->getId()]);
        $this->assertStringContainsString($document->getFileName(), $html);

        $html = $this->render("{{ download_link(id, {label: 'Unduh', attr: {class: 'x', title: 'a\"b'}}) }}", ['id' => $document->getId()]);
        $this->assertStringContainsString('Unduh', $html);
        $this->assertStringContainsString('class="x" title="a&quot;b"', $html);

        $this->assertSame('', trim($this->render('{{ download_link(null) }}')));
    }

    public function testImageView(): void
    {
        $image = $this->upload($this->pngFile());
        $pdf = $this->upload($this->pdfFile());

        $html = $this->render("{{ image_view(id, {width: '50'}) }}", ['id' => $image->getId()]);
        $this->assertStringContainsString('src="/kmj/'.$image->getId().'/download" width="50"', $html);

        $this->assertStringContainsString('no-image.png', $this->render('{{ image_view(id) }}', ['id' => $pdf->getId()]));
        $this->assertStringContainsString('no-image.png', $this->render('{{ image_view(null) }}'));
        $this->assertStringContainsString('no-image.png', $this->render('{{ image_view(id) }}', ['id' => (string) Uuid::v4()]));
    }

    public function testImageLink(): void
    {
        $image = $this->upload($this->pngFile());
        $pdf = $this->upload($this->pdfFile());

        $html = $this->render('{{ image_link(id) }}', ['id' => $image->getId()]);
        $this->assertStringContainsString('<img src="/kmj/'.$image->getId().'/download"', $html);

        // bukan gambar: jatuh ke link download berlabel nama file
        $html = $this->render('{{ image_link(id) }}', ['id' => $pdf->getId()]);
        $this->assertStringContainsString($pdf->getFileName(), $html);
        $this->assertStringNotContainsString('<img', $html);
    }

    public function testImageWithMissingFile(): void
    {
        $image = $this->upload($this->pngFile());
        unlink($image->getPath().'/'.$image->getFileName());

        $this->assertStringContainsString('no-image.png', $this->render('{{ image_view(id) }}', ['id' => $image->getId()]));
    }

    private function render(string $template, array $context = []): string
    {
        return static::getContainer()->get('twig')->createTemplate($template)->render($context);
    }
}
