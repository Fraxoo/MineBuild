<?php

namespace App\Entity;

use App\Enum\ReportReasonCode;
use App\Enum\ReportStatus;
use App\Enum\TargetType;
use App\Repository\ReportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReportRepository::class)]
class Report
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $reporter = null;

    #[ORM\Column(length: 255, enumType: TargetType::class)]
    private ?TargetType $target_type = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Build $build = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?Comment $comment = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $user = null;

    #[ORM\Column(length: 255, enumType: ReportReasonCode::class)]
    private ?ReportReasonCode $reason_code = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $message = null;

    #[ORM\Column(length: 255, enumType: ReportStatus::class)]
    private ?ReportStatus $status = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $handled_by = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $handled_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\OneToOne(mappedBy: 'report', cascade: ['persist', 'remove'])]
    private ?ModerationAction $moderationAction = null;


    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReporter(): ?User
    {
        return $this->reporter;
    }

    public function setReporter(User $reporter): static
    {
        $this->reporter = $reporter;
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;
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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function getStatus(): ?ReportStatus
    {
        return $this->status;
    }

    public function setStatus(ReportStatus $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getHandledBy(): ?User
    {
        return $this->handled_by;
    }

    public function setHandledBy(?User $handled_by): static
    {
        $this->handled_by = $handled_by;
        return $this;
    }

    public function getHandledAt(): ?\DateTimeImmutable
    {
        return $this->handled_at;
    }

    public function setHandledAt(?\DateTimeImmutable $handled_at): static
    {
        $this->handled_at = $handled_at;
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    public function getModerationAction(): ?ModerationAction
    {
        return $this->moderationAction;
    }

    public function setModerationAction(?ModerationAction $moderationAction): static
    {
        // unset the owning side of the relation if necessary
        if ($moderationAction === null && $this->moderationAction !== null) {
            $this->moderationAction->setReport(null);
        }

        // set the owning side of the relation if necessary
        if ($moderationAction !== null && $moderationAction->getReport() !== $this) {
            $moderationAction->setReport($this);
        }

        $this->moderationAction = $moderationAction;

        return $this;
    }

}
