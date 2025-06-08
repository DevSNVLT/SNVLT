<?php

namespace App\Entity\References;

use App\Repository\References\NationaliteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.nationalite')]
#[ORM\Entity(repositoryClass: NationaliteRepository::class)]
class Nationalite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $nationalite = null;

    #[ORM\OneToMany(mappedBy: 'nationalite', targetEntity: AttributairePs::class)]
    private Collection $attributairePs;

    #[ORM\OneToMany(mappedBy: 'nationalite', targetEntity: Commercant::class)]
    private Collection $commercants;

    public function __construct()
    {
        $this->attributairePs = new ArrayCollection();
        $this->commercants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(string $nationalite): static
    {
        $this->nationalite = $nationalite;

        return $this;
    }

    /**
     * @return Collection<int, AttributairePs>
     */
    public function getAttributairePs(): Collection
    {
        return $this->attributairePs;
    }

    public function addAttributaireP(AttributairePs $attributaireP): static
    {
        if (!$this->attributairePs->contains($attributaireP)) {
            $this->attributairePs->add($attributaireP);
            $attributaireP->setNationalite($this);
        }

        return $this;
    }

    public function removeAttributaireP(AttributairePs $attributaireP): static
    {
        if ($this->attributairePs->removeElement($attributaireP)) {
            // set the owning side to null (unless already changed)
            if ($attributaireP->getNationalite() === $this) {
                $attributaireP->setNationalite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commercant>
     */
    public function getCommercants(): Collection
    {
        return $this->commercants;
    }

    public function addCommercant(Commercant $commercant): static
    {
        if (!$this->commercants->contains($commercant)) {
            $this->commercants->add($commercant);
            $commercant->setNationalite($this);
        }

        return $this;
    }

    public function removeCommercant(Commercant $commercant): static
    {
        if ($this->commercants->removeElement($commercant)) {
            // set the owning side to null (unless already changed)
            if ($commercant->getNationalite() === $this) {
                $commercant->setNationalite(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->nationalite;
    }
}
