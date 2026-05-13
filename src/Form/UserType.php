<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', null, [
                'attr' => [
                    'placeholder' => 'Nom d\'utilisateur',
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez entrer un nom d\'utilisateur',
                    ),
                    new Length(
                        min: 3,
                        minMessage: 'Votre nom d\'utilisateur doit contenir au moins {{ limit }} caractères',
                        max: 255,
                        maxMessage: 'Votre nom d\'utilisateur ne doit pas dépasser {{ limit }} charactères',
                    ),
                ],
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'you@gmail.com',
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez entrer une adresse email',
                    ),
                    new Length(
                        max: 255,
                        maxMessage: 'Votre email ne doit pas dépasser {{ limit }} charactères',
                    ),
                ],
            ])
            ->add('avatar_url', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new File(
                        maxSize: '2M',
                        maxSizeMessage: 'Votre image ne doit pas dépasser 2 Mo.',
                        mimeTypes: [
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ],
                        mimeTypesMessage: 'Veuillez envoyer une image valide.',
                    )
                ],
            ])
            ->add('bio', null, [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Parlez-nous un peu de vous...',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
