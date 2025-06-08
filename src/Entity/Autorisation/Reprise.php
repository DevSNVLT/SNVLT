<?php

namespace App\Entity\Autorisation;

use App\Entity\Admin\Exercice;
use App\Entity\Administration\DemandeOperateur;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Entetes\Documentcp;
use App\Repository\Autorisations\RepriseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.reprise')]
#[ORM\Entity(repositoryClass: RepriseRepository::class)]
class Reprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?string $numero_autorisation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_autorisation = null;

    #[ORM\ManyToOne(inversedBy: 'reprises')]
    private ?Attribution $code_attribution = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(nullable: true)]
    private ?bool $validation_document = null;

    #[ORM\OneToMany(mappedBy: 'code_reprise', targetEntity: DemandeOperateur::class)]
    private Collection $demandeOperateurs;

    #[ORM\OneToMany(mappedBy: 'code_reprise', targetEntity: Documentcp::class)]
    private Collection $documentcps;

    #[ORM\OneToMany(mappedBy: 'code_reprise', targetEntity: Documentbrh::class)]
    private Collection $documentbrhs;

    #[ORM\ManyToOne(inversedBy: 'reprises')]
    private ?Exercice $exercice = null;

    #[ORM\Column(nullable: true)]
    private ?bool $statut = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $motif = null;

    public function __construct()
    {
        $this->demandeOperateurs = new ArrayCollection();
        $this->documentcps = new ArrayCollection();
        $this->documentbrhs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroAutorisation(): ?string
    {
        return $this->numero_autorisation;
    }

    public function setNumeroAutorisation(string $numero_autorisation): static
    {
        $this->numero_autorisation = $numero_autorisation;

        return $this;
    }

    public function getDateAutorisation(): ?\DateTimeInterface
    {
        return $this->date_autorisation;
    }

    public function setDateAutorisation(?\DateTimeInterface $date_autorisation): static
    {
        $this->date_autorisation = $date_autorisation;

        return $this;
    }

    public function getCodeAttribution(): ?Attribution
    {
        return $this->code_attribution;
    }

    public function setCodeAttribution(?Attribution $code_attribution): static
    {
        $this->code_attribution = $code_attribution;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

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

    public function isValidationDocument(): ?bool
    {
        return $this->validation_document;
    }

    public function setValidationDocument(?bool $validation_document): static
    {
        $this->validation_document = $validation_document;

        return $this;
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
            $demandeOperateur->setCodeReprise($this);
        }

        return $this;
    }

    public function removeDemandeOperateur(DemandeOperateur $demandeOperateur): static
    {
        if ($this->demandeOperateurs->removeElement($demandeOperateur)) {
            // set the owning side to null (unless already changed)
            if ($demandeOperateur->getCodeReprise() === $this) {
                $demandeOperateur->setCodeReprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentcp>
     */
    public function getDocumentcps(): Collection
    {
        return $this->documentcps;
    }

    public function addDocumentcp(Documentcp $documentcp): static
    {
        if (!$this->documentcps->contains($documentcp)) {
            $this->documentcps->add($documentcp);
            $documentcp->setCodeReprise($this);
        }

        return $this;
    }

    public function removeDocumentcp(Documentcp $documentcp): static
    {
        if ($this->documentcps->removeElement($documentcp)) {
            // set the owning side to null (unless already changed)
            if ($documentcp->getCodeReprise() === $this) {
                $documentcp->setCodeReprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentbrh>
     */
    public function getDocumentbrhs(): Collection
    {
        return $this->documentbrhs;
    }

    public function addDocumentbrh(Documentbrh $documentbrh): static
    {
        if (!$this->documentbrhs->contains($documentbrh)) {
            $this->documentbrhs->add($documentbrh);
            $documentbrh->setCodeReprise($this);
        }

        return $this;
    }

    public function removeDocumentbrh(Documentbrh $documentbrh): static
    {
        if ($this->documentbrhs->removeElement($documentbrh)) {
            // set the owning side to null (unless already changed)
            if ($documentbrh->getCodeReprise() === $this) {
                $documentbrh->setCodeReprise(null);
            }
        }

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

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(?bool $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMotif(): ?string
    {
        return $this->motif;
    }

    /**
     * @param string|null $motif
     */
    public function setMotif(?string $motif): void
    {
        $this->motif = $motif;
    }


}
