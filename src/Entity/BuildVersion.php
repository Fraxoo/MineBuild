<?php

namespace App\Entity;

use App\Repository\BuildVersionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BuildVersionRepository::class)]
class BuildVersion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'buildVersions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Mcversion $version = null;

    #[ORM\ManyToOne(inversedBy: 'buildVersions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Build $Build = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVersion(): ?Mcversion
    {
        return $this->version;
    }

    public function setVersion(?Mcversion $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function getBuild(): ?Build
    {
        return $this->Build;
    }

    public function setBuild(?Build $Build): static
    {
        $this->Build = $Build;

        return $this;
    }
}
