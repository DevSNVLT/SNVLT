<?php

namespace App\Entity\DocStats\Entetes;

use App\Entity\Admin\Exercice;
use App\Entity\Administration\DemandeOperateur;
use App\Entity\Administration\DocStatsGen;
use App\Entity\Autorisation\ContratBcbgfh;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Pages\Pagebcbgfh;
use App\Entity\References\TypeDocumentStatistique;
use App\Repository\DocStats\Entetes\DocumentbcbgfhRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.documentbcbgfh')]
#[ORM\Entity(repositoryClass: DocumentbcbgfhRepository::class)]
class Documentbcbgfh
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_docbcbgfh = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $delivre_docbcbgfh = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'documentbcbgfhs')]
    private ?TypeDocumentStatistique $type_document = null;

    #[ORM\OneToMany(mappedBy: 'code_docbcbgfh', targetEntity: Pagebcbgfh::class)]
    private Collection $pagebcbgfhs;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\ManyToOne(inversedBy: 'documentbcbgfhs')]
    private ?DocStatsGen $code_generation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $etat = null;

    #[ORM\ManyToOne(inversedBy: 'documentbcbgfhs')]
    private ?Exercice $exercice = null;

    #[ORM\Column(nullable: true)]
    private ?bool $transmission = null;

    #[ORM\ManyToOne(inversedBy: 'documentbcbgfhs')]
    private ?DemandeOperateur $code_demande = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_dr = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_cef = null;

    #[ORM\ManyToOne(inversedBy: 'documentbcbgfhs')]
    private ?ContratBcbgfh $code_contrat = null;

    public function __construct()
    {
        $this->pagebcbgfhs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDocbcbgfh(): ?string
    {
        return $this->numero_docbcbgfh;
    }

    public function setNumeroDocbcbgfh(string $numero_docbcbgfh): static
    {
        $this->numero_docbcbgfh = $numero_docbcbgfh;

        return $this;
    }

    public function getDelivreDocbcbgfh(): ?\DateTimeInterface
    {
        return $this->delivre_docbcbgfh;
    }

    public function setDelivreDocbcbgfh(\DateTimeInterface $delivre_docbcbgfh): static
    {
        $this->delivre_docbcbgfh = $delivre_docbcbgfh;

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
     * @return Collection<int, Pagebcbgfh>
     */
    public function getPagebcbgfhs(): Collection
    {
        return $this->pagebcbgfhs;
    }

    public function addPagebcbgfh(Pagebcbgfh $pagebcbgfh): static
    {
        if (!$this->pagebcbgfhs->contains($pagebcbgfh)) {
            $this->pagebcbgfhs->add($pagebcbgfh);
            $pagebcbgfh->setCodeDocbcbgfh($this);
        }

        return $this;
    }

    public function removePagebcbgfh(Pagebcbgfh $pagebcbgfh): static
    {
        if ($this->pagebcbgfhs->removeElement($pagebcbgfh)) {
            // set the owning side to null (unless already changed)
            if ($pagebcbgfh->getCodeDocbcbgfh() === $this) {
                $pagebcbgfh->setCodeDocbcbgfh(null);
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

    public function getCodeContrat(): ?ContratBcbgfh
    {
        return $this->code_contrat;
    }

    public function setCodeContrat(?ContratBcbgfh $code_contrat): static
    {
        $this->code_contrat = $code_contrat;

        return $this;
    }
}
