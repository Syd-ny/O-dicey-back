<?php

namespace App\Entity;

use App\Repository\GalleryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GalleryRepository::class)
 */
class Gallery
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $picture;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mainPicture;

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
}
