<?php

namespace App\Entity\DocStats\Saisie;

use App\Entity\Admin\Exercice;
use App\Entity\DocStats\Pages\Pagefp;
use App\Entity\References\Essence;
use App\Repository\DocStats\Saisie\LignepagefpRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.lignepagefp')]
#[ORM\Entity(repositoryClass: LignepagefpRepository::class)]
class Lignepagefp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagefps')]
    private ?Essence $essence = null;

    #[ORM\Column(nullable: true)]
    private ?float $stock_initial = null;

    #[ORM\Column(nullable: true)]
    private ?float $entree_parc = null;

    #[ORM\Column(nullable: true)]
    private ?float $transforme = null;

    #[ORM\Column(nullable: true)]
    private ?float $transfere = null;

    #[ORM\Column(nullable: true)]
    private ?float $stock_final = null;

    #[ORM\Column(nullable: true)]
    private ?float $sciage = null;

    #[ORM\Column(nullable: true)]
    private ?float $deroulage = null;

    #[ORM\Column(nullable: true)]
    private ?float $tranchage = null;

    #[ORM\Column(nullable: true)]
    private ?float $taxe_abattage = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagefps')]
    private ?Pagefp $code_pagefp = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagefps')]
    private ?Exercice $exercice = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEssence(): ?Essence
    {
        return $this->essence;
    }

    public function setEssence(?Essence $essence): static
    {
        $this->essence = $essence;

        return $this;
    }

    public function getStockInitial(): ?float
    {
        return $this->stock_initial;
    }

    public function setStockInitial(?float $stock_initial): static
    {
        $this->stock_initial = $stock_initial;

        return $this;
    }

    public function getEntreeParc(): ?float
    {
        return $this->entree_parc;
    }

    public function setEntreeParc(?float $entree_parc): static
    {
        $this->entree_parc = $entree_parc;

        return $this;
    }

    public function getTransforme(): ?float
    {
        return $this->transforme;
    }

    public function setTransforme(?float $transforme): static
    {
        $this->transforme = $transforme;

        return $this;
    }

    public function getTransfere(): ?float
    {
        return $this->transfere;
    }

    public function setTransfere(?float $transfere): static
    {
        $this->transfere = $transfere;

        return $this;
    }

    public function getStockFinal(): ?float
    {
        return $this->stock_final;
    }

    public function setStockFinal(?float $stock_final): static
    {
        $this->stock_final = $stock_final;

        return $this;
    }

    public function getSciage(): ?float
    {
        return $this->sciage;
    }

    public function setSciage(?float $sciage): static
    {
        $this->sciage = $sciage;

        return $this;
    }

    public function getDeroulage(): ?float
    {
        return $this->deroulage;
    }

    public function setDeroulage(?float $deroulage): static
    {
        $this->deroulage = $deroulage;

        return $this;
    }

    public function getTranchage(): ?float
    {
        return $this->tranchage;
    }

    public function setTranchage(?float $tranchage): static
    {
        $this->tranchage = $tranchage;

        return $this;
    }

    public function getTaxeAbattage(): ?float
    {
        return $this->taxe_abattage;
    }

    public function setTaxeAbattage(?float $taxe_abattage): static
    {
        $this->taxe_abattage = $taxe_abattage;

        return $this;
    }

    public function getCodePagefp(): ?Pagefp
    {
        return $this->code_pagefp;
    }

    public function setCodePagefp(?Pagefp $code_pagefp): static
    {
        $this->code_pagefp = $code_pagefp;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    public function setCreatedBy(string $created_by): static
    {
        $this->created_by = $created_by;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    public function setUpdatedBy(?string $updated_by): static
    {
        $this->updated_by = $updated_by;

        return $this;
    }

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): static
    {
        $this->exercice = $exercice;

        return $this;
    }
}
