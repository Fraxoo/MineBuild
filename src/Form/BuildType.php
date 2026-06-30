<?php

namespace App\Form;

use App\Entity\Build;
use App\Entity\Category;
use App\Entity\Mcversion;
use App\Enum\BuildDifficulty;
use App\Enum\BuildGameMode;
use App\Repository\McversionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
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
            ->add('Mcversion', EntityType::class, [
                'class' => Mcversion::class,
                'required' => true,
                'choice_label' => 'number',
                
                'mapped' => false,
                'placeholder' => 'Sélectionner une version',
            ])
            ->add('difficulty', EnumType::class, [
                'class' => BuildDifficulty::class,
                'required' => true,
                'placeholder' => 'Sélectionner une difficulté',
                'choice_label' => static fn (BuildDifficulty $difficulty): string => match ($difficulty) {
                    BuildDifficulty::EASY => 'Facile',
                    BuildDifficulty::MEDIUM => 'Moyen',
                    BuildDifficulty::HARD => 'Difficile',
                    BuildDifficulty::EXPERT => 'Expert',
                },
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
            ->add('game_mode', EnumType::class, [
                'class' => BuildGameMode::class,
                'required' => true,
                'placeholder' => 'Sélectionner un mode',
                'choice_label' => static fn (BuildGameMode $gameMode): string => match ($gameMode) {
                    BuildGameMode::SURVIVAL => 'Survie',
                    BuildGameMode::CREATIVE => 'Créatif',
                },
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
                        extensions: [
                            'zip',
                            'schematic',
                            'nbt',
                        ],
                        maxSizeMessage: 'Le fichier monde ne doit pas dépasser 50 Mo.',
                        extensionsMessage: 'Format autorisé : .zip, .schematic ou .nbt.',
                    ),
                ],
                'attr' => [
                    'accept' => '.zip,.schematic,.nbt',
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
