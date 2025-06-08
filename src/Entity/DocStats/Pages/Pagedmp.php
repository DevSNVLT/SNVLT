<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\DocStats\Entetes\Documentdmp;
use App\Entity\DocStats\Saisie\Lignepagedmp;
use App\Entity\References\PageDocGen;
use App\Repository\DocStats\Pages\PagedmpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.pagedmp')]
#[ORM\Entity(repositoryClass: PagedmpRepository::class)]
class Pagedmp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_pagedmp = null;

    #[ORM\Column(nullable: true)]
    private ?int $index_pagedmp = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\Column(nullable: true)]
    private ?int $mois = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localite = null;

    #[ORM\ManyToOne(inversedBy: 'pagedmps')]
    private ?PageDocGen $code_generation = null;

    #[ORM\ManyToOne(inversedBy: 'pagedmps')]
    private ?Documentdmp $code_docdmp = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_pagedmp', targetEntity: Lignepagedmp::class)]
    private Collection $lignepagedmps;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    public function __construct()
    {
        $this->lignepagedmps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroPagedmp(): ?string
    {
        return $this->numero_pagedmp;
    }

    public function setNumeroPagedmp(string $numero_pagedmp): static
    {
        $this->numero_pagedmp = $numero_pagedmp;

        return $this;
    }

    public function getIndexPagedmp(): ?int
    {
        return $this->index_pagedmp;
    }

    public function setIndexPagedmp(?int $index_pagedmp): static
    {
        $this->index_pagedmp = $index_pagedmp;

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

    public function getCodeGeneration(): ?PageDocGen
    {
        return $this->code_generation;
    }

    public function setCodeGeneration(?PageDocGen $code_generation): static
    {
        $this->code_generation = $code_generation;

        return $this;
    }

    public function getCodeDocdmp(): ?Documentdmp
    {
        return $this->code_docdmp;
    }

    public function setCodeDocdmp(?Documentdmp $code_docdmp): static
    {
        $this->code_docdmp = $code_docdmp;

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
     * @return Collection<int, Lignepagedmp>
     */
    public function getLignepagedmps(): Collection
    {
        return $this->lignepagedmps;
    }

    public function addLignepagedmp(Lignepagedmp $lignepagedmp): static
    {
        if (!$this->lignepagedmps->contains($lignepagedmp)) {
            $this->lignepagedmps->add($lignepagedmp);
            $lignepagedmp->setCodePagedmp($this);
        }

        return $this;
    }

    public function removeLignepagedmp(Lignepagedmp $lignepagedmp): static
    {
        if ($this->lignepagedmps->removeElement($lignepagedmp)) {
            // set the owning side to null (unless already changed)
            if ($lignepagedmp->getCodePagedmp() === $this) {
                $lignepagedmp->setCodePagedmp(null);
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
