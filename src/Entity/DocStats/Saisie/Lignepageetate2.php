<?php

namespace App\Entity\DocStats\Saisie;

use App\Entity\DocStats\Pages\Pageetate2;
use App\Entity\Docstats\Entetes\Documentetate2;
use App\Entity\References\Essence;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Entity\References\PageDocGen;
use App\Entity\References\Usine;
use App\Repository\DocStats\Saisie\Lignepageetate2Repository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.lignepageetate2')]
#[ORM\Entity(repositoryClass: Lignepageetate2Repository::class)]
class Lignepageetate2
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageetate2s')]
    private ?Exploitant $exploitant = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageetate2s')]
    private ?Foret $perimetre = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageetate2s')]
    private ?Usine $usine_destination = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageetate2s')]
    private ?Essence $essence = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageetate2s')]
    private ?Pageetate2 $code_pageetate2 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExploitant(): ?Exploitant
    {
        return $this->exploitant;
    }

    public function setExploitant(?Exploitant $exploitant): static
    {
        $this->exploitant = $exploitant;

        return $this;
    }

    public function getPerimetre(): ?Foret
    {
        return $this->perimetre;
    }

    public function setPerimetre(?Foret $perimetre): static
    {
        $this->perimetre = $perimetre;

        return $this;
    }

    public function getUsineDestination(): ?Usine
    {
        return $this->usine_destination;
    }

    public function setUsineDestination(?Usine $usine_destination): static
    {
        $this->usine_destination = $usine_destination;

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

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function setVolume(?float $volume): static
    {
        $this->volume = $volume;

        return $this;
    }

    public function getUniqueDoc(): ?string
    {
        return $this->unique_doc;
    }

    public function setUniqueDoc(?string $unique_doc): static
    {
        $this->unique_doc = $unique_doc;

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

    public function getCodePageetate2(): ?Pageetate2
    {
        return $this->code_pageetate2;
    }

    public function setCodePageetate2(?Pageetate2 $code_pageetate2): static
    {
        $this->code_pageetate2 = $code_pageetate2;

        return $this;
    }
}
