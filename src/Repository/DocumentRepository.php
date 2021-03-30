<?php

/**
 * This file is part of the upload-bundle.
 */

namespace Kematjaya\UploadBundle\Repository;

use Kematjaya\UploadBundle\Entity\DocumentInterface;
use Exception;

/**
 * @package Kematjaya\UploadBundle\Repository
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DocumentRepository implements DocumentRepositoryInterface
{
    
    public function createDocumentObject(): DocumentInterface 
    {
        throw new Exception(sprintf('please create an entity.'));
    }

    public function findOneById(string $uuid): ?DocumentInterface 
    {
        throw new Exception(sprintf('please create an entity.'));
    }

    public function save(DocumentInterface $entity): void 
    {
        throw new Exception(sprintf('please create an entity.'));
    }

}
