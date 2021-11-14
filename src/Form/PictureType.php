<?php

namespace App\Form;

use App\Entity\Picture;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PictureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('alternateText', TextType::class, [
                'label' => 'Texte alternatif',
                'required' => false
            ])
            ->add('source', HiddenType::class, [
                'required' => false
            ])
            ->add('file', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                // 'error_bubbling' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'maxSizeMessage' => 'Le fichier dépasse la taille maximale autorisée (1024kB)',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpg',
                            'image/jpeg'
                        ],
                        'mimeTypesMessage' => 'Le fichier ne correspond pas au format demandé (png, jpg, jpeg)',
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Picture::class,
        ]);
    }
}
