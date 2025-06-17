<?php

namespace App\Entity\DocStats\Saisie;

use App\Entity\Admin\Exercice;
use App\Entity\DocStats\Pages\Pagebtgu;
use App\Entity\DocStats\Pages\Pagelje;
use App\Entity\References\Essence;
use App\Entity\References\Exploitant;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\References\ZoneHemispherique;
use App\Entity\Transformation\Billon;
use App\Repository\DocStats\Saisie\LignepageljeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.lignepagelje')]
#[ORM\Entity(repositoryClass: LignepageljeRepository::class)]
class Lignepagelje
{
    const ADD_LJE_SUCCESS = 'LIGNE_LJE_ADDESD_SUCCESSFULLY';
    const ADD_LJE_ERROR = 'LIGNE_LJE_ADDESD_WITH_ERROR';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageljes')]
    private ?TypeDocumentStatistique $code_type_doc = null;

    #[ORM\Column(nullable: true)]
    private ?int $code_feuillet = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_dechargement = null;

    #[ORM\Column]
    private ?int $numero_arbre = null;

    #[ORM\Column(length: 1)]
    private ?string $lettre = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageljes')]
    private ?Essence $essence = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageljes')]
    private ?ZoneHemispherique $zh = null;

    #[ORM\Column]
    private ?float $x = null;

    #[ORM\Column]
    private ?float $y = null;

    #[ORM\Column]
    private ?int $lng = null;

    #[ORM\Column]
    private ?int $dm = null;

    #[ORM\Column]
    private ?float $volume = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageljes')]
    private ?Exercice $exercice = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageljes')]
    private ?Pagelje $code_pagelje = null;

    #[ORM\OneToMany(mappedBy: 'code_lignepagelje', targetEntity: Billon::class)]
    private Collection $billons;

    #[ORM\Column(nullable: true)]
    private ?bool $transforme = null;

    #[ORM\Column(nullable: true)]
    private ?bool $tronconnee = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageljes')]
    private ?Exploitant $rs_origine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $foret= null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $document_source = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $numero_document = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?int $last_code_usine = null;


    #[ORM\Column(nullable: true)]
    private ?bool $rebus = null;

    #[ORM\ManyToOne(inversedBy: 'lignepageljes')]
    private ?Pagebtgu $code_pagebtgu = null;

    #[ORM\OneToMany(mappedBy: 'code_bille', targetEntity: HistoriqueLje::class)]
    private Collection $historiqueLjes;

