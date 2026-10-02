<?php

namespace Kematjaya\UploadBundle\Controller;

use Kematjaya\UploadBundle\Repository\DocumentRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DownloadController extends AbstractController
{
    public function download(DocumentRepositoryInterface $repository, string $id): Response
    {
        $document = $repository->findOneById($id);
        if (null === $document) {
            return new Response(sprintf('unable to load document with id: %s', $id), Response::HTTP_NOT_FOUND);
        }

        $path = $document->getPath() . DIRECTORY_SEPARATOR . $document->getFileName();
        if (!is_file($path)) {
            return new Response('File not found !!', Response::HTTP_NOT_FOUND);
        }

        return $this->file($path, null, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
