<?php

namespace App\Entity\References;

use App\Entity\Autorisation\AutorisationPs;
use App\Repository\References\NaturePsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.nature_ps')]
#[ORM\Entity(repositoryClass: NaturePsRepository::class)]
class NaturePs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $libelle = null;

    #[ORM\Column(length: 50)]
    private ?string $unite = null;

    #[ORM\Column(nullable: true)]
    private ?int $montant_autorisation = null;

    #[ORM\ManyToOne(inversedBy: 'naturePs')]
    private ?TypeDossierPs $type_dossier_ps = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_produit', targetEntity: AutorisationPs::class)]
    private Collection $autorisationPs;

    #[ORM\Column(nullable: true)]
    private ?int $duree_autorisation = null;

    public function __construct()
    {
        $this->autorisationPs = new ArrayCollection();
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

    public function getUnite(): ?string
    {
        return $this->unite;
    }

    public function setUnite(string $unite): static
    {
        $this->unite = $unite;

        return $this;
    }

    public function getMontantAutorisation(): ?int
    {
        return $this->montant_autorisation;
    }

    public function setMontantAutorisation(?int $montant_autorisation): static
    {
        $this->montant_autorisation = $montant_autorisation;

        return $this;
    }

    public function getTypeDossierPs(): ?TypeDossierPs
    {
        return $this->type_dossier_ps;
    }

    public function setTypeDossierPs(?TypeDossierPs $type_dossier_ps): static
    {
        $this->type_dossier_ps = $type_dossier_ps;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    public function setCreatedBy(string $created_by): static
    {
        $this->created_by = $created_by;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    public function setUpdatedBy(?string $updated_by): static
    {
        $this->updated_by = $updated_by;

        return $this;
    }

    /**
     * @return Collection<int, AutorisationPs>
     */
    public function getAutorisationPs(): Collection
    {
        return $this->autorisationPs;
    }

    public function addAutorisationP(AutorisationPs $autorisationP): static
    {
        if (!$this->autorisationPs->contains($autorisationP)) {
            $this->autorisationPs->add($autorisationP);
            $autorisationP->setCodeProduit($this);
        }

        return $this;
    }

    public function removeAutorisationP(AutorisationPs $autorisationP): static
    {
        if ($this->autorisationPs->removeElement($autorisationP)) {
            // set the owning side to null (unless already changed)
            if ($autorisationP->getCodeProduit() === $this) {
                $autorisationP->setCodeProduit(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->libelle;
    }

    public function getDureeAutorisation(): ?int
    {
        return $this->duree_autorisation;
    }

    public function setDureeAutorisation(?int $duree_autorisation): static
    {
        $this->duree_autorisation = $duree_autorisation;

        return $this;
    }
}
