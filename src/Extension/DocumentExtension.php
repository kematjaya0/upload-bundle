<?php

namespace Kematjaya\UploadBundle\Extension;

use Kematjaya\UploadBundle\Repository\DocumentRepositoryInterface;
use Kematjaya\UploadBundle\Type\KmjFileType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class DocumentExtension extends AbstractTypeExtension
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly DocumentRepositoryInterface $repository,
    ) {}

    public static function getExtendedTypes(): iterable
    {
        return [
            KmjFileType::class,
        ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'html_class' => 'btn btn-sm btn-outline-success',
            'html_label' => null,
            'html_icon' => '<span class="fa fa-download"></span>',
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        if (!$form->getData()) {
            return;
        }

        $view->vars['html_class'] = $options['html_class'];
        $view->vars['html_label'] = $options['html_label'];
        $view->vars['html_icon'] = $options['html_icon'];

        $document = $this->repository->findOneById($form->getData());
        if (null === $document) {
            return;
        }

        $view->vars['html_label'] = $options['html_label'] ?: $document->getFileName();
        $view->vars['download_url'] = $this->urlGenerator->generate('kmj_upload_download', ['id' => $document->getId()]);
    }
}
