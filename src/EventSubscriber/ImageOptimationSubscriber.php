<?php

namespace Kematjaya\UploadBundle\EventSubscriber;

use Kematjaya\UploadBundle\Event\PostUploadFileEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @author apple
 */
class ImageOptimationSubscriber implements EventSubscriberInterface
{
    private const array ALLOWED_EXTENSIONS = ['jpeg', 'gif', 'jpg', 'png'];

    /**
     * @var array{remove_origin: bool, quality: int|string}
     */
    private readonly array $optimizer;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->optimizer = $parameterBag->get('upload')['optimizer']['image'];
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PostUploadFileEvent::EVENT_NAME => 'optimation',
        ];
    }

    public function optimation(PostUploadFileEvent $event): void
    {
        if (!$event->isCompress() || !extension_loaded('gd')) {
            return;
        }

        $uploadedFile = $event->getFile();
        if (!in_array(strtolower($uploadedFile->getExtension()), self::ALLOWED_EXTENSIONS, true)) {
            return;
        }

        // berekstensi gambar tetapi isinya bukan gambar: biarkan apa adanya
        $imageInfo = @getimagesize($uploadedFile->getPathname());
        if (false === $imageInfo) {
            return;
        }

        $optimizedFile = $this->compressImage($imageInfo['mime'], $uploadedFile);

        if (true === $this->optimizer['remove_origin']) {
            unlink($uploadedFile->getPathname());
        }

        $event->setFile($optimizedFile);
    }

    protected function compressImage(string $mimeType, File $originalFile): File
    {
        $quality = max(0, min(100, (int) $this->optimizer['quality']));
        $image = match ($mimeType) {
            'image/png' => imagecreatefrompng($originalFile->getPathname()),
            'image/gif' => imagecreatefromgif($originalFile->getPathname()),
            default => imagecreatefromjpeg($originalFile->getPathname()),
        };
        if (false === $image) {
            throw new \RuntimeException('failed to optimized image.');
        }

        $newImagePath = sprintf(
            '%s/%s-optimized.%s',
            $originalFile->getPath(),
            $originalFile->getBasename('.' . $originalFile->getExtension()),
            $originalFile->getExtension()
        );

        // format asli dipertahankan, supaya isi file cocok dengan ekstensinya
        $saved = match ($mimeType) {
            'image/png' => $this->savePng($image, $newImagePath, $quality),
            'image/gif' => imagegif($image, $newImagePath),
            default => imagejpeg($image, $newImagePath, $quality),
        };

        if (false === $saved) {
            throw new \RuntimeException('failed to optimized image.');
        }

        return new File($newImagePath);
    }

    private function savePng(\GdImage $image, string $path, int $quality): bool
    {
        imagealphablending($image, false);
        imagesavealpha($image, true);

        // PNG lossless: quality 0-100 dipetakan ke level kompresi 9-0
        return imagepng($image, $path, (int) round((100 - $quality) * 9 / 100));
    }
}
