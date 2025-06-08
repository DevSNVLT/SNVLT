<?php

namespace App\Entity\DocStats\Saisie;

use App\Entity\References\Essence;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Entity\References\Usine;
use App\Repository\DocStats\Saisie\LignepageetateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.lignepageetate')]
#[ORM\Entity(repositoryClass: LignepageetateRepository::class)]
class Lignepageetate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageetates')]
    private ?Exploitant $code_exploitant = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageetates')]
    private ?Foret $foret = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageetates')]
    private ?Usine $usine_origine = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageetates')]
    private ?Essence $essence = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume = null;

    #[ORM\Column(nullable: true)]
    private ?int $tarif = null;

    #[ORM\Column(nullable: true)]
    private ?float $montant = null;

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

    public function getCodeExploitant(): ?Exploitant
    {
        return $this->code_exploitant;
    }

    public function setCodeExploitant(?Exploitant $code_exploitant): static
    {
        $this->code_exploitant = $code_exploitant;

        return $this;
    }

    public function getForet(): ?Foret
    {
        return $this->foret;
    }

    public function setForet(?Foret $foret): static
    {
        $this->foret = $foret;

        return $this;
    }

    public function getUsineOrigine(): ?Usine
    {
        return $this->usine_origine;
    }

    public function setUsineOrigine(?Usine $usine_origine): static
    {
        $this->usine_origine = $usine_origine;

        return $this;
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

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function setVolume(?float $volume): static
    {
        $this->volume = $volume;

        return $this;
    }

    public function getTarif(): ?int
    {
        return $this->tarif;
    }

    public function setTarif(?int $tarif): static
    {
        $this->tarif = $tarif;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(?float $montant): static
    {
        $this->montant = $montant;

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
