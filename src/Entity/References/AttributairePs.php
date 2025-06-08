<?php

namespace App\Entity\References;

use App\Entity\Autorisation\AgreementPs;
use App\Repository\References\AttributairePsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.attributaire_ps')]
#[ORM\Entity(repositoryClass: AttributairePsRepository::class)]
class AttributairePs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $type_atributaire = null;

    #[ORM\Column(length: 255)]
    private ?string $raison_sociale = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $contact = null;

    #[ORM\ManyToOne(inversedBy: 'attributairePs')]
    private ?Nationalite $nationalite = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $sexe = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $cc = null;

    #[ORM\OneToMany(mappedBy: 'code_attributaire_ps', targetEntity: AgreementPs::class)]
    private Collection $agreementPs;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $personne_ressource = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_personne_ressource = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $mobile_personne_ressource = null;

    #[ORM\Column(nullable: true)]
    private ?bool $statut = null;

    #[ORM\Column(length: 50)]
    private ?string $code = null;

    public function __construct()
    {
        $this->agreementPs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeAtributaire(): ?string
    {
        return $this->type_atributaire;
    }

    public function setTypeAtributaire(?string $type_atributaire): static
    {
        $this->type_atributaire = $type_atributaire;

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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getContact(): ?string
    {
        return $this->contact;
    }

    public function setContact(?string $contact): static
    {
        $this->contact = $contact;

        return $this;
    }

    public function getNationalite(): ?Nationalite
    {
        return $this->nationalite;
    }

    public function setNationalite(?Nationalite $nationalite): static
    {
        $this->nationalite = $nationalite;

        return $this;
    }

    public function getSexe(): ?string
    {
        return $this->sexe;
    }

    public function setSexe(?string $sexe): static
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getCc(): ?string
    {
        return $this->cc;
    }

    public function setCc(?string $cc): static
    {
        $this->cc = $cc;

        return $this;
    }

    /**
     * @return Collection<int, AgreementPs>
     */
    public function getAgreementPs(): Collection
    {
        return $this->agreementPs;
    }

    public function addAgreementP(AgreementPs $agreementP): static
    {
        if (!$this->agreementPs->contains($agreementP)) {
            $this->agreementPs->add($agreementP);
            $agreementP->setCodeAttributairePs($this);
        }

        return $this;
    }

    public function removeAgreementP(AgreementPs $agreementP): static
    {
        if ($this->agreementPs->removeElement($agreementP)) {
            // set the owning side to null (unless already changed)
            if ($agreementP->getCodeAttributairePs() === $this) {
                $agreementP->setCodeAttributairePs(null);
            }
        }

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

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(?bool $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function __toString(): string
    {
        return $this->code . " - " . $this->raison_sociale;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

}
