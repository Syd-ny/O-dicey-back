<?php

namespace App\Form;

use App\Entity\Character;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CharacterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name',TextType::class,[
                "label" => "Nom du personnage",
                "attr" => [
                    "placeholder" => "Nom du personnage"
                ]
                ])

            ->add('stats',StatsType::class,[
                "label" => "Statistiques"
                ])

            ->add('inventory',TextareaType::class,[
                "label" => "Inventaire",
                "attr" => [
                    "placeholder" => "Inventaire du personnage"
                ]
            ])

            ->add('notes',TextareaType::class,[
                "label" => "Notes",
                "attr" => [
                    "placeholder" => "Notes sur le personnage et la partie"
                ]
            ])
          
        ;   
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Character::class,
            'custom_option' => "default"
        ]);
    }
}