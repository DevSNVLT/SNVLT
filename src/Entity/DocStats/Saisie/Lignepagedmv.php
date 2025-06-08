<?php

namespace App\Entity\DocStats\Saisie;

use App\Entity\DocStats\Pages\Pagedmv;
use App\Entity\References\Essence;
use App\Entity\References\TypeTransformation;
use App\Repository\DocStats\Saisie\LignepagedmvRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.lignepagedmv')]
#[ORM\Entity(repositoryClass: LignepagedmvRepository::class)]
class Lignepagedmv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagedmvs')]
    private ?Essence $essence = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagedmvs')]
    private ?TypeTransformation $type_transformation = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $destination = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagedmvs')]
    private ?Pagedmv $code_pagedmv = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
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

    public function getEssence(): ?Essence
    {
        return $this->essence;
    }

    public function setEssence(?Essence $essence): static
    {
        $this->essence = $essence;

        return $this;
    }

    public function getTypeTransformation(): ?TypeTransformation
    {
        return $this->type_transformation;
    }

    public function setTypeTransformation(?TypeTransformation $type_transformation): static
    {
        $this->type_transformation = $type_transformation;

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

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(?string $destination): static
    {
        $this->destination = $destination;

        return $this;
    }

    public function getCodePagedmv(): ?Pagedmv
    {
        return $this->code_pagedmv;
    }

    public function setCodePagedmv(?Pagedmv $code_pagedmv): static
    {
        $this->code_pagedmv = $code_pagedmv;

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
