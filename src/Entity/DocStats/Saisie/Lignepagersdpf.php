<?php

namespace App\Entity\DocStats\Saisie;

use App\Entity\DocStats\Pages\Pagersdpf;
use App\Entity\References\ProduitsUsine;
use App\Entity\References\Usine;
use App\Repository\DocStats\Saisie\LignepagersdpfRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.lignepagersdpf')]
#[ORM\Entity(repositoryClass: LignepagersdpfRepository::class)]
class Lignepagersdpf
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_entree = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagersdpfs')]
    private ?ProduitsUsine $nature_produit = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume_entre = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagersdpfs')]
    private ?Usine $usine_origine = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_sortie = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume_sorti = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $destination = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagersdpfs')]
    private ?Pagersdpf $code_pagersdpf = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateEntree(): ?\DateTimeInterface
    {
        return $this->date_entree;
    }

    public function setDateEntree(?\DateTimeInterface $date_entree): static
    {
        $this->date_entree = $date_entree;

        return $this;
    }

    public function getNatureProduit(): ?ProduitsUsine
    {
        return $this->nature_produit;
    }

    public function setNatureProduit(?ProduitsUsine $nature_produit): static
    {
        $this->nature_produit = $nature_produit;

        return $this;
    }

    public function getVolumeEntre(): ?float
    {
        return $this->volume_entre;
    }

    public function setVolumeEntre(float $volume_entre): static
    {
        $this->volume_entre = $volume_entre;

        return $this;
    }

    public function getUsineOrigine(): ?Usine
    {
        return $this->usine_origine;
    }

    public function setUsineOrigine(?Usine $usine_origine): static
    {
        $this->usine_origine = $usine_origine;

        return $this;
    }

    public function getDateSortie(): ?\DateTimeInterface
    {
        return $this->date_sortie;
    }

    public function setDateSortie(?\DateTimeInterface $date_sortie): static
    {
        $this->date_sortie = $date_sortie;

        return $this;
    }

    public function getVolumeSorti(): ?float
    {
        return $this->volume_sorti;
    }

    public function setVolumeSorti(?float $volume_sorti): static
    {
        $this->volume_sorti = $volume_sorti;

        return $this;
    }

    public function getDestination(): ?string
    {
        return $this->destination;
    }

    public function setDestination(?string $destination): static
    {
        $this->destination = $destination;

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

    public function getCodePagersdpf(): ?Pagersdpf
    {
        return $this->code_pagersdpf;
    }

    public function setCodePagersdpf(?Pagersdpf $code_pagersdpf): static
    {
        $this->code_pagersdpf = $code_pagersdpf;

        return $this;
    }
}
