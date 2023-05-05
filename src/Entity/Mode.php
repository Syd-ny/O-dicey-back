<?php

namespace App\Entity;

use App\Repository\ModeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ModeRepository::class)
 */
class Mode
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
    private $name;

    /**
     * @ORM\Column(type="json")
     */
    private $json_stats = [];

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

    public function getJsonStats(): ?array
    {
        return $this->json_stats;
    }

    public function setJsonStats(array $json_stats): self
    {
        $this->json_stats = $json_stats;

        return $this;
    }
}
