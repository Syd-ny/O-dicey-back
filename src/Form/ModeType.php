<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Mode;
use App\Entity\User;
use App\Entity\Game;

class ModeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('name',TextType::class,[
                "label" => "Nom de la partie",
                "attr" => [
                    "placeholder" => "Nom de la partie"
                ]
            ])

            ->add('jsonstats',TextType::class,[
                "label" => "Statistiques (JSON)",
                    "attr" => [
                        "placeholder"=>"Statistiques (JSON)",
                    ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Mode::class,
            'custom_option' => "default"
        ]);
    }
}