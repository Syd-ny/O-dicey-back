<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('email',EmailType::class,[
                "label" => "Email",
                "attr" => [
                    "placeholder" => "Email de l'utilisateur"
                ]
            ])

            ->add('login',TextType::class,[
                "label" => "Pseudo",
                "attr" => [
                    "placeholder" => "Pseudo de l'utilisateur"
                ]
            ])

            ->add('picture',TextType::class,[
                "label" => "Image de profil",
                "attr" => [
                    "placeholder" => "Url de l'image"
                ],
                "required" => false
            ]);

            // Custom form options to display conditionnally, if I'm updating a user I can't change their password
            if($options["custom_option"] !== "edit"){
                $builder
                    ->add('password',RepeatedType::class,[
                        "type" => PasswordType::class,
                        'invalid_message' => 'Les deux champs doivent être identiques',
                        'required' => true,
                        'first_options'  => ['label' => 'Le mot de passe',"attr" => ["placeholder" => "*****"]],
                        'second_options' => ['label' => 'Répétez le mot de passe',"attr" => ["placeholder" => "*****"]],
                    ]);
            }

            $builder
                ->add('roles',ChoiceType::class,[
                    "label" => "Privilèges",
                    "choices" => [
                        "Admin" => "ROLE_ADMIN",
                        "Maître du jeu" => "ROLE_DM",
                        "Utilisateur" => "ROLE_USER"
                    ],
                    "multiple" => true,
                    "expanded" => true
                ])
          
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'custom_option' => "default"
        ]);
    }
}