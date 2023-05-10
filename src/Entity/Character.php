<?php

namespace App\Entity;

use App\Repository\CharacterRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CharacterRepository::class)
 * @ORM\Table(name="`character`")
 */
class Character
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"character_list"})
     * @Groups({"character_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=64)
     * @Groups({"users", "charactersByUser"})
     * @Groups({"character_list"})
     * @Groups({"character_read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * @Groups({"charactersByUser"})
     * @Groups({"character_list"})
     * @Groups({"character_read"})
     */
    private $picture;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Groups({"users", "charactersByUser"})
     * @Groups({"character_list"})
     * @Groups({"character_read"})
     */
    private $stats = [];

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"users", "charactersByUser"})
     * @Groups({"character_list"})
     * @Groups({"character_read"})
     */
    private $inventory;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"users", "charactersByUser"})
     * @Groups({"character_list"})
     * @Groups({"character_read"})
     */
    private $notes;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"charactersByUser"})
     * @Groups({"character_list"})
     * @Groups({"character_read"})
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"charactersByUser"})
     * @Groups({"character_list"})
     * @Groups({"character_read"})
     */
    private $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="characters", cascade={"persist"}))
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"character_list"})
     * @Groups({"character_read"})
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="characters", cascade={"persist"}))
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"charactersByUser"}) 
     * @Groups({"character_list"})
     * @Groups({"character_read"})
     */
    private $game;

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

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getStats(): ?array
    {
        return $this->stats;
    }

    public function setStats(?array $stats): self
    {
        $this->stats = $stats;

        return $this;
    }

    public function getInventory(): ?string
    {
        return $this->inventory;
    }

    public function setInventory(?string $inventory): self
    {
        $this->inventory = $inventory;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }
}
