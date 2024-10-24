<?php

namespace Kematjaya\UploadBundle\Entity;

use Kematjaya\UploadBundle\Repository\DocumentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\HttpFoundation\File\File;
use DateTimeInterface;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[ORM\Table(name: "kmj_document")]
class Document extends AbstractDocument
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;
    #[ORM\Column(length: 255,type: "string")]
    private $class_name;
    #[ORM\Column(type: "datetime")]
    private $created_at;
    #[ORM\Column(length: 255,type: "string")]
    private $file_name;
    #[ORM\Column(length: 255,type: "string")]
    private $extension;
    #[ORM\Column(length: 255,type: "string", nullable: true)]
    private $path;

    public function getId(): ?string
    {
        return $this->id;
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

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): DocumentInterface
    {
        $this->created_at = $created_at;

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

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): DocumentInterface
    {
        $this->extension = $extension;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(?string $path): DocumentInterface
    {
        $this->path = $path;

        return $this;
    }

    public static function fromFile(File $file): DocumentInterface
    {
        return (new Document())
                ->setExtension($file->getExtension())
                ->setFileName($file->getFilename())
                ->setPath($file->getPath());
    }

}
