<?php

namespace App\Entity\Autorisation;

use App\Entity\Admin\Exercice;
use App\Entity\DocStats\Entetes\Documentbrepf;
use App\Entity\DocStats\Entetes\Documentbth;
use App\Repository\Autorisation\AutorisationExportateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.autorisation_exportateur')]
#[ORM\Entity(repositoryClass: AutorisationExportateurRepository::class)]
class AutorisationExportateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_autorisation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_autorisation = null;

    #[ORM\ManyToOne(inversedBy: 'autorisationExportateurs')]
    private ?Exercice $exercice = null;

    #[ORM\ManyToOne(inversedBy: 'autorisationExportateurs')]
    private ?AgreementExportateur $code_agreement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(nullable: true)]
    private ?bool $reprise = null;

    #[ORM\OneToMany(mappedBy: 'code_autorisation_exportateur', targetEntity: Documentbrepf::class)]
    private Collection $documentbrepfs;

    #[ORM\OneToMany(mappedBy: 'code_autorisation_exportateur', targetEntity: Documentbth::class)]
    private Collection $documentbths;

    public function __construct()
    {
        $this->documentbrepfs = new ArrayCollection();
        $this->documentbths = new ArrayCollection();
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

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): static
    {
        $this->exercice = $exercice;

        return $this;
    }

    public function getCodeAgreement(): ?AgreementExportateur
    {
        return $this->code_agreement;
    }

    public function setCodeAgreement(?AgreementExportateur $code_agreement): static
    {
        $this->code_agreement = $code_agreement;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeInterface $created_at): static
    {
        $this->created_at = $created_at;

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

    public function isReprise(): ?bool
    {
        return $this->reprise;
    }

    public function setReprise(?bool $reprise): static
    {
        $this->reprise = $reprise;

        return $this;
    }

    /**
     * @return Collection<int, Documentbrepf>
     */
    public function getDocumentbrepfs(): Collection
    {
        return $this->documentbrepfs;
    }

    public function addDocumentbrepf(Documentbrepf $documentbrepf): static
    {
        if (!$this->documentbrepfs->contains($documentbrepf)) {
            $this->documentbrepfs->add($documentbrepf);
            $documentbrepf->setCodeAutorisationExportateur($this);
        }

        return $this;
    }

    public function removeDocumentbrepf(Documentbrepf $documentbrepf): static
    {
        if ($this->documentbrepfs->removeElement($documentbrepf)) {
            // set the owning side to null (unless already changed)
            if ($documentbrepf->getCodeAutorisationExportateur() === $this) {
                $documentbrepf->setCodeAutorisationExportateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentbth>
     */
    public function getDocumentbths(): Collection
    {
        return $this->documentbths;
    }

    public function addDocumentbth(Documentbth $documentbth): static
    {
        if (!$this->documentbths->contains($documentbth)) {
            $this->documentbths->add($documentbth);
            $documentbth->setCodeAutorisationExportateur($this);
        }

        return $this;
    }

    public function removeDocumentbth(Documentbth $documentbth): static
    {
        if ($this->documentbths->removeElement($documentbth)) {
            // set the owning side to null (unless already changed)
            if ($documentbth->getCodeAutorisationExportateur() === $this) {
                $documentbth->setCodeAutorisationExportateur(null);
            }
        }

        return $this;
    }
}
