<?php

namespace App\Entity;

use App\Repository\ModeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ModeRepository::class)
 */
class Mode
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
     * @ORM\Column(type="string", length=128)
     * @Groups({"gallery_list", "gallery_read"})
     * @Groups({"modes"})
     * @Groups({"game_no_character"})
     * @Groups({"games", "newGame"})
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="json", name="json_stats")
     * @Groups({"gallery_list", "gallery_read"})
     * @Groups({"modes"})
     * @Groups({"games"})
     * ! notNull instead of not blank to be able to create a new mode
     * @Assert\NotNull
     */
    private $jsonstats = [];

    /**
     * @ORM\OneToMany(targetEntity=Game::class, mappedBy="mode", orphanRemoval=true)
     * 
     */
    private $games;

    public function __construct()
    {
        $this->games = new ArrayCollection();
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

    public function getJsonstats(): ?array
    {
        return $this->jsonstats;
    }

    public function setJsonStats(array $jsonstats): self
    {
        if (is_string($jsonstats)) {
            $decodedStats = json_decode($jsonstats, true);
            if ($decodedStats !== null && is_array($decodedStats)) {
                $this->jsonstats = $decodedStats;
            } else {
                $this->jsonstats = [];
            }
        } else {
            $this->jsonstats = $jsonstats;
        }

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setMode($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getMode() === $this) {
                $game->setMode(null);
            }
        }

        return $this;
    }
}
