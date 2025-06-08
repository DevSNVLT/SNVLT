<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.menu')]
#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nom_menu = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $icon_menu = null;

    #[ORM\Column(nullable: true)]
    private ?int $parent_menu = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $classname_menu = null;

    #[ORM\OneToMany(mappedBy: 'code_menu', targetEntity: Permission::class)]
    private Collection $permissions;

    public function __construct()
    {
        $this->permissions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomMenu(): ?string
    {
        return $this->nom_menu;
    }

    public function setNomMenu(string $nom_menu): static
    {
        $this->nom_menu = $nom_menu;

        return $this;
    }

    public function getIconMenu(): ?string
    {
        return $this->icon_menu;
    }

    public function setIconMenu(?string $icon_menu): static
    {
        $this->icon_menu = $icon_menu;

        return $this;
    }

    public function getParentMenu(): ?int
    {
        return $this->parent_menu;
    }

    public function setParentMenu(?int $parent_menu): static
    {
        $this->parent_menu = $parent_menu;

        return $this;
    }

    public function getClassnameMenu(): ?string
    {
        return $this->classname_menu;
    }

    public function setClassnameMenu(string $classname_menu): static
    {
        $this->classname_menu = $classname_menu;

        return $this;
    }

    /**
     * @return Collection<int, Permission>
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermission(Permission $permission): static
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->add($permission);
            $permission->setCodeMenu($this);
        }

        return $this;
    }

    public function removePermission(Permission $permission): static
    {
        if ($this->permissions->removeElement($permission)) {
            // set the owning side to null (unless already changed)
            if ($permission->getCodeMenu() === $this) {
                $permission->setCodeMenu(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
       return $this->nom_menu;
    }
}
