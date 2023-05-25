<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
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
     * @ORM\Column(type="string", length=128, unique=true)
     * @Groups({"users", "charactersByUser", "gamesByUser"})
     * @Groups({"gallery_list", "gallery_read"})
     * @Groups({"games", "newGame", "usersByGame"})
     * @Assert\NotBlank
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=64, unique=true)
     * @Groups({"users", "charactersByUser", "gamesByUser", "invitesByUser"})
     * @Groups({"gallery_list", "gallery_read"})
     * @Groups({"games", "newGame", "usersByGame"})
     * @Assert\NotBlank
     */
    private $login;

    /**
     * @ORM\Column(type="string", length=64)
     * @Assert\NotBlank
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=256, nullable=true)
     * @Groups({"users"})
     * @Groups({"gallery_list", "gallery_read"})
     * @Groups({"games", "usersByGame"})
     * @Assert\Url
     */
    private $picture;

    /**
     * @ORM\Column(type="datetime_immutable", nullable=true)
     * @Groups({"users", "charactersByUser", "gamesByUser"})
     * @Groups({"games"})
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="datetime_immutable")
     * @Groups({"users", "charactersByUser", "gamesByUser"})
     * @Groups({"gallery_list", "gallery_read"})
     * @Groups({"games"})
     * @Assert\NotBlank
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity=Game::class, mappedBy="dm", orphanRemoval=true)
     * @Groups({"users"})
     * @Groups({"gallery_list", "gallery_read"})
     */
    private $gamesDM;

    /**
     * @ORM\OneToMany(targetEntity=Character::class, mappedBy="user", orphanRemoval=true)
     * @Groups({"users", "charactersByUser"})
     * @Groups({"gallery_list", "gallery_read"})
     * @Groups({"games"})
     */
    private $characters;

    /**
     * @ORM\OneToMany(targetEntity=GameUsers::class, mappedBy="user", orphanRemoval=true)
     * @Groups({"users", "invitesByUser"})
     * @Groups({"gallery_list", "gallery_read"})
     * @Groups({"games"})
     */
    private $gameUsers;

    /**
     * @ORM\Column(type="json")
     * @Groups({"users"})
     * @Groups({"games"})
     * @Assert\NotBlank
     */
    private $roles = [];

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->gamesDM = new ArrayCollection();
        $this->characters = new ArrayCollection();
        $this->gameUsers = new ArrayCollection();
        $this->roles = ["ROLE_USER"];        
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

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
        
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): ?string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): ?string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
    
    /** 
    * @return Character|null
    */
    public function getCharacter(Game $game): ?Character
    {
        foreach ($this->characters as $character) {
            if ($character->getGame() === $game) {
                return $character;
            }
        }

        return null;
    }

}
