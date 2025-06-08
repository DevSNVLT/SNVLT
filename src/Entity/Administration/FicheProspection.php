<?php

namespace App\Entity\Administration;

use App\Entity\Autorisation\Attribution;
use App\Entity\References\Exploitant;
use App\Repository\Administration\FicheProspectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.fiche_prospection')]
#[ORM\Entity(repositoryClass: FicheProspectionRepository::class)]
class FicheProspection
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fichier = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'ficheProspections')]
    private ?Attribution $code_attribution = null;

    #[ORM\OneToMany(mappedBy: 'code_fiche_prospection', targetEntity: InventaireForestier::class)]
    private Collection $inventaireForestiers;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_fichep', targetEntity: ProspectionTemp::class)]
    private Collection $prospectionTemps;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lien_complet = null;

    #[ORM\ManyToOne(inversedBy: 'ficheProspections')]
    private ?Exploitant $code_exploitant = null;

    #[ORM\Column(nullable: true)]
    private ?bool $valide = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_inventaire = null;

    public function __construct()
    {
        $this->inventaireForestiers = new ArrayCollection();
        $this->prospectionTemps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(string $fichier): static
    {
        $this->fichier = $fichier;

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

    public function setUpdatedAt(\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getCodeAttribution(): ?Attribution
    {
        return $this->code_attribution;
    }

    public function setCodeAttribution(?Attribution $code_attribution): static
    {
        $this->code_attribution = $code_attribution;

        return $this;
    }

    /**
     * @return Collection<int, InventaireForestier>
     */
    public function getInventaireForestiers(): Collection
    {
        return $this->inventaireForestiers;
    }

    public function addInventaireForestier(InventaireForestier $inventaireForestier): static
    {
        if (!$this->inventaireForestiers->contains($inventaireForestier)) {
            $this->inventaireForestiers->add($inventaireForestier);
            $inventaireForestier->setCodeFicheProspection($this);
        }

        return $this;
    }

    public function removeInventaireForestier(InventaireForestier $inventaireForestier): static
    {
        if ($this->inventaireForestiers->removeElement($inventaireForestier)) {
            // set the owning side to null (unless already changed)
            if ($inventaireForestier->getCodeFicheProspection() === $this) {
                $inventaireForestier->setCodeFicheProspection(null);
            }
        }

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
     * @return Collection<int, ProspectionTemp>
     */
    public function getProspectionTemps(): Collection
    {
        return $this->prospectionTemps;
    }

    public function addProspectionTemp(ProspectionTemp $prospectionTemp): static
    {
        if (!$this->prospectionTemps->contains($prospectionTemp)) {
            $this->prospectionTemps->add($prospectionTemp);
            $prospectionTemp->setCodeFichep($this);
        }

        return $this;
    }

    public function removeProspectionTemp(ProspectionTemp $prospectionTemp): static
    {
        if ($this->prospectionTemps->removeElement($prospectionTemp)) {
            // set the owning side to null (unless already changed)
            if ($prospectionTemp->getCodeFichep() === $this) {
                $prospectionTemp->setCodeFichep(null);
            }
        }

        return $this;
    }

    public function getLienComplet(): ?string
    {
        return $this->lien_complet;
    }

    public function setLienComplet(?string $lien_complet): static
    {
        $this->lien_complet = $lien_complet;

        return $this;
    }

    public function getCodeExploitant(): ?Exploitant
    {
        return $this->code_exploitant;
    }

    public function setCodeExploitant(?Exploitant $code_exploitant): static
    {
        $this->code_exploitant = $code_exploitant;

        return $this;
    }

    public function isValide(): ?bool
    {
        return $this->valide;
    }

    public function setValide(?bool $valide): static
    {
        $this->valide = $valide;

        return $this;
    }

    public function getDateInventaire(): ?\DateTimeInterface
    {
        return $this->date_inventaire;
    }

    public function setDateInventaire(?\DateTimeInterface $date_inventaire): static
    {
        $this->date_inventaire = $date_inventaire;

        return $this;
    }
}
