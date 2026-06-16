<?php

namespace App\Twig\Components;

use App\Entity\Report;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Modal
{
    use DefaultActionTrait;


    public Report $report;
}
