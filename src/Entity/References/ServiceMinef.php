<?php

namespace App\Entity\References;

use App\Entity\Observateur\PublicationRapport;
use App\Entity\Observateur\Ticket;
use App\Entity\User;
use App\Repository\References\ServiceMinefRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'metier.service_minef')]
#[ORM\Entity(repositoryClass: ServiceMinefRepository::class)]
#[UniqueEntity(fields: ['email_responsable'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['libelle_service'], message: 'There is already a name with this ministry service')]
class ServiceMinef
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle_service = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $telephone_service = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_responsable = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $codeservice = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sigle = null;

    #[ORM\ManyToOne(inversedBy: 'serviceMinefs')]
    private ?Direction $code_direction = null;

    #[ORM\OneToMany(mappedBy: 'code_service', targetEntity: User::class)]
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

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $mobile_personne_ressource = null;

    #[ORM\OneToMany(mappedBy: 'code_service', targetEntity: DetailsModele::class)]
    private Collection $detailsModeles;

    #[ORM\OneToMany(mappedBy: 'code_service', targetEntity: CircuitCommunication::class)]
    private Collection $circuitCommunications;

    #[ORM\ManyToMany(targetEntity: Ticket::class, mappedBy: 'code_service')]
    private Collection $tickets;

    #[ORM\ManyToMany(targetEntity: PublicationRapport::class, mappedBy: 'code_service_minef')]
    private Collection $publicationRapports;

    public function __construct()
    {
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

    public function getLibelleService(): ?string
    {
        return $this->libelle_service;
    }

    public function setLibelleService(string $libelle_service): static
    {
        $this->libelle_service = $libelle_service;

        return $this;
    }

    public function getTelephoneService(): ?string
    {
        return $this->telephone_service;
    }

    public function setTelephoneService(?string $telephone_service): static
    {
        $this->telephone_service = $telephone_service;

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

    public function getCodeservice(): ?string
    {
        return $this->codeservice;
    }

    public function setCodeservice(?string $codeservice): static
    {
        $this->codeservice = $codeservice;

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

    public function getCodeDirection(): ?Direction
    {
        return $this->code_direction;
    }

    public function setCodeDirection(?Direction $code_direction): static
    {
        $this->code_direction = $code_direction;

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
            $utilisateur->setCodeService($this);
        }

        return $this;
    }

    public function removeUser(User $utilisateur): static
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getCodeService() === $this) {
                $utilisateur->setCodeService(null);
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

    public function __toString(): string
    {
        return $this->libelle_service;
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
            $detailsModele->setCodeService($this);
        }

        return $this;
    }

    public function removeDetailsModele(DetailsModele $detailsModele): static
    {
        if ($this->detailsModeles->removeElement($detailsModele)) {
            // set the owning side to null (unless already changed)
            if ($detailsModele->getCodeService() === $this) {
                $detailsModele->setCodeService(null);
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
            $circuitCommunication->setCodeService($this);
        }

        return $this;
    }

    public function removeCircuitCommunication(CircuitCommunication $circuitCommunication): static
    {
        if ($this->circuitCommunications->removeElement($circuitCommunication)) {
            // set the owning side to null (unless already changed)
            if ($circuitCommunication->getCodeService() === $this) {
                $circuitCommunication->setCodeService(null);
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
            $ticket->addCodeService($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            $ticket->removeCodeService($this);
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
            $publicationRapport->addCodeServiceMinef($this);
        }

        return $this;
    }

    public function removePublicationRapport(PublicationRapport $publicationRapport): static
    {
        if ($this->publicationRapports->removeElement($publicationRapport)) {
            $publicationRapport->removeCodeServiceMinef($this);
        }

        return $this;
    }
}
