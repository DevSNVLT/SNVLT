<?php

namespace App\Entity\References;

use App\Entity\Observateur\PublicationRapport;
use App\Entity\Observateur\Ticket;
use App\Entity\User;
use App\Repository\References\DirectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'metier.direction')]
#[ORM\Entity(repositoryClass: DirectionRepository::class)]
#[UniqueEntity(fields: ['denomination'], message: 'There is already a name with this ministry direction')]
class Direction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $denomination = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sigle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_responsable = null;

    #[ORM\OneToMany(mappedBy: 'code_direction', targetEntity: ServiceMinef::class)]
    private Collection $serviceMinefs;

    #[ORM\OneToMany(mappedBy: 'code_direction', targetEntity: User::class)]
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

    #[ORM\OneToMany(mappedBy: 'code_direction', targetEntity: DetailsModele::class)]
    private Collection $detailsModeles;

    #[ORM\OneToMany(mappedBy: 'code_direction', targetEntity: CircuitCommunication::class)]
    private Collection $circuitCommunications;

    #[ORM\ManyToMany(targetEntity: Ticket::class, mappedBy: 'code_direction')]
    private Collection $tickets;

    #[ORM\ManyToMany(targetEntity: PublicationRapport::class, mappedBy: 'code_direction')]
    private Collection $publicationRapports;

    public function __construct()
    {
        $this->serviceMinefs = new ArrayCollection();
        $this->utilisateurs = new ArrayCollection();
        $this->detailsModeles = new ArrayCollection();
        $this->circuitCommunications = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->publicationRapports = new ArrayCollection();
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

    public function getSigle(): ?string
    {
        return $this->sigle;
    }

    public function setSigle(?string $sigle): static
    {
        $this->sigle = $sigle;

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
     * @return Collection<int, ServiceMinef>
     */
    public function getServiceMinefs(): Collection
    {
        return $this->serviceMinefs;
    }

    public function addServiceMinef(ServiceMinef $serviceMinef): static
    {
        if (!$this->serviceMinefs->contains($serviceMinef)) {
            $this->serviceMinefs->add($serviceMinef);
            $serviceMinef->setCodeDirection($this);
        }

        return $this;
    }

    public function removeServiceMinef(ServiceMinef $serviceMinef): static
    {
        if ($this->serviceMinefs->removeElement($serviceMinef)) {
            // set the owning side to null (unless already changed)
            if ($serviceMinef->getCodeDirection() === $this) {
                $serviceMinef->setCodeDirection(null);
            }
        }

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
            $utilisateur->setCodeDirection($this);
        }

        return $this;
    }

    public function removeUser(User $utilisateur): static
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getCodeDirection() === $this) {
                $utilisateur->setCodeDirection(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->sigle;
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
     * @return Collection<int, DetailsModele>
     */
    public function getDetailsModeles(): Collection
    {
        return $this->detailsModeles;
    }

    public function addDetailsModele(DetailsModele $detailsModele): static
    {
        if (!$this->detailsModeles->contains($detailsModele)) {
            $this->detailsModeles->add($detailsModele);
            $detailsModele->setCodeDirection($this);
        }

        return $this;
    }

    public function removeDetailsModele(DetailsModele $detailsModele): static
    {
        if ($this->detailsModeles->removeElement($detailsModele)) {
            // set the owning side to null (unless already changed)
            if ($detailsModele->getCodeDirection() === $this) {
                $detailsModele->setCodeDirection(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CircuitCommunication>
     */
    public function getCircuitCommunications(): Collection
    {
        return $this->circuitCommunications;
    }

    public function addCircuitCommunication(CircuitCommunication $circuitCommunication): static
    {
        if (!$this->circuitCommunications->contains($circuitCommunication)) {
            $this->circuitCommunications->add($circuitCommunication);
            $circuitCommunication->setCodeDirection($this);
        }

        return $this;
    }

    public function removeCircuitCommunication(CircuitCommunication $circuitCommunication): static
    {
        if ($this->circuitCommunications->removeElement($circuitCommunication)) {
            // set the owning side to null (unless already changed)
            if ($circuitCommunication->getCodeDirection() === $this) {
                $circuitCommunication->setCodeDirection(null);
            }
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
            $ticket->addCodeDirection($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            $ticket->removeCodeDirection($this);
        }

        return $this;
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
            $publicationRapport->addCodeDirection($this);
        }

        return $this;
    }

    public function removePublicationRapport(PublicationRapport $publicationRapport): static
    {
        if ($this->publicationRapports->removeElement($publicationRapport)) {
            $publicationRapport->removeCodeDirection($this);
        }

        return $this;
    }


}
