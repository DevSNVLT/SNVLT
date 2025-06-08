<?php

namespace App\Entity\References;

use App\Repository\References\SousPrefectureRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.sousprefecture')]
#[ORM\Entity(repositoryClass: SousPrefectureRepository::class)]
class SousPrefecture
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column(length: 255)]
    private ?string $nom_sousprefecture = null;

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getNomSousprefecture(): ?string
    {
        return $this->nom_sousprefecture;
    }

    public function setNomSousprefecture(string $nom_sousprefecture): static
    {
        $this->nom_sousprefecture = $nom_sousprefecture;

        return $this;
    }
}
