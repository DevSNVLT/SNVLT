<?php

namespace App\Entity\References;

use App\Repository\TypeAutorisationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.type_autorisation')]
#[ORM\Entity(repositoryClass: TypeAutorisationRepository::class)]
class TypeAutorisation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $libelle = null;

    #[ORM\ManyToOne(inversedBy: 'typeAutorisations')]
    private ?TypeOperateur $type_operateur = null;

    #[ORM\ManyToMany(targetEntity: GrilleLegalite::class, inversedBy: 'typeAutorisations')]
    private Collection $code_doc_grille;

    public function __construct()
    {
        $this->code_doc_grille = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getTypeOperateur(): ?TypeOperateur
    {
        return $this->type_operateur;
    }

    public function setTypeOperateur(?TypeOperateur $type_operateur): static
    {
        $this->type_operateur = $type_operateur;

        return $this;
    }

    /**
     * @return Collection<int, GrilleLegalite>
     */
    public function getCodeDocGrille(): Collection
    {
        return $this->code_doc_grille;
    }

    public function addCodeDocGrille(GrilleLegalite $codeDocGrille): static
    {
        if (!$this->code_doc_grille->contains($codeDocGrille)) {
            $this->code_doc_grille->add($codeDocGrille);
        }

        return $this;
    }

    public function removeCodeDocGrille(GrilleLegalite $codeDocGrille): static
    {
        $this->code_doc_grille->removeElement($codeDocGrille);

        return $this;
    }
}
