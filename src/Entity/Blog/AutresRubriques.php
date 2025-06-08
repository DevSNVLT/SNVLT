<?php

namespace App\Entity\Blog;

use App\Repository\Blog\AutresRubriquesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'blog.autres_rubriques')]
#[ORM\Entity(repositoryClass: AutresRubriquesRepository::class)]
class AutresRubriques
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_prenoms = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $mot_ministre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo_ministre = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMotMinistre(): ?string
    {
        return $this->mot_ministre;
    }

    public function setMotMinistre(?string $mot_ministre): static
    {
        $this->mot_ministre = $mot_ministre;

        return $this;
    }

    public function getPhotoMinistre(): ?string
    {
        return $this->photo_ministre;
    }

    public function setPhotoMinistre(?string $photo_ministre): static
    {
        $this->photo_ministre = $photo_ministre;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNomPrenoms(): ?string
    {
        return $this->nom_prenoms;
    }

    /**
     * @param string|null $nom_prenoms
     */
    public function setNomPrenoms(?string $nom_prenoms): void
    {
        $this->nom_prenoms = $nom_prenoms;
    }

    public function __toString(): string
    {
       return $this->nom_prenoms;
    }
}
