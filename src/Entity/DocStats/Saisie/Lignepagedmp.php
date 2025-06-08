<?php

namespace App\Entity\DocStats\Saisie;

use App\Entity\DocStats\Pages\Pagedmp;
use App\Entity\References\Essence;
use App\Entity\References\ProduitsUsine;
use App\Repository\DocStats\Saisie\LignepagedmpRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.lignepagedmp')]
#[ORM\Entity(repositoryClass: LignepagedmpRepository::class)]
class Lignepagedmp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagedmps')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Essence $esssence = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagedmps')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ProduitsUsine $nature_matiere = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume_matiere = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $type_pdt = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume_pdt = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagedmps')]
    private ?Pagedmp $code_pagedmp = null;

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

    public function getEsssence(): ?Essence
    {
        return $this->esssence;
    }

    public function setEsssence(?Essence $esssence): static
    {
        $this->esssence = $esssence;

        return $this;
    }

    public function getNatureMatiere(): ?ProduitsUsine
    {
        return $this->nature_matiere;
    }

    public function setNatureMatiere(?ProduitsUsine $nature_matiere): static
    {
        $this->nature_matiere = $nature_matiere;

        return $this;
    }

    public function getVolumeMatiere(): ?float
    {
        return $this->volume_matiere;
    }

    public function setVolumeMatiere(?float $volume_matiere): static
    {
        $this->volume_matiere = $volume_matiere;

        return $this;
    }

    public function getTypePdt(): ?string
    {
        return $this->type_pdt;
    }

    public function setTypePdt(?string $type_pdt): static
    {
        $this->type_pdt = $type_pdt;

        return $this;
    }

    public function getVolumePdt(): ?float
    {
        return $this->volume_pdt;
    }

    public function setVolumePdt(?float $volume_pdt): static
    {
        $this->volume_pdt = $volume_pdt;

        return $this;
    }

    public function getCodePagedmp(): ?Pagedmp
    {
        return $this->code_pagedmp;
    }

    public function setCodePagedmp(?Pagedmp $code_pagedmp): static
    {
        $this->code_pagedmp = $code_pagedmp;

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
