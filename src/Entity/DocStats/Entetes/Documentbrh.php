<?php

namespace App\Entity\DocStats\Entetes;

use App\Entity\Admin\Exercice;
use App\Entity\Administration\DemandeOperateur;
use App\Entity\Administration\DocStatsGen;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\References\TypeDocumentStatistique;
use App\Repository\DocStats\Entetes\DocumentbrhRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.documentbrh')]
#[ORM\Entity(repositoryClass: DocumentbrhRepository::class)]
class Documentbrh
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_docbrh = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $delivre_docbrh = null;

    #[ORM\ManyToOne(inversedBy: 'documentbrhs')]
    private ?Reprise $code_reprise = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'documentbrhs')]
    private ?TypeDocumentStatistique $type_document = null;

    #[ORM\OneToMany(mappedBy: 'code_docbrh', targetEntity: Pagebrh::class)]
    private Collection $pagebrhs;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\ManyToOne(inversedBy: 'documentbrhs')]
    private ?DocStatsGen $code_generation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $etat = null;

    #[ORM\ManyToOne(inversedBy: 'documentbrhs')]
    private ?Exercice $exercice = null;

    #[ORM\Column(nullable: true)]
    private ?bool $transmission = null;

    #[ORM\ManyToOne(inversedBy: 'documentbrhs')]
    private ?DemandeOperateur $code_demande = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_dr = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_cef = null;

    public function __construct()
    {
        $this->pagebrhs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDocbrh(): ?string
    {
        return $this->numero_docbrh;
    }

    public function setNumeroDocbrh(string $numero_docbrh): static
    {
        $this->numero_docbrh = $numero_docbrh;

        return $this;
    }

    public function getDelivreDocbrh(): ?\DateTimeInterface
    {
        return $this->delivre_docbrh;
    }

    public function setDelivreDocbrh(\DateTimeInterface $delivre_docbrh): static
    {
        $this->delivre_docbrh = $delivre_docbrh;

        return $this;
    }

    public function getCodeReprise(): ?Reprise
    {
        return $this->code_reprise;
    }

    public function setCodeReprise(?Reprise $code_reprise): static
    {
        $this->code_reprise = $code_reprise;

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

    public function getTypeDocument(): ?TypeDocumentStatistique
    {
        return $this->type_document;
    }

    public function setTypeDocument(?TypeDocumentStatistique $type_document): static
    {
        $this->type_document = $type_document;

        return $this;
    }

    /**
     * @return Collection<int, Pagebrh>
     */
    public function getPagebrhs(): Collection
    {
        return $this->pagebrhs;
    }

    public function addPagebrh(Pagebrh $pagebrh): static
    {
        if (!$this->pagebrhs->contains($pagebrh)) {
            $this->pagebrhs->add($pagebrh);
            $pagebrh->setCodeDocbrh($this);
        }

        return $this;
    }

    public function removePagebrh(Pagebrh $pagebrh): static
    {
        if ($this->pagebrhs->removeElement($pagebrh)) {
            // set the owning side to null (unless already changed)
            if ($pagebrh->getCodeDocbrh() === $this) {
                $pagebrh->setCodeDocbrh(null);
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

    public function getCodeGeneration(): ?DocStatsGen
    {
        return $this->code_generation;
    }

    public function setCodeGeneration(?DocStatsGen $code_generation): static
    {
        $this->code_generation = $code_generation;

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

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): static
    {
        $this->exercice = $exercice;

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
