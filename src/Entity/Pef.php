<?php

namespace App\Entity;

use App\Repository\PefRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PefRepository::class)]
class Pef
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
     private ?int $gid = null;

    #[ORM\Column]
    private ?int $numero_pef = null;

    #[ORM\Column(length: 255)]
    private ?string $zone_ = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $aire_pef = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $quotas = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $ta = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $ts = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 0)]
    private ?string $tas = null;

    #[ORM\Column(type: 'geometry')]
    private $geom = null;

 

    public function getGid(): ?int
    {
        return $this->gid;
    }

    public function setGid(int $gid): static
    {
        $this->gid = $gid;

        return $this;
    }

    public function getNumeroPef(): ?int
    {
        return $this->numero_pef;
    }

    public function setNumeroPef(int $numero_pef): static
    {
        $this->numero_pef = $numero_pef;

        return $this;
    }

    public function getZone(): ?string
    {
        return $this->zone_;
    }

    public function setZone(string $zone_): static
    {
        $this->zone_ = $zone_;

        return $this;
    }

    public function getAirePef(): ?string
    {
        return $this->aire_pef;
    }

    public function setAirePef(string $aire_pef): static
    {
        $this->aire_pef = $aire_pef;

        return $this;
    }

    public function getQuotas(): ?string
    {
        return $this->quotas;
    }

    public function setQuotas(string $quotas): static
    {
        $this->quotas = $quotas;

        return $this;
    }

    public function getTa(): ?string
    {
        return $this->ta;
    }

    public function setTa(string $ta): static
    {
        $this->ta = $ta;

        return $this;
    }

    public function getTs(): ?string
    {
        return $this->ts;
    }

    public function setTs(string $ts): static
    {
        $this->ts = $ts;

        return $this;
    }

    public function getTas(): ?string
    {
        return $this->tas;
    }

    public function setTas(string $tas): static
    {
        $this->tas = $tas;

        return $this;
    }

    public function getGeom()
    {
        return $this->geom;
    }

    public function setGeom($geom): static
    {
        $this->geom = $geom;

        return $this;
    }
}
