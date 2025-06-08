<?php

namespace App\Entity\Observateur;

use App\Entity\Groupe;
use App\Entity\References\Cantonnement;
use App\Entity\References\Direction;
use App\Entity\References\Dr;
use App\Entity\References\Oi;
use App\Entity\References\ServiceMinef;
use App\Entity\User;
use App\Repository\Observateur\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'observateur.ticket')]
#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $sujet = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $message_text = null;

    #[ORM\ManyToMany(targetEntity: Groupe::class, inversedBy: 'tickets')]
    private Collection $destinataires;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?User $code_user = null;

    #[ORM\OneToMany(mappedBy: 'code_ticket', targetEntity: Fichiers::class)]
    private Collection $fichiers;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fichier = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?Cantonnement $code_cantonnement = null;

    #[ORM\ManyToMany(targetEntity: Dr::class, inversedBy: 'tickets')]
    private Collection $code_dr;

    #[ORM\ManyToMany(targetEntity: Cantonnement::class, inversedBy: 'tickets_cef')]
    private Collection $code_cef;

    #[ORM\ManyToMany(targetEntity: Direction::class, inversedBy: 'tickets')]
    private Collection $code_direction;

    #[ORM\ManyToMany(targetEntity: ServiceMinef::class, inversedBy: 'tickets')]
    private Collection $code_service;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'tickets_users')]
    private Collection $code_users;

    #[ORM\OneToMany(mappedBy: 'code_ticket', targetEntity: TicketFiles::class)]
    private Collection $ticketFiles;

    #[ORM\ManyToOne(inversedBy: 'tickets')]
    private ?Oi $code_oi = null;

    #[ORM\Column(nullable: true)]
    private ?int $x = null;

    #[ORM\Column(nullable: true)]
    private ?int $y = null;

    public function __construct()
    {
        $this->destinataires = new ArrayCollection();
        $this->fichiers = new ArrayCollection();
        $this->code_dr = new ArrayCollection();
        $this->code_cef = new ArrayCollection();
        $this->code_direction = new ArrayCollection();
        $this->code_service = new ArrayCollection();
        $this->code_users = new ArrayCollection();
        $this->ticketFiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): static
    {
        $this->sujet = $sujet;

        return $this;
    }

    public function getMessageText(): ?string
    {
        return $this->message_text;
    }

    public function setMessageText(?string $message_text): static
    {
        $this->message_text = $message_text;

        return $this;
    }

    /**
     * @return Collection<int, Groupe>
     */
    public function getDestinataires(): Collection
    {
        return $this->destinataires;
    }

    public function addDestinataire(Groupe $destinataire): static
    {
        if (!$this->destinataires->contains($destinataire)) {
            $this->destinataires->add($destinataire);
        }

        return $this;
    }

    public function removeDestinataire(Groupe $destinataire): static
    {
        $this->destinataires->removeElement($destinataire);

        return $this;
    }

    public function getCodeUser(): ?User
    {
        return $this->code_user;
    }

    public function setCodeUser(?User $code_user): static
    {
        $this->code_user = $code_user;

        return $this;
    }

    /**
     * @return Collection<int, Fichiers>
     */
    public function getFichiers(): Collection
    {
        return $this->fichiers;
    }

    public function addFichier(Fichiers $fichier): static
    {
        if (!$this->fichiers->contains($fichier)) {
            $this->fichiers->add($fichier);
            $fichier->setCodeTicket($this);
        }

        return $this;
    }

    public function removeFichier(Fichiers $fichier): static
    {
        if ($this->fichiers->removeElement($fichier)) {
            // set the owning side to null (unless already changed)
            if ($fichier->getCodeTicket() === $this) {
                $fichier->setCodeTicket(null);
            }
        }

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(?string $fichier): static
    {
        $this->fichier = $fichier;

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

    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    public function setUpdatedBy(string $updated_by): static
    {
        $this->updated_by = $updated_by;

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

    public function getCodeCantonnement(): ?Cantonnement
    {
        return $this->code_cantonnement;
    }

    public function setCodeCantonnement(?Cantonnement $code_cantonnement): static
    {
        $this->code_cantonnement = $code_cantonnement;

        return $this;
    }

    /**
     * @return Collection<int, Dr>
     */
    public function getCodeDr(): Collection
    {
        return $this->code_dr;
    }

    public function addCodeDr(Dr $codeDr): static
    {
        if (!$this->code_dr->contains($codeDr)) {
            $this->code_dr->add($codeDr);
        }

        return $this;
    }

    public function removeCodeDr(Dr $codeDr): static
    {
        $this->code_dr->removeElement($codeDr);

        return $this;
    }

    /**
     * @return Collection<int, Cantonnement>
     */
    public function getCodeCef(): Collection
    {
        return $this->code_cef;
    }

    public function addCodeCef(Cantonnement $codeCef): static
    {
        if (!$this->code_cef->contains($codeCef)) {
            $this->code_cef->add($codeCef);
        }

        return $this;
    }

    public function removeCodeCef(Cantonnement $codeCef): static
    {
        $this->code_cef->removeElement($codeCef);

        return $this;
    }

    /**
     * @return Collection<int, Direction>
     */
    public function getCodeDirection(): Collection
    {
        return $this->code_direction;
    }

    public function addCodeDirection(Direction $codeDirection): static
    {
        if (!$this->code_direction->contains($codeDirection)) {
            $this->code_direction->add($codeDirection);
        }

        return $this;
    }

    public function removeCodeDirection(Direction $codeDirection): static
    {
        $this->code_direction->removeElement($codeDirection);

        return $this;
    }

    /**
     * @return Collection<int, ServiceMinef>
     */
    public function getCodeService(): Collection
    {
        return $this->code_service;
    }

    public function addCodeService(ServiceMinef $codeService): static
    {
        if (!$this->code_service->contains($codeService)) {
            $this->code_service->add($codeService);
        }

        return $this;
    }

    public function removeCodeService(ServiceMinef $codeService): static
    {
        $this->code_service->removeElement($codeService);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getCodeUsers(): Collection
    {
        return $this->code_users;
    }

    public function addCodeUser(User $codeUser): static
    {
        if (!$this->code_users->contains($codeUser)) {
            $this->code_users->add($codeUser);
        }

        return $this;
    }

    public function removeCodeUser(User $codeUser): static
    {
        $this->code_users->removeElement($codeUser);

        return $this;
    }

    /**
     * @return Collection<int, TicketFiles>
     */
    public function getTicketFiles(): Collection
    {
        return $this->ticketFiles;
    }

    public function addTicketFile(TicketFiles $ticketFile): static
    {
        if (!$this->ticketFiles->contains($ticketFile)) {
            $this->ticketFiles->add($ticketFile);
            $ticketFile->setCodeTicket($this);
        }

        return $this;
    }

    public function removeTicketFile(TicketFiles $ticketFile): static
    {
        if ($this->ticketFiles->removeElement($ticketFile)) {
            // set the owning side to null (unless already changed)
            if ($ticketFile->getCodeTicket() === $this) {
                $ticketFile->setCodeTicket(null);
            }
        }

        return $this;
    }

    public function getCodeOi(): ?Oi
    {
        return $this->code_oi;
    }

    public function setCodeOi(?Oi $code_oi): static
    {
        $this->code_oi = $code_oi;

        return $this;
    }

    public function getX(): ?int
    {
        return $this->x;
    }

    public function setX(?int $x): static
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?int
    {
        return $this->y;
    }

    public function setY(?int $y): static
    {
        $this->y = $y;

        return $this;
    }
}
