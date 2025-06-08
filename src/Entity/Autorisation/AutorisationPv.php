<?php

namespace App\Entity\Autorisation;

use App\Entity\Admin\Exercice;
use App\Entity\Administration\DemandeOperateur;
use App\Entity\DocStats\Entetes\Documentbcbp;
use App\Entity\References\Exploitant;
use App\Repository\Autorisation\AutorisationPvRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.autorisation_pv')]
#[ORM\Entity(repositoryClass: AutorisationPvRepository::class)]
class AutorisationPv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_autorisation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_autorisation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $debut_validite = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $fin_validite = null;

    #[ORM\ManyToOne(inversedBy: 'autorisationPvs')]
    private ?AttributionPv $code_attribution_pv = null;

    #[ORM\ManyToOne(inversedBy: 'autorisationPvs')]
    private ?Exploitant $code_exploitant = null;

    #[ORM\ManyToOne(inversedBy: 'autorisationPvs')]
    private ?Exercice $exercice = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_autorisation_pv', targetEntity: Documentbcbp::class)]
    private Collection $documentbcbps;

    #[ORM\OneToMany(mappedBy: 'code_autorisation_pv', targetEntity: DemandeOperateur::class)]
    private Collection $demandeOperateurs;

    public function __construct()
    {
        $this->documentbcbps = new ArrayCollection();
        $this->demandeOperateurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroAutorisation(): ?string
    {
        return $this->numero_autorisation;
    }

    public function setNumeroAutorisation(string $numero_autorisation): static
    {
        $this->numero_autorisation = $numero_autorisation;

        return $this;
    }

    public function getDateAutorisation(): ?\DateTimeInterface
    {
        return $this->date_autorisation;
    }

    public function setDateAutorisation(\DateTimeInterface $date_autorisation): static
    {
        $this->date_autorisation = $date_autorisation;

        return $this;
    }

    public function getDebutValidite(): ?\DateTimeInterface
    {
        return $this->debut_validite;
    }

    public function setDebutValidite(\DateTimeInterface $debut_validite): static
    {
        $this->debut_validite = $debut_validite;

        return $this;
    }

    public function getFinValidite(): ?\DateTimeInterface
    {
        return $this->fin_validite;
    }

    public function setFinValidite(\DateTimeInterface $fin_validite): static
    {
        $this->fin_validite = $fin_validite;

        return $this;
    }

    public function getCodeAttributionPv(): ?AttributionPv
    {
        return $this->code_attribution_pv;
    }

    public function setCodeAttributionPv(?AttributionPv $code_attribution_pv): static
    {
        $this->code_attribution_pv = $code_attribution_pv;

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

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): static
    {
        $this->exercice = $exercice;

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
     * @return Collection<int, Documentbcbp>
     */
    public function getDocumentbcbps(): Collection
    {
        return $this->documentbcbps;
    }

    public function addDocumentbcbp(Documentbcbp $documentbcbp): static
    {
        if (!$this->documentbcbps->contains($documentbcbp)) {
            $this->documentbcbps->add($documentbcbp);
            $documentbcbp->setCodeAutorisationPv($this);
        }

        return $this;
    }

    public function removeDocumentbcbp(Documentbcbp $documentbcbp): static
    {
        if ($this->documentbcbps->removeElement($documentbcbp)) {
            // set the owning side to null (unless already changed)
            if ($documentbcbp->getCodeAutorisationPv() === $this) {
                $documentbcbp->setCodeAutorisationPv(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, DemandeOperateur>
     */
    public function getDemandeOperateurs(): Collection
    {
        return $this->demandeOperateurs;
    }

    public function addDemandeOperateur(DemandeOperateur $demandeOperateur): static
    {
        if (!$this->demandeOperateurs->contains($demandeOperateur)) {
            $this->demandeOperateurs->add($demandeOperateur);
            $demandeOperateur->setCodeAutorisationPv($this);
        }

        return $this;
    }

    public function removeDemandeOperateur(DemandeOperateur $demandeOperateur): static
    {
        if ($this->demandeOperateurs->removeElement($demandeOperateur)) {
            // set the owning side to null (unless already changed)
            if ($demandeOperateur->getCodeAutorisationPv() === $this) {
                $demandeOperateur->setCodeAutorisationPv(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return  $this->numero_autorisation;
    }
}
