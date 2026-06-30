<?php

namespace App\Form;

use App\Entity\Build;
use App\Entity\Comment;
use App\Entity\ModerationAction;
use App\Entity\User;
use App\Enum\ModerationActionType as ModerationActionTypeEnum;
use App\Enum\ReportReasonCode;
use App\Enum\TargetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModerationActionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('target_type', EnumType::class, [
                'class' => TargetType::class,
                'choice_label' => static fn (TargetType $targetType): string => match ($targetType) {
                    TargetType::BUILD => 'Build',
                    TargetType::COMMENT => 'Comment',
                    TargetType::USER => 'User',
                },
            ])
            ->add('action', EnumType::class, [
                'class' => ModerationActionTypeEnum::class,
                'choice_label' => static fn (ModerationActionTypeEnum $action): string => match ($action) {
                    ModerationActionTypeEnum::DELETE => 'Delete',
                },
            ])
            ->add('reason')
            ->add('reason_code', EnumType::class, [
                'class' => ReportReasonCode::class,
                'choice_label' => static fn (ReportReasonCode $reasonCode): string => 'report.reason.' . $reasonCode->value,
                'choice_translation_domain' => 'messages',
            ])
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
