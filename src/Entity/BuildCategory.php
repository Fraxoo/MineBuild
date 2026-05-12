<?php

namespace App\Entity;

use App\Repository\BuildCategoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuildCategoryRepository::class)]
class BuildCategory
{
    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'buildCategories')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Build $build = null;

    #[ORM\Id]
    #[ORM\ManyToOne(inversedBy: 'buildCategories')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Category $category = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    public function __construct(Build $build, Category $category)
    {
        $this->build = $build;
        $this->category = $category;
        $this->created_at = new \DateTimeImmutable();
    }

    public function getBuild(): ?Build
    {
        return $this->build;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
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
