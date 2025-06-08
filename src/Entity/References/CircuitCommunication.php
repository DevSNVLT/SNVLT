<?php

namespace App\Entity\References;

use App\Entity\Administration\DemandeOperateur;
use App\Repository\References\CircuitCommunicationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.circuit_communication')]
#[ORM\Entity(repositoryClass: CircuitCommunicationRepository::class)]
class CircuitCommunication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'circuitCommunications')]
    private ?ModeleCommunication $code_modele = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $service_validation = null;

    #[ORM\Column(nullable: true)]
    private ?int $num_seq = null;

    #[ORM\ManyToOne(inversedBy: 'circuitCommunications')]
    private ?DocumentOperateur $code_document_operateur = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'circuitCommunications')]
    private ?ServiceMinef $code_service = null;

    #[ORM\ManyToOne(inversedBy: 'circuitCommunications')]
    private ?Direction $code_direction = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $type_service = null;

    #[ORM\Column(nullable: true)]
    private ?bool $valide = null;

    #[ORM\Column(nullable: true)]
    private ?int $service_id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observation = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $operateur = null;

    #[ORM\ManyToOne(inversedBy: 'circuitCommunications')]
    private ?DemandeOperateur $code_demande_operateur = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeModele(): ?ModeleCommunication
    {
        return $this->code_modele;
    }

    public function setCodeModele(?ModeleCommunication $code_modele): static
    {
        $this->code_modele = $code_modele;

        return $this;
    }

    public function getServiceValidation(): ?string
    {
        return $this->service_validation;
    }

    public function setServiceValidation(?string $service_validation): static
    {
        $this->service_validation = $service_validation;

        return $this;
    }

    public function getNumSeq(): ?int
    {
        return $this->num_seq;
    }

    public function setNumSeq(?int $num_seq): static
    {
        $this->num_seq = $num_seq;

        return $this;
    }

    public function getCodeDocumentOperateur(): ?DocumentOperateur
    {
        return $this->code_document_operateur;
    }

    public function setCodeDocumentOperateur(?DocumentOperateur $code_document_operateur): static
    {
        $this->code_document_operateur = $code_document_operateur;

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

    public function getCodeService(): ?ServiceMinef
    {
        return $this->code_service;
    }

    public function setCodeService(?ServiceMinef $code_service): static
    {
        $this->code_service = $code_service;

        return $this;
    }

    public function getCodeDirection(): ?Direction
    {
        return $this->code_direction;
    }

    public function setCodeDirection(?Direction $code_direction): static
    {
        $this->code_direction = $code_direction;

        return $this;
    }

    public function getTypeService(): ?string
    {
        return $this->type_service;
    }

    public function setTypeService(?string $type_service): static
    {
        $this->type_service = $type_service;

        return $this;
    }

    public function isValide(): ?bool
    {
        return $this->valide;
    }

    public function setValide(?bool $valide): static
    {
        $this->valide = $valide;

        return $this;
    }

    public function getServiceId(): ?int
    {
        return $this->service_id;
    }

    public function setServiceId(?int $service_id): static
    {
        $this->service_id = $service_id;

        return $this;
    }

    public function getObservation(): ?string
    {
        return $this->observation;
    }

    public function setObservation(?string $observation): static
    {
        $this->observation = $observation;

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

    public function getOperateur(): ?string
    {
        return $this->operateur;
    }

    public function setOperateur(?string $operateur): static
    {
        $this->operateur = $operateur;

        return $this;
    }

    public function getCodeDemandeOperateur(): ?DemandeOperateur
    {
        return $this->code_demande_operateur;
    }

    public function setCodeDemandeOperateur(?DemandeOperateur $code_demande_operateur): static
    {
        $this->code_demande_operateur = $code_demande_operateur;

        return $this;
    }
}
