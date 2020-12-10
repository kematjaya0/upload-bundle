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
use Symfony\Component\HttpFoundation\File\File;

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
        $builder->addModelTransformer(new DocumentTransformer($this->documentManager, $options['class_name'], $options['additional_path']));
        
        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($options) {
            $file = $event->getForm()->getData();
            if($file instanceof File and $file->getError()) {
                $event->getForm()->addError(new FormError($file->getErrorMessage()));
            }
        });
        
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['additional_path', 'class_name']);
        $resolver->setDefaults([
            'additional_path' => null,
            'class_name' => null,
            'invalid_message' => 'The selected issue does not exist',
        ]);
    }

    public function getParent(): string
    {
        return FileType::class;
    }
}
