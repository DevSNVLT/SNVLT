<?php

namespace App\Entity\Transformation;

use App\Entity\References\Essence;
use App\Entity\References\TypeTransformation;
use App\Repository\Transformation\FicheJourTransfoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'transformation.fiche_jour_transfo')]
#[ORM\Entity(repositoryClass: FicheJourTransfoRepository::class)]
class FicheJourTransfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_fiche = null;

    #[ORM\ManyToOne(inversedBy: 'ficheJourTransfos')]
    private ?TypeTransformation $TypeTransformation = null;

    #[ORM\ManyToOne(inversedBy: 'ficheJourTransfos')]
    private ?Essence $essence = null;

    #[ORM\OneToMany(mappedBy: 'code_fiche', targetEntity: DetailFicheJourTransfo::class)]
    private Collection $detailFicheJourTransfos;

    public function __construct()
    {
        $this->detailFicheJourTransfos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateFiche(): ?\DateTimeInterface
    {
        return $this->date_fiche;
    }

    public function setDateFiche(\DateTimeInterface $date_fiche): static
    {
        $this->date_fiche = $date_fiche;

        return $this;
    }

    public function getTypeTransformation(): ?TypeTransformation
    {
        return $this->TypeTransformation;
    }

    public function setTypeTransformation(?TypeTransformation $TypeTransformation): static
    {
        $this->TypeTransformation = $TypeTransformation;

        return $this;
    }

    public function getEssence(): ?Essence
    {
        return $this->essence;
    }

    public function setEssence(?Essence $essence): static
    {
        $this->essence = $essence;

        return $this;
    }

    /**
     * @return Collection<int, DetailFicheJourTransfo>
     */
    public function getDetailFicheJourTransfos(): Collection
    {
        return $this->detailFicheJourTransfos;
    }

    public function addDetailFicheJourTransfo(DetailFicheJourTransfo $detailFicheJourTransfo): static
    {
        if (!$this->detailFicheJourTransfos->contains($detailFicheJourTransfo)) {
            $this->detailFicheJourTransfos->add($detailFicheJourTransfo);
            $detailFicheJourTransfo->setCodeFiche($this);
        }

        return $this;
    }

    public function removeDetailFicheJourTransfo(DetailFicheJourTransfo $detailFicheJourTransfo): static
    {
        if ($this->detailFicheJourTransfos->removeElement($detailFicheJourTransfo)) {
            // set the owning side to null (unless already changed)
            if ($detailFicheJourTransfo->getCodeFiche() === $this) {
                $detailFicheJourTransfo->setCodeFiche(null);
            }
        }

        return $this;
    }
}
