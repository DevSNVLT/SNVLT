<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\DocStats\Entetes\Documentetatg;
use App\Entity\DocStats\Saisie\Lignepageetatg;
use App\Entity\References\PageDocGen;
use App\Repository\DocStats\Pages\PageetatgRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.pageetatg')]
#[ORM\Entity(repositoryClass: PageetatgRepository::class)]
class Pageetatg
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_pageetatg = null;

    #[ORM\Column(nullable: true)]
    private ?int $index_pageetag = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\Column(nullable: true)]
    private ?int $mois = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localite = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume_sciage = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume_deroulage = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume_tranchage = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume_total = null;

    #[ORM\ManyToOne(inversedBy: 'pageetatgs')]
    private ?Documentetatg $code_docetatg = null;

    #[ORM\ManyToOne(inversedBy: 'pageetatgs')]
    private ?PageDocGen $code_generation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $upadated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $upadated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_pageetatg', targetEntity: Lignepageetatg::class)]
    private Collection $lignepageetatgs;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    public function __construct()
    {
        $this->lignepageetatgs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroPageetatg(): ?string
    {
        return $this->numero_pageetatg;
    }

    public function setNumeroPageetatg(string $numero_pageetatg): static
    {
        $this->numero_pageetatg = $numero_pageetatg;

        return $this;
    }

    public function getIndexPageetag(): ?int
    {
        return $this->index_pageetag;
    }

    public function setIndexPageetag(?int $index_pageetag): static
    {
        $this->index_pageetag = $index_pageetag;

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

    public function getVolumeSciage(): ?float
    {
        return $this->volume_sciage;
    }

    public function setVolumeSciage(?float $volume_sciage): static
    {
        $this->volume_sciage = $volume_sciage;

        return $this;
    }

    public function getVolumeDeroulage(): ?float
    {
        return $this->volume_deroulage;
    }

    public function setVolumeDeroulage(?float $volume_deroulage): static
    {
        $this->volume_deroulage = $volume_deroulage;

        return $this;
    }

    public function getVolumeTranchage(): ?float
    {
        return $this->volume_tranchage;
    }

    public function setVolumeTranchage(?float $volume_tranchage): static
    {
        $this->volume_tranchage = $volume_tranchage;

        return $this;
    }

    public function getVolumeTotal(): ?float
    {
        return $this->volume_total;
    }

    public function setVolumeTotal(?float $volume_total): static
    {
        $this->volume_total = $volume_total;

        return $this;
    }

    public function getCodeDocetatg(): ?Documentetatg
    {
        return $this->code_docetatg;
    }

    public function setCodeDocetatg(?Documentetatg $code_docetatg): static
    {
        $this->code_docetatg = $code_docetatg;

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

    public function getUpadatedAt(): ?\DateTimeInterface
    {
        return $this->upadated_at;
    }

    public function setUpadatedAt(?\DateTimeInterface $upadated_at): static
    {
        $this->upadated_at = $upadated_at;

        return $this;
    }

    public function getUpadatedBy(): ?string
    {
        return $this->upadated_by;
    }

    public function setUpadatedBy(?string $upadated_by): static
    {
        $this->upadated_by = $upadated_by;

        return $this;
    }

    /**
     * @return Collection<int, Lignepageetatg>
     */
    public function getLignepageetatgs(): Collection
    {
        return $this->lignepageetatgs;
    }

    public function addLignepageetatg(Lignepageetatg $lignepageetatg): static
    {
        if (!$this->lignepageetatgs->contains($lignepageetatg)) {
            $this->lignepageetatgs->add($lignepageetatg);
            $lignepageetatg->setCodePageetatg($this);
        }

        return $this;
    }

    public function removeLignepageetatg(Lignepageetatg $lignepageetatg): static
    {
        if ($this->lignepageetatgs->removeElement($lignepageetatg)) {
            // set the owning side to null (unless already changed)
            if ($lignepageetatg->getCodePageetatg() === $this) {
                $lignepageetatg->setCodePageetatg(null);
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
}
