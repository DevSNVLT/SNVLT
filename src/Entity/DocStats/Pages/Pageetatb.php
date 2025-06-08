<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\DocStats\Entetes\Documentetatb;
use App\Entity\References\Foret;
use App\Entity\References\PageDocGen;
use App\Repository\DocStats\Pages\PageetatbRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.pageetatb')]
#[ORM\Entity(repositoryClass: PageetatbRepository::class)]
class Pageetatb
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_pageetatb = null;

    #[ORM\Column]
    private ?int $index_pageetatb = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\Column(nullable: true)]
    private ?int $mois = null;

    #[ORM\ManyToOne(inversedBy: 'pageetatbs')]
    private ?Foret $perimetre = null;

    #[ORM\ManyToOne(inversedBy: 'pageetatbs')]
    private ?Documentetatb $code_docetatb = null;

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

    #[ORM\ManyToOne(inversedBy: 'pageetatbs')]
    private ?PageDocGen $code_generation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroPageetatb(): ?string
    {
        return $this->numero_pageetatb;
    }

    public function setNumeroPageetatb(string $numero_pageetatb): static
    {
        $this->numero_pageetatb = $numero_pageetatb;

        return $this;
    }

    public function getIndexPageetatb(): ?int
    {
        return $this->index_pageetatb;
    }

    public function setIndexPageetatb(int $index_pageetatb): static
    {
        $this->index_pageetatb = $index_pageetatb;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(?int $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getMois(): ?int
    {
        return $this->mois;
    }

    public function setMois(?int $mois): static
    {
        $this->mois = $mois;

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

    public function getCodeDocetatb(): ?Documentetatb
    {
        return $this->code_docetatb;
    }

    public function setCodeDocetatb(?Documentetatb $code_docetatb): static
    {
        $this->code_docetatb = $code_docetatb;

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

    public function getCodeGeneration(): ?PageDocGen
    {
        return $this->code_generation;
    }

    public function setCodeGeneration(?PageDocGen $code_generation): static
    {
        $this->code_generation = $code_generation;

        return $this;
    }
}