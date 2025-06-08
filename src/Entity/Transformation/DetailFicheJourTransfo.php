<?php

namespace App\Entity\Transformation;

use App\Repository\Transformation\DetailFicheJourTransfoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'transformation.detail_fiche_jour_transfo')]
#[ORM\Entity(repositoryClass: DetailFicheJourTransfoRepository::class)]
class DetailFicheJourTransfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $lng = null;

    #[ORM\Column(nullable: true)]
    private ?int $lrg = null;

    #[ORM\Column(nullable: true)]
    private ?int $ep = null;

    #[ORM\Column]
    private ?int $nb = null;

    #[ORM\ManyToOne(inversedBy: 'detailFicheJourTransfos')]
    private ?FicheJourTransfo $code_fiche = null;

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

    public function getNb(): ?int
    {
        return $this->nb;
    }

    public function setNb(int $nb): static
    {
        $this->nb = $nb;

        return $this;
    }

    public function getCodeFiche(): ?FicheJourTransfo
    {
        return $this->code_fiche;
    }

    public function setCodeFiche(?FicheJourTransfo $code_fiche): static
    {
        $this->code_fiche = $code_fiche;

        return $this;
    }
}
