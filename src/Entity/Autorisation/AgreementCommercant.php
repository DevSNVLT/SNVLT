<?php

namespace App\Entity\Autorisation;

use App\Entity\References\Commercant;
use App\Repository\Autorisation\AgreementCommercantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.agreement_commercant')]
#[ORM\Entity(repositoryClass: AgreementCommercantRepository::class)]
class AgreementCommercant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25)]
    private ?string $numero_dossier = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_agreement = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(nullable: true)]
    private ?bool $statut = null;

    #[ORM\ManyToOne(inversedBy: 'agreementCommercants')]
    private ?Commercant $code_commercant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'numero_dossier', targetEntity: DepotCommercant::class)]
    private Collection $depotCommercants;

    public function __construct()
    {
        $this->depotCommercants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDossier(): ?string
    {
        return $this->numero_dossier;
    }

    public function setNumeroDossier(string $numero_dossier): static
    {
        $this->numero_dossier = $numero_dossier;

        return $this;
    }

    public function getDateAgreement(): ?\DateTimeInterface
    {
        return $this->date_agreement;
    }

    public function setDateAgreement(?\DateTimeInterface $date_agreement): static
    {
        $this->date_agreement = $date_agreement;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
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

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(?bool $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getCodeCommercant(): ?Commercant
    {
        return $this->code_commercant;
    }

    public function setCodeCommercant(?Commercant $code_commercant): static
    {
        $this->code_commercant = $code_commercant;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, DepotCommercant>
     */
    public function getDepotCommercants(): Collection
    {
        return $this->depotCommercants;
    }

    public function addDepotCommercant(DepotCommercant $depotCommercant): static
    {
        if (!$this->depotCommercants->contains($depotCommercant)) {
            $this->depotCommercants->add($depotCommercant);
            $depotCommercant->setNumeroDossier($this);
        }

        return $this;
    }

    public function removeDepotCommercant(DepotCommercant $depotCommercant): static
    {
        if ($this->depotCommercants->removeElement($depotCommercant)) {
            // set the owning side to null (unless already changed)
            if ($depotCommercant->getNumeroDossier() === $this) {
                $depotCommercant->setNumeroDossier(null);
            }
        }

        return $this;
    }
}
