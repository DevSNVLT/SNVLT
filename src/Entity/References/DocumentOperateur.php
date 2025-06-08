<?php

namespace App\Entity\References;

use App\Entity\User;
use App\Repository\DocumentOperateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Table(name: 'metier.document_operateur')]
#[ORM\Entity(repositoryClass: DocumentOperateurRepository::class)]
#[Vich\Uploadable]
class DocumentOperateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;



    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;


    #[Vich\UploadableField(mapping: 'docs_operateurs', fileNameProperty: 'imageName')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: false)]
    private ?string $imageName = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $statut = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_etablissement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_expiration = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'documentOperateurs')]
    private ?GrilleLegalite $code_document_grille = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'documentOperateurs')]
    private ?TypeOperateur $type_operateur = null;

    #[ORM\Column(nullable: true)]
    private ?int $codeOperateur = null;

    #[ORM\OneToMany(mappedBy: 'code_document_operateur', targetEntity: CircuitCommunication::class)]
    private Collection $circuitCommunications;

    #[ORM\ManyToOne(inversedBy: 'documentOperateurs')]
    private ?User $demandeur_id = null;

    public function __construct()
    {
        $this->circuitCommunications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }



    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }


    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
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

    public function getDateEtablissement(): ?\DateTimeInterface
    {
        return $this->date_etablissement;
    }

    public function setDateEtablissement(?\DateTimeInterface $date_etablissement): static
    {
        $this->date_etablissement = $date_etablissement;

        return $this;
    }

    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->date_expiration;
    }

    public function setDateExpiration(?\DateTimeInterface $date_expiration): static
    {
        $this->date_expiration = $date_expiration;

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

    /**
     * @return \DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeImmutable|null $updatedAt
     */
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCodeDocumentGrille(): ?GrilleLegalite
    {
        return $this->code_document_grille;
    }

    public function setCodeDocumentGrille(?GrilleLegalite $code_document_grille): static
    {
        $this->code_document_grille = $code_document_grille;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    /**
     * @param string|null $updated_by
     */
    public function setUpdatedBy(?string $updated_by): void
    {
        $this->updated_by = $updated_by;
    }
/*public function __toString(): string
{
    return $this->code_document_grille->getLibelleDocument();
}*/

public function getTypeOperateur(): ?TypeOperateur
{
    return $this->type_operateur;
}

public function setTypeOperateur(?TypeOperateur $type_operateur): static
{
    $this->type_operateur = $type_operateur;

    return $this;
}

public function getCodeOperateur(): ?int
{
    return $this->codeOperateur;
}

public function setCodeOperateur(int $codeOperateur): static
{
    $this->codeOperateur = $codeOperateur;

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
        $circuitCommunication->setCodeDocumentOperateur($this);
    }

    return $this;
}

public function removeCircuitCommunication(CircuitCommunication $circuitCommunication): static
{
    if ($this->circuitCommunications->removeElement($circuitCommunication)) {
        // set the owning side to null (unless already changed)
        if ($circuitCommunication->getCodeDocumentOperateur() === $this) {
            $circuitCommunication->setCodeDocumentOperateur(null);
        }
    }

    return $this;
}

public function getDemandeurId(): ?User
{
    return $this->demandeur_id;
}

public function setDemandeurId(?User $demandeur_id): static
{
    $this->demandeur_id = $demandeur_id;

    return $this;
}

}
