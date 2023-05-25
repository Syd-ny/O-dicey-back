<?php

namespace App\Entity;

use App\Repository\GameUsersRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GameUsersRepository::class)
 */
class GameUsers
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
     * @ORM\Column(type="integer")
     * @Groups({"users", "invitesByUser"})
     * @Groups({"gameUsers"})
     * @Assert\PositiveOrZero
     * @Assert\NotBlank
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="gameUsers")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"gamesByUser", "invitesByUser"})
     * @Groups({"gameUsers"})
     * @Assert\NotBlank
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="gameUsers")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"gameUsers", "usersByGame"})
     * @Assert\NotBlank
     */
    private $user;

    public function __construct()
    {
        $this->status = 1;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

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
}
