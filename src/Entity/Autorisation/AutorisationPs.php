<?php

namespace App\Entity\Autorisation;

use App\Entity\Admin\Exercice;
use App\Entity\Administration\DemandeOperateur;
use App\Entity\DocStats\Entetes\Documentbcburb;
use App\Entity\References\NaturePs;
use App\Entity\References\TypeDossierPs;
use App\Repository\Autorisation\AutorisationPsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.autorisation_ps')]
#[ORM\Entity(repositoryClass: AutorisationPsRepository::class)]
class AutorisationPs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_auto_ps = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_delivrance = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_expiration = null;

    #[ORM\ManyToOne(inversedBy: 'autorisationPs')]
    private ?AgreementPs $code_dossier = null;

    #[ORM\ManyToOne(inversedBy: 'autorisationPs')]
    private ?NaturePs $code_produit = null;

    #[ORM\ManyToOne(inversedBy: 'autorisationPs')]
    private ?Exercice $exercice = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(nullable: true)]
    private ?int $montant_autorisation = null;

    #[ORM\OneToMany(mappedBy: 'permis', targetEntity: Documentbcburb::class)]
    private Collection $documentbcburbs;

    #[ORM\OneToMany(mappedBy: 'code_autorisationps', targetEntity: DemandeOperateur::class)]
    private Collection $demandeOperateurs;

    public function __construct()
    {
        $this->documentbcburbs = new ArrayCollection();
        $this->demandeOperateurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroAutoPs(): ?string
    {
        return $this->numero_auto_ps;
    }

    public function setNumeroAutoPs(string $numero_auto_ps): static
    {
        $this->numero_auto_ps = $numero_auto_ps;

        return $this;
    }

    public function getDateDelivrance(): ?\DateTimeInterface
    {
        return $this->date_delivrance;
    }

    public function setDateDelivrance(\DateTimeInterface $date_delivrance): static
    {
        $this->date_delivrance = $date_delivrance;

        return $this;
    }

    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->date_expiration;
    }

    public function setDateExpiration(\DateTimeInterface $date_expiration): static
    {
        $this->date_expiration = $date_expiration;

        return $this;
    }

    public function getCodeDossier(): ?AgreementPs
    {
        return $this->code_dossier;
    }

    public function setCodeDossier(?AgreementPs $code_dossier): static
    {
        $this->code_dossier = $code_dossier;

        return $this;
    }

    public function getCodeProduit(): ?NaturePs
    {
        return $this->code_produit;
    }

    public function setCodeProduit(?NaturePs $code_produit): static
    {
        $this->code_produit = $code_produit;

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

    public function getMontantAutorisation(): ?int
    {
        return $this->montant_autorisation;
    }

    public function setMontantAutorisation(?int $montant_autorisation): static
    {
        $this->montant_autorisation = $montant_autorisation;

        return $this;
    }

    /**
     * @return Collection<int, Documentbcburb>
     */
    public function getDocumentbcburbs(): Collection
    {
        return $this->documentbcburbs;
    }

    public function addDocumentbcburb(Documentbcburb $documentbcburb): static
    {
        if (!$this->documentbcburbs->contains($documentbcburb)) {
            $this->documentbcburbs->add($documentbcburb);
            $documentbcburb->setPermis($this);
        }

        return $this;
    }

    public function removeDocumentbcburb(Documentbcburb $documentbcburb): static
    {
        if ($this->documentbcburbs->removeElement($documentbcburb)) {
            // set the owning side to null (unless already changed)
            if ($documentbcburb->getPermis() === $this) {
                $documentbcburb->setPermis(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DemandeOperateur>
     */
    public function getDemandeOperateurs(): Collection
    {
        return $this->demandeOperateurs;
    }

    public function addDemandeOperateur(DemandeOperateur $demandeOperateur): static
    {
        if (!$this->demandeOperateurs->contains($demandeOperateur)) {
            $this->demandeOperateurs->add($demandeOperateur);
            $demandeOperateur->setCodeAutorisationps($this);
        }

        return $this;
    }

    public function removeDemandeOperateur(DemandeOperateur $demandeOperateur): static
    {
        if ($this->demandeOperateurs->removeElement($demandeOperateur)) {
            // set the owning side to null (unless already changed)
            if ($demandeOperateur->getCodeAutorisationps() === $this) {
                $demandeOperateur->setCodeAutorisationps(null);
            }
        }

        return $this;
    }
}
