<?php

namespace App\Entity;

use App\Repository\GameRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"character_list", "character_read", "character_add", "character_edit"})
     * @Groups({"gallery_list", "gallery_read"})
     * @Groups({"modes"})
     * @Groups({"users", "charactersByUser", "gamesByUser", "invitesByUser"})
     * @Groups({"games", "charactersByGame", "usersByGame", "gameUsers", "newGame"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"users", "charactersByUser", "gamesByUser", "invitesByUser"})
     * @Groups({"gallery_list", "gallery_read"})
     * @Groups({"games", "newGame", "charactersByGame"})
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"users", "gamesByUser"})
     * @Groups({"gallery_list", "gallery_read"})
     * @Groups({"games", "charactersByGame"})
     * @Assert\NotBlank
     * @Assert\PositiveOrZero
     */
    private $status;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"users", "gamesByUser"})
     * @Groups({"gallery_list", "gallery_read"})
     * @Groups({"games", "charactersByGame"})
     * 
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"users", "gamesByUser"})
     * @Groups({"gallery_list", "gallery_read"})
     * @Groups({"games", "charactersByGame", "newGame"})
     * @Assert\NotBlank
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Mode::class, inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"games", "newGame"})
     * @Groups({"game_no_character"})
     * @Groups({"gamesByUser"})
     * @Assert\NotBlank
     */
    private $mode;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="gamesDM")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"games", "newGame"})
     * @Groups({"game_no_character"})
     * @Groups({"gamesByUser"})
     * @Assert\NotBlank
     */
    private $dm;

    /**
     * @ORM\OneToMany(targetEntity=Character::class, mappedBy="game", orphanRemoval=true)
     * @Groups({"games"})
     * @Groups({"gamesByUser"})
     */
    private $characters;

    /**
     * @ORM\OneToMany(targetEntity=Gallery::class, mappedBy="game", orphanRemoval=true)
     * @Groups({"games"})
     */
    private $galleries;

    /**
     * @ORM\OneToMany(targetEntity=GameUsers::class, mappedBy="game", orphanRemoval=true)
     * @Groups({"games"})
     */
    private $gameUsers;

    public function __construct()
    {
        $this->characters = new ArrayCollection();
        $this->galleries = new ArrayCollection();
        $this->gameUsers = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->status = 0;
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getMode(): ?Mode
    {
        return $this->mode;
    }

    public function setMode(?Mode $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    public function getDm(): ?User
    {
        return $this->dm;
    }

    public function setDm(?User $dm): self
    {
        $this->dm = $dm;

        return $this;
    }

    /**
     * @return Collection<int, Character>
     */
    public function getCharacters(): Collection
    {
        return $this->characters;
    }

    public function addCharacter(Character $character): self
    {
        if (!$this->characters->contains($character)) {
            $this->characters[] = $character;
            $character->setGame($this);
        }

        return $this;
    }

    public function removeCharacter(Character $character): self
    {
        if ($this->characters->removeElement($character)) {
            // set the owning side to null (unless already changed)
            if ($character->getGame() === $this) {
                $character->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Gallery>
     */
    public function getGalleries(): Collection
    {
        return $this->galleries;
    }

    public function addGalleries(Gallery $gallery): self
    {
        if (!$this->galleries->contains($gallery)) {
            $this->galleries[] = $gallery;
            $gallery->setGame($this);
        }

        return $this;
    }

    public function removeGalleries(Gallery $gallery): self
    {
        if ($this->galleries->removeElement($gallery)) {
            // set the owning side to null (unless already changed)
            if ($gallery->getGame() === $this) {
                $gallery->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GameUsers>
     */
    public function getGameUsers(): Collection
    {
        return $this->gameUsers;
    }

    public function addGameUser(GameUsers $gameUser): self
    {
        if (!$this->gameUsers->contains($gameUser)) {
            $this->gameUsers[] = $gameUser;
            $gameUser->setGame($this);
        }

        return $this;
    }

    public function removeGameUser(GameUsers $gameUser): self
    {
        if ($this->gameUsers->removeElement($gameUser)) {
            // set the owning side to null (unless already changed)
            if ($gameUser->getGame() === $this) {
                $gameUser->setGame(null);
            }
        }

        return $this;
    }
}
