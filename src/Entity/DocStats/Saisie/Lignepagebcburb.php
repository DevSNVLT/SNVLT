<?php

namespace App\Entity\DocStats\Saisie;

use App\Entity\DocStats\Pages\Pagebcburb;
use App\Entity\References\ProduitsUsine;
use App\Repository\DocStats\Saisie\LignepagebcburbRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.lignepagebcburb')]
#[ORM\Entity(repositoryClass: LignepagebcburbRepository::class)]
class Lignepagebcburb
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagebcburbs')]
    private ?ProduitsUsine $designation = null;

    #[ORM\Column(nullable: true)]
    private ?int $Lng = null;

    #[ORM\Column(nullable: true)]
    private ?int $Lrg = null;

    #[ORM\Column(nullable: true)]
    private ?int $ep = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagebcburbs')]
    private ?Pagebcburb $code_pagebcburb = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?ProduitsUsine
    {
        return $this->designation;
    }

    public function setDesignation(?ProduitsUsine $designation): static
    {
        $this->designation = $designation;

        return $this;
    }

    public function getLng(): ?int
    {
        return $this->Lng;
    }

    public function setLng(int $Lng): static
    {
        $this->Lng = $Lng;

        return $this;
    }

    public function getLrg(): ?int
    {
        return $this->Lrg;
    }

    public function setLrg(?int $Lrg): static
    {
        $this->Lrg = $Lrg;

        return $this;
    }

    public function getEp(): ?int
    {
        return $this->ep;
    }

    public function setEp(?int $ep): static
    {
        $this->ep = $ep;

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

    public function getCodePagebcburb(): ?Pagebcburb
    {
        return $this->code_pagebcburb;
    }

    public function setCodePagebcburb(?Pagebcburb $code_pagebcburb): static
    {
        $this->code_pagebcburb = $code_pagebcburb;

        return $this;
    }
}
