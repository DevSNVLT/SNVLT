<?php

namespace App\Entity\Transformation;

use App\Entity\References\Usine;
use App\Repository\Transformation\FicheLotProdRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'transformation.fiche_lot_prod')]
#[ORM\Entity(repositoryClass: FicheLotProdRepository::class)]
class FicheLotProd
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_fiche = null;

    #[ORM\ManyToOne(inversedBy: 'ficheLotProds')]
    private ?FicheLot $code_fiche_lot = null;

    #[ORM\ManyToOne(inversedBy: 'ficheLotProds')]
    private ?Usine $code_usine = null;

    #[ORM\OneToMany(mappedBy: 'code_fiche', targetEntity: EltsProd::class)]
    private Collection $eltsProds;

    #[ORM\OneToMany(mappedBy: 'code_fiche_prod', targetEntity: Elements::class)]
    private Collection $elements;

    #[ORM\Column(nullable: true)]
    private ?float $volume = null;

    #[ORM\OneToMany(mappedBy: 'code_fiche_lot_prod', targetEntity: ElementsColis::class)]
    private Collection $elementsColis;

    public function __construct()
    {
        $this->eltsProds = new ArrayCollection();
        $this->elements = new ArrayCollection();
        $this->elementsColis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function setNumero(string $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getDateFiche(): ?\DateTimeInterface
    {
        return $this->date_fiche;
    }

    public function setDateFiche(\DateTimeInterface $date_fiche): static
    {
        $this->date_fiche = $date_fiche;

        return $this;
    }

    public function getCodeFicheLot(): ?FicheLot
    {
        return $this->code_fiche_lot;
    }

    public function setCodeFicheLot(?FicheLot $code_fiche_lot): static
    {
        $this->code_fiche_lot = $code_fiche_lot;

        return $this;
    }

    public function getCodeUsine(): ?Usine
    {
        return $this->code_usine;
    }

    public function setCodeUsine(?Usine $code_usine): static
    {
        $this->code_usine = $code_usine;

        return $this;
    }

    /**
     * @return Collection<int, EltsProd>
     */
    public function getEltsProds(): Collection
    {
        return $this->eltsProds;
    }

    public function addEltsProd(EltsProd $eltsProd): static
    {
        if (!$this->eltsProds->contains($eltsProd)) {
            $this->eltsProds->add($eltsProd);
            $eltsProd->setCodeFiche($this);
        }

        return $this;
    }

    public function removeEltsProd(EltsProd $eltsProd): static
    {
        if ($this->eltsProds->removeElement($eltsProd)) {
            // set the owning side to null (unless already changed)
            if ($eltsProd->getCodeFiche() === $this) {
                $eltsProd->setCodeFiche(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Elements>
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(Elements $element): static
    {
        if (!$this->elements->contains($element)) {
            $this->elements->add($element);
            $element->setCodeFicheProd($this);
        }

        return $this;
    }

    public function removeElement(Elements $element): static
    {
        if ($this->elements->removeElement($element)) {
            // set the owning side to null (unless already changed)
            if ($element->getCodeFicheProd() === $this) {
                $element->setCodeFicheProd(null);
            }
        }

        return $this;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function setVolume(?float $volume): static
    {
        $this->volume = $volume;

        return $this;
    }

    /**
     * @return Collection<int, ElementsColis>
     */
    public function getElementsColis(): Collection
    {
        return $this->elementsColis;
    }

    public function addElementsColi(ElementsColis $elementsColi): static
    {
        if (!$this->elementsColis->contains($elementsColi)) {
            $this->elementsColis->add($elementsColi);
            $elementsColi->setCodeFicheLotProd($this);
        }

        return $this;
    }

    public function removeElementsColi(ElementsColis $elementsColi): static
    {
        if ($this->elementsColis->removeElement($elementsColi)) {
            // set the owning side to null (unless already changed)
            if ($elementsColi->getCodeFicheLotProd() === $this) {
                $elementsColi->setCodeFicheLotProd(null);
            }
        }

        return $this;
    }
}
