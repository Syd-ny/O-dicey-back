<?php

namespace App\Form;

use App\Entity\Character;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModeStatsType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
      $data = $form->getParent()->getData();
      if ($data instanceof Character) {
          $view->vars['stats'] = $data->getStats();
      }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound' => false,
        ]);
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }
}
