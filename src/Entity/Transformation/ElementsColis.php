<?php

namespace App\Entity\Transformation;

use App\Repository\Transformation\ElementsColisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'transformation.elements_colis')]
#[ORM\Entity(repositoryClass: ElementsColisRepository::class)]
class ElementsColis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'elementsColis')]
    private ?Colis $code_colis = null;

    #[ORM\Column]
    private ?int $lng = null;

    #[ORM\Column]
    private ?int $lrg = null;

    #[ORM\Column]
    private ?int $ep = null;

    #[ORM\Column]
    private ?int $nombre = null;

    #[ORM\Column]
    private ?float $volume = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'elementsColis')]
    private ?Elements $code_elements = null;

    #[ORM\ManyToOne(inversedBy: 'elementsColis')]
    private ?FicheLotProd $code_fiche_lot_prod = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeColis(): ?Colis
    {
        return $this->code_colis;
    }

    public function setCodeColis(?Colis $code_colis): static
    {
        $this->code_colis = $code_colis;

        return $this;
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

    public function getNombre(): ?int
    {
        return $this->nombre;
    }

    public function setNombre(int $nombre): static
    {
        $this->nombre = $nombre;

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

    public function getCodeElements(): ?Elements
    {
        return $this->code_elements;
    }

    public function setCodeElements(?Elements $code_elements): static
    {
        $this->code_elements = $code_elements;

        return $this;
    }

    public function getCodeFicheLotProd(): ?FicheLotProd
    {
        return $this->code_fiche_lot_prod;
    }

    public function setCodeFicheLotProd(?FicheLotProd $code_fiche_lot_prod): static
    {
        $this->code_fiche_lot_prod = $code_fiche_lot_prod;

        return $this;
    }

}
