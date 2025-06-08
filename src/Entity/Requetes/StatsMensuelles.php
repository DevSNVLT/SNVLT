<?php

namespace App\Entity\Requetes;

use App\Repository\Requetes\StatsMensuellesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.stats_mensuelles')]
#[ORM\Entity(repositoryClass: StatsMensuellesRepository::class)]
class StatsMensuelles
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom_vernaculaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $denomination = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $raison_sociale_exploitant = null;
    #[ORM\Column( nullable: true)]
    private ?int $annee = null;
    #[ORM\Column(length: 255, nullable: true)]
    private ?int $mois = null;
    #[ORM\Column(length: 255, nullable: true)]
        private ?int $foret_id = null;
    #[ORM\Column(length: 255, nullable: true)]
        private ?int $essence_id = null;
    #[ORM\Column(length: 255, nullable: true)]
        private ?int $exploitant_id = null;

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
     * @return string|null
     */
    public function getDenomination(): ?string
    {
        return $this->denomination;
    }

    /**
     * @param string|null $denomination
     */
    public function setDenomination(?string $denomination): void
    {
        $this->denomination = $denomination;
    }

    /**
     * @return string|null
     */
    public function getRaisonSocialeExploitant(): ?string
    {
        return $this->raison_sociale_exploitant;
    }

    /**
     * @param string|null $raison_sociale_exploitant
     */
    public function setRaisonSocialeExploitant(?string $raison_sociale_exploitant): void
    {
        $this->raison_sociale_exploitant = $raison_sociale_exploitant;
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
     * @return int|null
     */
    public function getMois(): ?int
    {
        return $this->mois;
    }

    /**
     * @param int|null $mois
     */
    public function setMois(?int $mois): void
    {
        $this->mois = $mois;
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

    /**
     * @return int|null
     */
    public function getForetId(): ?int
    {
        return $this->foret_id;
    }

    /**
     * @param int|null $foret_id
     */
    public function setForetId(?int $foret_id): void
    {
        $this->foret_id = $foret_id;
    }

    /**
     * @return int|null
     */
    public function getEssenceId(): ?int
    {
        return $this->essence_id;
    }

    /**
     * @param int|null $essence_id
     */
    public function setEssenceId(?int $essence_id): void
    {
        $this->essence_id = $essence_id;
    }

    /**
     * @return int|null
     */
    public function getExploitantId(): ?int
    {
        return $this->exploitant_id;
    }

    /**
     * @param int|null $exploitant_id
     */
    public function setExploitantId(?int $exploitant_id): void
    {
        $this->exploitant_id = $exploitant_id;
    }

}
