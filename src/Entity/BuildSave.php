<?php

namespace App\Entity;

use App\Repository\BuildSaveRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuildSaveRepository::class)]
class BuildSave
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'saves')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Build $build = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'buildSaves')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function __construct(Build $build, User $user)
    {
        $this->build = $build;
        $this->user = $user;
        $this->created_at = new \DateTimeImmutable();
    }

    public function getBuild(): ?Build
    {
        return $this->build;
    }

    public function getUser(): ?User
    {
        return $this->user;
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
}
