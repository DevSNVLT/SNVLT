<?php

namespace App\Entity\DocStats\Entetes;

use App\Entity\Admin\Exercice;
use App\Entity\Administration\DemandeOperateur;
use App\Entity\Administration\DocStatsGen;
use App\Entity\Autorisation\AutorisationPs;
use App\Entity\DocStats\Pages\Pagebcburb;
use App\Entity\References\TypeDocumentStatistique;
use App\Repository\DocStats\Entetes\DocumentbcburbRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.documentbcburb')]
#[ORM\Entity(repositoryClass: DocumentbcburbRepository::class)]
class Documentbcburb
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_docbcburb = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $delivre_docbcburb = null;

    #[ORM\ManyToOne(inversedBy: 'documentbcburbs')]
    private ?AutorisationPs $permis = null;

    #[ORM\ManyToOne(inversedBy: 'documentbcburbs')]
    private ?TypeDocumentStatistique $type_document = null;

    #[ORM\ManyToOne(inversedBy: 'documentbcburbs')]
    private ?DocStatsGen $code_generation = null;

    #[ORM\ManyToOne(inversedBy: 'documentbcburbs')]
    private ?Exercice $exercice = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\Column(nullable: true)]
    private ?bool $etat = null;

    #[ORM\Column(nullable: true)]
    private ?bool $transmission = null;

    #[ORM\ManyToOne]
    private ?DemandeOperateur $code_demande = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_docbcburb', targetEntity: Pagebcburb::class)]
    private Collection $pagebcburbs;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_dr = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_cef = null;

    public function __construct()
    {
        $this->pagebcburbs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDocbcburb(): ?string
    {
        return $this->numero_docbcburb;
    }

    public function setNumeroDocbcburb(string $numero_docbcburb): static
    {
        $this->numero_docbcburb = $numero_docbcburb;

        return $this;
    }

    public function getDelivreDocbcburb(): ?\DateTimeInterface
    {
        return $this->delivre_docbcburb;
    }

    public function setDelivreDocbcburb(\DateTimeInterface $delivre_docbcburb): static
    {
        $this->delivre_docbcburb = $delivre_docbcburb;

        return $this;
    }

    public function getPermis(): ?AutorisationPs
    {
        return $this->permis;
    }

    public function setPermis(?AutorisationPs $permis): static
    {
        $this->permis = $permis;

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
     * @return Collection<int, Pagebcburb>
     */
    public function getPagebcburbs(): Collection
    {
        return $this->pagebcburbs;
    }

    public function addPagebcburb(Pagebcburb $pagebcburb): static
    {
        if (!$this->pagebcburbs->contains($pagebcburb)) {
            $this->pagebcburbs->add($pagebcburb);
            $pagebcburb->setCodeDocbcburb($this);
        }

        return $this;
    }

    public function removePagebcburb(Pagebcburb $pagebcburb): static
    {
        if ($this->pagebcburbs->removeElement($pagebcburb)) {
            // set the owning side to null (unless already changed)
            if ($pagebcburb->getCodeDocbcburb() === $this) {
                $pagebcburb->setCodeDocbcburb(null);
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
