<?php

namespace Kematjaya\UploadBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Kematjaya\UploadBundle\Repository\DocumentRepository;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
#[ORM\Table(name: 'kmj_document')]
class Document extends AbstractDocument
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $class_name = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $file_name = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $extension = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $path = null;

    public function getId(): ?string
    {
        return $this->id?->toRfc4122();
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

    public function getCreatedAt(): \DateTimeInterface
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
        return (new self())
            ->setExtension($file->getExtension())
            ->setFileName($file->getFilename())
            ->setPath($file->getPath());
    }
}
