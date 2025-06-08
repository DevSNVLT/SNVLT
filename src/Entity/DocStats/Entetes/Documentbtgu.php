<?php

namespace App\Entity\DocStats\Entetes;

use App\Entity\Admin\Exercice;
use App\Entity\Administration\DemandeOperateur;
use App\Entity\Administration\DocStatsGen;
use App\Entity\DocStats\Pages\Pagebtgu;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\References\Usine;
use App\Repository\DocStats\Entetes\DocumentbtguRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.documentbtgu')]
#[ORM\Entity(repositoryClass: DocumentbtguRepository::class)]
class Documentbtgu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_docbtgu = null;

    #[ORM\ManyToOne(inversedBy: 'documentbtgus')]
    private ?Usine $code_usine = null;

    #[ORM\ManyToOne(inversedBy: 'documentbtgus')]
    private ?TypeDocumentStatistique $type_document = null;

    #[ORM\ManyToOne(inversedBy: 'documentbtgus')]
    private ?DocStatsGen $code_generation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $delivre_docbtgu = null;

    #[ORM\ManyToOne(inversedBy: 'documentbtgus')]
    private ?Exercice $exercice = null;

    #[ORM\Column(nullable: true)]
    private ?bool $etat = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_docbtgu', targetEntity: Pagebtgu::class)]
    private Collection $pagebtgus;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\Column(nullable: true)]
    private ?bool $transmission = null;

    #[ORM\ManyToOne(inversedBy: 'documentbtgus')]
    private ?DemandeOperateur $code_demande = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_dr = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_cef = null;

    public function __construct()
    {
        $this->pagebtgus = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDocbtgu(): ?string
    {
        return $this->numero_docbtgu;
    }

    public function setNumeroDocbtgu(string $numero_docbtgu): static
    {
        $this->numero_docbtgu = $numero_docbtgu;

        return $this;
    }

    public function getCodeUsine(): ?Usine
    {
        return $this->code_usine;
    }

    public function setCodeUsine(?Usine $code_usine): static
    {
        $this->code_usine = $code_usine;

        return $this;
    }

    public function getTypeDocument(): ?TypeDocumentStatistique
    {
        return $this->type_document;
    }

    public function setTypeDocument(?TypeDocumentStatistique $type_document): static
    {
        $this->type_document = $type_document;

        return $this;
    }

    public function getCodeGeneration(): ?DocStatsGen
    {
        return $this->code_generation;
    }

    public function setCodeGeneration(?DocStatsGen $code_generation): static
    {
        $this->code_generation = $code_generation;

        return $this;
    }

    public function getDelivreDocbtgu(): ?\DateTimeInterface
    {
        return $this->delivre_docbtgu;
    }

    public function setDelivreDocbtgu(?\DateTimeInterface $delivre_docbtgu): static
    {
        $this->delivre_docbtgu = $delivre_docbtgu;

        return $this;
    }

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): static
    {
        $this->exercice = $exercice;

        return $this;
    }

    public function isEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(?bool $etat): static
    {
        $this->etat = $etat;

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

    public function __toString(): string
    {
        return $this->numero_docbtgu;
    }

    /**
     * @return Collection<int, Pagebtgu>
     */
    public function getPagebtgus(): Collection
    {
        return $this->pagebtgus;
    }

    public function addPagebtgu(Pagebtgu $pagebtgu): static
    {
        if (!$this->pagebtgus->contains($pagebtgu)) {
            $this->pagebtgus->add($pagebtgu);
            $pagebtgu->setCodeDocbtgu($this);
        }

        return $this;
    }

    public function removePagebtgu(Pagebtgu $pagebtgu): static
    {
        if ($this->pagebtgus->removeElement($pagebtgu)) {
            // set the owning side to null (unless already changed)
            if ($pagebtgu->getCodeDocbtgu() === $this) {
                $pagebtgu->setCodeDocbtgu(null);
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

    public function isTransmission(): ?bool
    {
        return $this->transmission;
    }

    public function setTransmission(?bool $transmission): static
    {
        $this->transmission = $transmission;

        return $this;
    }

    public function getCodeDemande(): ?DemandeOperateur
    {
        return $this->code_demande;
    }

    public function setCodeDemande(?DemandeOperateur $code_demande): static
    {
        $this->code_demande = $code_demande;

        return $this;
    }

    public function isSignatureDr(): ?bool
    {
        return $this->signature_dr;
    }

    public function setSignatureDr(?bool $signature_dr): static
    {
        $this->signature_dr = $signature_dr;

        return $this;
    }

    public function isSignatureCef(): ?bool
    {
        return $this->signature_cef;
    }

    public function setSignatureCef(?bool $signature_cef): static
    {
        $this->signature_cef = $signature_cef;

        return $this;
    }
}