    public function __construct()
    {
        $this->billons = new ArrayCollection();
        $this->historiqueLjes = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeTypeDoc(): ?TypeDocumentStatistique
    {
        return $this->code_type_doc;
    }

    public function setCodeTypeDoc(?TypeDocumentStatistique $code_type_doc): static
    {
        $this->code_type_doc = $code_type_doc;

        return $this;
    }

    public function getCodeFeuillet(): ?int
    {
        return $this->code_feuillet;
    }

    public function setCodeFeuillet(?int $code_feuillet): static
    {
        $this->code_feuillet = $code_feuillet;

        return $this;
    }

    public function getDateDechargement(): ?\DateTimeInterface
    {
        return $this->date_dechargement;
    }

    public function setDateDechargement(?\DateTimeInterface $date_dechargement): static
    {
        $this->date_dechargement = $date_dechargement;

        return $this;
    }

    public function getNumeroArbre(): ?int
    {
        return $this->numero_arbre;
    }

    public function setNumeroArbre(int $numero_arbre): static
    {
        $this->numero_arbre = $numero_arbre;

        return $this;
    }

    public function getLettre(): ?string
    {
        return $this->lettre;
    }

    public function setLettre(string $lettre): static
    {
        $this->lettre = $lettre;

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

    public function getZh(): ?ZoneHemispherique
    {
        return $this->zh;
    }

    public function setZh(?ZoneHemispherique $zh): static
    {
        $this->zh = $zh;

        return $this;
    }

    public function getX(): ?float
    {
        return $this->x;
    }

    public function setX(float $x): static
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?float
    {
        return $this->y;
    }

    public function setY(float $y): static
    {
        $this->y = $y;

        return $this;
    }

    public function getLng(): ?int
    {
        return $this->lng;
    }

    public function setLng(int $lng): static
    {
        $this->lng = $lng;

        return $this;
    }

    public function getDm(): ?int
    {
        return $this->dm;
    }

    public function setDm(int $dm): static
    {
        $this->dm = $dm;

        return $this;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function setVolume(float $volume): static
    {
        $this->volume = $volume;

        return $this;
    }

    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    public function setExercice(?Exercice $exercice): static
    {
        $this->exercice = $exercice;

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

    public function getCodePagelje(): ?Pagelje
    {
        return $this->code_pagelje;
    }

    public function setCodePagelje(?Pagelje $code_pagelje): static
    {
        $this->code_pagelje = $code_pagelje;

        return $this;
    }

    /**
     * @return Collection<int, Billon>
     */
    public function getBillons(): Collection
    {
        return $this->billons;
    }

    public function addBillon(Billon $billon): static
    {
        if (!$this->billons->contains($billon)) {
            $this->billons->add($billon);
            $billon->setCodeLignepagelje($this);
        }

        return $this;
    }

    public function removeBillon(Billon $billon): static
    {
        if ($this->billons->removeElement($billon)) {
            // set the owning side to null (unless already changed)
            if ($billon->getCodeLignepagelje() === $this) {
                $billon->setCodeLignepagelje(null);
            }
        }

        return $this;
    }

    public function isTransforme(): ?bool
    {
        return $this->transforme;
    }

    public function setTransforme(?bool $transforme): static
    {
        $this->transforme = $transforme;

        return $this;
    }

    public function isTronconnee(): ?bool
    {
        return $this->tronconnee;
    }

    public function setTronconnee(?bool $tronconnee): static
    {
        $this->tronconnee = $tronconnee;

        return $this;
    }

    public function getRsOrigine(): ?Exploitant
    {
        return $this->rs_origine;
    }

    public function setRsOrigine(?Exploitant $rs_origine): static
    {
        $this->rs_origine = $rs_origine;

        return $this;
    }

    public function getDocumentSource(): ?string
    {
        return $this->document_source;
    }

    public function setDocumentSource(?string $document_source): static
    {
        $this->document_source = $document_source;

        return $this;
    }

    public function isRebus(): ?bool
    {
        return $this->rebus;
    }

    public function setRebus(?bool $rebus): static
    {
        $this->rebus = $rebus;

        return $this;
    }

    public function getCodePagebtgu(): ?Pagebtgu
    {
        return $this->code_pagebtgu;
    }

    public function setCodePagebtgu(?Pagebtgu $code_pagebtgu): static
    {
        $this->code_pagebtgu = $code_pagebtgu;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getForet(): ?string
    {
        return $this->foret;
    }

    /**
     * @param string|null $foret
     */
    public function setForet(?string $foret): void
    {
        $this->foret = $foret;
    }

    /**
     * @return int|null
     */
    public function getLastCodeUsine(): ?int
    {
        return $this->last_code_usine;
    }

    /**
     * @param int|null $last_code_usine
     */
    public function setLastCodeUsine(?int $last_code_usine): void
    {
        $this->last_code_usine = $last_code_usine;
    }

    /**
     * @return string|null
     */
    public function getNumeroDocument(): ?string
    {
        return $this->numero_document;
    }

    /**
     * @param string|null $numero_document
     */
    public function setNumeroDocument(?string $numero_document): void
    {
        $this->numero_document = $numero_document;
    }

    /**
     * @return Collection<int, HistoriqueLje>
     */
    public function getHistoriqueLjes(): Collection
    {
        return $this->historiqueLjes;
    }

    public function addHistoriqueLje(HistoriqueLje $historiqueLje): static
    {
        if (!$this->historiqueLjes->contains($historiqueLje)) {
            $this->historiqueLjes->add($historiqueLje);
            $historiqueLje->setCodeBille($this);
        }

        return $this;
    }

    public function removeHistoriqueLje(HistoriqueLje $historiqueLje): static
    {
        if ($this->historiqueLjes->removeElement($historiqueLje)) {
            // set the owning side to null (unless already changed)
            if ($historiqueLje->getCodeBille() === $this) {
                $historiqueLje->setCodeBille(null);
            }
        }

        return $this;
    }


}
