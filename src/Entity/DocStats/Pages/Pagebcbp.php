<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\Administration\DocStatsGen;
use App\Entity\DocStats\Entetes\Documentbcbp;
use App\Entity\DocStats\Saisie\Lignepagebcbp;
use App\Entity\References\Cantonnement;
use App\Entity\References\Essence;
use App\Entity\References\PageDocGen;
use App\Entity\References\Usine;
use App\Repository\DocStats\Pages\PagebcbpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.pagebcbp')]
#[ORM\Entity(repositoryClass: PagebcbpRepository::class)]
class Pagebcbp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_pagebcbp = null;

    #[ORM\Column(nullable: true)]
    private ?int $index_pagebcbp = null;

    #[ORM\ManyToOne(inversedBy: 'pagebcbps')]
    private ?Essence $essence = null;

    #[ORM\ManyToOne(inversedBy: 'pagebcbps')]
    private ?Cantonnement $cantonnement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $transporteur = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $conducteur = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_chargement = null;

    #[ORM\Column(nullable: true)]
    private ?int $cout = null;

    #[ORM\ManyToOne(inversedBy: 'pagebcbps')]
    private ?Documentbcbp $code_docbcbp = null;

    #[ORM\ManyToOne(inversedBy: 'pagebcbps')]
    private ?Usine $parc_usine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $destination = null;

    #[ORM\Column(nullable: true)]
    private ?bool $confirmation_usine = null;

    #[ORM\Column(nullable: true)]
    private ?bool $soumettre = null;

    #[ORM\Column(nullable: true)]
    private ?bool $fini = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motivation_rejet = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $immatcamion = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    #[ORM\Column(nullable: true)]
    private ?int $nb_billes = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume = null;

    #[ORM\Column(nullable: true)]
    private ?bool $entre_lje = null;

    #[ORM\ManyToOne(inversedBy: 'pagebcbps')]
    private ?PageDocGen $code_generation = null;

    #[ORM\OneToMany(mappedBy: 'code_pagebcbp', targetEntity: Lignepagebcbp::class)]
    private Collection $lignepagebcbps;

    public function __construct()
    {
        $this->lignepagebcbps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroPagebcbp(): ?string
    {
        return $this->numero_pagebcbp;
    }

    public function setNumeroPagebcbp(string $numero_pagebcbp): static
    {
        $this->numero_pagebcbp = $numero_pagebcbp;

        return $this;
    }

    public function getIndexPagebcbp(): ?int
    {
        return $this->index_pagebcbp;
    }

    public function setIndexPagebcbp(?int $index_pagebcbp): static
    {
        $this->index_pagebcbp = $index_pagebcbp;

        return $this;
    }

    public function getEssence(): ?Essence
    {
        return $this->essence;
    }

    public function setEssence(?Essence $essence): static
    {
        $this->essence = $essence;

        return $this;
    }

    public function getCantonnement(): ?Cantonnement
    {
        return $this->cantonnement;
    }

    public function setCantonnement(?Cantonnement $cantonnement): static
    {
        $this->cantonnement = $cantonnement;

        return $this;
    }

    public function getTransporteur(): ?string
    {
        return $this->transporteur;
    }

    public function setTransporteur(?string $transporteur): static
    {
        $this->transporteur = $transporteur;

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

    public function getCout(): ?int
    {
        return $this->cout;
    }

    public function setCout(?int $cout): static
    {
        $this->cout = $cout;

        return $this;
    }

    public function getCodeDocbcbp(): ?Documentbcbp
    {
        return $this->code_docbcbp;
    }

    public function setCodeDocbcbp(?Documentbcbp $code_docbcbp): static
    {
        $this->code_docbcbp = $code_docbcbp;

        return $this;
    }

    public function getParcUsine(): ?Usine
    {
        return $this->parc_usine;
    }

    public function setParcUsine(?Usine $parc_usine): static
    {
        $this->parc_usine = $parc_usine;

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

    public function isConfirmationUsine(): ?bool
    {
        return $this->confirmation_usine;
    }

    public function setConfirmationUsine(?bool $confirmation_usine): static
    {
        $this->confirmation_usine = $confirmation_usine;

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

    public function getMotivationRejet(): ?string
    {
        return $this->motivation_rejet;
    }

    public function setMotivationRejet(?string $motivation_rejet): static
    {
        $this->motivation_rejet = $motivation_rejet;

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

    /**
     * @return string|null
     */
    public function getImmatcamion(): ?string
    {
        return $this->immatcamion;
    }

    /**
     * @param string|null $immatcamion
     */
    public function setImmatcamion(?string $immatcamion): void
    {
        $this->immatcamion = $immatcamion;
    }

    /**
     * @return string|null
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * @param string|null $photo
     */
    public function setPhoto(?string $photo): void
    {
        $this->photo = $photo;
    }

    public function isSoumettre(): ?bool
    {
        return $this->soumettre;
    }

    public function setSoumettre(?bool $soumettre): static
    {
        $this->soumettre = $soumettre;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNbBilles(): ?int
    {
        return $this->nb_billes;
    }

    /**
     * @param int|null $nb_billes
     */
    public function setNbBilles(?int $nb_billes): void
    {
        $this->nb_billes = $nb_billes;
    }

    /**
     * @return float|null
     */
    public function getVolume(): ?float
    {
        return $this->volume;
    }

    /**
     * @param float|null $volume
     */
    public function setVolume(?float $volume): void
    {
        $this->volume = $volume;
    }

    /**
     * @return bool|null
     */
    public function getEntreLje(): ?bool
    {
        return $this->entre_lje;
    }

    /**
     * @param bool|null $entre_lje
     */
    public function setEntreLje(?bool $entre_lje): void
    {
        $this->entre_lje = $entre_lje;
    }



    /**
     * @return Collection<int, Lignepagebcbp>
     */
    public function getLignepagebcbps(): Collection
    {
        return $this->lignepagebcbps;
    }

    public function addLignepagebcbp(Lignepagebcbp $lignepagebcbp): static
    {
        if (!$this->lignepagebcbps->contains($lignepagebcbp)) {
            $this->lignepagebcbps->add($lignepagebcbp);
            $lignepagebcbp->setCodePagebcbp($this);
        }

        return $this;
    }

    public function removeLignepagebcbp(Lignepagebcbp $lignepagebcbp): static
    {
        if ($this->lignepagebcbps->removeElement($lignepagebcbp)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebcbp->getCodePagebcbp() === $this) {
                $lignepagebcbp->setCodePagebcbp(null);
            }
        }

        return $this;
    }
}
