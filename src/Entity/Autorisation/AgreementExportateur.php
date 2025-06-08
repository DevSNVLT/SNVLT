<?php

namespace App\Entity\Autorisation;

use App\Entity\References\Exportateur;
use App\Repository\Autorisation\AgreementExportateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.agreement_exportateur')]
#[ORM\Entity(repositoryClass: AgreementExportateurRepository::class)]
class AgreementExportateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_decision = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_decision = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_agreement', targetEntity: AutorisationExportateur::class)]
    private Collection $autorisationExportateurs;

    #[ORM\Column(nullable: true)]
    private ?bool $statut = null;

    #[ORM\Column(nullable: true)]
    private ?bool $reprise = null;

    #[ORM\ManyToOne(inversedBy: 'agreementExportateurs')]
    private ?Exportateur $code_exportateur = null;

    public function __construct()
    {
        $this->autorisationExportateurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDecision(): ?string
    {
        return $this->numero_decision;
    }

    public function setNumeroDecision(string $numero_decision): static
    {
        $this->numero_decision = $numero_decision;

        return $this;
    }

    public function getDateDecision(): ?\DateTimeInterface
    {
        return $this->date_decision;
    }

    public function setDateDecision(?\DateTimeInterface $date_decision): static
    {
        $this->date_decision = $date_decision;

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

    /**
     * @return Collection<int, AutorisationExportateur>
     */
    public function getAutorisationExportateurs(): Collection
    {
        return $this->autorisationExportateurs;
    }

    public function addAutorisationExportateur(AutorisationExportateur $autorisationExportateur): static
    {
        if (!$this->autorisationExportateurs->contains($autorisationExportateur)) {
            $this->autorisationExportateurs->add($autorisationExportateur);
            $autorisationExportateur->setCodeAgreement($this);
        }

        return $this;
    }

    public function removeAutorisationExportateur(AutorisationExportateur $autorisationExportateur): static
    {
        if ($this->autorisationExportateurs->removeElement($autorisationExportateur)) {
            // set the owning side to null (unless already changed)
            if ($autorisationExportateur->getCodeAgreement() === $this) {
                $autorisationExportateur->setCodeAgreement(null);
            }
        }

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

    public function isReprise(): ?bool
    {
        return $this->reprise;
    }

    public function setReprise(?bool $reprise): static
    {
        $this->reprise = $reprise;

        return $this;
    }

    public function getCodeExportateur(): ?Exportateur
    {
        return $this->code_exportateur;
    }

    public function setCodeExportateur(?Exportateur $code_exportateur): static
    {
        $this->code_exportateur = $code_exportateur;

        return $this;
    }

    public function __toString(): string
    {
        return $this->numero_decision." ". $this->getCodeExportateur()->getRaisonSocialeExportateur();
    }
}
