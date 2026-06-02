<?php

namespace App\Entity;

use App\Repository\McversionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: McversionRepository::class)]
class Mcversion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $number = null;

    /**
     * @var Collection<int, BuildVersion>
     */
    #[ORM\OneToMany(targetEntity: BuildVersion::class, mappedBy: 'version')]
    private Collection $buildVersions;

    public function __construct()
    {
        $this->buildVersions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return Collection<int, BuildVersion>
     */
    public function getBuildVersions(): Collection
    {
        return $this->buildVersions;
    }

    public function addBuildVersion(BuildVersion $buildVersion): static
    {
        if (!$this->buildVersions->contains($buildVersion)) {
            $this->buildVersions->add($buildVersion);
            $buildVersion->setVersion($this);
        }

        return $this;
    }

    public function removeBuildVersion(BuildVersion $buildVersion): static
    {
        if ($this->buildVersions->removeElement($buildVersion)) {
            // set the owning side to null (unless already changed)
            if ($buildVersion->getVersion() === $this) {
                $buildVersion->setVersion(null);
            }
        }

        return $this;
    }
}
