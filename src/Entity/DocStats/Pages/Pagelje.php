<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\DocStats\Entetes\Documentlje;
use App\Entity\DocStats\Saisie\Lignepagelje;
use App\Entity\References\PageDocGen;
use App\Repository\DocStats\Pages\PageljeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.pagelje')]
#[ORM\Entity(repositoryClass: PageljeRepository::class)]
class Pagelje
{
    const PAGE_BRH_EDITED_SUCCESSFULLY= 'PAGE_BRH_EDITED_SUCCESSFULLY';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_pagelje = null;

    #[ORM\Column]
    private ?int $index_pagelje = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\Column(nullable: true)]
    private ?int $mois = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'pageljes')]
    private ?Documentlje $code_doclje = null;

    #[ORM\OneToMany(mappedBy: 'code_pagelje', targetEntity: Lignepagelje::class)]
    private Collection $lignepageljes;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?PageDocGen $code_generation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    public function __construct()
    {
        $this->lignepageljes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroPagelje(): ?string
    {
        return $this->numero_pagelje;
    }

    public function setNumeroPagelje(string $numero_pagelje): static
    {
        $this->numero_pagelje = $numero_pagelje;

        return $this;
    }

    public function getIndexPagelje(): ?int
    {
        return $this->index_pagelje;
    }

    public function setIndexPagelje(int $index_pagelje): static
    {
        $this->index_pagelje = $index_pagelje;

        return $this;
    }

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(?int $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getMois(): ?int
    {
        return $this->mois;
    }

    public function setMois(?int $mois): static
    {
        $this->mois = $mois;

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

    public function getCodeDoclje(): ?Documentlje
    {
        return $this->code_doclje;
    }

    public function setCodeDoclje(?Documentlje $code_doclje): static
    {
        $this->code_doclje = $code_doclje;

        return $this;
    }

    /**
     * @return Collection<int, Lignepagelje>
     */
    public function getLignepageljes(): Collection
    {
        return $this->lignepageljes;
    }

    public function addLignepagelje(Lignepagelje $lignepagelje): static
    {
        if (!$this->lignepageljes->contains($lignepagelje)) {
            $this->lignepageljes->add($lignepagelje);
            $lignepagelje->setCodePagelje($this);
        }

        return $this;
    }

    public function removeLignepagelje(Lignepagelje $lignepagelje): static
    {
        if ($this->lignepageljes->removeElement($lignepagelje)) {
            // set the owning side to null (unless already changed)
            if ($lignepagelje->getCodePagelje() === $this) {
                $lignepagelje->setCodePagelje(null);
            }
        }

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

    public function getCodeGeneration(): ?PageDocGen
    {
        return $this->code_generation;
    }

    public function setCodeGeneration(?PageDocGen $code_generation): static
    {
        $this->code_generation = $code_generation;

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
}
