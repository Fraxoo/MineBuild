<?php

namespace App\Twig\Components;

use App\Entity\User;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class PaginationComponent
{
    use DefaultActionTrait;

    public  $items = [];

    public int $totalItems = 0;

    public int $totalPages = 0;

    public int $currentPage = 1;

    public ?User $user ;

    public ?bool $isFollow = false;

}
