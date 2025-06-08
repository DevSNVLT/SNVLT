<?php

namespace App\Entity\Observateur;

use App\Repository\Observateur\RecommendationAnalyseRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'observateur.recommandation_analyse')]
#[ORM\Entity(repositoryClass: RecommendationAnalyseRepository::class)]
class RecommendationAnalyse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $fichier = null;

    #[ORM\ManyToOne(inversedBy: 'recommendationAnalyses')]
    private ?AnalyseRapport $code_analyse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(string $fichier): static
    {
        $this->fichier = $fichier;

        return $this;
    }

    public function getCodeAnalyse(): ?AnalyseRapport
    {
        return $this->code_analyse;
    }

    public function setCodeAnalyse(?AnalyseRapport $code_analyse): static
    {
        $this->code_analyse = $code_analyse;

        return $this;
    }
}
