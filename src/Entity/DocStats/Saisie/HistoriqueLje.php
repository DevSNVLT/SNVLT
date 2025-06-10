<?php

namespace App\Entity\DocStats\Saisie;

use App\Repository\DocStats\Saisie\HistoriqueLjeRepository;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.historique_lje')]
#[ORM\Entity(repositoryClass: HistoriqueLjeRepository::class)]
class HistoriqueLje
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $last_code_usine = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\ManyToOne(inversedBy: 'historiqueLjes')]
    private ?Lignepagelje $code_bille = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastCodeUsine(): ?int
    {
        return $this->last_code_usine;
    }

    public function setLastCodeUsine(?int $last_code_usine): static
    {
        $this->last_code_usine = $last_code_usine;

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

    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    public function setCreatedBy(?string $created_by): static
    {
        $this->created_by = $created_by;

        return $this;
    }

    public function getCodeBille(): ?Lignepagelje
    {
        return $this->code_bille;
    }

    public function setCodeBille(?Lignepagelje $code_bille): static
    {
        $this->code_bille = $code_bille;

        return $this;
    }
}
