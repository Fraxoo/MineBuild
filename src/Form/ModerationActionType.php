<?php

namespace App\Form;

use App\Entity\Build;
use App\Entity\Comment;
use App\Entity\ModerationAction;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModerationActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('target_type')
            ->add('action')
            ->add('reason')
            ->add('created_at', null, [
                'widget' => 'single_text',
            ])
            ->add('moderator', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('target_user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('build', EntityType::class, [
                'class' => Build::class,
                'choice_label' => 'id',
            ])
            ->add('comment', EntityType::class, [
                'class' => Comment::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ModerationAction::class,
        ]);
    }
}
