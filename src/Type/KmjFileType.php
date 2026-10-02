<?php

namespace Kematjaya\UploadBundle\Type;

use Kematjaya\UploadBundle\Manager\DocumentManagerInterface;
use Kematjaya\UploadBundle\Transformer\DocumentTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class KmjFileType extends AbstractType
{
    public function __construct(private readonly DocumentManagerInterface $documentManager) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new DocumentTransformer(
            $this->documentManager,
            $options['class_name'],
            $options['additional_path'],
            $options['compress']
        ));

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($options): void {
            $data = $event->getForm()->getData();
            if (!is_string($data) || empty($options['extensions'])) {
                return;
            }

            $file = $this->documentManager->findById($data);
            if (null === $file) {
                return;
            }

            $allowed = array_map(strtolower(...), $options['extensions']);
            if (in_array(strtolower($file->getExtension()), $allowed, true)) {
                return;
            }

            unlink($file->getPathname());
            $event->getForm()->addError(
                new FormError(sprintf('allowed extension: %s', implode(', ', $options['extensions'])))
            );
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'additional_path' => null,
            'class_name' => null,
            'compress' => true,
            'extensions' => [],
            'invalid_message' => 'The selected issue does not exist',
        ]);
        // 'extension' tidak dipakai; tetap didefinisikan agar form lama yang mengirimnya tidak error
        $resolver->setDefined(['extension']);
    }

    public function getParent(): string
    {
        return FileType::class;
    }
}
