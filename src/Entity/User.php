<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
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
     * @ORM\Column(type="string", length=128, unique=true)
     * 
     * @Groups({"gallery_list"})
     * @Groups({"gallery_read"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     * 
     * @Groups({"character_list"})
     * @Groups({"character_read"})
     * 
     * @Groups({"gallery_list"})
     * @Groups({"gallery_read"})
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=64)
     * 
     * @Groups({"gallery_list"})
     * @Groups({"gallery_read"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     * 
     * @Groups({"character_list"})
     * @Groups({"character_read"})
     * 
     * @Groups({"gallery_list"})
     * @Groups({"gallery_read"})
     */
    private $picture;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * 
     * @Groups({"gallery_list"})
     * @Groups({"gallery_read"})
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     * 
     * @Groups({"gallery_list"})
     * @Groups({"gallery_read"})
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Game::class, mappedBy="dm", orphanRemoval=true)
     * 
     * @Groups({"gallery_list"})
     * @Groups({"gallery_read"})
     */
    private $gamesDM;

    /**
     * @ORM\OneToMany(targetEntity=Character::class, mappedBy="user", orphanRemoval=true)
     * 
     * @Groups({"gallery_list"})
     * @Groups({"gallery_read"})
     */
    private $characters;

    /**
     * @ORM\OneToMany(targetEntity=GameUsers::class, mappedBy="user")
     * 
     * @Groups({"gallery_list"})
     * @Groups({"gallery_read"})
     */
    private $gameUsers;

    public function __construct()
    {
        $this->gamesDM = new ArrayCollection();
        $this->characters = new ArrayCollection();
        $this->gameUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    /**
     * @return Collection<int, Game>
     */
    public function getGamesDM(): Collection
    {
        return $this->gamesDM;
    }

    public function addGamesDM(Game $gamesDM): self
    {
        if (!$this->gamesDM->contains($gamesDM)) {
            $this->gamesDM[] = $gamesDM;
            $gamesDM->setDm($this);
        }

        return $this;
    }

    public function removeGamesDM(Game $gamesDM): self
    {
        if ($this->gamesDM->removeElement($gamesDM)) {
            // set the owning side to null (unless already changed)
            if ($gamesDM->getDm() === $this) {
                $gamesDM->setDm(null);
            }
        }

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
            $character->setUser($this);
        }

        return $this;
    }

    public function removeCharacter(Character $character): self
    {
        if ($this->characters->removeElement($character)) {
            // set the owning side to null (unless already changed)
            if ($character->getUser() === $this) {
                $character->setUser(null);
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
            $gameUser->setUser($this);
        }

        return $this;
    }

    public function removeGameUser(GameUsers $gameUser): self
    {
        if ($this->gameUsers->removeElement($gameUser)) {
            // set the owning side to null (unless already changed)
            if ($gameUser->getUser() === $this) {
                $gameUser->setUser(null);
            }
        }

        return $this;
    }
}
