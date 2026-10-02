<?php

namespace Kematjaya\UploadBundle\Tests;

use Kematjaya\UploadBundle\Event\PostUploadFileEvent;
use Kematjaya\UploadBundle\EventSubscriber\ImageOptimationSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class ImageOptimationSubscriberTest extends TestCase
{
    private string $dir;

    protected function setUp(): void
    {
        $this->dir = sys_get_temp_dir() . '/kmj-optimizer-' . uniqid();
        mkdir($this->dir);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->dir);
    }

    /**
     * @dataProvider images
     */
    public function testCompressKeepsFormat(string $name, string $format, string $mime): void
    {
        $event = new PostUploadFileEvent($this->image($name, $format));
        $this->subscriber()->optimation($event);

        $optimized = $event->getFile();
        $this->assertSame(pathinfo($name, PATHINFO_FILENAME) . '-optimized.' . pathinfo($name, PATHINFO_EXTENSION), $optimized->getFilename());
        $this->assertSame($mime, getimagesize($optimized->getPathname())['mime']);
        $this->assertFileDoesNotExist($this->dir . '/' . $name);
    }

    public static function images(): array
    {
        return [
            ['foto.jpg', 'jpeg', 'image/jpeg'],
            ['foto.jpeg', 'jpeg', 'image/jpeg'],
            ['FOTO.JPG', 'jpeg', 'image/jpeg'],
            ['logo.png', 'png', 'image/png'],
            ['anim.gif', 'gif', 'image/gif'],
        ];
    }

    public function testPngKeepsTransparency(): void
    {
        $image = imagecreatetruecolor(10, 10);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
        imagepng($image, $this->dir . '/transparan.png');

        $event = new PostUploadFileEvent(new File($this->dir . '/transparan.png'));
        $this->subscriber()->optimation($event);

        $optimized = imagecreatefrompng($event->getFile()->getPathname());
        $this->assertSame(127, imagecolorsforindex($optimized, imagecolorat($optimized, 5, 5))['alpha']);
    }

    public function testKeepOrigin(): void
    {
        $event = new PostUploadFileEvent($this->image('foto.jpg', 'jpeg'));
        $this->subscriber(false)->optimation($event);

        $this->assertFileExists($this->dir . '/foto.jpg');
        $this->assertFileExists($this->dir . '/foto-optimized.jpg');
    }

    public function testSkippedWhenCompressDisabled(): void
    {
        $file = $this->image('foto.jpg', 'jpeg');
        $event = new PostUploadFileEvent($file, false);
        $this->subscriber()->optimation($event);

        $this->assertSame($file, $event->getFile());
    }

    public function testNonImageIsLeftAlone(): void
    {
        file_put_contents($this->dir . '/catatan.txt', 'teks');
        file_put_contents($this->dir . '/palsu.jpg', 'bukan gambar');

        foreach (['catatan.txt', 'palsu.jpg'] as $name) {
            $file = new File($this->dir . '/' . $name);
            $event = new PostUploadFileEvent($file);
            $this->subscriber()->optimation($event);

            $this->assertSame($file, $event->getFile());
            $this->assertFileExists($this->dir . '/' . $name);
        }
    }

    private function subscriber(bool $removeOrigin = true): ImageOptimationSubscriber
    {
        return new ImageOptimationSubscriber(new ParameterBag([
            'upload' => ['optimizer' => ['image' => ['remove_origin' => $removeOrigin, 'quality' => 50]]],
        ]));
    }

    private function image(string $name, string $format): File
    {
        $image = imagecreatetruecolor(20, 20);
        imagefill($image, 0, 0, imagecolorallocate($image, 10, 120, 200));
        ('image' . $format)($image, $this->dir . '/' . $name);

        return new File($this->dir . '/' . $name);
    }
}
