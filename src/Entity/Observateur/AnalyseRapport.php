<?php

namespace App\Entity\Observateur;

use App\Repository\Observateur\AnalyseRapportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'observateur.analyse_rapport')]
#[ORM\Entity(repositoryClass: AnalyseRapportRepository::class)]
class AnalyseRapport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_ligne = null;

    #[ORM\ManyToOne(inversedBy: 'analyseRapports')]
    private ?PublicationRapport $code_rapport = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statut = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $resume = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fichier = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_analyse', targetEntity: RecommendationAnalyse::class)]
    private Collection $recommendationAnalyses;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fichier_recommande = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_analyse = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_oi = null;

    public function __construct()
    {
        $this->recommendationAnalyses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroLigne(): ?string
    {
        return $this->numero_ligne;
    }

    public function setNumeroLigne(string $numero_ligne): static
    {
        $this->numero_ligne = $numero_ligne;

        return $this;
    }

    public function getCodeRapport(): ?PublicationRapport
    {
        return $this->code_rapport;
    }

    public function setCodeRapport(?PublicationRapport $code_rapport): static
    {
        $this->code_rapport = $code_rapport;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(?string $resume): static
    {
        $this->resume = $resume;

        return $this;
    }

    public function getFichier(): ?string
    {
        return $this->fichier;
    }

    public function setFichier(?string $fichier): static
    {
        $this->fichier = $fichier;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    public function setCreatedBy(string $created_by): static
    {
        $this->created_by = $created_by;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    public function setUpdatedBy(?string $updated_by): static
    {
        $this->updated_by = $updated_by;

        return $this;
    }

    /**
     * @return Collection<int, RecommendationAnalyse>
     */
    public function getRecommendationAnalyses(): Collection
    {
        return $this->recommendationAnalyses;
    }

    public function addRecommendationAnalysis(RecommendationAnalyse $recommendationAnalysis): static
    {
        if (!$this->recommendationAnalyses->contains($recommendationAnalysis)) {
            $this->recommendationAnalyses->add($recommendationAnalysis);
            $recommendationAnalysis->setCodeAnalyse($this);
        }

        return $this;
    }

    public function removeRecommendationAnalysis(RecommendationAnalyse $recommendationAnalysis): static
    {
        if ($this->recommendationAnalyses->removeElement($recommendationAnalysis)) {
            // set the owning side to null (unless already changed)
            if ($recommendationAnalysis->getCodeAnalyse() === $this) {
                $recommendationAnalysis->setCodeAnalyse(null);
            }
        }

        return $this;
    }

    public function getFichierRecommande(): ?string
    {
        return $this->fichier_recommande;
    }

    public function setFichierRecommande(?string $fichier_recommande): static
    {
        $this->fichier_recommande = $fichier_recommande;

        return $this;
    }

    public function getDateAnalyse(): ?\DateTimeInterface
    {
        return $this->date_analyse;
    }

    public function setDateAnalyse(?\DateTimeInterface $date_analyse): static
    {
        $this->date_analyse = $date_analyse;

        return $this;
    }

    public function getDateOi(): ?\DateTimeInterface
    {
        return $this->date_oi;
    }

    public function setDateOi(?\DateTimeInterface $date_oi): static
    {
        $this->date_oi = $date_oi;

        return $this;
    }
}
