<?php

namespace App\Entity;

use App\Repository\BuildViewRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuildViewRepository::class)]
class BuildView
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'buildViews')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Build $build_id = null;

    #[ORM\ManyToOne(inversedBy: 'buildViews')]
    private ?User $user_id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ip_hash = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $viewed_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBuildId(): ?Build
    {
        return $this->build_id;
    }

    public function setBuildId(?Build $build_id): static
    {
        $this->build_id = $build_id;

        return $this;
    }

    public function getUserId(): ?User
    {
        return $this->user_id;
    }

    public function setUserId(?User $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getIpHash(): ?string
    {
        return $this->ip_hash;
    }

    public function setIpHash(?string $ip_hash): static
    {
        $this->ip_hash = $ip_hash;

        return $this;
    }

    public function getViewedAt(): ?\DateTimeImmutable
    {
        return $this->viewed_at;
    }

    public function setViewedAt(\DateTimeImmutable $viewed_at): static
    {
        $this->viewed_at = $viewed_at;

        return $this;
    }
}
