<?php

/**
 * This file is part of the upload-bundle.
 */

namespace Kematjaya\UploadBundle\Twig;

use Kematjaya\UploadBundle\Repository\DocumentRepositoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\Environment;
use Symfony\Contracts\Translation\TranslatorInterface;

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
    
    /**
     * 
     * @var TranslatorInterface
     */
    private $translator;
    
    /**
     * 
     * @var DocumentRepositoryInterface
     */
    private $repository;
    
    const LABEL_FILENAME = 'filename';
    const LABEL_DEFAULT = 'default';
    
    public function __construct(Environment $twig, DocumentRepositoryInterface $repository, TranslatorInterface $translator) 
    {
        $this->repository = $repository;
        $this->translator = $translator;
        $this->twig = $twig;
    }
    
    public function getFunctions()
    {
        return [
            new TwigFunction('download_link',[$this, 'downloadLink'], ['is_safe' => ['html']])
        ];
    }
    
    public function downloadLink(string $id = null, array $options = []):?string
    {
        if (!isset($options['attr']['class'])) {
            $options['attr']['class'] = 'btn btn-sm btn-outline-success';
        }
        if (!isset($options['icon'])) {
            $options['icon'] = '<span class="fa fa-download"></span>';
        }
        if (!isset($options['label_type'])) {
            $options['label_type'] = self::LABEL_DEFAULT;
            $options['label'] = $this->translator->trans('download');
        }
        
        if (self::LABEL_FILENAME === $options['label_type']) {
            $document = $id ? $this->repository->findOneById($id) : null;
            if ($document) {
                $options['label'] = $document->getFileName();
            }
        }
        
        foreach ($options['attr'] as $name => $value) {
            $options['attr'][$name] = sprintf('%s="%s"', $name, $value);
        }
        
        $options['attr'] = implode(" ", array_values($options['attr']));
        
        return $this->twig->render('@Upload/_download.twig', [
            'data' => $id, 'options' => $options
        ]);
    }
}
