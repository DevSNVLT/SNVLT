<?php

namespace App\Entity\Transformation;

use App\Entity\References\Essence;
use App\Entity\References\TypeTransformation;
use App\Repository\Transformation\ElementsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'transformation.elements')]
#[ORM\Entity(repositoryClass: ElementsRepository::class)]
class Elements
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $lng = null;

    #[ORM\Column(nullable: true)]
    private ?int $lrg = null;

    #[ORM\Column(nullable: true)]
    private ?int $ep = null;

    #[ORM\Column(nullable: true)]
    private ?int $nombre = null;

    #[ORM\ManyToOne(inversedBy: 'elements')]
    private ?Billon $code_billon = null;

    #[ORM\ManyToOne(inversedBy: 'elements')]
    private ?Contrat $code_contrat = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_enr = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume = null;

    #[ORM\OneToMany(mappedBy: 'code_elements', targetEntity: ElementsColis::class)]
    private Collection $elementsColis;

    #[ORM\ManyToOne(inversedBy: 'elements')]
    private ?FicheLotProd $code_fiche_prod = null;

    #[ORM\ManyToOne(inversedBy: 'elements')]
    private ?FicheLot $code_fiche_tronconnage = null;

    #[ORM\ManyToOne(inversedBy: 'elements')]
    private ?TypeTransformation $code_type_transfo = null;

    #[ORM\ManyToOne(inversedBy: 'elements')]
    private ?Essence $code_essence = null;


    public function __construct()
    {
        $this->elementsColis = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLng(): ?int
    {
        return $this->lng;
    }

    public function setLng(?int $lng): static
    {
        $this->lng = $lng;

        return $this;
    }

    public function getLrg(): ?int
    {
        return $this->lrg;
    }

    public function setLrg(?int $lrg): static
    {
        $this->lrg = $lrg;

        return $this;
    }

    public function getEp(): ?int
    {
        return $this->ep;
    }

    public function setEp(?int $ep): static
    {
        $this->ep = $ep;

        return $this;
    }

    public function getNombre(): ?int
    {
        return $this->nombre;
    }

    public function setNombre(?int $nombre): static
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getCodeBillon(): ?Billon
    {
        return $this->code_billon;
    }

    public function setCodeBillon(?Billon $code_billon): static
    {
        $this->code_billon = $code_billon;

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

    public function getDateEnr(): ?\DateTimeInterface
    {
        return $this->date_enr;
    }

    public function setDateEnr(?\DateTimeInterface $date_enr): static
    {
        $this->date_enr = $date_enr;

        return $this;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function setVolume(?float $volume): static
    {
        $this->volume = $volume;

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
            $elementsColi->setCodeElements($this);
        }

        return $this;
    }

    public function removeElementsColi(ElementsColis $elementsColi): static
    {
        if ($this->elementsColis->removeElement($elementsColi)) {
            // set the owning side to null (unless already changed)
            if ($elementsColi->getCodeElements() === $this) {
                $elementsColi->setCodeElements(null);
            }
        }

        return $this;
    }

    public function getCodeFicheProd(): ?FicheLotProd
    {
        return $this->code_fiche_prod;
    }

    public function setCodeFicheProd(?FicheLotProd $code_fiche_prod): static
    {
        $this->code_fiche_prod = $code_fiche_prod;

        return $this;
    }

    public function getCodeFicheTronconnage(): ?FicheLot
    {
        return $this->code_fiche_tronconnage;
    }

    public function setCodeFicheTronconnage(?FicheLot $code_fiche_tronconnage): static
    {
        $this->code_fiche_tronconnage = $code_fiche_tronconnage;

        return $this;
    }

    public function getCodeTypeTransfo(): ?TypeTransformation
    {
        return $this->code_type_transfo;
    }

    public function setCodeTypeTransfo(?TypeTransformation $code_type_transfo): static
    {
        $this->code_type_transfo = $code_type_transfo;

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
}
