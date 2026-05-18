<?php

namespace App\Form;

use App\Entity\Build;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BuildType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('dimensions_x')
            ->add('dimensions_y')
            ->add('dimensions_z')
            ->add('difficulty')
            ->add('time_estimated_min')
            ->add('game_version')
            ->add('game_mode')
            ->add('visibility')
            ->add('hidden_reason')
            ->add('hidden_at', null, [
                'widget' => 'single_text',
            ])
            ->add('views_count')
            ->add('likes_count')
            ->add('saves_count')
            ->add('downloads_count')
            ->add('ratings_count')
            ->add('rating_avg')
            ->add('created_at', null, [
                'widget' => 'single_text',
            ])
            ->add('updated_at', null, [
                'widget' => 'single_text',
            ])
            ->add('deleted_at', null, [
                'widget' => 'single_text',
            ])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('hidden_by', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Build::class,
        ]);
    }
}
