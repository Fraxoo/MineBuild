<?php

namespace App\Form;

use App\Entity\Build;
use App\Entity\BuildImage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class BuildImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('url', FileType::class, [
                'mapped' => false,
                'required' => true,
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
            ->add('alt')
            ->add('sort_order')


        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BuildImage::class,
        ]);
    }
}
