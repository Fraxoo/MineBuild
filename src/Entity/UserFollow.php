<?php

namespace App\Entity;

use App\Repository\UserFollowRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserFollowRepository::class)]
class UserFollow
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'followingRelations')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $follower = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'followerRelations')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $following = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function __construct(User $follower, User $following)
    {
        $this->follower = $follower;
        $this->following = $following;
        $this->created_at = new \DateTimeImmutable();
    }

    public function getFollower(): ?User
    {
        return $this->follower;
    }

    public function getFollowing(): ?User
    {
        return $this->following;
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
