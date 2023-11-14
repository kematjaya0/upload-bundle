<?php

namespace Kematjaya\UploadBundle\Type;

use Kematjaya\UploadBundle\Manager\DocumentManagerInterface;
use Kematjaya\UploadBundle\Transformer\DocumentTransformer;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Nur Hidayatullah <kematjaya0@gmail.com>
 */
class KmjFileType extends AbstractType
{

    /**
     *
     * @var DocumentManagerInterface
     */
    private $documentManager;

    public function __construct(DocumentManagerInterface $documentManager)
    {
        $this->documentManager = $documentManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new DocumentTransformer($this->documentManager, $options['class_name'], $options['additional_path'], $options["compress"]));

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($options) {
            $data = $event->getForm()->getData();
            if (null === $data) {
                return;
            }

            $file = $this->documentManager->findById($data);
            if (null === $file) {
                return;
            }

            if (empty($options['extensions'])) {
                return;
            }

            if (in_array($file->getExtension(), $options['extensions'])) {
                return;
            }

            unlink($file->getRealPath());
            $event->getForm()->addError(
                new FormError(sprintf("allowed extension: %s", implode(", ", $options['extensions'])))
            );
        });


    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['additional_path', 'class_name', 'extension', "compress"]);
        $resolver->setDefaults([
            'additional_path' => null,
            'class_name' => null,
            "compress" => true,
            'extensions' => [],
            'invalid_message' => 'The selected issue does not exist',
        ]);
    }

    public function getParent(): string
    {
        return FileType::class;
    }
}
