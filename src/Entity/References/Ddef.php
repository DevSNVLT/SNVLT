<?php

namespace App\Entity\References;

use App\Entity\DocStats\Pages\Pagepdtdrv;
use App\Entity\User;
use App\Repository\References\DdefRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

#[ORM\Table(name: 'metier.ddef')]
#[ORM\Entity(repositoryClass: DdefRepository::class)]
#[UniqueEntity(fields: ['email_personne_ressource'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['nom_ddef'], message: 'There is already a name with this departemental direction')]
class Ddef
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom_ddef = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_responsable = null;

    #[ORM\ManyToOne(inversedBy: 'ddefs')]
    private ?Dr $code_dr = null;

    #[ORM\OneToMany(mappedBy: 'code_ddef', targetEntity: Cantonnement::class)]
    private Collection $cantonnements;

    #[ORM\OneToMany(mappedBy: 'code_ddef', targetEntity: User::class)]
    private Collection $utilisateurs;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $personne_ressource = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_personne_ressource = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mobile_personne_ressource = null;

    #[ORM\OneToMany(mappedBy: 'ddef', targetEntity: Pagepdtdrv::class)]
    private Collection $pagepdtdrvs;

    #[ORM\OneToMany(mappedBy: 'code_ddef', targetEntity: Commercant::class)]
    private Collection $commercants;

    public function __construct()
    {
        $this->cantonnements = new ArrayCollection();
        $this->utilisateurs = new ArrayCollection();
        $this->pagepdtdrvs = new ArrayCollection();
        $this->commercants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomDdef(): ?string
    {
        return $this->nom_ddef;
    }

    public function setNomDdef(string $nom_ddef): static
    {
        $this->nom_ddef = $nom_ddef;

        return $this;
    }

    public function getEmailResponsable(): ?string
    {
        return $this->email_responsable;
    }

    public function setEmailResponsable(?string $email_responsable): static
    {
        $this->email_responsable = $email_responsable;

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
     * @return Collection<int, Cantonnement>
     */
    public function getCantonnements(): Collection
    {
        return $this->cantonnements;
    }

    public function addCantonnement(Cantonnement $cantonnement): static
    {
        if (!$this->cantonnements->contains($cantonnement)) {
            $this->cantonnements->add($cantonnement);
            $cantonnement->setCodeDdef($this);
        }

        return $this;
    }

    public function removeCantonnement(Cantonnement $cantonnement): static
    {
        if ($this->cantonnements->removeElement($cantonnement)) {
            // set the owning side to null (unless already changed)
            if ($cantonnement->getCodeDdef() === $this) {
                $cantonnement->setCodeDdef(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->nom_ddef;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUser(User $utilisateur): static
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->add($utilisateur);
            $utilisateur->setCodeDdef($this);
        }

        return $this;
    }

    public function removeUser(User $utilisateur): static
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getCodeDdef() === $this) {
                $utilisateur->setCodeDdef(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getUtilisateurs(): ArrayCollection|Collection
    {
        return $this->utilisateurs;
    }

    /**
     * @param ArrayCollection|Collection $utilisateurs
     */
    public function setUtilisateurs(ArrayCollection|Collection $utilisateurs): void
    {
        $this->utilisateurs = $utilisateurs;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @param \DateTimeImmutable|null $created_at
     */
    public function setCreatedAt(?\DateTimeImmutable $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return string|null
     */
    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    /**
     * @param string|null $created_by
     */
    public function setCreatedBy(?string $created_by): void
    {
        $this->created_by = $created_by;
    }

    /**
     * @return string|null
     */
    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    /**
     * @param string|null $updated_by
     */
    public function setUpdatedBy(?string $updated_by): void
    {
        $this->updated_by = $updated_by;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * @param \DateTimeInterface|null $updated_at
     */
    public function setUpdatedAt(?\DateTimeInterface $updated_at): void
    {
        $this->updated_at = $updated_at;
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

    /**
     * @return Collection<int, Pagepdtdrv>
     */
    public function getPagepdtdrvs(): Collection
    {
        return $this->pagepdtdrvs;
    }

    public function addPagepdtdrv(Pagepdtdrv $pagepdtdrv): static
    {
        if (!$this->pagepdtdrvs->contains($pagepdtdrv)) {
            $this->pagepdtdrvs->add($pagepdtdrv);
            $pagepdtdrv->setDdef($this);
        }

        return $this;
    }

    public function removePagepdtdrv(Pagepdtdrv $pagepdtdrv): static
    {
        if ($this->pagepdtdrvs->removeElement($pagepdtdrv)) {
            // set the owning side to null (unless already changed)
            if ($pagepdtdrv->getDdef() === $this) {
                $pagepdtdrv->setDdef(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commercant>
     */
    public function getCommercants(): Collection
    {
        return $this->commercants;
    }

    public function addCommercant(Commercant $commercant): static
    {
        if (!$this->commercants->contains($commercant)) {
            $this->commercants->add($commercant);
            $commercant->setCodeDdef($this);
        }

        return $this;
    }

    public function removeCommercant(Commercant $commercant): static
    {
        if ($this->commercants->removeElement($commercant)) {
            // set the owning side to null (unless already changed)
            if ($commercant->getCodeDdef() === $this) {
                $commercant->setCodeDdef(null);
            }
        }

        return $this;
    }


}
