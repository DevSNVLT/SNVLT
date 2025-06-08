<?php

namespace App\Entity\References;

use App\Entity\DocStats\Pages\Pagepdtdrv;
use App\Entity\Observateur\PublicationRapport;
use App\Entity\Observateur\Ticket;
use App\Entity\References\Ddef;
use App\Entity\User;
use App\Repository\References\DrRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'metier.dr')]
#[ORM\Entity(repositoryClass: DrRepository::class)]
#[UniqueEntity(fields: ['email_responsable'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['denomination'], message: 'There is already a name with this regional directorate')]
class Dr
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $denomination = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_responsable = null;

    #[ORM\OneToMany(mappedBy: 'code_dr', targetEntity: Cantonnement::class)]
    private Collection $cantonnements;

    #[ORM\OneToMany(mappedBy: 'code_dr', targetEntity: User::class)]
    private Collection $utilisateurs;

    #[ORM\OneToMany(mappedBy: 'code_dr', targetEntity: Ddef::class)]
    private Collection $ddefs;

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

    #[ORM\OneToMany(mappedBy: 'dref', targetEntity: Pagepdtdrv::class)]
    private Collection $pagepdtdrvs;

    #[ORM\OneToMany(mappedBy: 'code_dr', targetEntity: Commercant::class)]
    private Collection $commercants;

    #[ORM\ManyToMany(targetEntity: PublicationRapport::class, mappedBy: 'code_dr')]
    private Collection $publicationRapports;

    #[ORM\ManyToMany(targetEntity: Ticket::class, mappedBy: 'code_dr')]
    private Collection $tickets;


    public function __construct()
    {
        $this->cantonnements = new ArrayCollection();
        $this->utilisateurs = new ArrayCollection();
        $this->ddefs = new ArrayCollection();
        $this->pagepdtdrvs = new ArrayCollection();
        $this->commercants = new ArrayCollection();
        $this->publicationRapports = new ArrayCollection();
        $this->tickets = new ArrayCollection();
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
            $cantonnement->setCodeDr($this);
        }

        return $this;
    }

    public function removeCantonnement(Cantonnement $cantonnement): static
    {
        if ($this->cantonnements->removeElement($cantonnement)) {
            // set the owning side to null (unless already changed)
            if ($cantonnement->getCodeDr() === $this) {
                $cantonnement->setCodeDr(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->denomination;
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
            $utilisateur->setCodeDr($this);
        }

        return $this;
    }

    public function removeUser(User $utilisateur): static
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getCodeDr() === $this) {
                $utilisateur->setCodeDr(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ddef>
     */
    public function getDdefs(): Collection
    {
        return $this->ddefs;
    }

    public function addDdef(Ddef $ddef): static
    {
        if (!$this->ddefs->contains($ddef)) {
            $this->ddefs->add($ddef);
            $ddef->setCodeDr($this);
        }

        return $this;
    }

    public function removeDdef(Ddef $ddef): static
    {
        if ($this->ddefs->removeElement($ddef)) {
            // set the owning side to null (unless already changed)
            if ($ddef->getCodeDr() === $this) {
                $ddef->setCodeDr(null);
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
            $pagepdtdrv->setDref($this);
        }

        return $this;
    }

    public function removePagepdtdrv(Pagepdtdrv $pagepdtdrv): static
    {
        if ($this->pagepdtdrvs->removeElement($pagepdtdrv)) {
            // set the owning side to null (unless already changed)
            if ($pagepdtdrv->getDref() === $this) {
                $pagepdtdrv->setDref(null);
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
            $commercant->setCodeDr($this);
        }

        return $this;
    }

    public function removeCommercant(Commercant $commercant): static
    {
        if ($this->commercants->removeElement($commercant)) {
            // set the owning side to null (unless already changed)
            if ($commercant->getCodeDr() === $this) {
                $commercant->setCodeDr(null);
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

    /**
     * @return Collection<int, PublicationRapport>
     */
    public function getPublicationRapports(): Collection
    {
        return $this->publicationRapports;
    }

    public function addPublicationRapport(PublicationRapport $publicationRapport): static
    {
        if (!$this->publicationRapports->contains($publicationRapport)) {
            $this->publicationRapports->add($publicationRapport);
            $publicationRapport->addCodeDr($this);
        }

        return $this;
    }

    public function removePublicationRapport(PublicationRapport $publicationRapport): static
    {
        if ($this->publicationRapports->removeElement($publicationRapport)) {
            $publicationRapport->removeCodeDr($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): static
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets->add($ticket);
            $ticket->addCodeDr($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            $ticket->removeCodeDr($this);
        }

        return $this;
    }


}
