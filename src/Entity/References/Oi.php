<?php

namespace App\Entity\References;

use App\Entity\Observateur\PublicationRapport;
use App\Entity\Observateur\Ticket;
use App\Entity\User;
use App\Repository\References\OiRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.oi')]
#[ORM\Entity(repositoryClass: OiRepository::class)]
class Oi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $raison_sociale = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $sigle = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $personne_ressource = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_personne_ressource = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mobile_personne_ressource = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'code_oi', targetEntity: User::class)]
    private Collection $users;

    #[ORM\OneToMany(mappedBy: 'code_oi', targetEntity: PublicationRapport::class)]
    private Collection $publicationRapports;

    #[ORM\OneToMany(mappedBy: 'code_oi', targetEntity: Ticket::class)]
    private Collection $tickets;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->publicationRapports = new ArrayCollection();
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): static
    {
        $this->code = $code;

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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

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

    public function setCreatedAt(\DateTimeImmutable $created_at): static
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

    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    public function setUpdatedBy(?string $updated_by): static
    {
        $this->updated_by = $updated_by;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

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
            $user->setCodeOi($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCodeOi() === $this) {
                $user->setCodeOi(null);
            }
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
            $publicationRapport->setCodeOi($this);
        }

        return $this;
    }

    public function removePublicationRapport(PublicationRapport $publicationRapport): static
    {
        if ($this->publicationRapports->removeElement($publicationRapport)) {
            // set the owning side to null (unless already changed)
            if ($publicationRapport->getCodeOi() === $this) {
                $publicationRapport->setCodeOi(null);
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
            $ticket->setCodeOi($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getCodeOi() === $this) {
                $ticket->setCodeOi(null);
            }
        }

        return $this;
    }
}
