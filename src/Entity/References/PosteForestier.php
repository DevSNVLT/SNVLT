<?php

namespace App\Entity\References;

use App\Entity\User;
use App\Repository\References\PosteForestierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'metier.poste_forestier')]
#[ORM\Entity(repositoryClass: PosteForestierRepository::class)]
#[UniqueEntity(fields: ['email_responsable'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['denomination'], message: 'There is already a name with this forester checkpoint')]
class PosteForestier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $denomination = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_responsable = null;

    #[ORM\ManyToOne(inversedBy: 'posteForestiers')]
    private ?Cantonnement $code_cantonnement = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $situation_geographique = null;

    #[ORM\OneToMany(mappedBy: 'code_poste_controle', targetEntity: User::class)]
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

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDenomination(): ?string
    {
        return $this->denomination;
    }

    public function setDenomination(string $denomination): static
    {
        $this->denomination = $denomination;

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

    public function getCodeCantonnement(): ?Cantonnement
    {
        return $this->code_cantonnement;
    }

    public function setCodeCantonnement(?Cantonnement $code_cantonnement): static
    {
        $this->code_cantonnement = $code_cantonnement;

        return $this;
    }

    public function getSituationGeographique(): ?string
    {
        return $this->situation_geographique;
    }

    public function setSituationGeographique(?string $situation_geographique): static
    {
        $this->situation_geographique = $situation_geographique;

        return $this;
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
            $utilisateur->setCodePosteControle($this);
        }

        return $this;
    }

    public function removeUser(User $utilisateur): static
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getCodePosteControle() === $this) {
                $utilisateur->setCodePosteControle(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->denomination;
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


}
