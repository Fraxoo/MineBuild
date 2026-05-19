<?php

namespace App\Form;

use App\Entity\BuildMaterial;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BuildMaterialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Nom du matériau (ex: Stone Bricks)',
                ],
            ])
            ->add('quantity', IntegerType::class, [
                'required' => true,
                'label' => false,
                'attr' => [
                    'placeholder' => 'Qté',
                    'min' => 0,
                ],
            ])
            ->add('color', ColorType::class, [
                'label' => false,
                'required' => true,
                'attr' => [
                    'placeholder' => 'Couleur (optionnel)',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BuildMaterial::class,
        ]);
    }
}
