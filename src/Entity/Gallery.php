<?php

namespace App\Entity;

use App\Repository\GalleryRepository;
use Doctrine\ORM\Mapping as ORM;
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
     * 
     * @Groups({"gallery_list"})
     * @Groups({"gallery_read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     * @Groups({"gallery_list"})
     * @Groups({"gallery_read"})
     */
    private $picture;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"gallery_list"})
     * @Groups({"gallery_read"})
     */
    private $mainPicture;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="galleries")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"gallery_list"})
     * @Groups({"gallery_read"})
     */
    private $game;

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
