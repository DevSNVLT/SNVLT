<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\DocStats\Entetes\Documentetate;
use App\Entity\References\PageDocGen;
use App\Repository\DocStats\Pages\PageetateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.pageetate')]
#[ORM\Entity(repositoryClass: PageetateRepository::class)]
class Pageetate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'pageetates')]
    private ?Documentetate $code_docetate = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_pageetate = null;

    #[ORM\Column]
    private ?int $index_pageetate = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\Column(nullable: true)]
    private ?int $mois = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localite = null;

    #[ORM\Column(nullable: true)]
    private ?float $volumetotal = null;

    #[ORM\Column(nullable: true)]
    private ?float $volumezef = null;

    #[ORM\Column(nullable: true)]
    private ?float $volumetransfert = null;

    #[ORM\Column(nullable: true)]
    private ?float $montanttotal = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\ManyToOne(inversedBy: 'pageetates')]
    private ?PageDocGen $code_generation = null;

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

    public function getCodeDocetate(): ?Documentetate
    {
        return $this->code_docetate;
    }

    public function setCodeDocetate(?Documentetate $code_docetate): static
    {
        $this->code_docetate = $code_docetate;

        return $this;
    }

    public function getNumeroPageetate(): ?string
    {
        return $this->numero_pageetate;
    }

    public function setNumeroPageetate(string $numero_pageetate): static
    {
        $this->numero_pageetate = $numero_pageetate;

        return $this;
    }

    public function getIndexPageetate(): ?int
    {
        return $this->index_pageetate;
    }

    public function setIndexPageetate(int $index_pageetate): static
    {
        $this->index_pageetate = $index_pageetate;

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

    public function getLocalite(): ?string
    {
        return $this->localite;
    }

    public function setLocalite(?string $localite): static
    {
        $this->localite = $localite;

        return $this;
    }

    public function getVolumetotal(): ?float
    {
        return $this->volumetotal;
    }

    public function setVolumetotal(?float $volumetotal): static
    {
        $this->volumetotal = $volumetotal;

        return $this;
    }

    public function getVolumezef(): ?float
    {
        return $this->volumezef;
    }

    public function setVolumezef(?float $volumezef): static
    {
        $this->volumezef = $volumezef;

        return $this;
    }

    public function getVolumetransfert(): ?float
    {
        return $this->volumetransfert;
    }

    public function setVolumetransfert(?float $volumetransfert): static
    {
        $this->volumetransfert = $volumetransfert;

        return $this;
    }

    public function getMontanttotal(): ?float
    {
        return $this->montanttotal;
    }

    public function setMontanttotal(?float $montanttotal): static
    {
        $this->montanttotal = $montanttotal;

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

    public function getCodeGeneration(): ?PageDocGen
    {
        return $this->code_generation;
    }

    public function setCodeGeneration(?PageDocGen $code_generation): static
    {
        $this->code_generation = $code_generation;

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
