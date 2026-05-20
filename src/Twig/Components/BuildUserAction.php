<?php

namespace App\Twig\Components;

use App\Entity\Build;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class BuildUserAction
{
    use DefaultActionTrait;

    #[LiveProp()]
    public Build $build;



    public function __construct(
        private EntityManagerInterface $em,
        private Security $security,
    ) {
    }
}
