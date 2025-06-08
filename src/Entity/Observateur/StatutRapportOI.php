<?php

namespace App\Entity\Observateur;

use App\Repository\Observateur\StatutRapportOIRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'observateur.statut_rapport_oi')]
#[ORM\Entity(repositoryClass: StatutRapportOIRepository::class)]
class StatutRapportOI
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function __toString(): string
    {
        return $this->libelle;
    }
}
