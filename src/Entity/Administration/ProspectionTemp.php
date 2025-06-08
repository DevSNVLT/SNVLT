<?php

namespace App\Entity\Administration;

use App\Repository\Administration\ProspectionTempRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.prospection_temp')]
#[ORM\Entity(repositoryClass: ProspectionTempRepository::class)]
class ProspectionTemp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 4, nullable: true)]
    private ?string $code_essence = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $foret = null;

    #[ORM\Column(length: 4, nullable: true)]
    private ?string $zone_h = null;

    #[ORM\Column(nullable: true)]
    private ?float $x = null;

    #[ORM\Column(nullable: true)]
    private ?float $y = null;

    #[ORM\Column(nullable: true)]
    private ?float $lng = null;

    #[ORM\Column(nullable: true)]
    private ?float $dm = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume = null;

    #[ORM\Column(nullable: true)]
    private ?int $numero = null;

    #[ORM\ManyToOne(inversedBy: 'prospectionTemps')]
    private ?FicheProspection $code_fichep = null;

    #[ORM\Column(nullable: true)]
    private ?bool $lac = null;

    #[ORM\Column(nullable: true)]
    private ?bool $has_error = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motif_error = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeEssence(): ?string
    {
        return $this->code_essence;
    }

    public function setCodeEssence(?string $code_essence): static
    {
        $this->code_essence = $code_essence;

        return $this;
    }

    public function getForet(): ?string
    {
        return $this->foret;
    }

    public function setForet(string $foret): static
    {
        $this->foret = $foret;

        return $this;
    }

    public function getZoneH(): ?string
    {
        return $this->zone_h;
    }

    public function setZoneH(?string $zone_h): static
    {
        $this->zone_h = $zone_h;

        return $this;
    }

    public function getX(): ?float
    {
        return $this->x;
    }

    public function setX(?float $x): static
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?float
    {
        return $this->y;
    }

    public function setY(?float $y): static
    {
        $this->y = $y;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(?float $lng): static
    {
        $this->lng = $lng;

        return $this;
    }

    public function getDm(): ?float
    {
        return $this->dm;
    }

    public function setDm(?float $dm): static
    {
        $this->dm = $dm;

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

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(?int $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getCodeFichep(): ?FicheProspection
    {
        return $this->code_fichep;
    }

    public function setCodeFichep(?FicheProspection $code_fichep): static
    {
        $this->code_fichep = $code_fichep;

        return $this;
    }

    public function isLac(): ?bool
    {
        return $this->lac;
    }

    public function setLac(?bool $lac): static
    {
        $this->lac = $lac;

        return $this;
    }

    public function isHasError(): ?bool
    {
        return $this->has_error;
    }

    public function setHasError(?bool $has_error): static
    {
        $this->has_error = $has_error;

        return $this;
    }

    public function getMotifError(): ?string
    {
        return $this->motif_error;
    }

    public function setMotifError(?string $motif_error): static
    {
        $this->motif_error = $motif_error;

        return $this;
    }
}
