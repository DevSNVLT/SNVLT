<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\DocStats\Entetes\Documentfp;
use App\Entity\DocStats\Saisie\Lignepagefp;
use App\Entity\References\PageDocGen;
use App\Repository\DocStats\Pages\PagefpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.pagefp')]
#[ORM\Entity(repositoryClass: PagefpRepository::class)]
class Pagefp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    private ?string $numero_pagefp = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\Column(nullable: true)]
    private ?int $mois = null;

    #[ORM\Column(nullable: true)]
    private ?int $index_page = null;

    #[ORM\ManyToOne(inversedBy: 'pagefps')]
    private ?Documentfp $code_docfp = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localite = null;

    #[ORM\OneToMany(mappedBy: 'code_pagefp', targetEntity: Lignepagefp::class)]
    private Collection $lignepagefps;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'pagefps')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PageDocGen $code_generation = null;

    public function __construct()
    {
        $this->lignepagefps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroPagefp(): ?string
    {
        return $this->numero_pagefp;
    }

    public function setNumeroPagefp(string $numero_pagefp): static
    {
        $this->numero_pagefp = $numero_pagefp;

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

    public function getIndexPage(): ?int
    {
        return $this->index_page;
    }

    public function setIndexPage(?int $index_page): static
    {
        $this->index_page = $index_page;

        return $this;
    }

    public function getCodeDocfp(): ?Documentfp
    {
        return $this->code_docfp;
    }

    public function setCodeDocfp(?Documentfp $code_docfp): static
    {
        $this->code_docfp = $code_docfp;

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

    /**
     * @return Collection<int, Lignepagefp>
     */
    public function getLignepagefps(): Collection
    {
        return $this->lignepagefps;
    }

    public function addLignepagefp(Lignepagefp $lignepagefp): static
    {
        if (!$this->lignepagefps->contains($lignepagefp)) {
            $this->lignepagefps->add($lignepagefp);
            $lignepagefp->setCodePagefp($this);
        }

        return $this;
    }

    public function removeLignepagefp(Lignepagefp $lignepagefp): static
    {
        if ($this->lignepagefps->removeElement($lignepagefp)) {
            // set the owning side to null (unless already changed)
            if ($lignepagefp->getCodePagefp() === $this) {
                $lignepagefp->setCodePagefp(null);
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    public function setCreatedBy(?string $created_by): static
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
