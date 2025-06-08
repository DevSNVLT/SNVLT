<?php

namespace App\Entity\Requetes;

use App\Repository\Requetes\PerformanceBrhRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.performance_brh')]
#[ORM\Entity(repositoryClass: PerformanceBrhRepository::class)]
class PerformanceBrh
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id_performance = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created_at = null;


    #[ORM\Column(nullable: true)]
    private ?int $nb_ligne= null;

    #[ORM\Column]
    private ?float $volume = null;

    #[ORM\Column(nullable: true)]
    private ?int $nb_brh= null;

    /**
     * @return int|null
     */
    public function getIdPerformance(): ?int
    {
        return $this->id_performance;
    }

    /**
     * @param int|null $id_performance
     */
    public function setIdPerformance(?int $id_performance): void
    {
        $this->id_performance = $id_performance;
    }

    /**
     * @return string|null
     */
    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    /**
     * @param string|null $created_by
     */
    public function setCreatedBy(?string $created_by): void
    {
        $this->created_by = $created_by;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @param \DateTimeInterface|null $created_at
     */
    public function setCreatedAt(?\DateTimeInterface $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return int|null
     */
    public function getNbLigne(): ?int
    {
        return $this->nb_ligne;
    }

    /**
     * @param int|null $nb_ligne
     */
    public function setNbLigne(?int $nb_ligne): void
    {
        $this->nb_ligne = $nb_ligne;
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
    public function getNbBrh(): ?int
    {
        return $this->nb_brh;
    }

    /**
     * @param int|null $nb_brh
     */
    public function setNbBrh(?int $nb_brh): void
    {
        $this->nb_brh = $nb_brh;
    }




    public function __toString(): string
    {
        return $this->created_by;
    }


}
