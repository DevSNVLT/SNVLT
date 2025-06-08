<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\DocStats\Entetes\Documentbcburb;
use App\Entity\DocStats\Saisie\Lignepagebcburb;
use App\Entity\References\Cantonnement;
use App\Entity\References\PageDocGen;
use App\Entity\References\Usine;
use App\Repository\DocStats\Pages\PagebcburbRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.pagebcburb')]
#[ORM\Entity(repositoryClass: PagebcburbRepository::class)]
class Pagebcburb
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $numero_page = null;

    #[ORM\Column(nullable: true)]
    private ?int $index_page = null;

    #[ORM\ManyToOne(inversedBy: 'pagebcburbs')]
    private ?Usine $code_usine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $conducteur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $immatriculation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_chargement = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $Heure_depart = null;

    #[ORM\ManyToOne(inversedBy: 'pagebcburbs')]
    private ?Cantonnement $destination = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $commune = null;

    #[ORM\Column(nullable: true)]
    private ?int $nb_produits = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_cef_origine = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_cef_destination = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'pagebcburbs')]
    private ?PageDocGen $code_generation = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\ManyToOne(inversedBy: 'pagebcburbs')]
    private ?Documentbcburb $code_docbcburb = null;

    #[ORM\OneToMany(mappedBy: 'code_pagebcburb', targetEntity: Lignepagebcburb::class)]
    private Collection $lignepagebcburbs;

    public function __construct()
    {
        $this->lignepagebcburbs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroPage(): ?string
    {
        return $this->numero_page;
    }

    public function setNumeroPage(string $numero_page): static
    {
        $this->numero_page = $numero_page;

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

    public function getCodeUsine(): ?Usine
    {
        return $this->code_usine;
    }

    public function setCodeUsine(?Usine $code_usine): static
    {
        $this->code_usine = $code_usine;

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

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(?string $immatriculation): static
    {
        $this->immatriculation = $immatriculation;

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

    public function getHeureDepart(): ?string
    {
        return $this->Heure_depart;
    }

    public function setHeureDepart(?string $Heure_depart): static
    {
        $this->Heure_depart = $Heure_depart;

        return $this;
    }

    public function getDestination(): ?Cantonnement
    {
        return $this->destination;
    }

    public function setDestination(?Cantonnement $destination): static
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

    public function getNbProduits(): ?int
    {
        return $this->nb_produits;
    }

    public function setNbProduits(?int $nb_produits): static
    {
        $this->nb_produits = $nb_produits;

        return $this;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function setVolume(?float $volume): static
    {
        $this->volume = $volume;

        return $this;
    }

    public function isSignatureCefOrigine(): ?bool
    {
        return $this->signature_cef_origine;
    }

    public function setSignatureCefOrigine(?bool $signature_cef_origine): static
    {
        $this->signature_cef_origine = $signature_cef_origine;

        return $this;
    }

    public function isSignatureCefDestination(): ?bool
    {
        return $this->signature_cef_destination;
    }

    public function setSignatureCefDestination(?bool $signature_cef_destination): static
    {
        $this->signature_cef_destination = $signature_cef_destination;

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

    public function setUpdatedBy(string $updated_by): static
    {
        $this->updated_by = $updated_by;

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

    public function getCodeDocbcburb(): ?Documentbcburb
    {
        return $this->code_docbcburb;
    }

    public function setCodeDocbcburb(?Documentbcburb $code_docbcburb): static
    {
        $this->code_docbcburb = $code_docbcburb;

        return $this;
    }

    /**
     * @return Collection<int, Lignepagebcburb>
     */
    public function getLignepagebcburbs(): Collection
    {
        return $this->lignepagebcburbs;
    }

    public function addLignepagebcburb(Lignepagebcburb $lignepagebcburb): static
    {
        if (!$this->lignepagebcburbs->contains($lignepagebcburb)) {
            $this->lignepagebcburbs->add($lignepagebcburb);
            $lignepagebcburb->setCodePagebcburb($this);
        }

        return $this;
    }

    public function removeLignepagebcburb(Lignepagebcburb $lignepagebcburb): static
    {
        if ($this->lignepagebcburbs->removeElement($lignepagebcburb)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebcburb->getCodePagebcburb() === $this) {
                $lignepagebcburb->setCodePagebcburb(null);
            }
        }

        return $this;
    }
}
