<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\DocStats\Entetes\Documentdmv;
use App\Entity\DocStats\Saisie\Lignepagedmv;
use App\Entity\References\PageDocGen;
use App\Repository\DocStats\Pages\PagedmvRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.pagedmv')]
#[ORM\Entity(repositoryClass: PagedmvRepository::class)]
class Pagedmv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_pagedmv = null;

    #[ORM\Column]
    private ?int $index_pagedmv = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\Column(nullable: true)]
    private ?int $mois = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localite = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume_pt = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume_autre = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume_total = null;

    #[ORM\ManyToOne(inversedBy: 'pagedmvs')]
    private ?Documentdmv $code_docdmv = null;

    #[ORM\ManyToOne(inversedBy: 'pagedmvs')]
    private ?PageDocGen $code_generation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_pagedmv', targetEntity: Lignepagedmv::class)]
    private Collection $lignepagedmvs;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    public function __construct()
    {
        $this->lignepagedmvs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroPagedmv(): ?string
    {
        return $this->numero_pagedmv;
    }

    public function setNumeroPagedmv(string $numero_pagedmv): static
    {
        $this->numero_pagedmv = $numero_pagedmv;

        return $this;
    }

    public function getIndexPagedmv(): ?int
    {
        return $this->index_pagedmv;
    }

    public function setIndexPagedmv(int $index_pagedmv): static
    {
        $this->index_pagedmv = $index_pagedmv;

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

    public function getVolumePt(): ?float
    {
        return $this->volume_pt;
    }

    public function setVolumePt(?float $volume_pt): static
    {
        $this->volume_pt = $volume_pt;

        return $this;
    }

    public function getVolumeAutre(): ?float
    {
        return $this->volume_autre;
    }

    public function setVolumeAutre(float $volume_autre): static
    {
        $this->volume_autre = $volume_autre;

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

    public function getCodeDocdmv(): ?Documentdmv
    {
        return $this->code_docdmv;
    }

    public function setCodeDocdmv(?Documentdmv $code_docdmv): static
    {
        $this->code_docdmv = $code_docdmv;

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

    /**
     * @return Collection<int, Lignepagedmv>
     */
    public function getLignepagedmvs(): Collection
    {
        return $this->lignepagedmvs;
    }

    public function addLignepagedmv(Lignepagedmv $lignepagedmv): static
    {
        if (!$this->lignepagedmvs->contains($lignepagedmv)) {
            $this->lignepagedmvs->add($lignepagedmv);
            $lignepagedmv->setCodePagedmv($this);
        }

        return $this;
    }

    public function removeLignepagedmv(Lignepagedmv $lignepagedmv): static
    {
        if ($this->lignepagedmvs->removeElement($lignepagedmv)) {
            // set the owning side to null (unless already changed)
            if ($lignepagedmv->getCodePagedmv() === $this) {
                $lignepagedmv->setCodePagedmv(null);
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
