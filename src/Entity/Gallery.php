<?php

namespace App\Entity;

use App\Repository\GalleryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GalleryRepository::class)
 */
class Gallery
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
     * @ORM\Column(type="string", length=256)
     * @Groups({"gallery_list", "gallery_read"})
     * @Groups({"games"})
     * @Assert\NotBlank
     * @Assert\Url
     */
    private $picture;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"gallery_list", "gallery_read"})
     * @Groups({"games"})
     * @Assert\PositiveOrZero
     */
    private $mainPicture;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="galleries")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"gallery_list", "gallery_read"})
     * @Assert\NotBlank
     */
    private $game;

    public function __construct()
    {
        $this->mainPicture = 0;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getMainPicture(): ?int
    {
        return $this->mainPicture;
    }

    public function setMainPicture(?int $mainPicture): self
    {
        $this->mainPicture = $mainPicture;

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
