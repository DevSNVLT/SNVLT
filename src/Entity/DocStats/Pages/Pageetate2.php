<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\DocStats\Entetes\Documentetate2;
use App\Entity\DocStats\Saisie\Lignepageetate2;
use App\Entity\References\PageDocGen;
use App\Repository\DocStats\Pages\Pageetate2Repository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.pageetate2')]
#[ORM\Entity(repositoryClass: Pageetate2Repository::class)]
class Pageetate2
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_pageetate2 = null;

    #[ORM\Column]
    private ?int $index_pageetate2 = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\ManyToOne(inversedBy: 'pageetate2s')]
    private ?Documentetate2 $code_docetate2 = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localite = null;

    #[ORM\Column]
    private ?float $volumetotal = null;

    #[ORM\Column(nullable: true)]
    private ?bool $boolean = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_pageetate2', targetEntity: Lignepageetate2::class)]
    private Collection $lignepageetate2s;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\ManyToOne(inversedBy: 'pageetate2s')]
    private ?PageDocGen $code_generation = null;

    public function __construct()
    {
        $this->lignepageetate2s = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroPageetate2(): ?string
    {
        return $this->numero_pageetate2;
    }

    public function setNumeroPageetate2(string $numero_pageetate2): static
    {
        $this->numero_pageetate2 = $numero_pageetate2;

        return $this;
    }

    public function getIndexPageetate2(): ?int
    {
        return $this->index_pageetate2;
    }

    public function setIndexPageetate2(int $index_pageetate2): static
    {
        $this->index_pageetate2 = $index_pageetate2;

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

    public function getCodeDocetate2(): ?Documentetate2
    {
        return $this->code_docetate2;
    }

    public function setCodeDocetate2(?Documentetate2 $code_docetate2): static
    {
        $this->code_docetate2 = $code_docetate2;

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

    public function setVolumetotal(float $volumetotal): static
    {
        $this->volumetotal = $volumetotal;

        return $this;
    }

    public function isBoolean(): ?bool
    {
        return $this->boolean;
    }

    public function setBoolean(?bool $boolean): static
    {
        $this->boolean = $boolean;

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

    /**
     * @return Collection<int, Lignepageetate2>
     */
    public function getLignepageetate2s(): Collection
    {
        return $this->lignepageetate2s;
    }

    public function addLignepageetate2(Lignepageetate2 $lignepageetate2): static
    {
        if (!$this->lignepageetate2s->contains($lignepageetate2)) {
            $this->lignepageetate2s->add($lignepageetate2);
            $lignepageetate2->setCodePageetate2($this);
        }

        return $this;
    }

    public function removeLignepageetate2(Lignepageetate2 $lignepageetate2): static
    {
        if ($this->lignepageetate2s->removeElement($lignepageetate2)) {
            // set the owning side to null (unless already changed)
            if ($lignepageetate2->getCodePageetate2() === $this) {
                $lignepageetate2->setCodePageetate2(null);
            }
        }

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
}
