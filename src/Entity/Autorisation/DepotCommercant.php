<?php

namespace App\Entity\Autorisation;

use App\Repository\Autorisation\DepotCommercantRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.depot_commercant')]
#[ORM\Entity(repositoryClass: DepotCommercantRepository::class)]
class DepotCommercant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 25)]
    private ?string $numero_depot = null;

    #[ORM\ManyToOne(inversedBy: 'depotCommercants')]
    private ?AgreementCommercant $numero_dossier = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $cc = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $rc = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $quartier = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDepot(): ?string
    {
        return $this->numero_depot;
    }

    public function setNumeroDepot(string $numero_depot): static
    {
        $this->numero_depot = $numero_depot;

        return $this;
    }

    public function getNumeroDossier(): ?AgreementCommercant
    {
        return $this->numero_dossier;
    }

    public function setNumeroDossier(?AgreementCommercant $numero_dossier): static
    {
        $this->numero_dossier = $numero_dossier;

        return $this;
    }

    public function getCc(): ?string
    {
        return $this->cc;
    }

    public function setCc(?string $cc): static
    {
        $this->cc = $cc;

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

    public function getRc(): ?string
    {
        return $this->rc;
    }

    public function setRc(?string $rc): static
    {
        $this->rc = $rc;

        return $this;
    }

    public function getQuartier(): ?string
    {
        return $this->quartier;
    }

    public function setQuartier(?string $quartier): static
    {
        $this->quartier = $quartier;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(?\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;

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
}
