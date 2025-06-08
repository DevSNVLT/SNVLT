<?php

namespace App\Entity\Transformation;

use App\Entity\DocStats\Saisie\Lignepagelje;
use App\Entity\References\TypeTransformation;
use App\Repository\Transformation\BillonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'transformation.billon')]
#[ORM\Entity(repositoryClass: BillonRepository::class)]
class Billon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $numero_billon = null;

    #[ORM\Column]
    private ?int $lng = null;

    #[ORM\Column]
    private ?int $dm = null;

    #[ORM\Column]
    private ?float $volume = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_billonnage = null;

    #[ORM\Column(nullable: true)]
    private ?int $nb_elements = null;

    #[ORM\ManyToOne(inversedBy: 'billons')]
    private ?Lignepagelje $code_lignepagelje = null;

    #[ORM\ManyToOne(inversedBy: 'billons')]
    private ?TypeTransformation $type_transformation = null;

    #[ORM\OneToMany(mappedBy: 'code_billon', targetEntity: Elements::class)]
    private Collection $elements;

    #[ORM\Column(nullable: true)]
    private ?bool $cloture = null;

    #[ORM\Column(nullable: true)]
    private ?bool $rebus = null;

    #[ORM\ManyToOne(inversedBy: 'billons')]
    private ?FicheLot $code_lot = null;

    public function __construct()
    {
        $this->elements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroBillon(): ?string
    {
        return $this->numero_billon;
    }

    public function setNumeroBillon(string $numero_billon): static
    {
        $this->numero_billon = $numero_billon;

        return $this;
    }

    public function getLng(): ?int
    {
        return $this->lng;
    }

    public function setLng(int $lng): static
    {
        $this->lng = $lng;

        return $this;
    }

    public function getDm(): ?int
    {
        return $this->dm;
    }

    public function setDm(int $dm): static
    {
        $this->dm = $dm;

        return $this;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function setVolume(float $volume): static
    {
        $this->volume = $volume;

        return $this;
    }

    public function getDateBillonnage(): ?\DateTimeInterface
    {
        return $this->date_billonnage;
    }

    public function setDateBillonnage(\DateTimeInterface $date_billonnage): static
    {
        $this->date_billonnage = $date_billonnage;

        return $this;
    }

    public function getNbElements(): ?int
    {
        return $this->nb_elements;
    }

    public function setNbElements(?int $nb_elements): static
    {
        $this->nb_elements = $nb_elements;

        return $this;
    }

    public function getCodeLignepagelje(): ?Lignepagelje
    {
        return $this->code_lignepagelje;
    }

    public function setCodeLignepagelje(?Lignepagelje $code_lignepagelje): static
    {
        $this->code_lignepagelje = $code_lignepagelje;

        return $this;
    }

    public function getTypeTransformation(): ?TypeTransformation
    {
        return $this->type_transformation;
    }

    public function setTypeTransformation(?TypeTransformation $type_transformation): static
    {
        $this->type_transformation = $type_transformation;

        return $this;
    }

    /**
     * @return Collection<int, Elements>
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(Elements $element): static
    {
        if (!$this->elements->contains($element)) {
            $this->elements->add($element);
            $element->setCodeBillon($this);
        }

        return $this;
    }

    public function removeElement(Elements $element): static
    {
        if ($this->elements->removeElement($element)) {
            // set the owning side to null (unless already changed)
            if ($element->getCodeBillon() === $this) {
                $element->setCodeBillon(null);
            }
        }

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

    public function isRebus(): ?bool
    {
        return $this->rebus;
    }

    public function setRebus(?bool $rebus): static
    {
        $this->rebus = $rebus;

        return $this;
    }

    public function getCodeLot(): ?FicheLot
    {
        return $this->code_lot;
    }

    public function setCodeLot(?FicheLot $code_lot): static
    {
        $this->code_lot = $code_lot;

        return $this;
    }
}
