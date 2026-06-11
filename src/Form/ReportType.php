<?php

namespace App\Form;

use App\Entity\Build;
use App\Entity\Comment;
use App\Entity\Report;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('message' , TextareaType::class , [
                'attr' => [
                    'placeholder' => 'Veuillez décrire la raison du report',

                ]
            ])
            ->add('reason_code', ChoiceType::class, [
                'choices' => [
                    'Spam / publicité' => 'spam',
                    'Harcèlement / intimidation' => 'harassment',
                    'Discours haineux' => 'hate_speech',
                    'Contenu sexuel / nudité' => 'nudity',
                    'Violence / contenu choquant' => 'violence',
                    'Contenu illégal / dangereux' => 'illegal',
                    'Usurpation d’identité' => 'impersonation',
                    'Droits d’auteur / contenu volé' => 'copyright',
                    'Autre' => 'other',
                ],
                'placeholder' => 'Choisir un motif',
            ])

            // ->add('build', EntityType::class, [
            //     'class' => Build::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('comment', EntityType::class, [
            //     'class' => Comment::class,
            //     'choice_label' => 'id',
            // ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Report::class,
        ]);
    }
}
