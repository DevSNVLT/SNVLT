<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\DocStats\Entetes\Documentetath;
use App\Entity\DocStats\Saisie\Lignepageetath;
use App\Entity\References\PageDocGen;
use App\Repository\DocStats\Pages\PageetathRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.pageetath')]
#[ORM\Entity(repositoryClass: PageetathRepository::class)]
class Pageetath
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_pageetath = null;

    #[ORM\Column(nullable: true)]
    private ?int $index_pageetath = null;

    #[ORM\Column(nullable: true)]
    private ?int $mois = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localite = null;

    #[ORM\ManyToOne(inversedBy: 'pageetaths')]
    private ?Documentetath $code_docetath = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_pageetath', targetEntity: Lignepageetath::class)]
    private Collection $lignepageetaths;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\ManyToOne(inversedBy: 'pageetaths')]
    private ?PageDocGen $code_generation = null;

    public function __construct()
    {
        $this->lignepageetaths = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroPageetath(): ?string
    {
        return $this->numero_pageetath;
    }

    public function setNumeroPageetath(string $numero_pageetath): static
    {
        $this->numero_pageetath = $numero_pageetath;

        return $this;
    }

    public function getIndexPageetath(): ?int
    {
        return $this->index_pageetath;
    }

    public function setIndexPageetath(?int $index_pageetath): static
    {
        $this->index_pageetath = $index_pageetath;

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

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(?int $annee): static
    {
        $this->annee = $annee;

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

    public function getCodeDocetath(): ?Documentetath
    {
        return $this->code_docetath;
    }

    public function setCodeDocetath(?Documentetath $code_docetath): static
    {
        $this->code_docetath = $code_docetath;

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
     * @return Collection<int, Lignepageetath>
     */
    public function getLignepageetaths(): Collection
    {
        return $this->lignepageetaths;
    }

    public function addLignepageetath(Lignepageetath $lignepageetath): static
    {
        if (!$this->lignepageetaths->contains($lignepageetath)) {
            $this->lignepageetaths->add($lignepageetath);
            $lignepageetath->setCodePageetath($this);
        }

        return $this;
    }

    public function removeLignepageetath(Lignepageetath $lignepageetath): static
    {
        if ($this->lignepageetaths->removeElement($lignepageetath)) {
            // set the owning side to null (unless already changed)
            if ($lignepageetath->getCodePageetath() === $this) {
                $lignepageetath->setCodePageetath(null);
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
