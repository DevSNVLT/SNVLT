<?php

namespace App\Entity\Transformation;

use App\Entity\References\Usine;
use App\Repository\Fiche2TransfoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'transformation.fiche_2_transfo')]
#[ORM\Entity(repositoryClass: Fiche2TransfoRepository::class)]
class Fiche2Transfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_transformation = null;

    #[ORM\ManyToOne(inversedBy: 'fiche2Transfos')]
    private ?Pdt2Transfo $produit = null;

    #[ORM\Column(nullable: true)]
    private ?int $qte = null;

    #[ORM\OneToMany(mappedBy: 'code_fiche2', targetEntity: Details2Transfo::class)]
    private Collection $details2Transfos;

    #[ORM\ManyToOne(inversedBy: 'fiche2Transfos')]
    private ?Usine $codeindustriel = null;

    public function __construct()
    {
        $this->details2Transfos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateTransformation(): ?\DateTimeInterface
    {
        return $this->date_transformation;
    }

    public function setDateTransformation(?\DateTimeInterface $date_transformation): static
    {
        $this->date_transformation = $date_transformation;

        return $this;
    }

    public function getProduit(): ?Pdt2Transfo
    {
        return $this->produit;
    }

    public function setProduit(?Pdt2Transfo $produit): static
    {
        $this->produit = $produit;

        return $this;
    }

    public function getQte(): ?int
    {
        return $this->qte;
    }

    public function setQte(?int $qte): static
    {
        $this->qte = $qte;

        return $this;
    }

    /**
     * @return Collection<int, Details2Transfo>
     */
    public function getDetails2Transfos(): Collection
    {
        return $this->details2Transfos;
    }

    public function addDetails2Transfo(Details2Transfo $details2Transfo): static
    {
        if (!$this->details2Transfos->contains($details2Transfo)) {
            $this->details2Transfos->add($details2Transfo);
            $details2Transfo->setCodeFiche2($this);
        }

        return $this;
    }

    public function removeDetails2Transfo(Details2Transfo $details2Transfo): static
    {
        if ($this->details2Transfos->removeElement($details2Transfo)) {
            // set the owning side to null (unless already changed)
            if ($details2Transfo->getCodeFiche2() === $this) {
                $details2Transfo->setCodeFiche2(null);
            }
        }

        return $this;
    }

    public function getCodeindustriel(): ?Usine
    {
        return $this->codeindustriel;
    }

    public function setCodeindustriel(?Usine $codeindustriel): static
    {
        $this->codeindustriel = $codeindustriel;

        return $this;
    }
}
