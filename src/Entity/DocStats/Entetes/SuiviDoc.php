<?php

namespace App\Entity\DocStats\Entetes;

use App\Entity\Admin\Exercice;
use App\Entity\References\TypeDocumentStatistique;
use App\Repository\DocStats\Entetes\SuiviDocRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.suivi_doc')]
#[ORM\Entity(repositoryClass: SuiviDocRepository::class)]
class SuiviDoc
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_doc = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_delivrance = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $sois_transmis = null;

    #[ORM\ManyToOne(inversedBy: 'suiviDocs')]
    private ?TypeDocumentStatistique $document_type = null;

    #[ORM\Column]
    private ?int $id_doc_genere = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column]
    private ?int $nb_pages_generees = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $operateur = null;

    #[ORM\ManyToOne(inversedBy: 'suiviDocs')]
    private ?Exercice $exercice = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroDoc(): ?string
    {
        return $this->numero_doc;
    }

    public function setNumeroDoc(string $numero_doc): static
    {
        $this->numero_doc = $numero_doc;

        return $this;
    }

    public function getDateDelivrance(): ?\DateTimeInterface
    {
        return $this->date_delivrance;
    }

    public function setDateDelivrance(?\DateTimeInterface $date_delivrance): static
    {
        $this->date_delivrance = $date_delivrance;

        return $this;
    }

    public function getSoisTransmis(): ?string
    {
        return $this->sois_transmis;
    }

    public function setSoisTransmis(?string $sois_transmis): static
    {
        $this->sois_transmis = $sois_transmis;

        return $this;
    }

    public function getDocumentType(): ?TypeDocumentStatistique
    {
        return $this->document_type;
    }

    public function setDocumentType(?TypeDocumentStatistique $document_type): static
    {
        $this->document_type = $document_type;

        return $this;
    }

    public function getIdDocGenere(): ?int
    {
        return $this->id_doc_genere;
    }

    public function setIdDocGenere(int $id_doc_genere): static
    {
        $this->id_doc_genere = $id_doc_genere;

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

    public function getNbPagesGenerees(): ?int
    {
        return $this->nb_pages_generees;
    }

    public function setNbPagesGenerees(int $nb_pages_generees): static
    {
        $this->nb_pages_generees = $nb_pages_generees;

        return $this;
    }

    public function getOperateur(): ?string
    {
        return $this->operateur;
    }

    public function setOperateur(?string $operateur): static
    {
        $this->operateur = $operateur;

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
}
