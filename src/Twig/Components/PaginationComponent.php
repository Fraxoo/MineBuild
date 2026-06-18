<?php

namespace App\Twig\Components;

use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class PaginationComponent
{
    use DefaultActionTrait;


    public int $totalItems = 0;

    public int $totalPages = 0;

    public int $currentPage = 1;




}
