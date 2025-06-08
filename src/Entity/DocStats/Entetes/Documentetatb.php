<?php

namespace App\Entity\DocStats\Entetes;

use App\Entity\Admin\Exercice;
use App\Entity\Administration\DemandeOperateur;
use App\Entity\Administration\DocStatsGen;
use App\Entity\DocStats\Pages\Pageetatb;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\References\Foret;
use App\Repository\DocStats\Entetes\DocumentetatbRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.documentetatb')]
#[ORM\Entity(repositoryClass: DocumentetatbRepository::class)]
class Documentetatb
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'documentetatbs')]
    private ?Foret $code_foret = null;

    #[ORM\ManyToOne(inversedBy: 'documentetatbs')]
    private ?Exercice $exercice = null;

    #[ORM\ManyToOne(inversedBy: 'documentetatbs')]
    private ?DocStatsGen $code_generation = null;

    #[ORM\ManyToOne]
    private ?TypeDocumentStatistique $type_document = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_docetatb = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $delivre_docetatb = null;

    #[ORM\Column(nullable: true)]
    private ?bool $etat = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\Column]
    private ?bool $transmission = null;

    #[ORM\ManyToOne(inversedBy: 'documentetatbs')]
    private ?DemandeOperateur $code_demande = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_docetatb', targetEntity: Pageetatb::class)]
    private Collection $pageetatbs;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_dr = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_cef = null;

    public function __construct()
    {
        $this->pageetatbs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeForet(): ?Foret
    {
        return $this->code_foret;
    }

    public function setCodeForet(?Foret $code_foret): static
    {
        $this->code_foret = $code_foret;

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

    public function getNumeroDocetatb(): ?string
    {
        return $this->numero_docetatb;
    }

    public function setNumeroDocetatb(string $numero_docetatb): static
    {
        $this->numero_docetatb = $numero_docetatb;

        return $this;
    }

    public function getDelivreDocetatb(): ?\DateTimeInterface
    {
        return $this->delivre_docetatb;
    }

    public function setDelivreDocetatb(?\DateTimeInterface $delivre_docetatb): static
    {
        $this->delivre_docetatb = $delivre_docetatb;

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

    public function setTransmission(bool $transmission): static
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
     * @return Collection<int, Pageetatb>
     */
    public function getPageetatbs(): Collection
    {
        return $this->pageetatbs;
    }

    public function addPageetatb(Pageetatb $pageetatb): static
    {
        if (!$this->pageetatbs->contains($pageetatb)) {
            $this->pageetatbs->add($pageetatb);
            $pageetatb->setCodeDocetatb($this);
        }

        return $this;
    }

    public function removePageetatb(Pageetatb $pageetatb): static
    {
        if ($this->pageetatbs->removeElement($pageetatb)) {
            // set the owning side to null (unless already changed)
            if ($pageetatb->getCodeDocetatb() === $this) {
                $pageetatb->setCodeDocetatb(null);
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
