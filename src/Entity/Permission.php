<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.permission')]
#[ORM\Entity(repositoryClass: PermissionRepository::class)]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\ManyToOne(inversedBy: 'permissions')]
    private ?Menu $code_menu = null;

    #[ORM\ManyToOne(inversedBy: 'permissions')]
    private ?Groupe $code_groupe = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCodeMenu(): ?Menu
    {
        return $this->code_menu;
    }

    public function setCodeMenu(?Menu $code_menu): static
    {
        $this->code_menu = $code_menu;

        return $this;
    }

    public function getCodeGroupe(): ?Groupe
    {
        return $this->code_groupe;
    }

    public function setCodeGroupe(?Groupe $code_groupe): static
    {
        $this->code_groupe = $code_groupe;

        return $this;
    }

    public function __toString(): string
    {
        return $this->code_menu;
    }
}
