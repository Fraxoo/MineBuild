<?php

namespace App\Entity;

use App\Repository\BuildRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;



#[ORM\Entity(repositoryClass: BuildRepository::class)]
class Build
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'builds')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $author = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?int $dimensions_x = null;

    #[ORM\Column(nullable: true)]
    private ?int $dimensions_y = null;

    #[ORM\Column(nullable: true)]
    private ?int $dimensions_z = null;

    #[ORM\Column(length: 255)]
    private ?string $difficulty = null;

    #[ORM\Column]
    private ?int $time_estimated_min = null;

    #[ORM\Column(length: 255)]
    private ?string $game_version = null;

    #[ORM\Column(length: 255)]
    private ?string $game_mode = null;

    #[ORM\Column(length: 255)]
    private ?string $visibility = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $hidden_reason = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $hidden_by = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $hidden_at = null;

    #[ORM\Column(options: ['default' => 0])]
    private int $views_count = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $likes_count = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $saves_count = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $downloads_count = 0;

    #[ORM\Column(options: ['default' => 0])]
    private int $ratings_count = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 3, scale: 2, options: ['default' => '0.00'])]
    private string $rating_avg = '0.00';

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $deleted_at = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(mappedBy: 'build', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    /**
     * @var Collection<int, BuildImage>
     */
    #[ORM\OneToMany(mappedBy: 'build', targetEntity: BuildImage::class, orphanRemoval: true)]
    private Collection $images;

    /**
     * @var Collection<int, BuildMaterial>
     */
    #[ORM\OneToMany(mappedBy: 'build', targetEntity: BuildMaterial::class, orphanRemoval: true)]
    private Collection $materials;

    /**
     * @var Collection<int, BuildAsset>
     */
    #[ORM\OneToMany(mappedBy: 'build', targetEntity: BuildAsset::class, orphanRemoval: true)]
    private Collection $assets;

    /**
     * @var Collection<int, BuildCategory>
     */
    #[ORM\OneToMany(mappedBy: 'build', targetEntity: BuildCategory::class, orphanRemoval: true)]
    private Collection $buildCategories;

    /**
     * @var Collection<int, BuildTag>
     */
    #[ORM\OneToMany(mappedBy: 'build', targetEntity: BuildTag::class, orphanRemoval: true)]
    private Collection $buildTags;

    /**
     * @var Collection<int, BuildLike>
     */
    #[ORM\OneToMany(mappedBy: 'build', targetEntity: BuildLike::class, orphanRemoval: true)]
    private Collection $likes;

    /**
     * @var Collection<int, BuildSave>
     */
    #[ORM\OneToMany(mappedBy: 'build', targetEntity: BuildSave::class, orphanRemoval: true)]
    private Collection $saves;

    /**
     * @var Collection<int, BuildRating>
     */
    #[ORM\OneToMany(mappedBy: 'build', targetEntity: BuildRating::class, orphanRemoval: true)]
    private Collection $ratings;

    public function __construct()
    {
        $this->created_at = new \DateTimeImmutable();
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->materials = new ArrayCollection();
        $this->assets = new ArrayCollection();
        $this->buildCategories = new ArrayCollection();
        $this->buildTags = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->saves = new ArrayCollection();
        $this->ratings = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(User $author): static
    {
        $this->author = $author;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }
    
    public function getDimensionsX(): ?int
    {
        return $this->dimensions_x;
    }

    public function setDimensionsX(?int $dimensions_x): static
    {
        $this->dimensions_x = $dimensions_x;

        return $this;
    }

    public function getDimensionsY(): ?int
    {
        return $this->dimensions_y;
    }

    public function setDimensionsY(?int $dimensions_y): static
    {
        $this->dimensions_y = $dimensions_y;

        return $this;
    }

    public function getDimensionsZ(): ?int
    {
        return $this->dimensions_z;
    }

    public function setDimensionsZ(?int $dimensions_z): static
    {
        $this->dimensions_z = $dimensions_z;

        return $this;
    }

    public function getDifficulty(): ?string
    {
        return $this->difficulty;
    }

    public function setDifficulty(string $difficulty): static
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    public function getTimeEstimatedMin(): ?int
    {
        return $this->time_estimated_min;
    }

    public function setTimeEstimatedMin(int $time_estimated_min): static
    {
        $this->time_estimated_min = $time_estimated_min;

        return $this;
    }

    public function getHiddenBy(): ?User
    {
        return $this->hidden_by;
    }

    public function setHiddenBy(?User $hidden_by): static
    {
        $this->hidden_by = $hidden_by;
        return $this;
    }

    public function getGameVersion(): ?string
    {
        return $this->game_version;
    }

    public function setGameVersion(string $game_version): static
    {
        $this->game_version = $game_version;

        return $this;
    }

    public function getGameMode(): ?string
    {
        return $this->game_mode;
    }

    public function setGameMode(string $game_mode): static
    {
        $this->game_mode = $game_mode;

        return $this;
    }

    public function getVisibility(): ?string
    {
        return $this->visibility;
    }

    public function setVisibility(string $visibility): static
    {
        $this->visibility = $visibility;

        return $this;
    }

    public function getHiddenReason(): ?string
    {
        return $this->hidden_reason;
    }

    public function setHiddenReason(?string $hidden_reason): static
    {
        $this->hidden_reason = $hidden_reason;

        return $this;
    }

    public function getHiddenAt(): ?\DateTimeImmutable
    {
        return $this->hidden_at;
    }

    public function setHiddenAt(?\DateTimeImmutable $hidden_at): static
    {
        $this->hidden_at = $hidden_at;

        return $this;
    }

    public function getViewsCount(): int
    {
        return $this->views_count;
    }

    public function setViewsCount(int $views_count): static
    {
        $this->views_count = $views_count;

        return $this;
    }

    public function getLikesCount(): int
    {
        return $this->likes_count;
    }

    public function setLikesCount(int $likes_count): static
    {
        $this->likes_count = $likes_count;

        return $this;
    }

    public function getSavesCount(): int
    {
        return $this->saves_count;
    }

    public function setSavesCount(int $saves_count): static
    {
        $this->saves_count = $saves_count;

        return $this;
    }

    public function getDownloadsCount(): int
    {
        return $this->downloads_count;
    }

    public function setDownloadsCount(int $downloads_count): static
    {
        $this->downloads_count = $downloads_count;

        return $this;
    }

    public function getRatingsCount(): int
    {
        return $this->ratings_count;
    }

    public function setRatingsCount(int $ratings_count): static
    {
        $this->ratings_count = $ratings_count;

        return $this;
    }

    public function getRatingAvg(): string
    {
        return $this->rating_avg;
    }

    public function setRatingAvg(string $rating_avg): static
    {
        $this->rating_avg = $rating_avg;
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

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deleted_at;
    }

    public function setDeletedAt(?\DateTimeImmutable $deleted_at): static
    {
        $this->deleted_at = $deleted_at;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @return Collection<int, BuildImage>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    /**
     * @return Collection<int, BuildMaterial>
     */
    public function getMaterials(): Collection
    {
        return $this->materials;
    }

    /**
     * @return Collection<int, BuildAsset>
     */
    public function getAssets(): Collection
    {
        return $this->assets;
    }

    /**
     * @return Collection<int, BuildCategory>
     */
    public function getBuildCategories(): Collection
    {
        return $this->buildCategories;
    }

    /**
     * @return Collection<int, BuildTag>
     */
    public function getBuildTags(): Collection
    {
        return $this->buildTags;
    }

    /**
     * @return Collection<int, BuildLike>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    /**
     * @return Collection<int, BuildSave>
     */
    public function getSaves(): Collection
    {
        return $this->saves;
    }

    /**
     * @return Collection<int, BuildRating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }
}
