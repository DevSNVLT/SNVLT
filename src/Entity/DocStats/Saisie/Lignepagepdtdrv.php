<?php

namespace App\Entity\DocStats\Saisie;

use App\Entity\References\PageDocGen;
use App\Repository\DocStats\Saisie\LignepagepdtdrvRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.lignepagepdtdrv')]
#[ORM\Entity(repositoryClass: LignepagepdtdrvRepository::class)]
class Lignepagepdtdrv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $nb = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $destination = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $designation = null;

    #[ORM\Column(nullable: true)]
    private ?int $lng = null;

    #[ORM\Column(nullable: true)]
    private ?float $lg = null;

    #[ORM\Column(nullable: true)]
    private ?float $epaisseur = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagepdtdrvs')]
    private ?PageDocGen $code_page_pdtdrv = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNb(): ?int
    {
        return $this->nb;
    }

    public function setNb(?int $nb): static
    {
        $this->nb = $nb;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(?string $destination): static
    {
        $this->destination = $destination;

        return $this;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(?string $designation): static
    {
        $this->designation = $designation;

        return $this;
    }

    public function getLng(): ?int
    {
        return $this->lng;
    }

    public function setLng(?int $lng): static
    {
        $this->lng = $lng;

        return $this;
    }

    public function getLg(): ?float
    {
        return $this->lg;
    }

    public function setLg(?float $lg): static
    {
        $this->lg = $lg;

        return $this;
    }

    public function getEpaisseur(): ?float
    {
        return $this->epaisseur;
    }

    public function setEpaisseur(?float $epaisseur): static
    {
        $this->epaisseur = $epaisseur;

        return $this;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function setVolume(?float $volume): static
    {
        $this->volume = $volume;

        return $this;
    }

    public function getCodePagePdtdrv(): ?PageDocGen
    {
        return $this->code_page_pdtdrv;
    }

    public function setCodePagePdtdrv(?PageDocGen $code_page_pdtdrv): static
    {
        $this->code_page_pdtdrv = $code_page_pdtdrv;

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
}
