<?php

namespace App\Entity\Observateur;

use App\Entity\References\Cantonnement;
use App\Entity\References\Direction;
use App\Entity\References\Dr;
use App\Entity\References\Foret;
use App\Entity\References\Oi;
use App\Entity\References\ServiceMinef;
use App\Repository\Observateur\PublicationRapportRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'observateur.publication_rapport')]
#[ORM\Entity(repositoryClass: PublicationRapportRepository::class)]
class PublicationRapport
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $resume = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fichier = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(nullable: true)]
    private ?bool $valide = null;

    #[ORM\OneToMany(mappedBy: 'code_publication', targetEntity: AnnexeRapport::class)]
    private Collection $annexeRapports;

    #[ORM\ManyToOne(inversedBy: 'publicationRapports')]
    private ?Oi $code_oi = null;

    #[ORM\OneToMany(mappedBy: 'code_rapport', targetEntity: AnalyseRapport::class)]
    private Collection $analyseRapports;

    #[ORM\ManyToMany(targetEntity: Dr::class, inversedBy: 'publicationRapports')]
    private Collection $code_dr;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statut = null;

    #[ORM\Column(nullable: true)]
    private ?int $x = null;

    #[ORM\Column(nullable: true)]
    private ?int $y = null;

    #[ORM\ManyToMany(targetEntity: Cantonnement::class, inversedBy: 'publicationRapports')]
    private Collection $code_cef;

    #[ORM\ManyToMany(targetEntity: ServiceMinef::class, inversedBy: 'publicationRapports')]
    private Collection $code_service_minef;

    #[ORM\ManyToMany(targetEntity: Direction::class, inversedBy: 'publicationRapports')]
    private Collection $code_direction;

    #[ORM\ManyToMany(targetEntity: Foret::class, inversedBy: 'publicationRapports')]
    private Collection $codeforet;

    public function __construct()
    {
        $this->annexeRapports = new ArrayCollection();
        $this->analyseRapports = new ArrayCollection();
        $this->code_dr = new ArrayCollection();
        $this->code_cef = new ArrayCollection();
        $this->code_service_minef = new ArrayCollection();
        $this->code_direction = new ArrayCollection();
        $this->codeforet = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

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

    public function setCreatedBy(?string $created_by): static
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

    public function isValide(): ?bool
    {
        return $this->valide;
    }

    public function setValide(?bool $valide): static
    {
        $this->valide = $valide;

        return $this;
    }

    /**
     * @return Collection<int, AnnexeRapport>
     */
    public function getAnnexeRapports(): Collection
    {
        return $this->annexeRapports;
    }

    public function addAnnexeRapport(AnnexeRapport $annexeRapport): static
    {
        if (!$this->annexeRapports->contains($annexeRapport)) {
            $this->annexeRapports->add($annexeRapport);
            $annexeRapport->setCodePublication($this);
        }

        return $this;
    }

    public function removeAnnexeRapport(AnnexeRapport $annexeRapport): static
    {
        if ($this->annexeRapports->removeElement($annexeRapport)) {
            // set the owning side to null (unless already changed)
            if ($annexeRapport->getCodePublication() === $this) {
                $annexeRapport->setCodePublication(null);
            }
        }

        return $this;
    }

    public function getCodeOi(): ?Oi
    {
        return $this->code_oi;
    }

    public function setCodeOi(?Oi $code_oi): static
    {
        $this->code_oi = $code_oi;

        return $this;
    }

    /**
     * @return Collection<int, AnalyseRapport>
     */
    public function getAnalyseRapports(): Collection
    {
        return $this->analyseRapports;
    }

    public function addAnalyseRapport(AnalyseRapport $analyseRapport): static
    {
        if (!$this->analyseRapports->contains($analyseRapport)) {
            $this->analyseRapports->add($analyseRapport);
            $analyseRapport->setCodeRapport($this);
        }

        return $this;
    }

    public function removeAnalyseRapport(AnalyseRapport $analyseRapport): static
    {
        if ($this->analyseRapports->removeElement($analyseRapport)) {
            // set the owning side to null (unless already changed)
            if ($analyseRapport->getCodeRapport() === $this) {
                $analyseRapport->setCodeRapport(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Dr>
     */
    public function getCodeDr(): Collection
    {
        return $this->code_dr;
    }

    public function addCodeDr(Dr $codeDr): static
    {
        if (!$this->code_dr->contains($codeDr)) {
            $this->code_dr->add($codeDr);
        }

        return $this;
    }

    public function removeCodeDr(Dr $codeDr): static
    {
        $this->code_dr->removeElement($codeDr);

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

    /**
     * @return int|null
     */
    public function getX(): ?int
    {
        return $this->x;
    }

    /**
     * @param int|null $x
     */
    public function setX(?int $x): void
    {
        $this->x = $x;
    }

    /**
     * @return int|null
     */
    public function getY(): ?int
    {
        return $this->y;
    }

    /**
     * @param int|null $y
     */
    public function setY(?int $y): void
    {
        $this->y = $y;
    }

    /**
     * @return Collection<int, Cantonnement>
     */
    public function getCodeCef(): Collection
    {
        return $this->code_cef;
    }

    public function addCodeCef(Cantonnement $codeCef): static
    {
        if (!$this->code_cef->contains($codeCef)) {
            $this->code_cef->add($codeCef);
        }

        return $this;
    }

    public function removeCodeCef(Cantonnement $codeCef): static
    {
        $this->code_cef->removeElement($codeCef);

        return $this;
    }

    /**
     * @return Collection<int, ServiceMinef>
     */
    public function getCodeServiceMinef(): Collection
    {
        return $this->code_service_minef;
    }

    public function addCodeServiceMinef(ServiceMinef $codeServiceMinef): static
    {
        if (!$this->code_service_minef->contains($codeServiceMinef)) {
            $this->code_service_minef->add($codeServiceMinef);
        }

        return $this;
    }

    public function removeCodeServiceMinef(ServiceMinef $codeServiceMinef): static
    {
        $this->code_service_minef->removeElement($codeServiceMinef);

        return $this;
    }

    /**
     * @return Collection<int, Direction>
     */
    public function getCodeDirection(): Collection
    {
        return $this->code_direction;
    }

    public function addCodeDirection(Direction $codeDirection): static
    {
        if (!$this->code_direction->contains($codeDirection)) {
            $this->code_direction->add($codeDirection);
        }

        return $this;
    }

    public function removeCodeDirection(Direction $codeDirection): static
    {
        $this->code_direction->removeElement($codeDirection);

        return $this;
    }

    /**
     * @return Collection<int, Foret>
     */
    public function getCodeforet(): Collection
    {
        return $this->codeforet;
    }

    public function addCodeforet(Foret $codeforet): static
    {
        if (!$this->codeforet->contains($codeforet)) {
            $this->codeforet->add($codeforet);
        }

        return $this;
    }

    public function removeCodeforet(Foret $codeforet): static
    {
        $this->codeforet->removeElement($codeforet);

        return $this;
    }

}
