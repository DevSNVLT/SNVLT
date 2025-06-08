<?php

namespace App\Entity\DocStats\Saisie;

use App\Entity\DocStats\Pages\Pageetatg;
use App\Entity\References\Essence;
use App\Repository\DocStats\Saisie\LignepageetatgRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.lignepageetatg')]
#[ORM\Entity(repositoryClass: LignepageetatgRepository::class)]
class Lignepageetatg
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 4, nullable: true)]
    private ?string $codepdt_etatg = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageetatgs')]
    private ?Essence $code_essence = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume_utilise = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume_obtenu = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageetatgs')]
    private ?Pageetatg $code_pageetatg = null;

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

    public function getCodepdtEtatg(): ?string
    {
        return $this->codepdt_etatg;
    }

    public function setCodepdtEtatg(?string $codepdt_etatg): static
    {
        $this->codepdt_etatg = $codepdt_etatg;

        return $this;
    }

    public function getCodeEssence(): ?Essence
    {
        return $this->code_essence;
    }

    public function setCodeEssence(?Essence $code_essence): static
    {
        $this->code_essence = $code_essence;

        return $this;
    }

    public function getVolumeUtilise(): ?float
    {
        return $this->volume_utilise;
    }

    public function setVolumeUtilise(?float $volume_utilise): static
    {
        $this->volume_utilise = $volume_utilise;

        return $this;
    }

    public function getVolumeObtenu(): ?float
    {
        return $this->volume_obtenu;
    }

    public function setVolumeObtenu(?float $volume_obtenu): static
    {
        $this->volume_obtenu = $volume_obtenu;

        return $this;
    }

    public function getCodePageetatg(): ?Pageetatg
    {
        return $this->code_pageetatg;
    }

    public function setCodePageetatg(?Pageetatg $code_pageetatg): static
    {
        $this->code_pageetatg = $code_pageetatg;

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
