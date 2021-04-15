<?php

namespace Kematjaya\UploadBundle\Repository;

use Kematjaya\UploadBundle\Entity\Document;
use Kematjaya\UploadBundle\Entity\DocumentInterface;
use Kematjaya\UploadBundle\Repository\DocumentRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Document|null find($id, $lockMode = null, $lockVersion = null)
 * @method Document|null findOneBy(array $criteria, array $orderBy = null)
 * @method Document[]    findAll()
 * @method Document[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DocumentRepository extends ServiceEntityRepository implements DocumentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Document::class);
    }

    public function createDocumentObject(): DocumentInterface 
    {
        return new Document();
    }

    public function findOneById(string $uuid): ?DocumentInterface 
    {
        return $this->find($uuid);
    }

    public function save(DocumentInterface $entity): void 
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }
}
