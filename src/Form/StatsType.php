<?php 

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class StatsType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
            ->add('level', IntegerType::class, [
                'label' => 'Niveau',
            ])
            ->add('class', TextType::class, [
                'label' => 'Classe',
            ])
            ->add('background', TextType::class, [
                'label' => 'Historique',
            ])
            ->add('player_name', TextType::class, [
                'label' => 'Nom du joueur',
            ])
            ->add('race', TextType::class, [
                'label' => 'Race',
            ])
            ->add('alignment', TextType::class, [
                'label' => 'Alignement',
            ])
            ->add('experience', TextType::class, [
                'label' => 'Expérience',
            ])
            ->add('age', TextType::class, [
                'label' => 'Âge',
            ])
            ->add('height', TextType::class, [
                'label' => 'Hauteur(cm)',
            ])
            ->add('Weight', TextType::class, [
                'label' => 'Poids(kg)',
            ])
            ->add('eyes', TextType::class, [
                'label' => 'Yeux',
            ])
            ->add('skin', TextType::class, [
                'label' => 'Peau',
            ])
            ->add('hair', TextType::class, [
                'label' => 'Cheveux',
            ])
        ;
    }
}