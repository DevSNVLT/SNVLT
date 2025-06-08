<?php

namespace App\Entity\Autorisation;

use App\Entity\References\AttributairePs;
use App\Entity\References\TypeDossierPs;
use App\Repository\Autorisations\AgreementPsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.agreement_ps')]
#[ORM\Entity(repositoryClass: AgreementPsRepository::class)]
class AgreementPs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_dossier = null;

    #[ORM\Column(nullable: true)]
    private ?int $montant_agrement = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_ouverture = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'agreementPs')]
    private ?AttributairePs $code_attributaire_ps = null;

    #[ORM\ManyToOne(inversedBy: 'agreementPs')]
    private ?TypeDossierPs $code_type_dossier = null;

    #[ORM\OneToMany(mappedBy: 'code_dossier', targetEntity: AutorisationPs::class)]
    private Collection $autorisationPs;

    #[ORM\Column(nullable: true)]
    private ?bool $statut = null;

    #[ORM\Column(nullable: true)]
    private ?bool $reprise = null;

    public function __construct()
    {

        $this->autorisationPs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDossier(): ?string
    {
        return $this->numero_dossier;
    }

    public function setNumeroDossier(string $numero_dossier): static
    {
        $this->numero_dossier = $numero_dossier;

        return $this;
    }

    public function getMontantAgrement(): ?int
    {
        return $this->montant_agrement;
    }

    public function setMontantAgrement(?int $montant_agrement): static
    {
        $this->montant_agrement = $montant_agrement;

        return $this;
    }

    public function getDateOuverture(): ?\DateTimeInterface
    {
        return $this->date_ouverture;
    }

    public function setDateOuverture(?\DateTimeInterface $date_ouverture): static
    {
        $this->date_ouverture = $date_ouverture;

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

    public function setCreatedBy(?string $created_by): static
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

    public function getCodeAttributairePs(): ?AttributairePs
    {
        return $this->code_attributaire_ps;
    }

    public function setCodeAttributairePs(?AttributairePs $code_attributaire_ps): static
    {
        $this->code_attributaire_ps = $code_attributaire_ps;

        return $this;
    }

    public function getCodeTypeDossier(): ?TypeDossierPs
    {
        return $this->code_type_dossier;
    }

    public function setCodeTypeDossier(?TypeDossierPs $code_type_dossier): static
    {
        $this->code_type_dossier = $code_type_dossier;

        return $this;
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(?bool $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function isReprise(): ?bool
    {
        return $this->reprise;
    }

    public function setReprise(?bool $reprise): static
    {
        $this->reprise = $reprise;

        return $this;
    }

    /**
     * @return Collection<int, AutorisationPs>
     */
    public function getAutorisationPs(): Collection
    {
        return $this->autorisationPs;
    }

    public function addAutorisationP(AutorisationPs $autorisationP): static
    {
        if (!$this->autorisationPs->contains($autorisationP)) {
            $this->autorisationPs->add($autorisationP);
            $autorisationP->setCodeDossier($this);
        }

        return $this;
    }

    public function removeAutorisationP(AutorisationPs $autorisationP): static
    {
        if ($this->autorisationPs->removeElement($autorisationP)) {
            // set the owning side to null (unless already changed)
            if ($autorisationP->getCodeDossier() === $this) {
                $autorisationP->setCodeDossier(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->numero_dossier . " - ". $this->getCodeAttributairePs()->getRaisonSociale();
    }
}
