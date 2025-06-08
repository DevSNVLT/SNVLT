<?php

namespace App\Entity\DocStats;

use App\Entity\Administration\Gadget;
use App\Entity\Observateur\Ticket;
use App\Entity\Permission;
use App\Entity\User;
use App\Repository\DocStats\DocsStatsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.docs_stats')]
#[ORM\Entity(repositoryClass: DocsStatsRepository::class)]
class DocsStats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $abv = null;


    #[ORM\Column(nullable: true)]
    private ?int $nb_delivres = null;

    #[ORM\Column(nullable: true)]
    private ?int $nb_saisi = null;

    #[ORM\Column(nullable: true)]
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
     * @return string|null
     */
    public function getAbv(): ?string
    {
        return $this->abv;
    }

    /**
     * @param string|null $abv
     */
    public function setAbv(?string $abv): void
    {
        $this->abv = $abv;
    }

    /**
     * @return int|null
     */
    public function getNbDelivres(): ?int
    {
        return $this->nb_delivres;
    }

    /**
     * @param int|null $nb_delivres
     */
    public function setNbDelivres(?int $nb_delivres): void
    {
        $this->nb_delivres = $nb_delivres;
    }

    /**
     * @return int|null
     */
    public function getNbSaisi(): ?int
    {
        return $this->nb_saisi;
    }

    /**
     * @param int|null $nb_saisi
     */
    public function setNbSaisi(?int $nb_saisi): void
    {
        $this->nb_saisi = $nb_saisi;
    }

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
