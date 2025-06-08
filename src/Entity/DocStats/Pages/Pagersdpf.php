<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\DocStats\Entetes\Documentrsdpf;
use App\Entity\DocStats\Saisie\Lignepagersdpf;
use App\Entity\References\PageDocGen;
use App\Repository\DocStats\Pages\PagersdpfRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.pagersdpf')]
#[ORM\Entity(repositoryClass: PagersdpfRepository::class)]
class Pagersdpf
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $numero_page = null;

    #[ORM\ManyToOne(inversedBy: 'pagersdpfs')]
    private ?PageDocGen $code_generation = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(nullable: true)]
    private ?int $index_page = null;

    #[ORM\ManyToOne(inversedBy: 'pagersdpfs')]
    private ?Documentrsdpf $code_docrsdpf = null;

    #[ORM\OneToMany(mappedBy: 'code_pagersdpf', targetEntity: Lignepagersdpf::class)]
    private Collection $lignepagersdpfs;

    public function __construct()
    {
        $this->lignepagersdpfs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroPage(): ?string
    {
        return $this->numero_page;
    }

    public function setNumeroPage(?string $numero_page): static
    {
        $this->numero_page = $numero_page;

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

    public function getIndexPage(): ?int
    {
        return $this->index_page;
    }

    public function setIndexPage(?int $index_page): static
    {
        $this->index_page = $index_page;

        return $this;
    }

    public function getCodeDocrsdpf(): ?Documentrsdpf
    {
        return $this->code_docrsdpf;
    }

    public function setCodeDocrsdpf(?Documentrsdpf $code_docrsdpf): static
    {
        $this->code_docrsdpf = $code_docrsdpf;

        return $this;
    }

    /**
     * @return Collection<int, Lignepagersdpf>
     */
    public function getLignepagersdpfs(): Collection
    {
        return $this->lignepagersdpfs;
    }

    public function addLignepagersdpf(Lignepagersdpf $lignepagersdpf): static
    {
        if (!$this->lignepagersdpfs->contains($lignepagersdpf)) {
            $this->lignepagersdpfs->add($lignepagersdpf);
            $lignepagersdpf->setCodePagersdpf($this);
        }

        return $this;
    }

    public function removeLignepagersdpf(Lignepagersdpf $lignepagersdpf): static
    {
        if ($this->lignepagersdpfs->removeElement($lignepagersdpf)) {
            // set the owning side to null (unless already changed)
            if ($lignepagersdpf->getCodePagersdpf() === $this) {
                $lignepagersdpf->setCodePagersdpf(null);
            }
        }

        return $this;
    }
}
