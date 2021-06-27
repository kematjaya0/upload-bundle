<?php

/**
 * This file is part of the upload-bundle.
 */

namespace Kematjaya\UploadBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;
use Minwork\Helper\Arr;

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
    
    public function __construct(Environment $twig) 
    {
        $this->twig = $twig;
    }
    
    public function getFunctions()
    {
        return [
            new TwigFunction('image_link',[$this, 'imageLink'], ['is_safe' => ['html']])
        ];
    }
    
    public function imageLink(string $id = null, array $attribute = []):?string
    {
        return $this->twig->render('@Upload/_image.twig', [
            'data' => $id, 'attributes' => $this->generateHTMLAttributes($attribute)
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
