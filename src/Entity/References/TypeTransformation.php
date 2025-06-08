<?php

namespace App\Entity\References;

use App\Entity\DocStats\Saisie\Lignepagedmv;
use App\Entity\Transformation\Billon;
use App\Entity\Transformation\Contrat;
use App\Entity\Transformation\Elements;
use App\Entity\Transformation\FicheJourTransfo;
use App\Entity\Transformation\Pdt2Transfo;
use App\Repository\References\TypeTransformationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'metier.type_transformation')]
#[ORM\Entity(repositoryClass: TypeTransformationRepository::class)]
#[UniqueEntity(fields: ['libelle'], message: 'This name already exists')]
class TypeTransformation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $libelle = null;

    #[ORM\ManyToMany(targetEntity: Usine::class, mappedBy: 'type_transformation')]
    private Collection $usines;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'type_transformation', targetEntity: Lignepagedmv::class)]
    private Collection $lignepagedmvs;

    #[ORM\OneToMany(mappedBy: 'type_transformation', targetEntity: Billon::class)]
    private Collection $billons;

    #[ORM\OneToMany(mappedBy: 'TypeTransformation', targetEntity: FicheJourTransfo::class)]
    private Collection $ficheJourTransfos;

    #[ORM\OneToMany(mappedBy: 'type_transfo', targetEntity: Contrat::class)]
    private Collection $contrats;

    #[ORM\OneToMany(mappedBy: 'type_produit', targetEntity: Pdt2Transfo::class)]
    private Collection $pdt2Transfos;

    #[ORM\OneToMany(mappedBy: 'code_type_transfo', targetEntity: Elements::class)]
    private Collection $elements;


    public function __construct()
    {
        $this->usines = new ArrayCollection();
        $this->lignepagedmvs = new ArrayCollection();
        $this->billons = new ArrayCollection();
        $this->ficheJourTransfos = new ArrayCollection();
        $this->contrats = new ArrayCollection();
        $this->pdt2Transfos = new ArrayCollection();
        $this->elements = new ArrayCollection();
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

    /**
     * @return Collection<int, Usine>
     */
    public function getUsines(): Collection
    {
        return $this->usines;
    }

    public function addUsine(Usine $usine): static
    {
        if (!$this->usines->contains($usine)) {
            $this->usines->add($usine);
            $usine->addTypeTransformation($this);
        }

        return $this;
    }

    public function removeUsine(Usine $usine): static
    {
        if ($this->usines->removeElement($usine)) {
            $usine->removeTypeTransformation($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->libelle;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @param \DateTimeImmutable|null $created_at
     */
    public function setCreatedAt(?\DateTimeImmutable $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return string|null
     */
    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    /**
     * @param string|null $created_by
     */
    public function setCreatedBy(?string $created_by): void
    {
        $this->created_by = $created_by;
    }

    /**
     * @return string|null
     */
    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    /**
     * @param string|null $updated_by
     */
    public function setUpdatedBy(?string $updated_by): void
    {
        $this->updated_by = $updated_by;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * @param \DateTimeInterface|null $updated_at
     */
    public function setUpdatedAt(?\DateTimeInterface $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return Collection<int, Lignepagedmv>
     */
    public function getLignepagedmvs(): Collection
    {
        return $this->lignepagedmvs;
    }

    public function addLignepagedmv(Lignepagedmv $lignepagedmv): static
    {
        if (!$this->lignepagedmvs->contains($lignepagedmv)) {
            $this->lignepagedmvs->add($lignepagedmv);
            $lignepagedmv->setTypeTransformation($this);
        }

        return $this;
    }

    public function removeLignepagedmv(Lignepagedmv $lignepagedmv): static
    {
        if ($this->lignepagedmvs->removeElement($lignepagedmv)) {
            // set the owning side to null (unless already changed)
            if ($lignepagedmv->getTypeTransformation() === $this) {
                $lignepagedmv->setTypeTransformation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Billon>
     */
    public function getBillons(): Collection
    {
        return $this->billons;
    }

    public function addBillon(Billon $billon): static
    {
        if (!$this->billons->contains($billon)) {
            $this->billons->add($billon);
            $billon->setTypeTransformation($this);
        }

        return $this;
    }

    public function removeBillon(Billon $billon): static
    {
        if ($this->billons->removeElement($billon)) {
            // set the owning side to null (unless already changed)
            if ($billon->getTypeTransformation() === $this) {
                $billon->setTypeTransformation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FicheJourTransfo>
     */
    public function getFicheJourTransfos(): Collection
    {
        return $this->ficheJourTransfos;
    }

    public function addFicheJourTransfo(FicheJourTransfo $ficheJourTransfo): static
    {
        if (!$this->ficheJourTransfos->contains($ficheJourTransfo)) {
            $this->ficheJourTransfos->add($ficheJourTransfo);
            $ficheJourTransfo->setTypeTransformation($this);
        }

        return $this;
    }

    public function removeFicheJourTransfo(FicheJourTransfo $ficheJourTransfo): static
    {
        if ($this->ficheJourTransfos->removeElement($ficheJourTransfo)) {
            // set the owning side to null (unless already changed)
            if ($ficheJourTransfo->getTypeTransformation() === $this) {
                $ficheJourTransfo->setTypeTransformation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contrat>
     */
    public function getContrats(): Collection
    {
        return $this->contrats;
    }

    public function addContrat(Contrat $contrat): static
    {
        if (!$this->contrats->contains($contrat)) {
            $this->contrats->add($contrat);
            $contrat->setTypeTransfo($this);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): static
    {
        if ($this->contrats->removeElement($contrat)) {
            // set the owning side to null (unless already changed)
            if ($contrat->getTypeTransfo() === $this) {
                $contrat->setTypeTransfo(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pdt2Transfo>
     */
    public function getPdt2Transfos(): Collection
    {
        return $this->pdt2Transfos;
    }

    public function addPdt2Transfo(Pdt2Transfo $pdt2Transfo): static
    {
        if (!$this->pdt2Transfos->contains($pdt2Transfo)) {
            $this->pdt2Transfos->add($pdt2Transfo);
            $pdt2Transfo->setTypeProduit($this);
        }

        return $this;
    }

    public function removePdt2Transfo(Pdt2Transfo $pdt2Transfo): static
    {
        if ($this->pdt2Transfos->removeElement($pdt2Transfo)) {
            // set the owning side to null (unless already changed)
            if ($pdt2Transfo->getTypeProduit() === $this) {
                $pdt2Transfo->setTypeProduit(null);
            }
        }

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
            $element->setCodeTypeTransfo($this);
        }

        return $this;
    }

    public function removeElement(Elements $element): static
    {
        if ($this->elements->removeElement($element)) {
            // set the owning side to null (unless already changed)
            if ($element->getCodeTypeTransfo() === $this) {
                $element->setCodeTypeTransfo(null);
            }
        }

        return $this;
    }


}
