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

class GameType extends AbstractType
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

            ->add('Status',ChoiceType::class,[
                "label" => "statut de la partie",
                    "choices" => [
                        "En cours" => 0,
                        "terminée" => 1,
                        "inactive" => 2,
                    ],
                    "multiple" => false,
                    "expanded" => true
            ])

            // ! penser à ajouter la possibilité d'ajouter/supprimer une image

            ->add('Mode',EntityType::class,[
                "label" => "Mode de jeu",
                'class' => Mode::class,
                "choice_label" => "name",
                "multiple" => false,
                "expanded" => false
            ])

            ->add('dm', EntityType::class, [
                "label" => "Maitre du jeu",
                'class' => User::class,
                "choice_label" => "login",
                "multiple" => false,
                "expanded" => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
            'custom_option' => "default"
        ]);
    }
}