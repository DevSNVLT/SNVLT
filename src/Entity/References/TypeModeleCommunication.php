<?php

namespace App\Entity\References;

use App\Repository\References\TypeModeleCommunicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.type_modele_communication')]
#[ORM\Entity(repositoryClass: TypeModeleCommunicationRepository::class)]
class TypeModeleCommunication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'code_type_modele_communication', targetEntity: ModeleCommunication::class)]
    private Collection $modeleCommunications;

    public function __construct()
    {
        $this->modeleCommunications = new ArrayCollection();
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
     * @return Collection<int, ModeleCommunication>
     */
    public function getModeleCommunications(): Collection
    {
        return $this->modeleCommunications;
    }

    public function addModeleCommunication(ModeleCommunication $modeleCommunication): static
    {
        if (!$this->modeleCommunications->contains($modeleCommunication)) {
            $this->modeleCommunications->add($modeleCommunication);
            $modeleCommunication->setCodeTypeModeleCommunication($this);
        }

        return $this;
    }

    public function removeModeleCommunication(ModeleCommunication $modeleCommunication): static
    {
        if ($this->modeleCommunications->removeElement($modeleCommunication)) {
            // set the owning side to null (unless already changed)
            if ($modeleCommunication->getCodeTypeModeleCommunication() === $this) {
                $modeleCommunication->setCodeTypeModeleCommunication(null);
            }
        }

        return $this;
    }
    public function __toString(): string
    {
        return $this->libelle;
    }
}
