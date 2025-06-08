<?php

namespace App\Entity\DocStats\Saisie;

use App\Entity\DocStats\Pages\Pagebrepf;
use App\Entity\References\Essence;
use App\Entity\References\ProduitsUsine;
use App\Repository\DocStats\Saisie\LignepagebrepfRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.lignepagebrepf')]
#[ORM\Entity(repositoryClass: LignepagebrepfRepository::class)]
class Lignepagebrepf
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagebrepfs')]
    private ?ProduitsUsine $nature_pdt = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagebrepfs')]
    private ?Essence $essence = null;

    #[ORM\Column(nullable: true)]
    private ?int $nb_elements = null;

    #[ORM\Column(nullable: true)]
    private ?int $lng = null;

    #[ORM\Column(nullable: true)]
    private ?int $lrg = null;

    #[ORM\Column(nullable: true)]
    private ?int $ep = null;

    #[ORM\Column(nullable: true)]
    private ?float $cubage = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observation = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagebrepfs')]
    private ?Pagebrepf $code_pagebrepf = null;

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

    public function getNaturePdt(): ?ProduitsUsine
    {
        return $this->nature_pdt;
    }

    public function setNaturePdt(?ProduitsUsine $nature_pdt): static
    {
        $this->nature_pdt = $nature_pdt;

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

    public function getNbElements(): ?int
    {
        return $this->nb_elements;
    }

    public function setNbElements(?int $nb_elements): static
    {
        $this->nb_elements = $nb_elements;

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

    public function getLrg(): ?int
    {
        return $this->lrg;
    }

    public function setLrg(?int $lrg): static
    {
        $this->lrg = $lrg;

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

    public function getCubage(): ?float
    {
        return $this->cubage;
    }

    public function setCubage(?float $cubage): static
    {
        $this->cubage = $cubage;

        return $this;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): static
    {
        $this->observation = $observation;

        return $this;
    }

    public function getCodePagebrepf(): ?Pagebrepf
    {
        return $this->code_pagebrepf;
    }

    public function setCodePagebrepf(?Pagebrepf $code_pagebrepf): static
    {
        $this->code_pagebrepf = $code_pagebrepf;

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
