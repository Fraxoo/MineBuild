<?php

namespace App\Twig\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class DeleteModal
{
    use DefaultActionTrait;

    public int $id;

    public string $type;


}
