<?php

namespace App\Entity\Administration;

use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.notification')]
#[ORM\Entity(repositoryClass: NotificationRepository::class)]
class Notification
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $sujet = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $from_user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\ManyToOne(inversedBy: 'notifications')]
    private ?User $to_user = null;

    #[ORM\Column(nullable: true)]
    private ?bool $lu = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $reference = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $related_to_entity = null;

    #[ORM\Column(nullable: true)]
    private ?int $related_to_id = null;

    #[ORM\Column(nullable: true)]
    private ?bool $cloture = null;


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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }


    public function getFromUser(): ?string
    {
        return $this->from_user;
    }

    public function setFromUser(?string $from_user): static
    {
        $this->from_user = $from_user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): static
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

    public function getToUser(): ?User
    {
        return $this->to_user;
    }

    public function setToUser(?User $to_user): static
    {
        $this->to_user = $to_user;

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

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(?string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getRelatedToEntity(): ?string
    {
        return $this->related_to_entity;
    }

    public function setRelatedToEntity(?string $related_to_entity): static
    {
        $this->related_to_entity = $related_to_entity;

        return $this;
    }

    public function getRelatedToId(): ?int
    {
        return $this->related_to_id;
    }

    public function setRelatedToId(?int $related_to_id): static
    {
        $this->related_to_id = $related_to_id;

        return $this;
    }

    public function isCloture(): ?bool
    {
        return $this->cloture;
    }

    public function setCloture(?bool $cloture): static
    {
        $this->cloture = $cloture;

        return $this;
    }
}
