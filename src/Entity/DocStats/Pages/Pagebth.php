<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\Administration\DocStatsGen;
use App\Entity\DocStats\Entetes\Documentbth;
use App\Entity\References\PageDocGen;
use App\Repository\DocStats\Pages\PagebthRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.pagebth')]
#[ORM\Entity(repositoryClass: PagebthRepository::class)]
class Pagebth
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $numero_pagebth = null;

    #[ORM\Column]
    private ?int $index_page = null;

    #[ORM\ManyToOne(inversedBy: 'pagebths')]
    private ?Documentbth $code_docbth = null;

    #[ORM\ManyToOne(inversedBy: 'pagebths')]
    private ?PageDocGen $code_generation = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

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

    public function getNumeroPagebth(): ?string
    {
        return $this->numero_pagebth4;
    }

    public function setNumeroPagebth(?string $numero_pagebth4): static
    {
        $this->numero_pagebth4 = $numero_pagebth4;

        return $this;
    }

    public function getIndexPage(): ?int
    {
        return $this->index_page;
    }

    public function setIndexPage(int $index_page): static
    {
        $this->index_page = $index_page;

        return $this;
    }

    public function getCodeDocbth(): ?Documentbth
    {
        return $this->code_docbth;
    }

    public function setCodeDocbth(?Documentbth $code_docbth): static
    {
        $this->code_docbth = $code_docbth;

        return $this;
    }

    public function getCodeGeneration(): ?PageDocGen
    {
        return $this->code_generation;
    }

    public function setCodeGeneration(?PageDocGen $code_generation): static
    {
        $this->code_generation = $code_generation;

        return $this;
    }

    public function getUniqueDoc(): ?string
    {
        return $this->unique_doc;
    }

    public function setUniqueDoc(?string $unique_doc): static
    {
        $this->unique_doc = $unique_doc;

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
}
