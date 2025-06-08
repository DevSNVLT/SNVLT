<?php

namespace App\Entity\Transformation;

use App\Entity\Admin\Exercice;
use App\Entity\References\Essence;
use App\Repository\Transformation\ColisRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'transformation.colis')]
#[ORM\Entity(repositoryClass: ColisRepository::class)]
class Colis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $reference = null;

    #[ORM\ManyToOne(inversedBy: 'colis')]
    private ?Contrat $code_contrat = null;

    #[ORM\ManyToOne(inversedBy: 'colis')]
    private ?Essence $code_essence = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_confection = null;

    #[ORM\Column(nullable: true)]
    private ?int $etat_hygro = null;

    #[ORM\ManyToOne(inversedBy: 'colis')]
    private ?Exercice $exercice = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(nullable: true)]
    private ?bool $cloture = null;

    #[ORM\OneToMany(mappedBy: 'code_colis', targetEntity: ElementsColis::class)]
    private Collection $elementsColis;

    public function __construct()
    {
        $this->elementsColis = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getCodeContrat(): ?Contrat
    {
        return $this->code_contrat;
    }

    public function setCodeContrat(?Contrat $code_contrat): static
    {
        $this->code_contrat = $code_contrat;

        return $this;
    }

    public function getCodeEssence(): ?Essence
    {
        return $this->code_essence;
    }

    public function setCodeEssence(?Essence $code_essence): static
    {
        $this->code_essence = $code_essence;

        return $this;
    }

    public function getDateConfection(): ?\DateTimeInterface
    {
        return $this->date_confection;
    }

    public function setDateConfection(?\DateTimeInterface $date_confection): static
    {
        $this->date_confection = $date_confection;

        return $this;
    }

    public function getEtatHygro(): ?int
    {
        return $this->etat_hygro;
    }

    public function setEtatHygro(?int $etat_hygro): static
    {
        $this->etat_hygro = $etat_hygro;

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

    public function isCloture(): ?bool
    {
        return $this->cloture;
    }

    public function setCloture(?bool $cloture): static
    {
        $this->cloture = $cloture;

        return $this;
    }

    /**
     * @return Collection<int, ElementsColis>
     */
    public function getElementsColis(): Collection
    {
        return $this->elementsColis;
    }

    public function addElementsColi(ElementsColis $elementsColi): static
    {
        if (!$this->elementsColis->contains($elementsColi)) {
            $this->elementsColis->add($elementsColi);
            $elementsColi->setCodeColis($this);
        }

        return $this;
    }

    public function removeElementsColi(ElementsColis $elementsColi): static
    {
        if ($this->elementsColis->removeElement($elementsColi)) {
            // set the owning side to null (unless already changed)
            if ($elementsColi->getCodeColis() === $this) {
                $elementsColi->setCodeColis(null);
            }
        }

        return $this;
    }
}
