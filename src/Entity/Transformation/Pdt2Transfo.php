<?php

namespace App\Entity\Transformation;

use App\Entity\References\TypeTransformation;
use App\Repository\Transformation\Pdt2TransfoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'transformation.pdt_2_transfo')]
#[ORM\Entity(repositoryClass: Pdt2TransfoRepository::class)]
class Pdt2Transfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom_pdt = null;

    #[ORM\ManyToOne(inversedBy: 'pdt2Transfos')]
    private ?TypeTransformation $type_produit = null;

    #[ORM\OneToMany(mappedBy: 'produit', targetEntity: Fiche2Transfo::class)]
    private Collection $fiche2Transfos;

    public function __construct()
    {
        $this->fiche2Transfos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPdt(): ?string
    {
        return $this->nom_pdt;
    }

    public function setNomPdt(?string $nom_pdt): static
    {
        $this->nom_pdt = $nom_pdt;

        return $this;
    }

    public function getTypeProduit(): ?TypeTransformation
    {
        return $this->type_produit;
    }

    public function setTypeProduit(?TypeTransformation $type_produit): static
    {
        $this->type_produit = $type_produit;

        return $this;
    }

    /**
     * @return Collection<int, Fiche2Transfo>
     */
    public function getFiche2Transfos(): Collection
    {
        return $this->fiche2Transfos;
    }

    public function addFiche2Transfo(Fiche2Transfo $fiche2Transfo): static
    {
        if (!$this->fiche2Transfos->contains($fiche2Transfo)) {
            $this->fiche2Transfos->add($fiche2Transfo);
            $fiche2Transfo->setProduit($this);
        }

        return $this;
    }

    public function removeFiche2Transfo(Fiche2Transfo $fiche2Transfo): static
    {
        if ($this->fiche2Transfos->removeElement($fiche2Transfo)) {
            // set the owning side to null (unless already changed)
            if ($fiche2Transfo->getProduit() === $this) {
                $fiche2Transfo->setProduit(null);
            }
        }

        return $this;
    }
}
