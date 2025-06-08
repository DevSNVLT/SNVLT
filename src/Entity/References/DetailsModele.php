<?php

namespace App\Entity\References;

use App\Repository\References\DetailsModeleRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.details_modele')]
#[ORM\Entity(repositoryClass: DetailsModeleRepository::class)]
class DetailsModele
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $Type_service = null;

    #[ORM\ManyToOne(inversedBy: 'detailsModeles')]
    private ?ServiceMinef $code_service = null;

    #[ORM\ManyToOne(inversedBy: 'detailsModeles')]
    private ?Direction $code_direction = null;

    #[ORM\Column(nullable: true)]
    private ?int $numseq = null;

    #[ORM\ManyToOne(inversedBy: 'detailsModeles')]
    private ?ModeleCommunication $code_modele = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTypeService(): ?string
    {
        return $this->Type_service;
    }

    public function setTypeService(?string $Type_service): static
    {
        $this->Type_service = $Type_service;

        return $this;
    }

    public function getCodeService(): ?ServiceMinef
    {
        return $this->code_service;
    }

    public function setCodeService(?ServiceMinef $code_service): static
    {
        $this->code_service = $code_service;

        return $this;
    }

    public function getCodeDirection(): ?Direction
    {
        return $this->code_direction;
    }

    public function setCodeDirection(?Direction $code_direction): static
    {
        $this->code_direction = $code_direction;

        return $this;
    }

    public function getNumseq(): ?int
    {
        return $this->numseq;
    }

    public function setNumseq(?int $numseq): static
    {
        $this->numseq = $numseq;

        return $this;
    }

    public function getCodeModele(): ?ModeleCommunication
    {
        return $this->code_modele;
    }

    public function setCodeModele(?ModeleCommunication $code_modele): static
    {
        $this->code_modele = $code_modele;

        return $this;
    }


}
