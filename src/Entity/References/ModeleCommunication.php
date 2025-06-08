<?php

namespace App\Entity\References;

use App\Repository\References\ModeleCommunicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'metier.modele_communication')]
#[UniqueEntity(fields: ['libelle_modele'], message: 'There is already a model with this name')]
#[ORM\Entity(repositoryClass: ModeleCommunicationRepository::class)]
class ModeleCommunication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]

    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $libelle_modele = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $statut = null;

    #[ORM\OneToMany(mappedBy: 'code_modele', targetEntity: DetailsModele::class)]
    private Collection $detailsModeles;

    #[ORM\OneToMany(mappedBy: 'code_modele', targetEntity: CircuitCommunication::class)]
    private Collection $circuitCommunications;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'modeleCommunications')]
    private ?TypeModeleCommunication $code_type_modele_communication = null;

    public function __construct()
    {
        $this->detailsModeles = new ArrayCollection();
        $this->circuitCommunications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelleModele(): ?string
    {
        return $this->libelle_modele;
    }

    public function setLibelleModele(string $libelle_modele): static
    {
        $this->libelle_modele = $libelle_modele;

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
     * @return Collection<int, DetailsModele>
     */
    public function getDetailsModeles(): Collection
    {
        return $this->detailsModeles;
    }

    public function addDetailsModele(DetailsModele $detailsModele): static
    {
        if (!$this->detailsModeles->contains($detailsModele)) {
            $this->detailsModeles->add($detailsModele);
            $detailsModele->setCodeModele($this);
        }

        return $this;
    }

    public function removeDetailsModele(DetailsModele $detailsModele): static
    {
        if ($this->detailsModeles->removeElement($detailsModele)) {
            // set the owning side to null (unless already changed)
            if ($detailsModele->getCodeModele() === $this) {
                $detailsModele->setCodeModele(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CircuitCommunication>
     */
    public function getCircuitCommunications(): Collection
    {
        return $this->circuitCommunications;
    }

    public function addCircuitCommunication(CircuitCommunication $circuitCommunication): static
    {
        if (!$this->circuitCommunications->contains($circuitCommunication)) {
            $this->circuitCommunications->add($circuitCommunication);
            $circuitCommunication->setCodeModele($this);
        }

        return $this;
    }

    public function removeCircuitCommunication(CircuitCommunication $circuitCommunication): static
    {
        if ($this->circuitCommunications->removeElement($circuitCommunication)) {
            // set the owning side to null (unless already changed)
            if ($circuitCommunication->getCodeModele() === $this) {
                $circuitCommunication->setCodeModele(null);
            }
        }

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

    public function getCodeTypeModeleCommunication(): ?TypeModeleCommunication
    {
        return $this->code_type_modele_communication;
    }

    public function setCodeTypeModeleCommunication(?TypeModeleCommunication $code_type_modele_communication): static
    {
        $this->code_type_modele_communication = $code_type_modele_communication;

        return $this;
    }
}
