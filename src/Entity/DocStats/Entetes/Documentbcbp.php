<?php

namespace App\Entity\DocStats\Entetes;

use App\Entity\Admin\Exercice;
use App\Entity\Administration\DemandeOperateur;
use App\Entity\Administration\DocStatsGen;
use App\Entity\Autorisation\AutorisationPv;
use App\Entity\DocStats\Pages\Pagebcbp;
use App\Entity\References\TypeDocumentStatistique;
use App\Repository\DocStats\Entetes\DocumentbcbpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.documentbcbp')]
#[ORM\Entity(repositoryClass: DocumentbcbpRepository::class)]
class Documentbcbp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'documentbcbps')]
    private ?DocStatsGen $code_generation = null;

    #[ORM\ManyToOne(inversedBy: 'documentbcbps')]
    private ?TypeDocumentStatistique $type_document = null;

    #[ORM\Column(length: 255)]
    private ?string $numero_docbcbp = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $delivre_docbcbp = null;

    #[ORM\ManyToOne(inversedBy: 'documentbcbps')]
    private ?Exercice $exercice = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\Column(nullable: true)]
    private ?bool $etat = null;

    #[ORM\Column(nullable: true)]
    private ?bool $transmission = null;

    #[ORM\ManyToOne(inversedBy: 'documentbcbps')]
    private ?DemandeOperateur $code_demande = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_docbcbp', targetEntity: Pagebcbp::class)]
    private Collection $pagebcbps;

    #[ORM\ManyToOne(inversedBy: 'documentbcbps')]
    private ?AutorisationPv $code_autorisation_pv = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_dr = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_cef = null;

    public function __construct()
    {
        $this->pagebcbps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNumeroDocbcbp(): ?string
    {
        return $this->numero_docbcbp;
    }

    public function setNumeroDocbcbp(string $numero_docbcbp): static
    {
        $this->numero_docbcbp = $numero_docbcbp;

        return $this;
    }

    public function getDelivreDocbcbp(): ?\DateTimeInterface
    {
        return $this->delivre_docbcbp;
    }

    public function setDelivreDocbcbp(?\DateTimeInterface $delivre_docbcbp): static
    {
        $this->delivre_docbcbp = $delivre_docbcbp;

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

    public function getUniqueDoc(): ?string
    {
        return $this->unique_doc;
    }

    public function setUniqueDoc(?string $unique_doc): static
    {
        $this->unique_doc = $unique_doc;

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
     * @return Collection<int, Pagebcbp>
     */
    public function getPagebcbps(): Collection
    {
        return $this->pagebcbps;
    }

    public function addPagebcbp(Pagebcbp $pagebcbp): static
    {
        if (!$this->pagebcbps->contains($pagebcbp)) {
            $this->pagebcbps->add($pagebcbp);
            $pagebcbp->setCodeDocbcbp($this);
        }

        return $this;
    }

    public function removePagebcbp(Pagebcbp $pagebcbp): static
    {
        if ($this->pagebcbps->removeElement($pagebcbp)) {
            // set the owning side to null (unless already changed)
            if ($pagebcbp->getCodeDocbcbp() === $this) {
                $pagebcbp->setCodeDocbcbp(null);
            }
        }

        return $this;
    }

    public function getCodeAutorisationPv(): ?AutorisationPv
    {
        return $this->code_autorisation_pv;
    }

    public function setCodeAutorisationPv(?AutorisationPv $code_autorisation_pv): static
    {
        $this->code_autorisation_pv = $code_autorisation_pv;

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
