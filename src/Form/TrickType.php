<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Category;
use App\Entity\Picture;
use App\Form\PictureType;
use App\Form\VideoType;
use App\Repository\PictureRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $trick = $builder->getData();
        $builder
            ->add('mainPicture', EntityType::class, [
                'class' => Picture::class,
                'choices' => $trick->getPictures(),
                'multiple' => false,
                'expanded' => true,
                'label' => false,
                'choice_label' => false
            ])
            ->add('pictures', CollectionType::class, [
                'entry_type' => PictureType::class,
                'label' => false,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('videos', CollectionType::class, [
                'entry_type' => VideoType::class,
                'label' => false,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ])
            ->add('title', TextType::class, [
                'label' => 'Nom du trick'
            ])
            ->add('description',  TextareaType::class, [
                'label' => 'Description du trick',
                'attr' => ['rows' => 10]
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'label' => 'Catégorie',
                'choice_label' => 'name'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
            'trick' => null
        ]);
    }
}
