<?php

namespace App\Entity;

use App\Enum\ModerationActionType;
use App\Enum\ReportReasonCode;
use App\Enum\TargetType;
use App\Repository\ModerationActionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ModerationActionRepository::class)]
class ModerationAction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $moderator = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'target_user_id', nullable: true, onDelete: 'SET NULL')]
    private ?User $target_user = null;

    #[ORM\Column(length: 255, enumType: TargetType::class)]
    private ?TargetType $target_type = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Build $build = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Comment $comment = null;

    #[ORM\Column(length: 255, enumType: ModerationActionType::class)]
    private ?ModerationActionType $action = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $reason = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, enumType: ReportReasonCode::class)]
    private ?ReportReasonCode $reason_code = null;

    #[ORM\OneToOne(inversedBy: 'moderationAction', cascade: ['persist', 'remove'])]
    private ?Report $report = null;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModerator(): ?User
    {
        return $this->moderator;
    }

    public function setModerator(User $moderator): static
    {
        $this->moderator = $moderator;
        return $this;
    }

    public function getTargetUser(): ?User
    {
        return $this->target_user;
    }

    public function setTargetUser(?User $target_user): static
    {
        $this->target_user = $target_user;
        return $this;
    }

    public function getTargetType(): ?TargetType
    {
        return $this->target_type;
    }

    public function setTargetType(TargetType $target_type): static
    {
        $this->target_type = $target_type;
        return $this;
    }

    public function getBuild(): ?Build
    {
        return $this->build;
    }

    public function setBuild(?Build $build): static
    {
        $this->build = $build;
        return $this;
    }

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): static
    {
        $this->comment = $comment;
        return $this;
    }

    public function getAction(): ?ModerationActionType
    {
        return $this->action;
    }

    public function setAction(ModerationActionType $action): static
    {
        $this->action = $action;
        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getReasonCode(): ?ReportReasonCode
    {
        return $this->reason_code;
    }

    public function setReasonCode(ReportReasonCode $reason_code): static
    {
        $this->reason_code = $reason_code;

        return $this;
    }

    public function getReport(): ?Report
    {
        return $this->report;
    }

    public function setReport(?Report $report): static
    {
        $this->report = $report;

        return $this;
    }

}
