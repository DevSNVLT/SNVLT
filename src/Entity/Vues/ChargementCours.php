<?php

namespace App\Entity\Vues;

use App\Repository\Vues\ChargementCoursRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.chargement_cours')]
#[ORM\Entity(repositoryClass: ChargementCoursRepository::class)]
class ChargementCours
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_pagebrh = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $immatcamion = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_chargement = null;

    #[ORM\Column(length: 100)]
    private ?string $rs_usine = null;

    #[ORM\Column(length: 100)]
    private ?string $rs_exploitant = null;

    #[ORM\Column(length: 100)]
    private ?int $exo = null;

    #[ORM\Column(length: 100)]
    private ?float $volume = null;

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
    public function getNumeroPagebrh(): ?string
    {
        return $this->numero_pagebrh;
    }

    /**
     * @param string|null $numero_pagebrh
     */
    public function setNumeroPagebrh(?string $numero_pagebrh): void
    {
        $this->numero_pagebrh = $numero_pagebrh;
    }

    /**
     * @return string|null
     */
    public function getImmatcamion(): ?string
    {
        return $this->immatcamion;
    }

    /**
     * @param string|null $immatcamion
     */
    public function setImmatcamion(?string $immatcamion): void
    {
        $this->immatcamion = $immatcamion;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateChargement(): ?\DateTimeInterface
    {
        return $this->date_chargement;
    }

    /**
     * @param \DateTimeInterface|null $date_chargement
     */
    public function setDateChargement(?\DateTimeInterface $date_chargement): void
    {
        $this->date_chargement = $date_chargement;
    }

    /**
     * @return string|null
     */
    public function getRsUsine(): ?string
    {
        return $this->rs_usine;
    }

    /**
     * @param string|null $rs_usine
     */
    public function setRsUsine(?string $rs_usine): void
    {
        $this->rs_usine = $rs_usine;
    }

    /**
     * @return string|null
     */
    public function getRsExploitant(): ?string
    {
        return $this->rs_exploitant;
    }

    /**
     * @param string|null $rs_exploitant
     */
    public function setRsExploitant(?string $rs_exploitant): void
    {
        $this->rs_exploitant = $rs_exploitant;
    }

    /**
     * @return int|null
     */
    public function getExo(): ?int
    {
        return $this->exo;
    }

    /**
     * @param int|null $exo
     */
    public function setExo(?int $exo): void
    {
        $this->exo = $exo;
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

}
