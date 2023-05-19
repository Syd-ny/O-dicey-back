<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class GalleryType extends AbstractType
{
    

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('picture', TextType::class, [
                'label' => 'Image',
                'help' => 'Url de l\'image',
            ])
            ->add('main_picture', ChoiceType::class, [
                'label' => 'Définir comme image principale ?',
                'help' => 'Vous ne pouvez sélectionner qu\'une image principale à la fois',
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Oui' => 1,
                    'Non' => 0
                ]
            ])
        ;
    }
}