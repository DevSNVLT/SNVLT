<?php

namespace App\Entity\Requetes;

use App\Repository\Requetes\TypeRequetesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.type_requetes')]
#[ORM\Entity(repositoryClass: TypeRequetesRepository::class)]
class TypeRequetes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'code_type_requetes', targetEntity: MenuRequetes::class)]
    private Collection $menuRequetes;

    public function __construct()
    {
        $this->menuRequetes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, MenuRequetes>
     */
    public function getMenuRequetes(): Collection
    {
        return $this->menuRequetes;
    }

    public function addMenuRequete(MenuRequetes $menuRequete): static
    {
        if (!$this->menuRequetes->contains($menuRequete)) {
            $this->menuRequetes->add($menuRequete);
            $menuRequete->setCodeTypeRequetes($this);
        }

        return $this;
    }

    public function removeMenuRequete(MenuRequetes $menuRequete): static
    {
        if ($this->menuRequetes->removeElement($menuRequete)) {
            // set the owning side to null (unless already changed)
            if ($menuRequete->getCodeTypeRequetes() === $this) {
                $menuRequete->setCodeTypeRequetes(null);
            }
        }

        return $this;
    }
}
