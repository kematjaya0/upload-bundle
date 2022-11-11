<?php

namespace Kematjaya\UploadBundle\Repository;

use Kematjaya\UploadBundle\Entity\DocumentInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
interface DocumentRepositoryInterface 
{
    public function createDocumentObject():DocumentInterface;
    
    public function findOneById(string $uuid):?DocumentInterface;
    
    public function save(DocumentInterface $entity):void;
    
    public function remove(string $uuid):void;
}
