<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\DocStats\Entetes\Documentbrepf;
use App\Entity\DocStats\Saisie\Lignepagebrepf;
use App\Entity\References\PageDocGen;
use App\Entity\References\Usine;
use App\Repository\DocStats\Pages\PagebrepfRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.pagebrepf')]
#[ORM\Entity(repositoryClass: PagebrepfRepository::class)]
class Pagebrepf
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'pagebrepfs')]
    private ?Usine $usine_origine = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $immatriculation_vehicule = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_transport = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $transitaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $destination = null;

    #[ORM\Column(nullable: true)]
    private ?float $cubage = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $etat_hygro = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_cef = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_dr = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'pagebrepfs')]
    private ?Documentbrepf $code_docbrepf = null;

    #[ORM\ManyToOne(inversedBy: 'pagebrepfs')]
    private ?PageDocGen $code_generation = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\OneToMany(mappedBy: 'code_pagebrepf', targetEntity: Lignepagebrepf::class)]
    private Collection $lignepagebrepfs;

    #[ORM\Column(length: 50)]
    private ?string $numero_pagebrepf = null;

    #[ORM\Column(nullable: true)]
    private ?int $index_page = null;

    public function __construct()
    {
        $this->lignepagebrepfs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getImmatriculationVehicule(): ?string
    {
        return $this->immatriculation_vehicule;
    }

    public function setImmatriculationVehicule(?string $immatriculation_vehicule): static
    {
        $this->immatriculation_vehicule = $immatriculation_vehicule;

        return $this;
    }

    public function getDateTransport(): ?\DateTimeInterface
    {
        return $this->date_transport;
    }

    public function setDateTransport(?\DateTimeInterface $date_transport): static
    {
        $this->date_transport = $date_transport;

        return $this;
    }

    public function getTransitaire(): ?string
    {
        return $this->transitaire;
    }

    public function setTransitaire(?string $transitaire): static
    {
        $this->transitaire = $transitaire;

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

    public function getCubage(): ?float
    {
        return $this->cubage;
    }

    public function setCubage(?float $cubage): static
    {
        $this->cubage = $cubage;

        return $this;
    }

    public function getEtatHygro(): ?string
    {
        return $this->etat_hygro;
    }

    public function setEtatHygro(?string $etat_hygro): static
    {
        $this->etat_hygro = $etat_hygro;

        return $this;
    }

    public function isSignatureCef(): ?bool
    {
        return $this->signature_cef;
    }

    public function setSignatureCef(?bool $signature_cef): static
    {
        $this->signature_cef = $signature_cef;

        return $this;
    }

    public function isSignatureDr(): ?bool
    {
        return $this->signature_dr;
    }

    public function setSignatureDr(?bool $signature_dr): static
    {
        $this->signature_dr = $signature_dr;

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

    public function getCodeDocbrepf(): ?Documentbrepf
    {
        return $this->code_docbrepf;
    }

    public function setCodeDocbrepf(?Documentbrepf $code_docbrepf): static
    {
        $this->code_docbrepf = $code_docbrepf;

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

    /**
     * @return Collection<int, Lignepagebrepf>
     */
    public function getLignepagebrepfs(): Collection
    {
        return $this->lignepagebrepfs;
    }

    public function addLignepagebrepf(Lignepagebrepf $lignepagebrepf): static
    {
        if (!$this->lignepagebrepfs->contains($lignepagebrepf)) {
            $this->lignepagebrepfs->add($lignepagebrepf);
            $lignepagebrepf->setCodePagebrepf($this);
        }

        return $this;
    }

    public function removeLignepagebrepf(Lignepagebrepf $lignepagebrepf): static
    {
        if ($this->lignepagebrepfs->removeElement($lignepagebrepf)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebrepf->getCodePagebrepf() === $this) {
                $lignepagebrepf->setCodePagebrepf(null);
            }
        }

        return $this;
    }

    public function getNumeroPagebrepf(): ?string
    {
        return $this->numero_pagebrepf;
    }

    public function setNumeroPagebrepf(string $numero_pagebrepf): static
    {
        $this->numero_pagebrepf = $numero_pagebrepf;

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
}
