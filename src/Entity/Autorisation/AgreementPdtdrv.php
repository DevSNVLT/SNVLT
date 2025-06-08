<?php

namespace App\Entity\Autorisation;

use App\Entity\References\Commercant;
use App\Repository\Autorisation\AgreementPdtdrvRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AgreementPdtdrvRepository::class)]
class AgreementPdtdrv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_dossier = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_ouverture = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?Commercant $code_commercant = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $creaeted_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_agreement', targetEntity: AutorisationPdtdrv::class)]
    private Collection $autorisationPdtdrvs;

    public function __construct()
    {
        $this->autorisationPdtdrvs = new ArrayCollection();
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

    public function getDateOuverture(): ?\DateTimeInterface
    {
        return $this->date_ouverture;
    }

    public function setDateOuverture(\DateTimeInterface $date_ouverture): static
    {
        $this->date_ouverture = $date_ouverture;

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

    public function getCreaetedAt(): ?\DateTimeInterface
    {
        return $this->creaeted_at;
    }

    public function setCreaetedAt(\DateTimeInterface $creaeted_at): static
    {
        $this->creaeted_at = $creaeted_at;

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
     * @return Collection<int, AutorisationPdtdrv>
     */
    public function getAutorisationPdtdrvs(): Collection
    {
        return $this->autorisationPdtdrvs;
    }

    public function addAutorisationPdtdrv(AutorisationPdtdrv $autorisationPdtdrv): static
    {
        if (!$this->autorisationPdtdrvs->contains($autorisationPdtdrv)) {
            $this->autorisationPdtdrvs->add($autorisationPdtdrv);
            $autorisationPdtdrv->setCodeAgreement($this);
        }

        return $this;
    }

    public function removeAutorisationPdtdrv(AutorisationPdtdrv $autorisationPdtdrv): static
    {
        if ($this->autorisationPdtdrvs->removeElement($autorisationPdtdrv)) {
            // set the owning side to null (unless already changed)
            if ($autorisationPdtdrv->getCodeAgreement() === $this) {
                $autorisationPdtdrv->setCodeAgreement(null);
            }
        }

        return $this;
    }
}
