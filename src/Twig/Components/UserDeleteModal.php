<?php

namespace App\Twig\Components;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class UserDeleteModal
{
    public int $id;

    public string $type;

    public ?string $title = null;
}
