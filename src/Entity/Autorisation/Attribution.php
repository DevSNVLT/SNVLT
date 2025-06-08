<?php

namespace App\Entity\Autorisation;

use App\Entity\Admin\Exercice;
use App\Entity\Administration\FicheProspection;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Repository\Autorisations\AttributionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.attribution')]
#[ORM\Entity(repositoryClass: AttributionRepository::class)]
class Attribution
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $numero_decision = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_decision = null;

    #[ORM\ManyToOne(inversedBy: 'attributions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Exploitant $code_exploitant = null;

    #[ORM\ManyToOne(inversedBy: 'attributions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Foret $code_foret = null;

    #[ORM\Column(nullable: true)]
    private ?bool $statut = null;

    #[ORM\OneToMany(mappedBy: 'code_attribution', targetEntity: Reprise::class)]
    private Collection $reprises;

    #[ORM\Column(nullable: true)]
    private ?bool $reprise = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(nullable: true)]
    private ?bool $validation_document = null;

    #[ORM\OneToMany(mappedBy: 'code_attribution', targetEntity: FicheProspection::class)]
    private Collection $ficheProspections;

    #[ORM\ManyToOne(inversedBy: 'attributions')]
    private ?Exercice $exercice = null;

    #[ORM\Column(nullable: true)]
    private ?bool $retire = null;

    #[ORM\Column(nullable: true)]
    private ?bool $abandonne = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $motif = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_retrait = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_abandon = null;

    public function __construct()
    {
        $this->reprises = new ArrayCollection();
        $this->ficheProspections = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDecision(): ?string
    {
        return $this->numero_decision;
    }

    public function setNumeroDecision(string $numero_decision): static
    {
        $this->numero_decision = $numero_decision;

        return $this;
    }

    public function getDateDecision(): ?\DateTimeInterface
    {
        return $this->date_decision;
    }

    public function setDateDecision(?\DateTimeInterface $date_decision): static
    {
        $this->date_decision = $date_decision;

        return $this;
    }

    public function getCodeExploitant(): ?Exploitant
    {
        return $this->code_exploitant;
    }

    public function setCodeExploitant(?Exploitant $code_exploitant): static
    {
        $this->code_exploitant = $code_exploitant;

        return $this;
    }

    public function getCodeForet(): ?Foret
    {
        return $this->code_foret;
    }

    public function setCodeForet(?Foret $code_foret): static
    {
        $this->code_foret = $code_foret;

        return $this;
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(?bool $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection<int, Reprise>
     */
    public function getReprises(): Collection
    {
        return $this->reprises;
    }

    public function addReprise(Reprise $reprise): static
    {
        if (!$this->reprises->contains($reprise)) {
            $this->reprises->add($reprise);
            $reprise->setCodeAttribution($this);
        }

        return $this;
    }

    public function removeReprise(Reprise $reprise): static
    {
        if ($this->reprises->removeElement($reprise)) {
            // set the owning side to null (unless already changed)
            if ($reprise->getCodeAttribution() === $this) {
                $reprise->setCodeAttribution(null);
            }
        }

        return $this;
    }

    public function isReprise(): ?bool
    {
        return $this->reprise;
    }

    public function setReprise(?bool $reprise): static
    {
        $this->reprise = $reprise;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    public function setCreatedBy(?string $created_by): static
    {
        $this->created_by = $created_by;

        return $this;
    }

    public function isValidationDocument(): ?bool
    {
        return $this->validation_document;
    }

    public function setValidationDocument(?bool $validation_document): static
    {
        $this->validation_document = $validation_document;

        return $this;
    }

    public function __toString(): string
    {
        return $this->code_foret->getDenomination();
    }

    /**
     * @return Collection<int, FicheProspection>
     */
    public function getFicheProspections(): Collection
    {
        return $this->ficheProspections;
    }

    public function addFicheProspection(FicheProspection $ficheProspection): static
    {
        if (!$this->ficheProspections->contains($ficheProspection)) {
            $this->ficheProspections->add($ficheProspection);
            $ficheProspection->setCodeAttribution($this);
        }

        return $this;
    }

    public function removeFicheProspection(FicheProspection $ficheProspection): static
    {
        if ($this->ficheProspections->removeElement($ficheProspection)) {
            // set the owning side to null (unless already changed)
            if ($ficheProspection->getCodeAttribution() === $this) {
                $ficheProspection->setCodeAttribution(null);
            }
        }

        return $this;
    }

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): static
    {
        $this->exercice = $exercice;

        return $this;
    }

    public function isRetire(): ?bool
    {
        return $this->retire;
    }

    public function setRetire(?bool $retire): static
    {
        $this->retire = $retire;

        return $this;
    }

    public function isAbandonne(): ?bool
    {
        return $this->abandonne;
    }

    public function setAbandonne(?bool $abandonne): static
    {
        $this->abandonne = $abandonne;

        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): static
    {
        $this->motif = $motif;

        return $this;
    }

    public function getDateRetrait(): ?\DateTimeInterface
    {
        return $this->date_retrait;
    }

    public function setDateRetrait(?\DateTimeInterface $date_retrait): static
    {
        $this->date_retrait = $date_retrait;

        return $this;
    }

    public function getDateAbandon(): ?\DateTimeInterface
    {
        return $this->date_abandon;
    }

    public function setDateAbandon(?\DateTimeInterface $date_abandon): static
    {
        $this->date_abandon = $date_abandon;

        return $this;
    }
}
