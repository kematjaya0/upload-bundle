<?php

namespace Kematjaya\UploadBundle\Extension;

use Kematjaya\UploadBundle\Type\KmjFileType;
use Kematjaya\UploadBundle\Repository\DocumentRepositoryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DocumentExtension extends AbstractTypeExtension
{
    /**
     *
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    
    /**
     *
     * @var DocumentManagerInterface 
     */
    private $repository;
    
    public function __construct(UrlGeneratorInterface $urlGenerator, DocumentRepositoryInterface $repository) 
    {
        $this->urlGenerator = $urlGenerator;
        $this->repository = $repository;
    }
    
    public static function getExtendedTypes(): iterable 
    {
        return [
            KmjFileType::class
        ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['html_class', 'html_label', 'html_icon']);
        $resolver->setDefaults([
            'html_class' => 'btn btn-sm btn-outline-success',
            'html_label' => null,
            'html_icon' => '<span class="fa fa-download"></span>'
        ]);
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if ($form->getData()) {
            $view->vars['html_class'] = $options['html_class'];
            $view->vars['html_label'] = $options['html_label'];
            $view->vars['html_icon'] = $options['html_icon'];
            
            $document = $this->repository->findOneById($form->getData());
            if ($document) {
                $view->vars['html_label'] = $options['html_label'] ? $options['html_label'] : $document->getFileName();
                $view->vars['download_url'] = $this->urlGenerator->generate("kmj_upload_download", ['id' => $document->getId()]);
            }
            
        }
    }
}
