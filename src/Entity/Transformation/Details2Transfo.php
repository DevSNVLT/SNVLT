<?php

namespace App\Entity\Transformation;

use App\Entity\References\Essence;
use App\Repository\Transformation\Details2TransfoRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'transformation.details_2_transfo')]
#[ORM\Entity(repositoryClass: Details2TransfoRepository::class)]
class Details2Transfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $lng = null;

    #[ORM\Column]
    private ?int $lrg = null;

    #[ORM\Column]
    private ?int $ep = null;

    #[ORM\Column]
    private ?int $nb = null;

    #[ORM\Column]
    private ?float $volume = null;

    #[ORM\ManyToOne(inversedBy: 'details2Transfos')]
    private ?Fiche2Transfo $code_fiche2 = null;

    #[ORM\ManyToOne(inversedBy: 'details2Transfos')]
    private ?Essence $code_essence = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLng(): ?int
    {
        return $this->lng;
    }

    public function setLng(int $lng): static
    {
        $this->lng = $lng;

        return $this;
    }

    public function getLrg(): ?int
    {
        return $this->lrg;
    }

    public function setLrg(int $lrg): static
    {
        $this->lrg = $lrg;

        return $this;
    }

    public function getEp(): ?int
    {
        return $this->ep;
    }

    public function setEp(int $ep): static
    {
        $this->ep = $ep;

        return $this;
    }

    public function getNb(): ?int
    {
        return $this->nb;
    }

    public function setNb(int $nb): static
    {
        $this->nb = $nb;

        return $this;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function setVolume(float $volume): static
    {
        $this->volume = $volume;

        return $this;
    }

    public function getCodeFiche2(): ?Fiche2Transfo
    {
        return $this->code_fiche2;
    }

    public function setCodeFiche2(?Fiche2Transfo $code_fiche2): static
    {
        $this->code_fiche2 = $code_fiche2;

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


}
