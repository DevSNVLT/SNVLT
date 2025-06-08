<?php

namespace App\Entity\Requetes;

use App\Repository\Requetes\MenuRequetesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.menu_requetes')]
#[ORM\Entity(repositoryClass: MenuRequetesRepository::class)]
class MenuRequetes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'menuRequetes')]
    private ?TypeRequetes $code_type_requetes = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $reference = null;

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

    public function getCodeTypeRequetes(): ?TypeRequetes
    {
        return $this->code_type_requetes;
    }

    public function setCodeTypeRequetes(?TypeRequetes $code_type_requetes): static
    {
        $this->code_type_requetes = $code_type_requetes;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }
}
