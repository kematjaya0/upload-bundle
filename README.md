# upload-bundle
1. installation
```
composer require kematjaya/upload-bundle
```
2. add to bundles.php
```
...
Kematjaya\UploadBundle\UploadBundle::class => ['all' => true]
...
```
3. create class for implement DocumentInterface
```
/// src/Entity/Document.php
<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Kematjaya\UploadBundle\Entity\DocumentInterface;
use Kematjaya\UploadBundle\Entity\AbstractDocument;
/**
 * @ORM\Entity(repositoryClass=DocumentRepository::class)
 */
class Document extends AbstractDocument
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $class_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $file_name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extension;

    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): DocumentInterface
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getClassName(): ?string
    {
        return $this->class_name;
    }

    public function setClassName(string $class_name): DocumentInterface
    {
        $this->class_name = $class_name;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->file_name;
    }

    public function setFileName(string $file_name): DocumentInterface
    {
        $this->file_name = $file_name;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): DocumentInterface
    {
        $this->path = $path;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): DocumentInterface
    {
        $this->extension = $extension;

        return $this;
    }

    public static function fromFile(\Symfony\Component\HttpFoundation\File\File $file): DocumentInterface 
    {
        return (new Document())
                ->setExtension($file->getExtension())
                ->setFileName($file->getFilename())
                ->setPath($file->getPath());
    }

}
```
4. create repo that implement DocumentRepositoryInterface
```
<?php

namespace App\Repository;

use App\Entity\Document;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Kematjaya\UploadBundle\Repository\DocumentRepositoryInterface;
use Kematjaya\UploadBundle\Entity\DocumentInterface;
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
        $this->_em->beginTransaction();
        try{
            
            $this->_em->persist($entity);
        
            $this->_em->flush();
            
            $this->_em->commit();
            
        } catch (\Exception $ex) 
        {
            $this->_em->rollback();
            
            throw $ex;
        }
    }

}
```
5. register repo in config/services.yml and set upload path in parameter
```
parameters:
    upload: '%kernel.project_dir%/public/uploads'
    
services:
    Kematjaya\UploadBundle\Repository\DocumentRepositoryInterface:
        class: App\Repository\DocumentRepository
```
6. use KmjFileType in form
```
...
use Kematjaya\UploadBundle\Type\KmjFileType;
...
...
public function buildForm(FormBuilderInterface $builder, array $options)
{
    ...
    $builder->add('attachment', KmjFileType::class, [
      'label' => 'attachment'
    ]);
    
    // or add additional directory inside upload dir
    $builder->add('attachment', KmjFileType::class, [
      'label' => 'attachment',
      'additional_path' => 'foo'
    ]);
    ...
}
```
7. update your database schema
```
php bin/console doctrine:schema:update --force
```
8. add form theme in config/packages/twig.yml
```
twig:
    form_themes: [
            '@Upload/fields.html.twig',
            ......
        ]
```
