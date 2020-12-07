<?php

namespace Kematjaya\UploadBundle\Controller;

use Kematjaya\UploadBundle\Repository\DocumentRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DownloadController extends AbstractController
{
    public function download(DocumentRepositoryInterface $repository, string $id)
    {
        $document = $repository->findOneById($id);
        if(!$document)
        {
            throw new \Exception(sprintf('unable to load document with id: %s'. $id));
        }
        
        $file = new File($document->getPath() . DIRECTORY_SEPARATOR . $document->getFileName());
        
        return $this->file($file);
        
    }
}
