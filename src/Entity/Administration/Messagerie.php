<?php

namespace App\Entity\Administration;

use App\Entity\User;
use App\Repository\Administration\MessagerieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.messagerie')]
#[ORM\Entity(repositoryClass: MessagerieRepository::class)]
class Messagerie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $sujet = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'messageries')]
    private Collection $from_user;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $text_message = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(nullable: true)]
    private ?bool $lu = null;

    #[ORM\OneToMany(mappedBy: 'code_email', targetEntity: PieceJointe::class)]
    private Collection $pieceJointes;

    #[ORM\ManyToOne(inversedBy: 'code_utilisateur')]
    private ?User $code_utilisateur = null;

    public function __construct()
    {
        $this->from_user = new ArrayCollection();
        $this->pieceJointes = new ArrayCollection();
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

    /**
     * @return Collection<int, User>
     */
    public function getFromUser(): Collection
    {
        return $this->from_user;
    }

    public function addFromUser(User $fromUser): static
    {
        if (!$this->from_user->contains($fromUser)) {
            $this->from_user->add($fromUser);
        }

        return $this;
    }

    public function removeFromUser(User $fromUser): static
    {
        $this->from_user->removeElement($fromUser);

        return $this;
    }

    public function getTextMessage(): ?string
    {
        return $this->text_message;
    }

    public function setTextMessage(?string $text_message): static
    {
        $this->text_message = $text_message;

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

    public function isLu(): ?bool
    {
        return $this->lu;
    }

    public function setLu(?bool $lu): static
    {
        $this->lu = $lu;

        return $this;
    }

    /**
     * @return Collection<int, PieceJointe>
     */
    public function getPieceJointes(): Collection
    {
        return $this->pieceJointes;
    }

    public function addPieceJointe(PieceJointe $pieceJointe): static
    {
        if (!$this->pieceJointes->contains($pieceJointe)) {
            $this->pieceJointes->add($pieceJointe);
            $pieceJointe->setCodeEmail($this);
        }

        return $this;
    }

    public function removePieceJointe(PieceJointe $pieceJointe): static
    {
        if ($this->pieceJointes->removeElement($pieceJointe)) {
            // set the owning side to null (unless already changed)
            if ($pieceJointe->getCodeEmail() === $this) {
                $pieceJointe->setCodeEmail(null);
            }
        }

        return $this;
    }

    public function getCodeUtilisateur(): ?User
    {
        return $this->code_utilisateur;
    }

    public function setCodeUtilisateur(?User $code_utilisateur): static
    {
        $this->code_utilisateur = $code_utilisateur;

        return $this;
    }
}
