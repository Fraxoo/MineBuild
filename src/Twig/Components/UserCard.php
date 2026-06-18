<?php

namespace App\Twig\Components;

use App\Entity\User;
use App\Entity\UserFollow;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class UserCard
{
    public $item;

        public ?string $followType = null;


}
