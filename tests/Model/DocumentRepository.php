<?php

namespace Kematjaya\UploadBundle\Tests\Model;

use Kematjaya\UploadBundle\Entity\DocumentInterface;
use Kematjaya\UploadBundle\Repository\DocumentRepositoryInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DocumentRepository implements DocumentRepositoryInterface
{
    
    public function createDocumentObject(): DocumentInterface 
    {
        return new Document();
    }

    public function findOneById(string $uuid): ?DocumentInterface 
    {
        return null;
    }

    public function save(DocumentInterface $entity): void 
    {
        
    }

    public function remove(string $uuid): void
    {
        // TODO: Implement remove() method.
    }
}
