<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     * @Groups({"character_list"})
     * @Groups({"character_read"})
     * 
     * @Groups({"gallery_list"})
     * @Groups({"gallery_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * 
     * @Groups({"character_list"})
     * @Groups({"character_read"})
     * 
     * @Groups({"gallery_list"})
     * @Groups({"gallery_read"})
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     * 
     * @Groups({"character_list"})
     * @Groups({"character_read"})
     * 
     * @Groups({"gallery_list"})
     * @Groups({"gallery_read"})
     */
    private $status;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * 
     * @Groups({"character_list"})
     * @Groups({"character_read"})
     * 
     * @Groups({"gallery_list"})
     * @Groups({"gallery_read"})
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     * 
     * @Groups({"character_list"})
     * @Groups({"character_read"})
     * 
     * @Groups({"gallery_list"})
     * @Groups({"gallery_read"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=Mode::class, inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     * 
     */
    private $mode;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="gamesDM")
     * @ORM\JoinColumn(nullable=false)
     * 
     */
    private $dm;

    /**
     * @ORM\OneToMany(targetEntity=Character::class, mappedBy="game", orphanRemoval=true)
     * 
     */
    private $characters;

    /**
     * @ORM\OneToMany(targetEntity=Gallery::class, mappedBy="game", orphanRemoval=true)
     * 
     */
    private $galleries;

    /**
     * @ORM\OneToMany(targetEntity=GameUsers::class, mappedBy="game")
     * 
     */
    private $gameUsers;

    public function __construct()
    {
        $this->characters = new ArrayCollection();
        $this->galleries = new ArrayCollection();
        $this->gameUsers = new ArrayCollection();
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
