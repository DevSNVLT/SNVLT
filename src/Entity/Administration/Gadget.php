<?php

namespace App\Entity\Administration;

use App\Entity\Groupe;
use App\Entity\References\TypeOperateur;
use App\Repository\Administration\GadgetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.gadget')]
#[ORM\Entity(repositoryClass: GadgetRepository::class)]
class Gadget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\ManyToMany(targetEntity: TypeOperateur::class, inversedBy: 'gadgets')]
    private Collection $code_operateur;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $reference = null;

    #[ORM\ManyToMany(targetEntity: Groupe::class, inversedBy: 'gadgets')]
    private Collection $code_groupe;

    public function __construct()
    {
        $this->code_operateur = new ArrayCollection();
        $this->code_groupe = new ArrayCollection();
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
     * @return Collection<int, TypeOperateur>
     */
    public function getCodeOperateur(): Collection
    {
        return $this->code_operateur;
    }

    public function addCodeOperateur(TypeOperateur $codeOperateur): static
    {
        if (!$this->code_operateur->contains($codeOperateur)) {
            $this->code_operateur->add($codeOperateur);
        }

        return $this;
    }

    public function removeCodeOperateur(TypeOperateur $codeOperateur): static
    {
        $this->code_operateur->removeElement($codeOperateur);

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

    /**
     * @return Collection<int, Groupe>
     */
    public function getCodeGroupe(): Collection
    {
        return $this->code_groupe;
    }

    public function addCodeGroupe(Groupe $codeGroupe): static
    {
        if (!$this->code_groupe->contains($codeGroupe)) {
            $this->code_groupe->add($codeGroupe);
        }

        return $this;
    }

    public function removeCodeGroupe(Groupe $codeGroupe): static
    {
        $this->code_groupe->removeElement($codeGroupe);

        return $this;
    }
}
