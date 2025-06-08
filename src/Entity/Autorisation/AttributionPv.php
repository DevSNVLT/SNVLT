<?php

namespace App\Entity\Autorisation;

use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Repository\Autorisations\AttributionPvRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.attribution_pv')]
#[ORM\Entity(repositoryClass: AttributionPvRepository::class)]
class AttributionPv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numero_decision = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_decision = null;

    #[ORM\Column(nullable: true)]
    private ?bool $statut = null;

    #[ORM\Column(length: 255)]
    private ?string $raison_sociale = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type_attributaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $personne_ressource = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_personne_ressource = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mobile_personne_ressource = null;

    #[ORM\ManyToOne(inversedBy: 'attributionPvs')]
    private ?Foret $code_parcelle = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_attribution_pv', targetEntity: AutorisationPv::class)]
    private Collection $autorisationPvs;

    #[ORM\Column(nullable: true)]
    private ?bool $reprise = null;

    public function __construct()
    {
        $this->autorisationPvs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDecision(): ?int
    {
        return $this->numero_decision;
    }

    public function setNumeroDecision(int $numero_decision): static
    {
        $this->numero_decision = $numero_decision;

        return $this;
    }

    public function getDateDecision(): ?\DateTimeInterface
    {
        return $this->date_decision;
    }

    public function setDateDecision(\DateTimeInterface $date_decision): static
    {
        $this->date_decision = $date_decision;

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



    public function getRaisonSociale(): ?string
    {
        return $this->raison_sociale;
    }

    public function setRaisonSociale(string $raison_sociale): static
    {
        $this->raison_sociale = $raison_sociale;

        return $this;
    }

    public function getTypeAttributaire(): ?string
    {
        return $this->type_attributaire;
    }

    public function setTypeAttributaire(?string $type_attributaire): static
    {
        $this->type_attributaire = $type_attributaire;

        return $this;
    }

    public function getPersonneRessource(): ?string
    {
        return $this->personne_ressource;
    }

    public function setPersonneRessource(?string $personne_ressource): static
    {
        $this->personne_ressource = $personne_ressource;

        return $this;
    }

    public function getEmailPersonneRessource(): ?string
    {
        return $this->email_personne_ressource;
    }

    public function setEmailPersonneRessource(?string $email_personne_ressource): static
    {
        $this->email_personne_ressource = $email_personne_ressource;

        return $this;
    }

    public function getMobilePersonneRessource(): ?string
    {
        return $this->mobile_personne_ressource;
    }

    public function setMobilePersonneRessource(?string $mobile_personne_ressource): static
    {
        $this->mobile_personne_ressource = $mobile_personne_ressource;

        return $this;
    }

    public function getCodeParcelle(): ?Foret
    {
        return $this->code_parcelle;
    }

    public function setCodeParcelle(?Foret $code_parcelle): static
    {
        $this->code_parcelle = $code_parcelle;

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
     * @return Collection<int, AutorisationPv>
     */
    public function getAutorisationPvs(): Collection
    {
        return $this->autorisationPvs;
    }

    public function addAutorisationPv(AutorisationPv $autorisationPv): static
    {
        if (!$this->autorisationPvs->contains($autorisationPv)) {
            $this->autorisationPvs->add($autorisationPv);
            $autorisationPv->setCodeAttributionPv($this);
        }

        return $this;
    }

    public function removeAutorisationPv(AutorisationPv $autorisationPv): static
    {
        if ($this->autorisationPvs->removeElement($autorisationPv)) {
            // set the owning side to null (unless already changed)
            if ($autorisationPv->getCodeAttributionPv() === $this) {
                $autorisationPv->setCodeAttributionPv(null);
            }
        }

        return $this;
    }

    /**
     * @return bool|null
     */
    public function getReprise(): ?bool
    {
        return $this->reprise;
    }

    /**
     * @param bool|null $reprise
     */
    public function setReprise(?bool $reprise): void
    {
        $this->reprise = $reprise;
    }



    public function __toString(): string
    {
        return  $this->numero_decision . " du " . $this->date_decision->format('d/m/Y') . " - " . $this->raison_sociale;
    }

}
