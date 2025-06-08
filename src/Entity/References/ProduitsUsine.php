<?php

namespace App\Entity\References;

use App\Entity\DocStats\Saisie\Lignepagebcburb;
use App\Entity\DocStats\Saisie\Lignepagebrepf;
use App\Entity\DocStats\Saisie\Lignepagedmp;
use App\Entity\DocStats\Saisie\Lignepageetath;
use App\Entity\DocStats\Saisie\Lignepagersdpf;
use App\Repository\References\ProduitsUsineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.produits_usine')]
#[ORM\Entity(repositoryClass: ProduitsUsineRepository::class)]
class ProduitsUsine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_produit = null;

    #[ORM\OneToMany(mappedBy: 'nature_produit', targetEntity: Lignepageetath::class)]
    private Collection $lignepageetaths;

    #[ORM\OneToMany(mappedBy: 'nature_matiere', targetEntity: Lignepagedmp::class)]
    private Collection $lignepagedmps;

    #[ORM\OneToMany(mappedBy: 'designation', targetEntity: Lignepagebcburb::class)]
    private Collection $lignepagebcburbs;

    #[ORM\OneToMany(mappedBy: 'nature_pdt', targetEntity: Lignepagebrepf::class)]
    private Collection $lignepagebrepfs;

    #[ORM\OneToMany(mappedBy: 'nature_produit', targetEntity: Lignepagersdpf::class)]
    private Collection $lignepagersdpfs;

    public function __construct()
    {
        $this->lignepageetaths = new ArrayCollection();
        $this->lignepagedmps = new ArrayCollection();
        $this->lignepagebcburbs = new ArrayCollection();
        $this->lignepagebrepfs = new ArrayCollection();
        $this->lignepagersdpfs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomProduit(): ?string
    {
        return $this->nom_produit;
    }

    public function setNomProduit(string $nom_produit): static
    {
        $this->nom_produit = $nom_produit;

        return $this;
    }

    /**
     * @return Collection<int, Lignepageetath>
     */
    public function getLignepageetaths(): Collection
    {
        return $this->lignepageetaths;
    }

    public function addLignepageetath(Lignepageetath $lignepageetath): static
    {
        if (!$this->lignepageetaths->contains($lignepageetath)) {
            $this->lignepageetaths->add($lignepageetath);
            $lignepageetath->setNatureProduit($this);
        }

        return $this;
    }

    public function removeLignepageetath(Lignepageetath $lignepageetath): static
    {
        if ($this->lignepageetaths->removeElement($lignepageetath)) {
            // set the owning side to null (unless already changed)
            if ($lignepageetath->getNatureProduit() === $this) {
                $lignepageetath->setNatureProduit(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lignepagedmp>
     */
    public function getLignepagedmps(): Collection
    {
        return $this->lignepagedmps;
    }

    public function addLignepagedmp(Lignepagedmp $lignepagedmp): static
    {
        if (!$this->lignepagedmps->contains($lignepagedmp)) {
            $this->lignepagedmps->add($lignepagedmp);
            $lignepagedmp->setNatureMatiere($this);
        }

        return $this;
    }

    public function removeLignepagedmp(Lignepagedmp $lignepagedmp): static
    {
        if ($this->lignepagedmps->removeElement($lignepagedmp)) {
            // set the owning side to null (unless already changed)
            if ($lignepagedmp->getNatureMatiere() === $this) {
                $lignepagedmp->setNatureMatiere(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lignepagebcburb>
     */
    public function getLignepagebcburbs(): Collection
    {
        return $this->lignepagebcburbs;
    }

    public function addLignepagebcburb(Lignepagebcburb $lignepagebcburb): static
    {
        if (!$this->lignepagebcburbs->contains($lignepagebcburb)) {
            $this->lignepagebcburbs->add($lignepagebcburb);
            $lignepagebcburb->setDesignation($this);
        }

        return $this;
    }

    public function removeLignepagebcburb(Lignepagebcburb $lignepagebcburb): static
    {
        if ($this->lignepagebcburbs->removeElement($lignepagebcburb)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebcburb->getDesignation() === $this) {
                $lignepagebcburb->setDesignation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lignepagebrepf>
     */
    public function getLignepagebrepfs(): Collection
    {
        return $this->lignepagebrepfs;
    }

    public function addLignepagebrepf(Lignepagebrepf $lignepagebrepf): static
    {
        if (!$this->lignepagebrepfs->contains($lignepagebrepf)) {
            $this->lignepagebrepfs->add($lignepagebrepf);
            $lignepagebrepf->setNaturePdt($this);
        }

        return $this;
    }

    public function removeLignepagebrepf(Lignepagebrepf $lignepagebrepf): static
    {
        if ($this->lignepagebrepfs->removeElement($lignepagebrepf)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebrepf->getNaturePdt() === $this) {
                $lignepagebrepf->setNaturePdt(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lignepagersdpf>
     */
    public function getLignepagersdpfs(): Collection
    {
        return $this->lignepagersdpfs;
    }

    public function addLignepagersdpf(Lignepagersdpf $lignepagersdpf): static
    {
        if (!$this->lignepagersdpfs->contains($lignepagersdpf)) {
            $this->lignepagersdpfs->add($lignepagersdpf);
            $lignepagersdpf->setNatureProduit($this);
        }

        return $this;
    }

    public function removeLignepagersdpf(Lignepagersdpf $lignepagersdpf): static
    {
        if ($this->lignepagersdpfs->removeElement($lignepagersdpf)) {
            // set the owning side to null (unless already changed)
            if ($lignepagersdpf->getNatureProduit() === $this) {
                $lignepagersdpf->setNatureProduit(null);
            }
        }

        return $this;
    }
}
