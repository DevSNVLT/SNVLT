<?php

namespace App\Entity\DocStats\Saisie;

use App\Entity\DocStats\Pages\Pageetath;
use App\Entity\References\Essence;
use App\Entity\References\ProduitsUsine;
use App\Repository\DocStats\Saisie\LignepageetathRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.lignepageetath')]
#[ORM\Entity(repositoryClass: LignepageetathRepository::class)]
class Lignepageetath
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_prenoms = null;

    #[ORM\Column(length: 25, nullable: true)]
    private ?string $cc_client = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageetaths')]
    private ?Essence $essence = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageetaths')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProduitsUsine $nature_produit = null;

    #[ORM\Column]
    private ?int $qte_pdt = null;

    #[ORM\Column(nullable: true)]
    private ?int $prix = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localite = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $numero_depot = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageetaths')]
    private ?Pageetath $code_pageetath = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPrenoms(): ?string
    {
        return $this->nom_prenoms;
    }

    public function setNomPrenoms(string $nom_prenoms): static
    {
        $this->nom_prenoms = $nom_prenoms;

        return $this;
    }

    public function getCcClient(): ?string
    {
        return $this->cc_client;
    }

    public function setCcClient(?string $cc_client): static
    {
        $this->cc_client = $cc_client;

        return $this;
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

    public function getNatureProduit(): ?ProduitsUsine
    {
        return $this->nature_produit;
    }

    public function setNatureProduit(?ProduitsUsine $nature_produit): static
    {
        $this->nature_produit = $nature_produit;

        return $this;
    }

    public function getQtePdt(): ?int
    {
        return $this->qte_pdt;
    }

    public function setQtePdt(int $qte_pdt): static
    {
        $this->qte_pdt = $qte_pdt;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(?int $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getLocalite(): ?string
    {
        return $this->localite;
    }

    public function setLocalite(?string $localite): static
    {
        $this->localite = $localite;

        return $this;
    }

    public function getNumeroDepot(): ?string
    {
        return $this->numero_depot;
    }

    public function setNumeroDepot(?string $numero_depot): static
    {
        $this->numero_depot = $numero_depot;

        return $this;
    }

    public function getCodePageetath(): ?Pageetath
    {
        return $this->code_pageetath;
    }

    public function setCodePageetath(?Pageetath $code_pageetath): static
    {
        $this->code_pageetath = $code_pageetath;

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
}
