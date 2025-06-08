<?php

namespace App\Entity\Vues;

use App\Entity\References\Essence;
use App\Repository\Vues\EssenceVolumeTop10Repository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'essence_volume_top_10')]
#[ORM\Entity(repositoryClass: EssenceVolumeTop10Repository::class)]
class EssenceVolumeTop10
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $nom_vernaculaire = null;

    #[ORM\Column]
    private ?float $cubage = null;

    #[ORM\Column]
    private ?int $exo = null;


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
     * @return Essence|null
     */
    public function getNomVernaculaire(): ?string
    {
        return $this->nom_vernaculaire;
    }

    /**
     * @param Essence|null $nom_vernaculaire
     */
    public function setNomVernaculaire(?string $nom_vernaculaire): void
    {
        $this->nom_vernaculaire = $nom_vernaculaire;
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


}
