<?php

namespace Kematjaya\UploadBundle\Tests;

use Symfony\Component\Uid\Uuid;

class DownloadTest extends BundleTestCase
{
    public function testDownload(): void
    {
        $document = $this->upload($this->pdfFile());

        $this->client->request('GET', '/kmj/' . $document->getId() . '/download');

        $response = $this->client->getResponse();
        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringStartsWith('inline', $response->headers->get('Content-Disposition'));
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
    }

    public function testUnknownDocument(): void
    {
        $this->client->request('GET', '/kmj/' . Uuid::v4() . '/download');

        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }

    public function testDocumentWithoutFile(): void
    {
        $document = $this->upload($this->pdfFile());
        unlink($document->getPath() . '/' . $document->getFileName());

        $this->client->request('GET', '/kmj/' . $document->getId() . '/download');

        $this->assertSame(404, $this->client->getResponse()->getStatusCode());
    }
}
