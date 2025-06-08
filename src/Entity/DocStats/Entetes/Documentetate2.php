<?php

namespace App\Entity\DocStats\Entetes;

use App\Entity\Admin\Exercice;
use App\Entity\Administration\DemandeOperateur;
use App\Entity\Administration\DocStatsGen;
use App\Entity\DocStats\Pages\Pageetate2;
use App\Entity\DocStats\Saisie\Lignepageetate2;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\References\Usine;
use App\Repository\DocStats\Entetes\Documentetate2Repository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.documentetate2')]
#[ORM\Entity(repositoryClass: Documentetate2Repository::class)]
class Documentetate2
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'documentetate2s')]
    private ?Usine $code_usine = null;

    #[ORM\ManyToOne(inversedBy: 'documentetate2s')]
    private ?Exercice $exercice = null;

    #[ORM\ManyToOne(inversedBy: 'documentetate2s')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DocStatsGen $code_generation = null;

    #[ORM\ManyToOne(inversedBy: 'documentetate2s')]
    private ?TypeDocumentStatistique $type_document = null;

    #[ORM\ManyToOne(inversedBy: 'documentetate2s')]
    private ?DemandeOperateur $code_demande = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_docetate2 = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $delivre_docetate2 = null;

    #[ORM\Column(nullable: true)]
    private ?bool $etat = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\Column(nullable: true)]
    private ?bool $transmission = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_docetate2', targetEntity: Pageetate2::class)]
    private Collection $pageetate2s;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_dr = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_cef = null;


    public function __construct()
    {
        $this->pageetate2s = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): static
    {
        $this->exercice = $exercice;

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

    public function getTypeDocument(): ?TypeDocumentStatistique
    {
        return $this->type_document;
    }

    public function setTypeDocument(?TypeDocumentStatistique $type_document): static
    {
        $this->type_document = $type_document;

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

    public function getNumeroDocetate2(): ?string
    {
        return $this->numero_docetate2;
    }

    public function setNumeroDocetate2(string $numero_docetate2): static
    {
        $this->numero_docetate2 = $numero_docetate2;

        return $this;
    }

    public function getDelivreDocetate2(): ?\DateTimeInterface
    {
        return $this->delivre_docetate2;
    }

    public function setDelivreDocetate2(?\DateTimeInterface $delivre_docetate2): static
    {
        $this->delivre_docetate2 = $delivre_docetate2;

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
     * @return Collection<int, Pageetate2>
     */
    public function getPageetate2s(): Collection
    {
        return $this->pageetate2s;
    }

    public function addPageetate2(Pageetate2 $pageetate2): static
    {
        if (!$this->pageetate2s->contains($pageetate2)) {
            $this->pageetate2s->add($pageetate2);
            $pageetate2->setCodeDocetate2($this);
        }

        return $this;
    }

    public function removePageetate2(Pageetate2 $pageetate2): static
    {
        if ($this->pageetate2s->removeElement($pageetate2)) {
            // set the owning side to null (unless already changed)
            if ($pageetate2->getCodeDocetate2() === $this) {
                $pageetate2->setCodeDocetate2(null);
            }
        }

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
