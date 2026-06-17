<?php

namespace App\Twig\Components;

use App\Entity\Comment;
use App\Entity\Report;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class Modal
{
    use DefaultActionTrait;


    public Report $report;

    public bool $isReportModal = false;

    public bool $isUserModal = false;

    public bool $isBuildModal = false;

    public bool $isCommentModal = false;

    public Comment $comment;
}
