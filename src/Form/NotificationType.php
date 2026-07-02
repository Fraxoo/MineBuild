<?php

namespace App\Form;

use App\Entity\Build;
use App\Entity\Comment;
use App\Entity\Notification;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type')
            ->add('message')
            ->add('read_at', null, [
                'widget' => 'single_text',
            ])
            ->add('created_at', null, [
                'widget' => 'single_text',
            ])
            ->add('recipient', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('actor', EntityType::class, [
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
            'data_class' => Notification::class,
        ]);
    }
}
