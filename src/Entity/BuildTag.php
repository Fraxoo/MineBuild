<?php

namespace App\Entity;

use App\Repository\BuildTagRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuildTagRepository::class)]
class BuildTag
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'buildTags')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Build $build = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'buildTags')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Tag $tag = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function __construct(Build $build, Tag $tag)
    {
        $this->build = $build;
        $this->tag = $tag;
        $this->created_at = new \DateTimeImmutable();
    }

    public function getBuild(): ?Build
    {
        return $this->build;
    }

    public function getTag(): ?Tag
    {
        return $this->tag;
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
