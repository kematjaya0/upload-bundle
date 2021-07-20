<?php

namespace Kematjaya\UploadBundle\Controller;

use Kematjaya\UploadBundle\Repository\DocumentRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DownloadController extends AbstractController
{
    public function download(DocumentRepositoryInterface $repository, string $id)
    {
        try {
            $document = $repository->findOneById($id);
            if (!$document) {
                throw new \Exception(sprintf('unable to load document with id: %s'. $id));
            }
            
            if (!file_exists($document->getPath() . DIRECTORY_SEPARATOR . $document->getFileName())) {
                throw new \Exception('File not found !!');
            }
            $file = new File($document->getPath() . DIRECTORY_SEPARATOR . $document->getFileName());
            
            return $this->file($file, null, ResponseHeaderBag::DISPOSITION_INLINE);
        }catch (\Exception $exception){
            return new Response($exception->getMessage());
        }
    }
}
