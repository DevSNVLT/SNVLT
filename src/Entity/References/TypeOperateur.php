<?php

namespace App\Entity\References;

use App\Entity\Administration\DemandeOperateur;
use App\Entity\Administration\Gadget;
use App\Entity\Helper\Media;
use App\Entity\User;
use App\Repository\References\TypeOperateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'metier.type_operateur')]
#[ORM\Entity(repositoryClass: TypeOperateurRepository::class)]
#[UniqueEntity(fields: ['libelle_operateur'], message: 'This name already exists')]
class TypeOperateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $libelle_operateur = null;

    #[ORM\OneToMany(mappedBy: 'code_operateur', targetEntity: User::class)]
    private Collection $utilisateurs;

    #[ORM\OneToMany(mappedBy: 'code_operateur', targetEntity: GrilleLegalite::class)]
    private Collection $grilleLegalites;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'code_operateur', targetEntity: DemandeOperateur::class)]
    private Collection $demandeOperateurs;

    #[ORM\OneToMany(mappedBy: 'type_operateur', targetEntity: DocumentOperateur::class)]
    private Collection $documentOperateurs;

    #[ORM\OneToMany(mappedBy: 'type_operateur', targetEntity: TypeAutorisation::class)]
    private Collection $typeAutorisations;

    #[ORM\OneToMany(mappedBy: 'code_type_operateur', targetEntity: TypeDocumentStatistique::class)]
    private Collection $typeDocumentStatistiques;

    #[ORM\ManyToMany(targetEntity: Gadget::class, mappedBy: 'code_operateur')]
    private Collection $gadgets;

    #[ORM\ManyToMany(targetEntity: Media::class, mappedBy: 'code_operateur')]
    private Collection $media;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
        $this->grilleLegalites = new ArrayCollection();
        $this->demandeOperateurs = new ArrayCollection();
        $this->documentOperateurs = new ArrayCollection();
        $this->typeAutorisations = new ArrayCollection();
        $this->typeDocumentStatistiques = new ArrayCollection();
        $this->gadgets = new ArrayCollection();
        $this->media = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleOperateur(): ?string
    {
        return $this->libelle_operateur;
    }

    public function setLibelleOperateur(?string $libelle_operateur): static
    {
        $this->libelle_operateur = $libelle_operateur;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUser(User $utilisateur): static
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->add($utilisateur);
            $utilisateur->setCodeOperateur($this);
        }

        return $this;
    }

    public function removeUser(User $utilisateur): static
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getCodeOperateur() === $this) {
                $utilisateur->setCodeOperateur(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->libelle_operateur;
    }

    /**
     * @return Collection<int, GrilleLegalite>
     */
    public function getGrilleLegalites(): Collection
    {
        return $this->grilleLegalites;
    }

    public function addGrilleLegalite(GrilleLegalite $grilleLegalite): static
    {
        if (!$this->grilleLegalites->contains($grilleLegalite)) {
            $this->grilleLegalites->add($grilleLegalite);
            $grilleLegalite->setCodeOperateur($this);
        }

        return $this;
    }

    public function removeGrilleLegalite(GrilleLegalite $grilleLegalite): static
    {
        if ($this->grilleLegalites->removeElement($grilleLegalite)) {
            // set the owning side to null (unless already changed)
            if ($grilleLegalite->getCodeOperateur() === $this) {
                $grilleLegalite->setCodeOperateur(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getUtilisateurs(): ArrayCollection|Collection
    {
        return $this->utilisateurs;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @return string|null
     */
    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    /**
     * @return string|null
     */
    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * @return Collection<int, DemandeOperateur>
     */
    public function getDemandeOperateurs(): Collection
    {
        return $this->demandeOperateurs;
    }

    public function addDemandeOperateur(DemandeOperateur $demandeOperateur): static
    {
        if (!$this->demandeOperateurs->contains($demandeOperateur)) {
            $this->demandeOperateurs->add($demandeOperateur);
            $demandeOperateur->setCodeOperateur($this);
        }

        return $this;
    }

    public function removeDemandeOperateur(DemandeOperateur $demandeOperateur): static
    {
        if ($this->demandeOperateurs->removeElement($demandeOperateur)) {
            // set the owning side to null (unless already changed)
            if ($demandeOperateur->getCodeOperateur() === $this) {
                $demandeOperateur->setCodeOperateur(null);
            }
        }

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
            $documentOperateur->setTypeOperateur($this);
        }

        return $this;
    }

    public function removeDocumentOperateur(DocumentOperateur $documentOperateur): static
    {
        if ($this->documentOperateurs->removeElement($documentOperateur)) {
            // set the owning side to null (unless already changed)
            if ($documentOperateur->getTypeOperateur() === $this) {
                $documentOperateur->setTypeOperateur(null);
            }
        }

        return $this;
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
            $typeAutorisation->setTypeOperateur($this);
        }

        return $this;
    }

    public function removeTypeAutorisation(TypeAutorisation $typeAutorisation): static
    {
        if ($this->typeAutorisations->removeElement($typeAutorisation)) {
            // set the owning side to null (unless already changed)
            if ($typeAutorisation->getTypeOperateur() === $this) {
                $typeAutorisation->setTypeOperateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TypeDocumentStatistique>
     */
    public function getTypeDocumentStatistiques(): Collection
    {
        return $this->typeDocumentStatistiques;
    }

    public function addTypeDocumentStatistique(TypeDocumentStatistique $typeDocumentStatistique): static
    {
        if (!$this->typeDocumentStatistiques->contains($typeDocumentStatistique)) {
            $this->typeDocumentStatistiques->add($typeDocumentStatistique);
            $typeDocumentStatistique->setCodeTypeOperateur($this);
        }

        return $this;
    }

    public function removeTypeDocumentStatistique(TypeDocumentStatistique $typeDocumentStatistique): static
    {
        if ($this->typeDocumentStatistiques->removeElement($typeDocumentStatistique)) {
            // set the owning side to null (unless already changed)
            if ($typeDocumentStatistique->getCodeTypeOperateur() === $this) {
                $typeDocumentStatistique->setCodeTypeOperateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Gadget>
     */
    public function getGadgets(): Collection
    {
        return $this->gadgets;
    }

    public function addGadget(Gadget $gadget): static
    {
        if (!$this->gadgets->contains($gadget)) {
            $this->gadgets->add($gadget);
            $gadget->addCodeOperateur($this);
        }

        return $this;
    }

    public function removeGadget(Gadget $gadget): static
    {
        if ($this->gadgets->removeElement($gadget)) {
            $gadget->removeCodeOperateur($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->media;
    }

    public function addMedium(Media $medium): static
    {
        if (!$this->media->contains($medium)) {
            $this->media->add($medium);
            $medium->addCodeOperateur($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): static
    {
        if ($this->media->removeElement($medium)) {
            $medium->removeCodeOperateur($this);
        }

        return $this;
    }

}
