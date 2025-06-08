<?php

namespace App\Entity\Transformation;

use App\Entity\References\Usine;
use App\Repository\Transformation\FicheLotRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'transformation.fiche_lot')]
#[ORM\Entity(repositoryClass: FicheLotRepository::class)]
class FicheLot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_lot = null;

    #[ORM\ManyToOne(inversedBy: 'ficheLots')]
    private ?Usine $code_usine = null;

    #[ORM\Column(nullable: true)]
    private ?bool $cloture = null;

    #[ORM\Column(nullable: true)]
    private ?bool $cloture_prod = null;

    #[ORM\OneToMany(mappedBy: 'code_lot', targetEntity: Billon::class)]
    private Collection $billons;

    #[ORM\Column(nullable: true)]
    private ?float $volume = null;

    #[ORM\OneToMany(mappedBy: 'code_fiche_lot', targetEntity: FicheLotProd::class)]
    private Collection $ficheLotProds;

    #[ORM\OneToMany(mappedBy: 'code_fiche_tronconnage', targetEntity: Elements::class)]
    private Collection $elements;

    public function __construct()
    {
        $this->billons = new ArrayCollection();
        $this->ficheLotProds = new ArrayCollection();
        $this->elements = new ArrayCollection();
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

    public function getDateLot(): ?\DateTimeInterface
    {
        return $this->date_lot;
    }

    public function setDateLot(\DateTimeInterface $date_lot): static
    {
        $this->date_lot = $date_lot;

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

    public function isCloture(): ?bool
    {
        return $this->cloture;
    }

    public function setCloture(?bool $cloture): static
    {
        $this->cloture = $cloture;

        return $this;
    }

    /**
     * @return Collection<int, Billon>
     */
    public function getBillons(): Collection
    {
        return $this->billons;
    }

    public function addBillon(Billon $billon): static
    {
        if (!$this->billons->contains($billon)) {
            $this->billons->add($billon);
            $billon->setCodeLot($this);
        }

        return $this;
    }

    public function removeBillon(Billon $billon): static
    {
        if ($this->billons->removeElement($billon)) {
            // set the owning side to null (unless already changed)
            if ($billon->getCodeLot() === $this) {
                $billon->setCodeLot(null);
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
     * @return bool|null
     */
    public function getClotureProd(): ?bool
    {
        return $this->cloture_prod;
    }

    /**
     * @param bool|null $cloture_prod
     */
    public function setClotureProd(?bool $cloture_prod): void
    {
        $this->cloture_prod = $cloture_prod;
    }



    /**
     * @return Collection<int, FicheLotProd>
     */
    public function getFicheLotProds(): Collection
    {
        return $this->ficheLotProds;
    }

    public function addFicheLotProd(FicheLotProd $ficheLotProd): static
    {
        if (!$this->ficheLotProds->contains($ficheLotProd)) {
            $this->ficheLotProds->add($ficheLotProd);
            $ficheLotProd->setCodeFicheLot($this);
        }

        return $this;
    }

    public function removeFicheLotProd(FicheLotProd $ficheLotProd): static
    {
        if ($this->ficheLotProds->removeElement($ficheLotProd)) {
            // set the owning side to null (unless already changed)
            if ($ficheLotProd->getCodeFicheLot() === $this) {
                $ficheLotProd->setCodeFicheLot(null);
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
            $element->setCodeFicheTronconnage($this);
        }

        return $this;
    }

    public function removeElement(Elements $element): static
    {
        if ($this->elements->removeElement($element)) {
            // set the owning side to null (unless already changed)
            if ($element->getCodeFicheTronconnage() === $this) {
                $element->setCodeFicheTronconnage(null);
            }
        }

        return $this;
    }
}
