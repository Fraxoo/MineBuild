<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Uid\Uuid;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
#[ORM\UniqueConstraint(name: 'UNIQ_USER_EMAIL', fields: ['email'])]
#[UniqueEntity(fields: ['username'], message: 'There is already an account with this username')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(length: 180)]
    private ?string $username = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $avatar_url = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $bio = null;

    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Role $role = null;

    #[ORM\Column]
    private ?bool $is_active = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * @var Collection<int, Build>
     */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Build::class, orphanRemoval: true)]
    private Collection $builds;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    /**
     * @var Collection<int, BuildLike>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BuildLike::class, orphanRemoval: true)]
    private Collection $buildLikes;

    /**
     * @var Collection<int, BuildSave>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BuildSave::class, orphanRemoval: true)]
    private Collection $buildSaves;

    /**
     * @var Collection<int, BuildRating>
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: BuildRating::class, orphanRemoval: true)]
    private Collection $buildRatings;

    /**
     * @var Collection<int, UserFollow>
     */
    #[ORM\OneToMany(mappedBy: 'follower', targetEntity: UserFollow::class, orphanRemoval: true)]
    private Collection $followingRelations;

    /**
     * @var Collection<int, UserFollow>
     */
    #[ORM\OneToMany(mappedBy: 'following', targetEntity: UserFollow::class, orphanRemoval: true)]
    private Collection $followerRelations;

    public function __construct()
    {
        $this->is_active = true;
        $this->created_at = new \DateTimeImmutable();
        $this->builds = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->buildLikes = new ArrayCollection();
        $this->buildSaves = new ArrayCollection();
        $this->buildRatings = new ArrayCollection();
        $this->followingRelations = new ArrayCollection();
        $this->followerRelations = new ArrayCollection();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = [];
        if ($this->role?->getCode()) {
            $roles[] = $this->role->getCode();
        }
        $roles[] = 'ROLE_USER';
        return array_values(array_unique($roles));
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    public function setAvatarUrl(?string $avatar_url): static
    {
        $this->avatar_url = $avatar_url;

        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): static
    {
        $this->is_active = $is_active;

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

    public function getRole(): ?Role
    {
        return $this->role;
    }

    public function setRole(Role $role): static
    {
        $this->role = $role;
        return $this;
    }

    /**
     * @return Collection<int, Build>
     */
    public function getBuilds(): Collection
    {
        return $this->builds;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @return Collection<int, BuildLike>
     */
    public function getBuildLikes(): Collection
    {
        return $this->buildLikes;
    }

    /**
     * @return Collection<int, BuildSave>
     */
    public function getBuildSaves(): Collection
    {
        return $this->buildSaves;
    }

    /**
     * @return Collection<int, BuildRating>
     */
    public function getBuildRatings(): Collection
    {
        return $this->buildRatings;
    }

    /**
     * @return Collection<int, UserFollow>
     */
    public function getFollowingRelations(): Collection
    {
        return $this->followingRelations;
    }

    /**
     * @return Collection<int, UserFollow>
     */
    public function getFollowerRelations(): Collection
    {
        return $this->followerRelations;
    }
}
