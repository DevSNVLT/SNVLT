<?php

namespace App\Entity\References;

use App\Entity\User;
use App\Repository\References\CaroiRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'observateur.caroi')]
#[ORM\Entity(repositoryClass: CaroiRepository::class)]
class Caroi
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_prenoms = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $service_minef = null;

    #[ORM\ManyToOne(inversedBy: 'carois')]
    private ?User $code_user = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fonction = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $craeted_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPrenoms(): ?string
    {
        return $this->nom_prenoms;
    }

    public function setNomPrenoms(string $nom_prenoms): static
    {
        $this->nom_prenoms = $nom_prenoms;

        return $this;
    }

    public function getServiceMinef(): ?string
    {
        return $this->service_minef;
    }

    public function setServiceMinef(?string $service_minef): static
    {
        $this->service_minef = $service_minef;

        return $this;
    }

    public function getCodeUser(): ?User
    {
        return $this->code_user;
    }

    public function setCodeUser(?User $code_user): static
    {
        $this->code_user = $code_user;

        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(?string $fonction): static
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function getCraetedAt(): ?\DateTimeInterface
    {
        return $this->craeted_at;
    }

    public function setCraetedAt(\DateTimeInterface $craeted_at): static
    {
        $this->craeted_at = $craeted_at;

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
}
