<?php

namespace Kematjaya\UploadBundle\Twig;

use Kematjaya\UploadBundle\Repository\DocumentRepositoryInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ImageExtension extends AbstractExtension
{
    public function __construct(
        private readonly DocumentRepositoryInterface $repository,
        private readonly Environment $twig,
    ) {
    }

    public function getTests(): array
    {
        return [
            new TwigTest('is_image', [$this, 'isImage']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('image_view', [$this, 'imageView'], ['is_safe' => ['html']]),
            new TwigFunction('image_link', [$this, 'imageLink'], ['is_safe' => ['html']]),
        ];
    }

    public function isImage(?string $id): bool
    {
        $document = null !== $id ? $this->repository->findOneById($id) : null;
        if (null === $document) {
            return false;
        }

        $path = $document->getPath().DIRECTORY_SEPARATOR.$document->getFileName();

        return is_file($path) && false !== @getimagesize($path);
    }

    public function imageView(?string $id = null, array $attribute = []): ?string
    {
        return $this->twig->render('@Upload/_single_image.twig', [
            'data' => $id, 'attributes' => $this->generateHTMLAttributes($attribute),
        ]);
    }

    public function imageLink(?string $id = null, array $attribute = []): ?string
    {
        $options = $attribute;
        $options['label_type'] = DownloadExtension::LABEL_FILENAME;

        return $this->twig->render('@Upload/_image.twig', [
            'data' => $id, 'options' => $options, 'attributes' => $this->generateHTMLAttributes($attribute),
        ]);
    }

    protected function generateHTMLAttributes(array $attributes = []): ?string
    {
        return HtmlAttributes::render($attributes);
    }
}
