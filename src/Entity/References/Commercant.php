<?php

namespace App\Entity\References;

use App\Entity\Administration\DemandeOperateur;
use App\Entity\Autorisation\AgreementCommercant;
use App\Entity\DocStats\Entetes\Documentrsdpf;
use App\Entity\User;
use App\Repository\References\CommercantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.commercant')]
#[ORM\Entity(repositoryClass: CommercantRepository::class)]
class Commercant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $numero_commercant = null;

    #[ORM\Column(length: 50)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenoms = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $mobile = null;

    #[ORM\ManyToOne(inversedBy: 'commercants')]
    private ?Nationalite $nationalite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\OneToMany(mappedBy: 'code_commercant', targetEntity: Documentrsdpf::class)]
    private Collection $documentrsdpfs;

    #[ORM\OneToMany(mappedBy: 'code_commercant', targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'code_commercant', targetEntity: DemandeOperateur::class)]
    private Collection $demandeOperateurs;

    #[ORM\ManyToOne(inversedBy: 'commercants')]
    private ?Cantonnement $code_cantonnement = null;

    #[ORM\ManyToOne(inversedBy: 'commercants')]
    private ?Ddef $code_ddef = null;

    #[ORM\ManyToOne(inversedBy: 'commercants')]
    private ?Dr $code_dr = null;

    #[ORM\OneToMany(mappedBy: 'code_commercant', targetEntity: AgreementCommercant::class)]
    private Collection $agreementCommercants;

    public function __construct()
    {
        $this->documentrsdpfs = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->demandeOperateurs = new ArrayCollection();
        $this->agreementCommercants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroCommercant(): ?string
    {
        return $this->numero_commercant;
    }

    public function setNumeroCommercant(?string $numero_commercant): static
    {
        $this->numero_commercant = $numero_commercant;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenoms(): ?string
    {
        return $this->prenoms;
    }

    public function setPrenoms(string $prenoms): static
    {
        $this->prenoms = $prenoms;

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

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): static
    {
        $this->mobile = $mobile;

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

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * @return Collection<int, Documentrsdpf>
     */
    public function getDocumentrsdpfs(): Collection
    {
        return $this->documentrsdpfs;
    }

    public function addDocumentrsdpf(Documentrsdpf $documentrsdpf): static
    {
        if (!$this->documentrsdpfs->contains($documentrsdpf)) {
            $this->documentrsdpfs->add($documentrsdpf);
            $documentrsdpf->setCodeCommercant($this);
        }

        return $this;
    }

    public function removeDocumentrsdpf(Documentrsdpf $documentrsdpf): static
    {
        if ($this->documentrsdpfs->removeElement($documentrsdpf)) {
            // set the owning side to null (unless already changed)
            if ($documentrsdpf->getCodeCommercant() === $this) {
                $documentrsdpf->setCodeCommercant(null);
            }
        }

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
            $user->setCodeCommercant($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCodeCommercant() === $this) {
                $user->setCodeCommercant(null);
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
            $demandeOperateur->setCodeCommercant($this);
        }

        return $this;
    }

    public function removeDemandeOperateur(DemandeOperateur $demandeOperateur): static
    {
        if ($this->demandeOperateurs->removeElement($demandeOperateur)) {
            // set the owning side to null (unless already changed)
            if ($demandeOperateur->getCodeCommercant() === $this) {
                $demandeOperateur->setCodeCommercant(null);
            }
        }

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

    public function getCodeDdef(): ?Ddef
    {
        return $this->code_ddef;
    }

    public function setCodeDdef(?Ddef $code_ddef): static
    {
        $this->code_ddef = $code_ddef;

        return $this;
    }

    public function getCodeDr(): ?Dr
    {
        return $this->code_dr;
    }

    public function setCodeDr(?Dr $code_dr): static
    {
        $this->code_dr = $code_dr;

        return $this;
    }

    /**
     * @return Collection<int, AgreementCommercant>
     */
    public function getAgreementCommercants(): Collection
    {
        return $this->agreementCommercants;
    }

    public function addAgreementCommercant(AgreementCommercant $agreementCommercant): static
    {
        if (!$this->agreementCommercants->contains($agreementCommercant)) {
            $this->agreementCommercants->add($agreementCommercant);
            $agreementCommercant->setCodeCommercant($this);
        }

        return $this;
    }

    public function removeAgreementCommercant(AgreementCommercant $agreementCommercant): static
    {
        if ($this->agreementCommercants->removeElement($agreementCommercant)) {
            // set the owning side to null (unless already changed)
            if ($agreementCommercant->getCodeCommercant() === $this) {
                $agreementCommercant->setCodeCommercant(null);
            }
        }

        return $this;
    }
}
