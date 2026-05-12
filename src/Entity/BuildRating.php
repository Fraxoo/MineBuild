<?php

namespace App\Entity;

use App\Repository\BuildRatingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuildRatingRepository ::class)]
class BuildRating
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'ratings')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Build $build = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'buildRatings')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $rating = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    public function __construct(Build $build, User $user, int $rating)
    {
        $this->build = $build;
        $this->user = $user;
        $this->rating = $rating;
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

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;
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
}
