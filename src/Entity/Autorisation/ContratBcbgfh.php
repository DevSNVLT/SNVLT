<?php

namespace App\Entity\Autorisation;

use App\Entity\DocStats\Entetes\Documentbcbgfh;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Repository\Autorisation\ContratBcbgfhRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.contrat_bcbgfh')]
#[ORM\Entity(repositoryClass: ContratBcbgfhRepository::class)]
class ContratBcbgfh
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_contrat = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_contrat = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_signature = null;

    #[ORM\Column(nullable: true)]
    private ?int $duree = null;

    #[ORM\Column(nullable: true)]
    private ?int $nb_tiges = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume_cumul = null;

    #[ORM\ManyToOne(inversedBy: 'contratBcbgfhs')]
    private ?Foret $code_foret = null;

    #[ORM\ManyToOne(inversedBy: 'contratBcbgfhs')]
    private ?Exploitant $code_exploitant = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $cretaed_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(nullable: true)]
    private ?bool $statut = null;

    #[ORM\OneToMany(mappedBy: 'code_contrat', targetEntity: Documentbcbgfh::class)]
    private Collection $documentbcbgfhs;

    public function __construct()
    {
        $this->documentbcbgfhs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroContrat(): ?string
    {
        return $this->numero_contrat;
    }

    public function setNumeroContrat(string $numero_contrat): static
    {
        $this->numero_contrat = $numero_contrat;

        return $this;
    }

    public function getDateContrat(): ?\DateTimeInterface
    {
        return $this->date_contrat;
    }

    public function setDateContrat(?\DateTimeInterface $date_contrat): static
    {
        $this->date_contrat = $date_contrat;

        return $this;
    }

    public function getDateSignature(): ?\DateTimeInterface
    {
        return $this->date_signature;
    }

    public function setDateSignature(?\DateTimeInterface $date_signature): static
    {
        $this->date_signature = $date_signature;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getNbTiges(): ?int
    {
        return $this->nb_tiges;
    }

    public function setNbTiges(?int $nb_tiges): static
    {
        $this->nb_tiges = $nb_tiges;

        return $this;
    }

    public function getVolumeCumul(): ?float
    {
        return $this->volume_cumul;
    }

    public function setVolumeCumul(?float $volume_cumul): static
    {
        $this->volume_cumul = $volume_cumul;

        return $this;
    }

    public function getCodeForet(): ?Foret
    {
        return $this->code_foret;
    }

    public function setCodeForet(?Foret $code_foret): static
    {
        $this->code_foret = $code_foret;

        return $this;
    }

    public function getCodeExploitant(): ?Exploitant
    {
        return $this->code_exploitant;
    }

    public function setCodeExploitant(?Exploitant $code_exploitant): static
    {
        $this->code_exploitant = $code_exploitant;

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

    public function getCretaedBy(): ?string
    {
        return $this->cretaed_by;
    }

    public function setCretaedBy(string $cretaed_by): static
    {
        $this->cretaed_by = $cretaed_by;

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
     * @return Collection<int, Documentbcbgfh>
     */
    public function getDocumentbcbgfhs(): Collection
    {
        return $this->documentbcbgfhs;
    }

    public function addDocumentbcbgfh(Documentbcbgfh $documentbcbgfh): static
    {
        if (!$this->documentbcbgfhs->contains($documentbcbgfh)) {
            $this->documentbcbgfhs->add($documentbcbgfh);
            $documentbcbgfh->setCodeContrat($this);
        }

        return $this;
    }

    public function removeDocumentbcbgfh(Documentbcbgfh $documentbcbgfh): static
    {
        if ($this->documentbcbgfhs->removeElement($documentbcbgfh)) {
            // set the owning side to null (unless already changed)
            if ($documentbcbgfh->getCodeContrat() === $this) {
                $documentbcbgfh->setCodeContrat(null);
            }
        }

        return $this;
    }
}
