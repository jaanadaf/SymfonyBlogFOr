<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;




#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $email = null;

    // Propriété username
    #[ORM\Column(length: 255, unique: true)]
    private ?string $username = null;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Article::class)]
    private Collection $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getArticles(): Collection
    {
        return $this->articles;
    }

    // Getter pour username
    public function getUsername(): ?string
    {
        return $this->username;
    }

    // Setter pour username
    public function setUsername(string $username): static
    {
        $this->username = $username;
        return $this;
    }

    // Getter pour email
    public function getEmail(): ?string
    {
        return $this->email;
    }

    // Setter pour email
    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    // Ajout de la méthode __toString
    public function __toString(): string
    {
        return $this->username ?? $this->email ?? 'Unknown';
    }
}
