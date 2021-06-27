<?php

/**
 * This file is part of the upload-bundle.
 */

namespace Kematjaya\UploadBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;

/**
 * @package Kematjaya\UploadBundle\Twig
 * @license https://opensource.org/licenses/MIT MIT
 * @author  Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DownloadExtension extends AbstractExtension
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
            new TwigFunction('download_link',[$this, 'downloadLink'], ['is_safe' => ['html']])
        ];
    }
    
    public function downloadLink(string $id = null):?string
    {
        return $this->twig->render('@Upload/_download.twig', [
            'data' => $id
        ]);
    }
}
