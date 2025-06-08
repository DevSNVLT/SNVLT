<?php

namespace App\Entity;

use App\Entity\Administration\Gadget;
use App\Entity\Observateur\Ticket;
use App\Repository\DisponibiliteParcBillonsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.disponibilite_parc_billons')]
#[ORM\Entity(repositoryClass: DisponibiliteParcBillonsRepository::class)]
class DisponibiliteParcBillons
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_vernaculaire = null;

    #[ORM\Column(nullable: true)]
    private ?int $nb_billons = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume = null;

    #[ORM\Column(nullable: true)]
    private ?int $code_usine = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string|null
     */
    public function getNomVernaculaire(): ?string
    {
        return $this->nom_vernaculaire;
    }

    /**
     * @param string|null $nom_vernaculaire
     */
    public function setNomVernaculaire(?string $nom_vernaculaire): void
    {
        $this->nom_vernaculaire = $nom_vernaculaire;
    }

    /**
     * @return int|null
     */
    public function getNbBillons(): ?int
    {
        return $this->nb_billons;
    }

    /**
     * @param int|null $nb_billes
     */
    public function setNbBillons(?int $nb_billons): void
    {
        $this->nb_billons = $nb_billons;
    }

    /**
     * @return float|null
     */
    public function getVolume(): ?float
    {
        return $this->volume;
    }

    /**
     * @param float|null $volume
     */
    public function setVolume(?float $volume): void
    {
        $this->volume = $volume;
    }

    /**
     * @return int|null
     */
    public function getCodeUsine(): ?int
    {
        return $this->code_usine;
    }

    /**
     * @param int|null $code_usine
     */
    public function setCodeUsine(?int $code_usine): void
    {
        $this->code_usine = $code_usine;
    }
}