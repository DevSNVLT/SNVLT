<?php

namespace App\Entity\Administration;

use App\Entity\References\Imprimeur;
use App\Entity\References\TypeDocumentStatistique;
use App\Repository\Administration\StockDocRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.stock_doc')]
#[ORM\Entity(repositoryClass: StockDocRepository::class)]
class StockDoc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $qte = null;

    #[ORM\Column(length: 255)]
    private ?string $type_mouvement = null;

    #[ORM\Column(nullable: true)]
    private ?int $solde = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'stockDocs')]
    private ?TypeDocumentStatistique $code_type_doc_stat = null;

    #[ORM\OneToMany(mappedBy: 'code_type_doc', targetEntity: DocStatsGen::class)]
    private Collection $docStatsGens;

    #[ORM\Column(length: 1, nullable: true)]
    private ?string $lettre = null;

    #[ORM\ManyToOne(inversedBy: 'stockDocs')]
    private ?Imprimeur $code_imprimeur = null;

    public function __construct()
    {
        $this->docStatsGens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQte(): ?int
    {
        return $this->qte;
    }

    public function setQte(int $qte): static
    {
        $this->qte = $qte;

        return $this;
    }

    public function getTypeMouvement(): ?string
    {
        return $this->type_mouvement;
    }

    public function setTypeMouvement(string $type_mouvement): static
    {
        $this->type_mouvement = $type_mouvement;

        return $this;
    }

    public function getSolde(): ?int
    {
        return $this->solde;
    }

    public function setSolde(?int $solde): static
    {
        $this->solde = $solde;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): static
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

    public function getCodeTypeDocStat(): ?TypeDocumentStatistique
    {
        return $this->code_type_doc_stat;
    }

    public function setCodeTypeDocStat(?TypeDocumentStatistique $code_type_doc_stat): static
    {
        $this->code_type_doc_stat = $code_type_doc_stat;

        return $this;
    }

    /**
     * @return Collection<int, DocStatsGen>
     */
    public function getDocStatsGens(): Collection
    {
        return $this->docStatsGens;
    }

    public function addDocStatsGen(DocStatsGen $docStatsGen): static
    {
        if (!$this->docStatsGens->contains($docStatsGen)) {
            $this->docStatsGens->add($docStatsGen);
            $docStatsGen->setCodeTypeDoc($this);
        }

        return $this;
    }

    public function removeDocStatsGen(DocStatsGen $docStatsGen): static
    {
        if ($this->docStatsGens->removeElement($docStatsGen)) {
            // set the owning side to null (unless already changed)
            if ($docStatsGen->getCodeTypeDoc() === $this) {
                $docStatsGen->setCodeTypeDoc(null);
            }
        }

        return $this;
    }

    public function getLettre(): ?string
    {
        return $this->lettre;
    }

    public function setLettre(string $lettre): static
    {
        $this->lettre = $lettre;

        return $this;
    }

    public function getCodeImprimeur(): ?Imprimeur
    {
        return $this->code_imprimeur;
    }

    public function setCodeImprimeur(?Imprimeur $code_imprimeur): static
    {
        $this->code_imprimeur = $code_imprimeur;

        return $this;
    }
}
