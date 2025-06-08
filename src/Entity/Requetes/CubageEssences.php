<?php

namespace App\Entity\Requetes;

use App\Repository\Requetes\CubageEssencesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.cubage_essences_id')]
#[ORM\Entity(repositoryClass: CubageEssencesRepository::class)]
class CubageEssences
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom_vernaculaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?int $annee = null;

    #[ORM\Column(length: 255, nullable: true)]
        private ?float $cubage = null;

    #[ORM\Column(length: 255, nullable: true)]
        private ?int $nb_billes = null;

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
    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    /**
     * @param int|null $annee
     */
    public function setAnnee(?int $annee): void
    {
        $this->annee = $annee;
    }

    /**
     * @return float|null
     */
    public function getCubage(): ?float
    {
        return $this->cubage;
    }

    /**
     * @param float|null $cubage
     */
    public function setCubage(?float $cubage): void
    {
        $this->cubage = $cubage;
    }

    /**
     * @return int|null
     */
    public function getNbBilles(): ?int
    {
        return $this->nb_billes;
    }

    /**
     * @param int|null $nb_billes
     */
    public function setNbBilles(?int $nb_billes): void
    {
        $this->nb_billes = $nb_billes;
    }
}
