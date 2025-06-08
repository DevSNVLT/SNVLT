<?php

namespace App\Entity;

use App\Repository\MenuPermissionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.menu_permission')]
#[ORM\Entity(repositoryClass: MenuPermissionRepository::class)]
class MenuPermission
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: false)]
    private ?string $nom_Menu = null;

    #[ORM\Column(length: 100, nullable: false)]
    private ?string $icon_menu = null;

    #[ORM\Column(nullable: false)]
    private ?int $parent_menu= null;

    #[ORM\Column(length: 255, nullable: false)]
    private ?string $classname_menu = null;

    #[ORM\Column(nullable: false)]
    private ?int $code_groupe_id= null;

    #[ORM\Column]
    private ?int $id_permission = null;

    /**
     * @return int|null
     */
    public function getIdMenu(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id_Menu
     */
    public function setIdMenu(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getNomMenu(): ?string
    {
        return $this->nom_Menu;
    }

    /**
     * @param string|null $nom_Menu
     */
    public function setNomMenu(?string $nom_Menu): void
    {
        $this->nom_Menu = $nom_Menu;
    }

    /**
     * @return string|null
     */
    public function getIconMenu(): ?string
    {
        return $this->icon_menu;
    }

    /**
     * @param string|null $icon_menu
     */
    public function setIconMenu(?string $icon_menu): void
    {
        $this->icon_menu = $icon_menu;
    }

    /**
     * @return int|null
     */
    public function getParentMenu(): ?int
    {
        return $this->parent_menu;
    }

    /**
     * @param int|null $parent_menu
     */
    public function setParentMenu(?int $parent_menu): void
    {
        $this->parent_menu = $parent_menu;
    }

    /**
     * @return string|null
     */
    public function getClassnameMenu(): ?string
    {
        return $this->classname_menu;
    }

    /**
     * @param string|null $classname_menu
     */
    public function setClassnameMenu(?string $classname_menu): void
    {
        $this->classname_menu = $classname_menu;
    }

    /**
     * @return int|null
     */
    public function getCodeGroupe(): ?int
    {
        return $this->code_groupe_id;
    }

    /**
     * @param int|null $code_groupe_id
     */
    public function setCodeGroupe(?int $code_groupe_id): void
    {
        $this->code_groupe_id = $code_groupe_id;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getCodeGroupeId(): ?int
    {
        return $this->code_groupe_id;
    }

    /**
     * @param int|null $code_groupe_id
     */
    public function setCodeGroupeId(?int $code_groupe_id): void
    {
        $this->code_groupe_id = $code_groupe_id;
    }

    /**
     * @return int|null
     */
    public function getIdPermission(): ?int
    {
        return $this->id_permission;
    }

    /**
     * @param int|null $id_permission
     */
    public function setIdPermission(?int $id_permission): void
    {
        $this->id_permission = $id_permission;
    }



    public function __toString(): string
    {
        return $this->nom_Menu;
    }


}
