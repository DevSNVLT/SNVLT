<?php

namespace App\Entity\Observateur;

use App\Repository\Observateur\TypeRapportOiRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'observateur.type_rapport_oi')]
#[ORM\Entity(repositoryClass: TypeRapportOiRepository::class)]
class TypeRapportOi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelle = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }
}
