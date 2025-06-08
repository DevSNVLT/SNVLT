<?php

namespace App\Entity\References;

use App\Entity\Administration\StockDoc;
use App\Repository\References\ImprimeurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.imprimeur')]
#[ORM\Entity(repositoryClass: ImprimeurRepository::class)]
class Imprimeur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $raison_sociale_imprimeur = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $code_imprimeur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\OneToMany(mappedBy: 'code_imprimeur', targetEntity: StockDoc::class)]
    private Collection $stockDocs;

    public function __construct()
    {
        $this->stockDocs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaisonSocialeImprimeur(): ?string
    {
        return $this->raison_sociale_imprimeur;
    }

    public function setRaisonSocialeImprimeur(string $raison_sociale_imprimeur): static
    {
        $this->raison_sociale_imprimeur = $raison_sociale_imprimeur;

        return $this;
    }

    public function getCodeImprimeur(): ?string
    {
        return $this->code_imprimeur;
    }

    public function setCodeImprimeur(?string $code_imprimeur): static
    {
        $this->code_imprimeur = $code_imprimeur;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return Collection<int, StockDoc>
     */
    public function getStockDocs(): Collection
    {
        return $this->stockDocs;
    }

    public function addStockDoc(StockDoc $stockDoc): static
    {
        if (!$this->stockDocs->contains($stockDoc)) {
            $this->stockDocs->add($stockDoc);
            $stockDoc->setCodeImprimeur($this);
        }

        return $this;
    }

    public function removeStockDoc(StockDoc $stockDoc): static
    {
        if ($this->stockDocs->removeElement($stockDoc)) {
            // set the owning side to null (unless already changed)
            if ($stockDoc->getCodeImprimeur() === $this) {
                $stockDoc->setCodeImprimeur(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->raison_sociale_imprimeur;
    }
}
