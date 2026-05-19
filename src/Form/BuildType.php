<?php

namespace App\Form;

use App\Entity\Build;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\File;

class BuildType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $requireImages = (bool) ($options['require_images'] ?? true);
        $locale = $options['locale'];
        $builder
            ->add('image_files', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => $requireImages,
                'multiple' => true,
                'data_class' => null,
                'constraints' => [
                    new Count(
                        min: $requireImages ? 1 : 0,
                        max: 5,
                        minMessage: 'Veuillez ajouter au moins une image.',
                        maxMessage: 'Vous ne pouvez pas ajouter plus de 5 images.',
                    ),
                    new All([
                        new File(
                            maxSize: '10M',
                            maxSizeMessage: 'Chaque image ne doit pas dépasser 10 Mo.',
                            mimeTypes: [
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ],
                            mimeTypesMessage: 'Veuillez envoyer une image valide (JPG, PNG, WebP).',
                        )
                    ])
                ],
            ])
            ->add('title', null, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'ex: Château médiéval épique',
                    'maxlength' => 100,
                ],
            ])
            ->add('description', null, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Décris ton build : inspiration, fonctionnalités, conseils…',
                    'maxlength' => 2000,
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => function (Category $category) use ($locale) {
                    return $locale === 'en'
                        ? $category->getName()
                        : $category->getNameFr();
                },
                'placeholder' => 'Sélectionner une catégorie',
                'mapped' => false,
                'required' => true,
            ])
            ->add('game_version', ChoiceType::class, [
                'required' => true,
                'placeholder' => 'Sélectionner une version',
                'choices' => [
                    '1.21.x' => '1.21',
                    '1.20.x' => '1.20',
                    '1.19.x' => '1.19',
                    '1.18.x' => '1.18',
                    '1.17.x' => '1.17',
                    '1.16.x' => '1.16',
                    '1.15.x' => '1.15',
                    '1.14.x' => '1.14',
                    '1.13.x' => '1.13',
                    '1.12.x' => '1.12',
                    '1.11.x' => '1.11',
                    '1.10.x' => '1.10',
                    '1.9.x' => '1.9',
                    '1.8.x' => '1.8',
                ],
            ])
            ->add('difficulty', ChoiceType::class, [
                'required' => true,
                'placeholder' => 'Sélectionner une difficulté',
                'choices' => [
                    'Facile' => 'easy',
                    'Moyen' => 'medium',
                    'Difficile' => 'hard',
                    'Expert' => 'expert',
                ],
            ])
            ->add('dimensions_x', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Longueur',
                    'min' => 0,
                ],
            ])
            ->add('dimensions_z', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Largeur',
                    'min' => 0,
                ],
            ])
            ->add('dimensions_y', IntegerType::class, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Hauteur',
                    'min' => 0,
                ],
            ])
            ->add('time_estimated_min', IntegerType::class, [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Temps estimé (min)',
                    'min' => 0,
                ],
            ])
            ->add('game_mode', ChoiceType::class, [
                'required' => true,
                'placeholder' => 'Sélectionner un mode',
                'choices' => [
                    'Survie' => 'survival',
                    'Créatif' => 'creative',
                ],
            ])
            ->add('modded', CheckboxType::class, [
                'required' => false,
            ])
            ->add('tags', HiddenType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('materials', CollectionType::class, [
                'entry_type' => BuildMaterialType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'required' => true,
            ])
            ->add('world_file', FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new File(
                        maxSize: '50M',
                        maxSizeMessage: 'Le fichier monde ne doit pas dépasser 50 Mo.',
                    )
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Build::class,
            'require_images' => true,
            'locale' => 'fr',
        ]);
    }
}
