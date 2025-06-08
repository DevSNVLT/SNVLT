<?php

namespace App\Entity;

use App\Entity\Admin\LogSnvlt;
use App\Entity\Administration\DemandeOperateur;
use App\Entity\Administration\Messagerie;
use App\Entity\Administration\Notification;
use App\Entity\Observateur\Ticket;
use App\Entity\References\Cantonnement;
use App\Entity\References\Caroi;
use App\Entity\References\Commercant;
use App\Entity\References\Ddef;
use App\Entity\References\Direction;
use App\Entity\References\DocumentOperateur;
use App\Entity\References\Dr;
use App\Entity\References\Exploitant;
use App\Entity\References\Exportateur;
use App\Entity\References\Oi;
use App\Entity\References\PosteForestier;
use App\Entity\References\ServiceMinef;
use App\Entity\References\Titre;
use App\Entity\References\TypeOperateur;
use App\Entity\References\Usine;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['mobile'], message: 'There is already a number with this mobile phone')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\Email(message: 'The email {{ value }} is not a valid email.')]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    /*#[Assert\Regex(pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{8,}$/', message: "Le mot de passe doit contenir au moins 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial")]*/
    private $isVerified = false;


    #[ORM\Column(length: 255)]
    private ?string $nom_utilisateur = null;

    #[ORM\Column(length: 255)]
    private ?string $prenoms_utilisateur = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Groupe $code_groupe = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(nullable: true)]
    private ?bool $statut = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    private ?Exploitant $codeexploitant = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    private ?ServiceMinef $code_service = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    private ?Direction $code_direction = null;

    #[ORM\Column(nullable: true)]
    private ?bool $agent_sodef = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    private ?Usine $codeindustriel = null;


    #[ORM\Column(nullable: true)]
    private ?bool $actif = null;

    #[ORM\Column(nullable: true)]
    private ?bool $nouveau = null;

    #[ORM\Column(length: 20, nullable: true, unique: true)]
   /* #[Assert\Positive(message: "Désolé ce numéro de téléphone n'est pas correct")]*/
    private ?string $mobile = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $profile_user = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    private ?Dr $code_dr = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    private ?Cantonnement $code_cantonnement = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    private ?TypeOperateur $code_operateur = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    private ?Ddef $code_ddef = null;

    #[ORM\ManyToOne(inversedBy: 'utilisateurs')]
    private ?PosteForestier $code_poste_controle = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(nullable: true)]
    private ?bool $isResponsable = null;

    #[ORM\OneToMany(mappedBy: 'to_user', targetEntity: Notification::class)]
    private Collection $notifications;

    #[ORM\OneToMany(mappedBy: 'demandeur', targetEntity: DemandeOperateur::class)]
    private Collection $demandeOperateurs;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Exportateur $code_exportateur = null;

    #[ORM\Column(length: 4, nullable: true)]
    private ?string $code_sms = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $locale = null;

    #[ORM\ManyToMany(targetEntity: Messagerie::class, mappedBy: 'from_user')]
    private Collection $messageries;

    #[ORM\OneToMany(mappedBy: 'code_utilisateur', targetEntity: Messagerie::class)]
    private Collection $mes_messages;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Titre $titre = null;

    #[ORM\OneToMany(mappedBy: 'demandeur_id', targetEntity: DocumentOperateur::class)]
    private Collection $documentOperateurs;

    #[ORM\OneToMany(mappedBy: 'created_by', targetEntity: LogSnvlt::class)]
    private Collection $logSnvlts;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Commercant $code_commercant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fonction = null;

    #[ORM\OneToMany(mappedBy: 'code_user', targetEntity: Ticket::class)]
    private Collection $tickets;

    #[ORM\ManyToOne(inversedBy: 'users')]
    private ?Oi $code_oi = null;

    #[ORM\OneToMany(mappedBy: 'code_user', targetEntity: Caroi::class)]
    private Collection $carois;

    #[ORM\ManyToMany(targetEntity: Ticket::class, mappedBy: 'code_users')]
    private Collection $tickets_users;

    public function __construct()
    {
        $this->notifications = new ArrayCollection();
        $this->demandeOperateurs = new ArrayCollection();
        $this->messageries = new ArrayCollection();
        $this->mes_messages = new ArrayCollection();
        $this->documentOperateurs = new ArrayCollection();
        $this->logSnvlts = new ArrayCollection();
        $this->tickets = new ArrayCollection();
        $this->carois = new ArrayCollection();
        $this->tickets_users = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): static
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getNomUtilisateur(): ?string
    {
        return $this->nom_utilisateur;
    }

    /**
     * @param string|null $nom_utilisateur
     */
    public function setNomUtilisateur(?string $nom_utilisateur): void
    {
        $this->nom_utilisateur = $nom_utilisateur;
    }

    /**
     * @return string|null
     */
    public function getPrenomsUtilisateur(): ?string
    {
        return $this->prenoms_utilisateur;
    }

    /**
     * @param string|null $prenoms_utilisateur
     */
    public function setPrenomsUtilisateur(?string $prenoms_utilisateur): void
    {
        $this->prenoms_utilisateur = $prenoms_utilisateur;
    }

    /**
     * @return Groupe|null
     */
    public function getCodeGroupe(): ?Groupe
    {
        return $this->code_groupe;
    }

    /**
     * @param Groupe|null $code_groupe
     */
    public function setCodeGroupe(?Groupe $code_groupe): void
    {
        $this->code_groupe = $code_groupe;
    }

    /**
     * @return string|null
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * @param string|null $photo
     */
    public function setPhoto(?string $photo): void
    {
        $this->photo = $photo;
    }

    /**
     * @return bool|null
     */
    public function getStatut(): ?bool
    {
        return $this->statut;
    }

    /**
     * @param bool|null $statut
     */
    public function setStatut(?bool $statut): void
    {
        $this->statut = $statut;
    }

    /**
     * @return Exploitant|null
     */
    public function getCodeexploitant(): ?Exploitant
    {
        return $this->codeexploitant;
    }

    /**
     * @param Exploitant|null $codeexploitant
     */
    public function setCodeexploitant(?Exploitant $codeexploitant): void
    {
        $this->codeexploitant = $codeexploitant;
    }

    /**
     * @return ServiceMinef|null
     */
    public function getCodeService(): ?ServiceMinef
    {
        return $this->code_service;
    }

    /**
     * @param ServiceMinef|null $code_service
     */
    public function setCodeService(?ServiceMinef $code_service): void
    {
        $this->code_service = $code_service;
    }

    /**
     * @return Direction|null
     */
    public function getCodeDirection(): ?Direction
    {
        return $this->code_direction;
    }

    /**
     * @param Direction|null $code_direction
     */
    public function setCodeDirection(?Direction $code_direction): void
    {
        $this->code_direction = $code_direction;
    }

    /**
     * @return bool|null
     */
    public function getAgentSodef(): ?bool
    {
        return $this->agent_sodef;
    }

    /**
     * @param bool|null $agent_sodef
     */
    public function setAgentSodef(?bool $agent_sodef): void
    {
        $this->agent_sodef = $agent_sodef;
    }

    /**
     * @return Usine|null
     */
    public function getCodeindustriel(): ?Usine
    {
        return $this->codeindustriel;
    }

    /**
     * @param Usine|null $codeindustriel
     */
    public function setCodeindustriel(?Usine $codeindustriel): void
    {
        $this->codeindustriel = $codeindustriel;
    }

    /**
     * @return bool|null
     */
    public function getActif(): ?bool
    {
        return $this->actif;
    }

    /**
     * @param bool|null $actif
     */
    public function setActif(?bool $actif): void
    {
        $this->actif = $actif;
    }

    /**
     * @return bool|null
     */
    public function getNouveau(): ?bool
    {
        return $this->nouveau;
    }

    /**
     * @param bool|null $nouveau
     */
    public function setNouveau(?bool $nouveau): void
    {
        $this->nouveau = $nouveau;
    }

    /**
     * @return string|null
     */
    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    /**
     * @param string|null $mobile
     */
    public function setMobile(?string $mobile): void
    {
        $this->mobile = $mobile;
    }

    /**
     * @return string|null
     */
    public function getProfileUser(): ?string
    {
        return $this->profile_user;
    }

    /**
     * @param string|null $profile_user
     */
    public function setProfileUser(?string $profile_user): void
    {
        $this->profile_user = $profile_user;
    }

    /**
     * @return Dr|null
     */
    public function getCodeDr(): ?Dr
    {
        return $this->code_dr;
    }

    /**
     * @param Dr|null $code_dr
     */
    public function setCodeDr(?Dr $code_dr): void
    {
        $this->code_dr = $code_dr;
    }

    /**
     * @return Cantonnement|null
     */
    public function getCodeCantonnement(): ?Cantonnement
    {
        return $this->code_cantonnement;
    }

    /**
     * @param Cantonnement|null $code_cantonnement
     */
    public function setCodeCantonnement(?Cantonnement $code_cantonnement): void
    {
        $this->code_cantonnement = $code_cantonnement;
    }

    /**
     * @return TypeOperateur|null
     */
    public function getCodeOperateur(): ?TypeOperateur
    {
        return $this->code_operateur;
    }

    /**
     * @param TypeOperateur|null $code_operateur
     */
    public function setCodeOperateur(?TypeOperateur $code_operateur): void
    {
        $this->code_operateur = $code_operateur;
    }

    /**
     * @return Ddef|null
     */
    public function getCodeDdef(): ?Ddef
    {
        return $this->code_ddef;
    }

    /**
     * @param Ddef|null $code_ddef
     */
    public function setCodeDdef(?Ddef $code_ddef): void
    {
        $this->code_ddef = $code_ddef;
    }

    /**
     * @return PosteForestier|null
     */
    public function getCodePosteControle(): ?PosteForestier
    {
        return $this->code_poste_controle;
    }

    /**
     * @param PosteForestier|null $code_poste_controle
     */
    public function setCodePosteControle(?PosteForestier $code_poste_controle): void
    {
        $this->code_poste_controle = $code_poste_controle;
    }

    public function __toString(): string
    {
        return $this->nom_utilisateur." ". $this->prenoms_utilisateur;
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

    public function isIsResponsable(): ?bool
    {
        return $this->isResponsable;
    }

    public function setIsResponsable(?bool $isResponsable): static
    {
        $this->isResponsable = $isResponsable;

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): static
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setToUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): static
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getToUser() === $this) {
                $notification->setToUser(null);
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
            $demandeOperateur->setDemandeur($this);
        }

        return $this;
    }

    public function removeDemandeOperateur(DemandeOperateur $demandeOperateur): static
    {
        if ($this->demandeOperateurs->removeElement($demandeOperateur)) {
            // set the owning side to null (unless already changed)
            if ($demandeOperateur->getDemandeur() === $this) {
                $demandeOperateur->setDemandeur(null);
            }
        }

        return $this;
    }

    public function getCodeExportateur(): ?Exportateur
    {
        return $this->code_exportateur;
    }

    public function setCodeExportateur(?Exportateur $code_exportateur): static
    {
        $this->code_exportateur = $code_exportateur;

        return $this;
    }

    public function getCodeSms(): ?string
    {
        return $this->code_sms;
    }

    public function setCodeSms(?string $code_sms): static
    {
        $this->code_sms = $code_sms;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): static
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * @return Collection<int, Messagerie>
     */
    public function getMessageries(): Collection
    {
        return $this->messageries;
    }

    public function addMessagery(Messagerie $messagery): static
    {
        if (!$this->messageries->contains($messagery)) {
            $this->messageries->add($messagery);
            $messagery->addFromUser($this);
        }

        return $this;
    }

    public function removeMessagery(Messagerie $messagery): static
    {
        if ($this->messageries->removeElement($messagery)) {
            $messagery->removeFromUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Messagerie>
     */
    public function getCodeUtilisateur(): Collection
    {
        return $this->mes_messages;
    }

    public function addCodeUtilisateur(Messagerie $mes_messages): static
    {
        if (!$this->mes_messages->contains($mes_messages)) {
            $this->mes_messages->add($mes_messages);
            $mes_messages->setCodeUtilisateur($this);
        }

        return $this;
    }

    public function removeCodeUtilisateur(Messagerie $mes_messages): static
    {
        if ($this->mes_messages->removeElement($mes_messages)) {
            // set the owning side to null (unless already changed)
            if ($mes_messages->getCodeUtilisateur() === $this) {
                $mes_messages->setCodeUtilisateur(null);
            }
        }

        return $this;
    }

    public function getTitre(): ?Titre
    {
        return $this->titre;
    }

    public function setTitre(?Titre $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * @return Collection<int, DocumentOperateur>
     */
    public function getDocumentOperateurs(): Collection
    {
        return $this->documentOperateurs;
    }

    public function addDocumentOperateur(DocumentOperateur $documentOperateur): static
    {
        if (!$this->documentOperateurs->contains($documentOperateur)) {
            $this->documentOperateurs->add($documentOperateur);
            $documentOperateur->setDemandeurId($this);
        }

        return $this;
    }

    public function removeDocumentOperateur(DocumentOperateur $documentOperateur): static
    {
        if ($this->documentOperateurs->removeElement($documentOperateur)) {
            // set the owning side to null (unless already changed)
            if ($documentOperateur->getDemandeurId() === $this) {
                $documentOperateur->setDemandeurId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, LogSnvlt>
     */
    public function getLogSnvlts(): Collection
    {
        return $this->logSnvlts;
    }

    public function addLogSnvlt(LogSnvlt $logSnvlt): static
    {
        if (!$this->logSnvlts->contains($logSnvlt)) {
            $this->logSnvlts->add($logSnvlt);
            $logSnvlt->setCreatedBy($this);
        }

        return $this;
    }

    public function removeLogSnvlt(LogSnvlt $logSnvlt): static
    {
        if ($this->logSnvlts->removeElement($logSnvlt)) {
            // set the owning side to null (unless already changed)
            if ($logSnvlt->getCreatedBy() === $this) {
                $logSnvlt->setCreatedBy(null);
            }
        }

        return $this;
    }

    public function getCodeCommercant(): ?Commercant
    {
        return $this->code_commercant;
    }

    public function setCodeCommercant(?Commercant $code_commercant): static
    {
        $this->code_commercant = $code_commercant;

        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(?string $fonction): static
    {
        $this->fonction = $fonction;

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
            $ticket->setCodeUser($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): static
    {
        if ($this->tickets->removeElement($ticket)) {
            // set the owning side to null (unless already changed)
            if ($ticket->getCodeUser() === $this) {
                $ticket->setCodeUser(null);
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

    /**
     * @return Collection<int, Caroi>
     */
    public function getCarois(): Collection
    {
        return $this->carois;
    }

    public function addCaroi(Caroi $caroi): static
    {
        if (!$this->carois->contains($caroi)) {
            $this->carois->add($caroi);
            $caroi->setCodeUser($this);
        }

        return $this;
    }

    public function removeCaroi(Caroi $caroi): static
    {
        if ($this->carois->removeElement($caroi)) {
            // set the owning side to null (unless already changed)
            if ($caroi->getCodeUser() === $this) {
                $caroi->setCodeUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTicketsUsers(): Collection
    {
        return $this->tickets_users;
    }

    public function addTicketsUser(Ticket $ticketsUser): static
    {
        if (!$this->tickets_users->contains($ticketsUser)) {
            $this->tickets_users->add($ticketsUser);
            $ticketsUser->addCodeUser($this);
        }

        return $this;
    }

    public function removeTicketsUser(Ticket $ticketsUser): static
    {
        if ($this->tickets_users->removeElement($ticketsUser)) {
            $ticketsUser->removeCodeUser($this);
        }

        return $this;
    }

}
