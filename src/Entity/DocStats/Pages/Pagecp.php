<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\DocStats\Entetes\Documentcp;
use App\Entity\DocStats\Saisie\Lignepagecp;
use App\Entity\References\PageDocGen;
use App\Repository\DocStats\Pages\PagecpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.pagecp')]
#[ORM\Entity(repositoryClass: PagecpRepository::class)]
class Pagecp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_pagecp = null;

    #[ORM\Column(nullable: true)]
    private ?int $index_pagecp = null;

    #[ORM\Column(nullable: true)]
    private ?int $mois = null;

    #[ORM\Column(nullable: true)]
    private ?int $annee = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $village_pagecp = null;

    #[ORM\Column(nullable: true)]
    private ?bool $fini = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'pagecps')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Documentcp $code_doccp = null;

    #[ORM\OneToMany(mappedBy: 'code_pagecp', targetEntity: Lignepagecp::class)]
    private Collection $lignepagecps;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\ManyToOne(inversedBy: 'pagecps')]
    private ?PageDocGen $code_generation = null;

    public function __construct()
    {
        $this->lignepagecps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroPagecp(): ?string
    {
        return $this->numero_pagecp;
    }

    public function setNumeroPagecp(string $numero_pagecp): static
    {
        $this->numero_pagecp = $numero_pagecp;

        return $this;
    }

    public function getIndex(): ?int
    {
        return $this->index_pagecp;
    }

    public function setIndex(?int $index_pagecp): static
    {
        $this->index_pagecp = $index_pagecp;

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

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getVillagePagecp(): ?string
    {
        return $this->village_pagecp;
    }

    public function setVillagePagecp(?string $village_pagecp): static
    {
        $this->village_pagecp = $village_pagecp;

        return $this;
    }

    public function isFini(): ?bool
    {
        return $this->fini;
    }

    public function setFini(?bool $fini): static
    {
        $this->fini = $fini;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @param \DateTimeInterface|null $created_at
     */
    public function setCreatedAt(?\DateTimeInterface $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return string|null
     */
    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    /**
     * @param string|null $created_by
     */
    public function setCreatedBy(?string $created_by): void
    {
        $this->created_by = $created_by;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * @param \DateTimeInterface|null $updated_at
     */
    public function setUpdatedAt(?\DateTimeInterface $updated_at): void
    {
        $this->updated_at = $updated_at;
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

    public function getCodeDoccp(): ?Documentcp
    {
        return $this->code_doccp;
    }

    public function setCodeDoccp(?Documentcp $code_doccp): static
    {
        $this->code_doccp = $code_doccp;

        return $this;
    }

    /**
     * @return Collection<int, Lignepagecp>
     */
    public function getLignepagecps(): Collection
    {
        return $this->lignepagecps;
    }

    public function addLignepagecp(Lignepagecp $lignepagecp): static
    {
        if (!$this->lignepagecps->contains($lignepagecp)) {
            $this->lignepagecps->add($lignepagecp);
            $lignepagecp->setCodePagecp($this);
        }

        return $this;
    }

    public function removeLignepagecp(Lignepagecp $lignepagecp): static
    {
        if ($this->lignepagecps->removeElement($lignepagecp)) {
            // set the owning side to null (unless already changed)
            if ($lignepagecp->getCodePagecp() === $this) {
                $lignepagecp->setCodePagecp(null);
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
}
