<?php

namespace App\Entity\References;

use App\Entity\Administration\DemandeOperateur;
use App\Entity\Autorisation\AgreementExportateur;
use App\Entity\User;
use App\Repository\References\ExportateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'metier.exportateur')]
#[ORM\Entity(repositoryClass: ExportateurRepository::class)]
#[UniqueEntity(fields: ['code_exportateur'], message: 'The Exporter code already exists')]
#[UniqueEntity(fields: ['raison_sociale_exportateur'], message: 'There is already a name with this exporter')]
#[UniqueEntity(fields: ['email_personne_ressource'], message: 'There is already an account with this email')]
class Exportateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $code_exportateur = null;

    #[ORM\Column(length: 255)]
    private ?string $raison_sociale_exportateur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse_exportateur = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $tel_exportateur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_exportateur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $personne_ressource = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_personne_ressource = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mobile_personne_ressource = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'exportateurs')]
    private ?Cantonnement $code_cantonnement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sigle = null;

    #[ORM\OneToMany(mappedBy: 'code_exportateur', targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'code_exportateur', targetEntity: DemandeOperateur::class)]
    private Collection $demandeOperateurs;

    #[ORM\OneToMany(mappedBy: 'code_exportateur', targetEntity: AgreementExportateur::class)]
    private Collection $agreementExportateurs;

    #[ORM\Column(nullable: true)]
    private ?bool $statut = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->demandeOperateurs = new ArrayCollection();
        $this->agreementExportateurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeExportateur(): ?string
    {
        return $this->code_exportateur;
    }

    public function setCodeExportateur(string $code_exportateur): static
    {
        $this->code_exportateur = $code_exportateur;

        return $this;
    }

    public function getRaisonSocialeExportateur(): ?string
    {
        return $this->raison_sociale_exportateur;
    }

    public function setRaisonSocialeExportateur(string $raison_sociale_exportateur): static
    {
        $this->raison_sociale_exportateur = $raison_sociale_exportateur;

        return $this;
    }

    public function getAdresseExportateur(): ?string
    {
        return $this->adresse_exportateur;
    }

    public function setAdresseExportateur(?string $adresse_exportateur): static
    {
        $this->adresse_exportateur = $adresse_exportateur;

        return $this;
    }

    public function getTelExportateur(): ?string
    {
        return $this->tel_exportateur;
    }

    public function setTelExportateur(?string $tel_exportateur): static
    {
        $this->tel_exportateur = $tel_exportateur;

        return $this;
    }

    public function getEmailExportateur(): ?string
    {
        return $this->email_exportateur;
    }

    public function setEmailExportateur(?string $email_exportateur): static
    {
        $this->email_exportateur = $email_exportateur;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(?\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): static
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

    public function getCodeCantonnement(): ?Cantonnement
    {
        return $this->code_cantonnement;
    }

    public function setCodeCantonnement(?Cantonnement $code_cantonnement): static
    {
        $this->code_cantonnement = $code_cantonnement;

        return $this;
    }
    public function __toString(): string
    {
       return $this->raison_sociale_exportateur;
    }

    public function getSigle(): ?string
    {
        return $this->sigle;
    }

    public function setSigle(?string $sigle): static
    {
        $this->sigle = $sigle;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setCodeExportateur($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCodeExportateur() === $this) {
                $user->setCodeExportateur(null);
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
            $demandeOperateur->setCodeExportateur($this);
        }

        return $this;
    }

    public function removeDemandeOperateur(DemandeOperateur $demandeOperateur): static
    {
        if ($this->demandeOperateurs->removeElement($demandeOperateur)) {
            // set the owning side to null (unless already changed)
            if ($demandeOperateur->getCodeExportateur() === $this) {
                $demandeOperateur->setCodeExportateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AgreementExportateur>
     */
    public function getAgreementExportateurs(): Collection
    {
        return $this->agreementExportateurs;
    }

    public function addAgreementExportateur(AgreementExportateur $agreementExportateur): static
    {
        if (!$this->agreementExportateurs->contains($agreementExportateur)) {
            $this->agreementExportateurs->add($agreementExportateur);
            $agreementExportateur->setCodeExportateur($this);
        }

        return $this;
    }

    public function removeAgreementExportateur(AgreementExportateur $agreementExportateur): static
    {
        if ($this->agreementExportateurs->removeElement($agreementExportateur)) {
            // set the owning side to null (unless already changed)
            if ($agreementExportateur->getCodeExportateur() === $this) {
                $agreementExportateur->setCodeExportateur(null);
            }
        }

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
}
