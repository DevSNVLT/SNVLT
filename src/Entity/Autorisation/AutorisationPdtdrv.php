<?php

namespace App\Entity\Autorisation;

use App\Entity\Admin\Exercice;
use App\Repository\Autorisation\AutorisationPdtdrvRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AutorisationPdtdrvRepository::class)]
class AutorisationPdtdrv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_permis = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_autorisation = null;

    #[ORM\ManyToOne(inversedBy: 'autorisationPdtdrvs')]
    private ?AgreementPdtdrv $code_agreement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'autorisationPdtdrvs')]
    private ?Exercice $exercice = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroPermis(): ?string
    {
        return $this->numero_permis;
    }

    public function setNumeroPermis(string $numero_permis): static
    {
        $this->numero_permis = $numero_permis;

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

    public function getCodeAgreement(): ?AgreementPdtdrv
    {
        return $this->code_agreement;
    }

    public function setCodeAgreement(?AgreementPdtdrv $code_agreement): static
    {
        $this->code_agreement = $code_agreement;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    public function setCreatedBy(?string $created_by): static
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

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): static
    {
        $this->exercice = $exercice;

        return $this;
    }
}
