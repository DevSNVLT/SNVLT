<?php

namespace App\Entity\References;

use App\Repository\References\GrilleLegaliteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.grille_legalite')]
#[ORM\Entity(repositoryClass: GrilleLegaliteRepository::class)]
class GrilleLegalite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle_document = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description_document = null;

    #[ORM\Column(nullable: true)]
    private ?int $duree = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $periodicite = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'grilleLegalites')]
    private ?TypeOperateur $code_operateur = null;

    #[ORM\OneToMany(mappedBy: 'code_document_grille', targetEntity: DocumentOperateur::class)]
    private Collection $documentOperateurs;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\ManyToMany(targetEntity: TypeAutorisation::class, mappedBy: 'code_doc_grille')]
    private Collection $typeAutorisations;

    public function __construct()
    {
        $this->documentOperateurs = new ArrayCollection();
        $this->typeAutorisations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleDocument(): ?string
    {
        return $this->libelle_document;
    }

    public function setLibelleDocument(string $libelle_document): static
    {
        $this->libelle_document = $libelle_document;

        return $this;
    }

    public function getDescriptionDocument(): ?string
    {
        return $this->description_document;
    }

    public function setDescriptionDocument(?string $description_document): static
    {
        $this->description_document = $description_document;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getPeriodicite(): ?string
    {
        return $this->periodicite;
    }

    public function setPeriodicite(?string $periodicite): static
    {
        $this->periodicite = $periodicite;

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

    public function getCodeOperateur(): ?TypeOperateur
    {
        return $this->code_operateur;
    }

    public function setCodeOperateur(?TypeOperateur $code_operateur): static
    {
        $this->code_operateur = $code_operateur;

        return $this;
    }

    /**
     * @return Collection<int, DocumentOperateur>
     */
    public function getDocumentOperateurs(): Collection
    {
        return $this->documentOperateurs;
    }

    public function addDocumentOperateur(DocumentOperateur $documentOperateur): static
    {
        if (!$this->documentOperateurs->contains($documentOperateur)) {
            $this->documentOperateurs->add($documentOperateur);
            $documentOperateur->setCodeDocumentGrille($this);
        }

        return $this;
    }

    public function removeDocumentOperateur(DocumentOperateur $documentOperateur): static
    {
        if ($this->documentOperateurs->removeElement($documentOperateur)) {
            // set the owning side to null (unless already changed)
            if ($documentOperateur->getCodeDocumentGrille() === $this) {
                $documentOperateur->setCodeDocumentGrille(null);
            }
        }

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

    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    public function setUpdatedBy(?string $updated_by): static
    {
        $this->updated_by = $updated_by;

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
    public function __toString(): string
    {
        return $this->libelle_document;
    }

    /**
     * @return Collection<int, TypeAutorisation>
     */
    public function getTypeAutorisations(): Collection
    {
        return $this->typeAutorisations;
    }

    public function addTypeAutorisation(TypeAutorisation $typeAutorisation): static
    {
        if (!$this->typeAutorisations->contains($typeAutorisation)) {
            $this->typeAutorisations->add($typeAutorisation);
            $typeAutorisation->addCodeDocGrille($this);
        }

        return $this;
    }

    public function removeTypeAutorisation(TypeAutorisation $typeAutorisation): static
    {
        if ($this->typeAutorisations->removeElement($typeAutorisation)) {
            $typeAutorisation->removeCodeDocGrille($this);
        }

        return $this;
    }
}
