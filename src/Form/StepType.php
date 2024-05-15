<?php

namespace App\Form;

use App\Entity\Step;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\OptionsResolver\OptionsResolver;

class StepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('position', ChoiceType::class, [
                'choices' => [
                    'Priorité importante' => 'priorité_importante',
                    'Priorité moins importante' => 'priorité_moins_importante',
                ],
                'label' => 'Position',
                'required' => true,
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'En cours' => 'en_cours',
                    'Terminé' => 'terminé',
                    'En attente' => 'en_attente',
                ],
                'label' => 'Statut',
                'required' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Step::class,
        ]);
    }
}
