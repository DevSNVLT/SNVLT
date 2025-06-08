<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\Administration\DocStatsGen;
use App\Entity\DocStats\Entetes\Documentpdtdrv;
use App\Entity\References\Cantonnement;
use App\Entity\References\Ddef;
use App\Entity\References\Dr;
use App\Repository\DocStats\Pages\PagepdtdrvRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.pagepdtdrv')]
#[ORM\Entity(repositoryClass: PagepdtdrvRepository::class)]
class Pagepdtdrv
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_pagepdtdrv = null;

    #[ORM\Column]
    private ?int $index_pagepdtdrv = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fournisseur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $localisation_fournisseur = null;

    #[ORM\ManyToOne(inversedBy: 'pagepdtdrvs')]
    private ?Dr $dref = null;

    #[ORM\ManyToOne(inversedBy: 'pagepdtdrvs')]
    private ?Ddef $ddef = null;

    #[ORM\ManyToOne(inversedBy: 'pagepdtdrvs')]
    private ?Cantonnement $cef = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $conducteur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_chargement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $heure_depart = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $destination = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commune = null;

    #[ORM\Column(nullable: true)]
    private ?int $nb_pdt = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume_total = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $immatriculation = null;

    #[ORM\Column(length: 50)]
    private ?string $numpage_pdtdrv = null;

    #[ORM\ManyToOne(inversedBy: 'pagepdtdrvs')]
    private ?Documentpdtdrv $code_docpdtdrv = null;

    #[ORM\ManyToOne(inversedBy: 'pagepdtdrvs')]
    private ?DocStatsGen $code_generation = null;

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

    public function getNumeroPagepdtdrv(): ?string
    {
        return $this->numero_pagepdtdrv;
    }

    public function setNumeroPagepdtdrv(string $numero_pagepdtdrv): static
    {
        $this->numero_pagepdtdrv = $numero_pagepdtdrv;

        return $this;
    }

    public function getIndexPagepdtdrv(): ?int
    {
        return $this->index_pagepdtdrv;
    }

    public function setIndexPagepdtdrv(int $index_pagepdtdrv): static
    {
        $this->index_pagepdtdrv = $index_pagepdtdrv;

        return $this;
    }

    public function getFournisseur(): ?string
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?string $fournisseur): static
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    public function getLocalisationFournisseur(): ?string
    {
        return $this->localisation_fournisseur;
    }

    public function setLocalisationFournisseur(?string $localisation_fournisseur): static
    {
        $this->localisation_fournisseur = $localisation_fournisseur;

        return $this;
    }

    public function getDref(): ?Dr
    {
        return $this->dref;
    }

    public function setDref(?Dr $dref): static
    {
        $this->dref = $dref;

        return $this;
    }

    public function getDdef(): ?Ddef
    {
        return $this->ddef;
    }

    public function setDdef(?Ddef $ddef): static
    {
        $this->ddef = $ddef;

        return $this;
    }

    public function getCef(): ?Cantonnement
    {
        return $this->cef;
    }

    public function setCef(?Cantonnement $cef): static
    {
        $this->cef = $cef;

        return $this;
    }

    public function getConducteur(): ?string
    {
        return $this->conducteur;
    }

    public function setConducteur(?string $conducteur): static
    {
        $this->conducteur = $conducteur;

        return $this;
    }

    public function getDateChargement(): ?\DateTimeInterface
    {
        return $this->date_chargement;
    }

    public function setDateChargement(?\DateTimeInterface $date_chargement): static
    {
        $this->date_chargement = $date_chargement;

        return $this;
    }

    public function getHeureDepart(): ?\DateTimeInterface
    {
        return $this->heure_depart;
    }

    public function setHeureDepart(?\DateTimeInterface $heure_depart): static
    {
        $this->heure_depart = $heure_depart;

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

    public function getCommune(): ?string
    {
        return $this->commune;
    }

    public function setCommune(?string $commune): static
    {
        $this->commune = $commune;

        return $this;
    }

    public function getNbPdt(): ?int
    {
        return $this->nb_pdt;
    }

    public function setNbPdt(?int $nb_pdt): static
    {
        $this->nb_pdt = $nb_pdt;

        return $this;
    }

    public function getVolumeTotal(): ?float
    {
        return $this->volume_total;
    }

    public function setVolumeTotal(?float $volume_total): static
    {
        $this->volume_total = $volume_total;

        return $this;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(?string $immatriculation): static
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getNumpagePdtdrv(): ?string
    {
        return $this->numpage_pdtdrv;
    }

    public function setNumpagePdtdrv(string $numpage_pdtdrv): static
    {
        $this->numpage_pdtdrv = $numpage_pdtdrv;

        return $this;
    }

    public function getCodeDocpdtdrv(): ?Documentpdtdrv
    {
        return $this->code_docpdtdrv;
    }

    public function setCodeDocpdtdrv(?Documentpdtdrv $code_docpdtdrv): static
    {
        $this->code_docpdtdrv = $code_docpdtdrv;

        return $this;
    }

    public function getCodeGeneration(): ?DocStatsGen
    {
        return $this->code_generation;
    }

    public function setCodeGeneration(?DocStatsGen $code_generation): static
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
