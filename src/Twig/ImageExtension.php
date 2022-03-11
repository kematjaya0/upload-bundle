<?php

/**
 * This file is part of the upload-bundle.
 */

namespace Kematjaya\UploadBundle\Twig;

use Kematjaya\UploadBundle\Repository\DocumentRepositoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigTest;
use Twig\Environment;
use Minwork\Helper\Arr;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @package Kematjaya\UploadBundle\Twig
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class ImageExtension extends AbstractExtension 
{
    /**
     * 
     * @var Environment
     */
    private $twig;
    
    /**
     * 
     * @var DocumentRepositoryInterface
     */
    private $repository;
    
    public function __construct(DocumentRepositoryInterface $repository, Environment $twig) 
    {
        $this->repository = $repository;
        $this->twig = $twig;
    }
    
    public function getTests()
    {
        return [
            new TwigTest('is_image', function (string $id) {
                $document = $this->repository->findOneById($id);
                if (!$document) {
                    
                    return false;
                }
                
                $file = new File(
                    $document->getPath() . DIRECTORY_SEPARATOR . $document->getFileName()
                );
                
                $imageInfo = getimagesize($file->getRealPath());
                
                return false !== $imageInfo;
            })
        ];
    }
    
    public function getFunctions()
    {
        return [
            new TwigFunction('image_view',[$this, 'imageView'], ['is_safe' => ['html']]),
            new TwigFunction('image_link',[$this, 'imageLink'], ['is_safe' => ['html']])
        ];
    }
    
    /**
     * 
     * @param string $id
     * @param array $attribute
     * @return string|null
     */
    public function imageView(string $id = null, array $attribute = []):?string
    {
        return $this->twig->render('@Upload/_single_image.twig', [
            'data' => $id, 'attributes' => $this->generateHTMLAttributes($attribute)
        ]);
    }
    
    /**
     * 
     * @param string $id
     * @param array $attribute
     * @return string|null
     */
    public function imageLink(string $id = null, array $attribute = []):?string
    {
        $options = $attribute;
        $options['label_type'] = DownloadExtension::LABEL_FILENAME;
        return $this->twig->render('@Upload/_image.twig', [
            'data' => $id, 'options' => $options, 'attributes' => $this->generateHTMLAttributes($attribute)
        ]);
    }
    
    /**
     * 
     * @param array $attributes
     * @return string|null
     */
    protected function generateHTMLAttributes(array $attributes = array()):?string
    {
        $htmls = Arr::map($attributes, function ($k, $v) {
            
            return sprintf('%s="%s"', trim($k), trim($v));
        });
        
        return trim(implode(" ", array_values($htmls)));
    }
}
