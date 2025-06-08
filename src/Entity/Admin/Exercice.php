<?php

namespace App\Entity\Admin;

use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\AutorisationExportateur;
use App\Entity\Autorisation\AutorisationPdtdrv;
use App\Entity\Autorisation\AutorisationPs;
use App\Entity\Autorisation\AutorisationPv;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentbcbp;
use App\Entity\DocStats\Entetes\Documentbcburb;
use App\Entity\DocStats\Entetes\Documentbrepf;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Entetes\Documentbtgu;
use App\Entity\DocStats\Entetes\Documentbth;
use App\Entity\DocStats\Entetes\Documentcp;
use App\Entity\DocStats\Entetes\Documentdmp;
use App\Entity\DocStats\Entetes\Documentdmv;
use App\Entity\DocStats\Entetes\Documentetate;
use App\Entity\DocStats\Entetes\Documentetate2;
use App\Entity\DocStats\Entetes\Documentetatg;
use App\Entity\DocStats\Entetes\Documentetath;
use App\Entity\DocStats\Entetes\Documentfp;
use App\Entity\DocStats\Entetes\Documentlje;
use App\Entity\DocStats\Entetes\Documentpdtdrv;
use App\Entity\DocStats\Entetes\Documentrsdpf;
use App\Entity\DocStats\Entetes\SuiviDoc;
use App\Entity\DocStats\Saisie\Lignepagebcbp;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\DocStats\Saisie\Lignepagebtgu;
use App\Entity\DocStats\Saisie\Lignepagecp;
use App\Entity\DocStats\Saisie\Lignepagefp;
use App\Entity\DocStats\Saisie\Lignepagelje;
use App\Entity\Transformation\Colis;
use App\Entity\Transformation\Contrat;
use App\Repository\Admin\ExerciceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'admin.exercice')]
#[ORM\Entity(repositoryClass: ExerciceRepository::class)]
class Exercice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $annee = null;

    #[ORM\Column(nullable: true)]
    private ?bool $cloture = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_expiration_rallonge = null;

    #[ORM\Column(nullable: true)]
    private ?bool $rallonge = null;

    #[ORM\Column(nullable: true)]
    private ?int $nb_mois = null;
    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Documentlje::class)]
    private Collection $documentljes;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Documentbtgu::class)]
    private Collection $documentbtgus;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Lignepagelje::class)]
    private Collection $lignepageljes;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Lignepagebcbp::class)]
    private Collection $lignepagebcbps;
    
    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Lignepagebtgu::class)]
    private Collection $lignepagebtgus;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Documentfp::class)]
    private Collection $documentfps;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Lignepagefp::class)]
    private Collection $lignepagefps;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Documentcp::class)]
    private Collection $documentcps;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Documentbrh::class)]
    private Collection $documentbrhs;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Documentbcbp::class)]
    private Collection $documentbcbps;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Documentetate::class)]
    private Collection $documentetates;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Documentetate2::class)]
    private Collection $documentetate2s;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Documentetatg::class)]
    private Collection $documentetatgs;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Documentetath::class)]
    private Collection $documentetaths;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Documentdmp::class)]
    private Collection $documentdmps;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Documentdmv::class)]
    private Collection $documentdmvs;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Documentpdtdrv::class)]
    private Collection $documentpdtdrvs;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: AutorisationPs::class)]
    private Collection $autorisationPs;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: AutorisationPv::class)]
    private Collection $autorisationPvs;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: AutorisationPdtdrv::class)]
    private Collection $autorisationPdtdrvs;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Contrat::class)]
    private Collection $contrats;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Documentbcburb::class)]
    private Collection $documentbcburbs;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Documentbrepf::class)]
    private Collection $documentbrepfs;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: AutorisationExportateur::class)]
    private Collection $autorisationExportateurs;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Documentrsdpf::class)]
    private Collection $documentrsdpfs;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Documentbth::class)]
    private Collection $documentbths;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Colis::class)]
    private Collection $colis;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $datefin = null;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Lignepagebrh::class)]
    private Collection $lignepagebrhs;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Attribution::class)]
    private Collection $attributions;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: Reprise::class)]
    private Collection $reprises;

    #[ORM\OneToMany(mappedBy: 'code_exercice', targetEntity: Lignepagecp::class)]
    private Collection $lignepagecps;

    #[ORM\OneToMany(mappedBy: 'exercice', targetEntity: SuiviDoc::class)]
    private Collection $suiviDocs;

    public function __construct()
    {
        $this->documentljes = new ArrayCollection();
        $this->documentbtgus = new ArrayCollection();
        $this->lignepageljes = new ArrayCollection();
        $this->lignepagebtgus = new ArrayCollection();
        $this->documentfps = new ArrayCollection();
        $this->lignepagefps = new ArrayCollection();
        $this->documentcps = new ArrayCollection();
        $this->documentbrhs = new ArrayCollection();
        $this->documentbcbps = new ArrayCollection();
        $this->documentetates = new ArrayCollection();
        $this->documentetate2s = new ArrayCollection();
        $this->documentetatgs = new ArrayCollection();
        $this->documentetaths = new ArrayCollection();
        $this->documentdmps = new ArrayCollection();
        $this->documentdmvs = new ArrayCollection();
        $this->documentpdtdrvs = new ArrayCollection();
        $this->autorisationPs = new ArrayCollection();
        $this->autorisationPvs = new ArrayCollection();
        $this->autorisationPdtdrvs = new ArrayCollection();
        $this->contrats = new ArrayCollection();
        $this->documentbcburbs = new ArrayCollection();
        $this->documentbrepfs = new ArrayCollection();
        $this->autorisationExportateurs = new ArrayCollection();
        $this->documentrsdpfs = new ArrayCollection();
        $this->documentbths = new ArrayCollection();
        $this->colis = new ArrayCollection();
        $this->lignepagebrhs = new ArrayCollection();
        $this->attributions = new ArrayCollection();
        $this->reprises = new ArrayCollection();
        $this->lignepagecps = new ArrayCollection();
        $this->suiviDocs = new ArrayCollection();
        $this->lignepagebcbps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function isCloture(): ?bool
    {
        return $this->cloture;
    }

    public function setCloture(?bool $cloture): static
    {
        $this->cloture = $cloture;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateExpirationRallonge(): ?\DateTimeInterface
    {
        return $this->date_expiration_rallonge;
    }

    /**
     * @param \DateTimeInterface|null $date_expiration_rallonge
     */
    public function setDateExpirationRallonge(?\DateTimeInterface $date_expiration_rallonge): void
    {
        $this->date_expiration_rallonge = $date_expiration_rallonge;
    }

    /**
     * @return bool|null
     */
    public function getRallonge(): ?bool
    {
        return $this->rallonge;
    }

    /**
     * @param bool|null $rallonge
     */
    public function setRallonge(?bool $rallonge): void
    {
        $this->rallonge = $rallonge;
    }

    /**
     * @return int|null
     */
    public function getNbMois(): ?int
    {
        return $this->nb_mois;
    }

    /**
     * @param int|null $nb_mois
     */
    public function setNbMois(?int $nb_mois): void
    {
        $this->nb_mois = $nb_mois;
    }



    public function __toString(): string
    {
        return $this->annee;
    }

    /**
     * @return Collection<int, Documentlje>
     */
    public function getDocumentljes(): Collection
    {
        return $this->documentljes;
    }

    public function addDocumentlje(Documentlje $documentlje): static
    {
        if (!$this->documentljes->contains($documentlje)) {
            $this->documentljes->add($documentlje);
            $documentlje->setExercice($this);
        }

        return $this;
    }

    public function removeDocumentlje(Documentlje $documentlje): static
    {
        if ($this->documentljes->removeElement($documentlje)) {
            // set the owning side to null (unless already changed)
            if ($documentlje->getExercice() === $this) {
                $documentlje->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentbtgu>
     */
    public function getDocumentbtgus(): Collection
    {
        return $this->documentbtgus;
    }

    public function addDocumentbtgu(Documentbtgu $documentbtgu): static
    {
        if (!$this->documentbtgus->contains($documentbtgu)) {
            $this->documentbtgus->add($documentbtgu);
            $documentbtgu->setExercice($this);
        }

        return $this;
    }

    public function removeDocumentbtgu(Documentbtgu $documentbtgu): static
    {
        if ($this->documentbtgus->removeElement($documentbtgu)) {
            // set the owning side to null (unless already changed)
            if ($documentbtgu->getExercice() === $this) {
                $documentbtgu->setExercice(null);
            }
        }

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
            $lignepagelje->setExercice($this);
        }

        return $this;
    }

    public function removeLignepagelje(Lignepagelje $lignepagelje): static
    {
        if ($this->lignepageljes->removeElement($lignepagelje)) {
            // set the owning side to null (unless already changed)
            if ($lignepagelje->getExercice() === $this) {
                $lignepagelje->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lignepagebtgu>
     */
    public function getLignepagebtgus(): Collection
    {
        return $this->lignepagebtgus;
    }

    public function addLignepagebtgu(Lignepagebtgu $lignepagebtgu): static
    {
        if (!$this->lignepagebtgus->contains($lignepagebtgu)) {
            $this->lignepagebtgus->add($lignepagebtgu);
            $lignepagebtgu->setExercice($this);
        }

        return $this;
    }

    public function removeLignepagebtgu(Lignepagebtgu $lignepagebtgu): static
    {
        if ($this->lignepagebtgus->removeElement($lignepagebtgu)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebtgu->getExercice() === $this) {
                $lignepagebtgu->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentfp>
     */
    public function getDocumentfps(): Collection
    {
        return $this->documentfps;
    }

    public function addDocumentfp(Documentfp $documentfp): static
    {
        if (!$this->documentfps->contains($documentfp)) {
            $this->documentfps->add($documentfp);
            $documentfp->setExercice($this);
        }

        return $this;
    }

    public function removeDocumentfp(Documentfp $documentfp): static
    {
        if ($this->documentfps->removeElement($documentfp)) {
            // set the owning side to null (unless already changed)
            if ($documentfp->getExercice() === $this) {
                $documentfp->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lignepagefp>
     */
    public function getLignepagefps(): Collection
    {
        return $this->lignepagefps;
    }

    public function addLignepagefp(Lignepagefp $lignepagefp): static
    {
        if (!$this->lignepagefps->contains($lignepagefp)) {
            $this->lignepagefps->add($lignepagefp);
            $lignepagefp->setExercice($this);
        }

        return $this;
    }

    public function removeLignepagefp(Lignepagefp $lignepagefp): static
    {
        if ($this->lignepagefps->removeElement($lignepagefp)) {
            // set the owning side to null (unless already changed)
            if ($lignepagefp->getExercice() === $this) {
                $lignepagefp->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentcp>
     */
    public function getDocumentcps(): Collection
    {
        return $this->documentcps;
    }

    public function addDocumentcp(Documentcp $documentcp): static
    {
        if (!$this->documentcps->contains($documentcp)) {
            $this->documentcps->add($documentcp);
            $documentcp->setExercice($this);
        }

        return $this;
    }

    public function removeDocumentcp(Documentcp $documentcp): static
    {
        if ($this->documentcps->removeElement($documentcp)) {
            // set the owning side to null (unless already changed)
            if ($documentcp->getExercice() === $this) {
                $documentcp->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentbrh>
     */
    public function getDocumentbrhs(): Collection
    {
        return $this->documentbrhs;
    }

    public function addDocumentbrh(Documentbrh $documentbrh): static
    {
        if (!$this->documentbrhs->contains($documentbrh)) {
            $this->documentbrhs->add($documentbrh);
            $documentbrh->setExercice($this);
        }

        return $this;
    }

    public function removeDocumentbrh(Documentbrh $documentbrh): static
    {
        if ($this->documentbrhs->removeElement($documentbrh)) {
            // set the owning side to null (unless already changed)
            if ($documentbrh->getExercice() === $this) {
                $documentbrh->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentbcbp>
     */
    public function getDocumentbcbps(): Collection
    {
        return $this->documentbcbps;
    }

    public function addDocumentbcbp(Documentbcbp $documentbcbp): static
    {
        if (!$this->documentbcbps->contains($documentbcbp)) {
            $this->documentbcbps->add($documentbcbp);
            $documentbcbp->setExercice($this);
        }

        return $this;
    }

    public function removeDocumentbcbp(Documentbcbp $documentbcbp): static
    {
        if ($this->documentbcbps->removeElement($documentbcbp)) {
            // set the owning side to null (unless already changed)
            if ($documentbcbp->getExercice() === $this) {
                $documentbcbp->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentetate>
     */
    public function getDocumentetates(): Collection
    {
        return $this->documentetates;
    }

    public function addDocumentetate(Documentetate $documentetate): static
    {
        if (!$this->documentetates->contains($documentetate)) {
            $this->documentetates->add($documentetate);
            $documentetate->setExercice($this);
        }

        return $this;
    }

    public function removeDocumentetate(Documentetate $documentetate): static
    {
        if ($this->documentetates->removeElement($documentetate)) {
            // set the owning side to null (unless already changed)
            if ($documentetate->getExercice() === $this) {
                $documentetate->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentetate2>
     */
    public function getDocumentetate2s(): Collection
    {
        return $this->documentetate2s;
    }

    public function addDocumentetate2(Documentetate2 $documentetate2): static
    {
        if (!$this->documentetate2s->contains($documentetate2)) {
            $this->documentetate2s->add($documentetate2);
            $documentetate2->setExercice($this);
        }

        return $this;
    }

    public function removeDocumentetate2(Documentetate2 $documentetate2): static
    {
        if ($this->documentetate2s->removeElement($documentetate2)) {
            // set the owning side to null (unless already changed)
            if ($documentetate2->getExercice() === $this) {
                $documentetate2->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentetatg>
     */
    public function getDocumentetatgs(): Collection
    {
        return $this->documentetatgs;
    }

    public function addDocumentetatg(Documentetatg $documentetatg): static
    {
        if (!$this->documentetatgs->contains($documentetatg)) {
            $this->documentetatgs->add($documentetatg);
            $documentetatg->setExercice($this);
        }

        return $this;
    }

    public function removeDocumentetatg(Documentetatg $documentetatg): static
    {
        if ($this->documentetatgs->removeElement($documentetatg)) {
            // set the owning side to null (unless already changed)
            if ($documentetatg->getExercice() === $this) {
                $documentetatg->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentetath>
     */
    public function getDocumentetaths(): Collection
    {
        return $this->documentetaths;
    }

    public function addDocumentetath(Documentetath $documentetath): static
    {
        if (!$this->documentetaths->contains($documentetath)) {
            $this->documentetaths->add($documentetath);
            $documentetath->setExercice($this);
        }

        return $this;
    }

    public function removeDocumentetath(Documentetath $documentetath): static
    {
        if ($this->documentetaths->removeElement($documentetath)) {
            // set the owning side to null (unless already changed)
            if ($documentetath->getExercice() === $this) {
                $documentetath->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentdmp>
     */
    public function getDocumentdmps(): Collection
    {
        return $this->documentdmps;
    }

    public function addDocumentdmp(Documentdmp $documentdmp): static
    {
        if (!$this->documentdmps->contains($documentdmp)) {
            $this->documentdmps->add($documentdmp);
            $documentdmp->setExercice($this);
        }

        return $this;
    }

    public function removeDocumentdmp(Documentdmp $documentdmp): static
    {
        if ($this->documentdmps->removeElement($documentdmp)) {
            // set the owning side to null (unless already changed)
            if ($documentdmp->getExercice() === $this) {
                $documentdmp->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentdmv>
     */
    public function getDocumentdmvs(): Collection
    {
        return $this->documentdmvs;
    }

    public function addDocumentdmv(Documentdmv $documentdmv): static
    {
        if (!$this->documentdmvs->contains($documentdmv)) {
            $this->documentdmvs->add($documentdmv);
            $documentdmv->setExercice($this);
        }

        return $this;
    }

    public function removeDocumentdmv(Documentdmv $documentdmv): static
    {
        if ($this->documentdmvs->removeElement($documentdmv)) {
            // set the owning side to null (unless already changed)
            if ($documentdmv->getExercice() === $this) {
                $documentdmv->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentpdtdrv>
     */
    public function getDocumentpdtdrvs(): Collection
    {
        return $this->documentpdtdrvs;
    }

    public function addDocumentpdtdrv(Documentpdtdrv $documentpdtdrv): static
    {
        if (!$this->documentpdtdrvs->contains($documentpdtdrv)) {
            $this->documentpdtdrvs->add($documentpdtdrv);
            $documentpdtdrv->setExercice($this);
        }

        return $this;
    }

    public function removeDocumentpdtdrv(Documentpdtdrv $documentpdtdrv): static
    {
        if ($this->documentpdtdrvs->removeElement($documentpdtdrv)) {
            // set the owning side to null (unless already changed)
            if ($documentpdtdrv->getExercice() === $this) {
                $documentpdtdrv->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AutorisationPs>
     */
    public function getAutorisationPs(): Collection
    {
        return $this->autorisationPs;
    }

    public function addAutorisationP(AutorisationPs $autorisationP): static
    {
        if (!$this->autorisationPs->contains($autorisationP)) {
            $this->autorisationPs->add($autorisationP);
            $autorisationP->setExercice($this);
        }

        return $this;
    }

    public function removeAutorisationP(AutorisationPs $autorisationP): static
    {
        if ($this->autorisationPs->removeElement($autorisationP)) {
            // set the owning side to null (unless already changed)
            if ($autorisationP->getExercice() === $this) {
                $autorisationP->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AutorisationPv>
     */
    public function getAutorisationPvs(): Collection
    {
        return $this->autorisationPvs;
    }

    public function addAutorisationPv(AutorisationPv $autorisationPv): static
    {
        if (!$this->autorisationPvs->contains($autorisationPv)) {
            $this->autorisationPvs->add($autorisationPv);
            $autorisationPv->setExercice($this);
        }

        return $this;
    }

    public function removeAutorisationPv(AutorisationPv $autorisationPv): static
    {
        if ($this->autorisationPvs->removeElement($autorisationPv)) {
            // set the owning side to null (unless already changed)
            if ($autorisationPv->getExercice() === $this) {
                $autorisationPv->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AutorisationPdtdrv>
     */
    public function getAutorisationPdtdrvs(): Collection
    {
        return $this->autorisationPdtdrvs;
    }

    public function addAutorisationPdtdrv(AutorisationPdtdrv $autorisationPdtdrv): static
    {
        if (!$this->autorisationPdtdrvs->contains($autorisationPdtdrv)) {
            $this->autorisationPdtdrvs->add($autorisationPdtdrv);
            $autorisationPdtdrv->setExercice($this);
        }

        return $this;
    }

    public function removeAutorisationPdtdrv(AutorisationPdtdrv $autorisationPdtdrv): static
    {
        if ($this->autorisationPdtdrvs->removeElement($autorisationPdtdrv)) {
            // set the owning side to null (unless already changed)
            if ($autorisationPdtdrv->getExercice() === $this) {
                $autorisationPdtdrv->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Contrat>
     */
    public function getContrats(): Collection
    {
        return $this->contrats;
    }

    public function addContrat(Contrat $contrat): static
    {
        if (!$this->contrats->contains($contrat)) {
            $this->contrats->add($contrat);
            $contrat->setExercice($this);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): static
    {
        if ($this->contrats->removeElement($contrat)) {
            // set the owning side to null (unless already changed)
            if ($contrat->getExercice() === $this) {
                $contrat->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentbcburb>
     */
    public function getDocumentbcburbs(): Collection
    {
        return $this->documentbcburbs;
    }

    public function addDocumentbcburb(Documentbcburb $documentbcburb): static
    {
        if (!$this->documentbcburbs->contains($documentbcburb)) {
            $this->documentbcburbs->add($documentbcburb);
            $documentbcburb->setExercice($this);
        }

        return $this;
    }

    public function removeDocumentbcburb(Documentbcburb $documentbcburb): static
    {
        if ($this->documentbcburbs->removeElement($documentbcburb)) {
            // set the owning side to null (unless already changed)
            if ($documentbcburb->getExercice() === $this) {
                $documentbcburb->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentbrepf>
     */
    public function getDocumentbrepfs(): Collection
    {
        return $this->documentbrepfs;
    }

    public function addDocumentbrepf(Documentbrepf $documentbrepf): static
    {
        if (!$this->documentbrepfs->contains($documentbrepf)) {
            $this->documentbrepfs->add($documentbrepf);
            $documentbrepf->setExercice($this);
        }

        return $this;
    }

    public function removeDocumentbrepf(Documentbrepf $documentbrepf): static
    {
        if ($this->documentbrepfs->removeElement($documentbrepf)) {
            // set the owning side to null (unless already changed)
            if ($documentbrepf->getExercice() === $this) {
                $documentbrepf->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AutorisationExportateur>
     */
    public function getAutorisationExportateurs(): Collection
    {
        return $this->autorisationExportateurs;
    }

    public function addAutorisationExportateur(AutorisationExportateur $autorisationExportateur): static
    {
        if (!$this->autorisationExportateurs->contains($autorisationExportateur)) {
            $this->autorisationExportateurs->add($autorisationExportateur);
            $autorisationExportateur->setExercice($this);
        }

        return $this;
    }

    public function removeAutorisationExportateur(AutorisationExportateur $autorisationExportateur): static
    {
        if ($this->autorisationExportateurs->removeElement($autorisationExportateur)) {
            // set the owning side to null (unless already changed)
            if ($autorisationExportateur->getExercice() === $this) {
                $autorisationExportateur->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentrsdpf>
     */
    public function getDocumentrsdpfs(): Collection
    {
        return $this->documentrsdpfs;
    }

    public function addDocumentrsdpf(Documentrsdpf $documentrsdpf): static
    {
        if (!$this->documentrsdpfs->contains($documentrsdpf)) {
            $this->documentrsdpfs->add($documentrsdpf);
            $documentrsdpf->setExercice($this);
        }

        return $this;
    }

    public function removeDocumentrsdpf(Documentrsdpf $documentrsdpf): static
    {
        if ($this->documentrsdpfs->removeElement($documentrsdpf)) {
            // set the owning side to null (unless already changed)
            if ($documentrsdpf->getExercice() === $this) {
                $documentrsdpf->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentbth>
     */
    public function getDocumentbths(): Collection
    {
        return $this->documentbths;
    }

    public function addDocumentbth(Documentbth $documentbth): static
    {
        if (!$this->documentbths->contains($documentbth)) {
            $this->documentbths->add($documentbth);
            $documentbth->setExercice($this);
        }

        return $this;
    }

    public function removeDocumentbth(Documentbth $documentbth): static
    {
        if ($this->documentbths->removeElement($documentbth)) {
            // set the owning side to null (unless already changed)
            if ($documentbth->getExercice() === $this) {
                $documentbth->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Colis>
     */
    public function getColis(): Collection
    {
        return $this->colis;
    }

    public function addColi(Colis $coli): static
    {
        if (!$this->colis->contains($coli)) {
            $this->colis->add($coli);
            $coli->setExercice($this);
        }

        return $this;
    }

    public function removeColi(Colis $coli): static
    {
        if ($this->colis->removeElement($coli)) {
            // set the owning side to null (unless already changed)
            if ($coli->getExercice() === $this) {
                $coli->setExercice(null);
            }
        }

        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(?\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;

        return $this;
    }

    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->datefin;
    }

    public function setDatefin(?\DateTimeInterface $datefin): static
    {
        $this->datefin = $datefin;

        return $this;
    }

    /**
     * @return Collection<int, Lignepagebrh>
     */
    public function getLignepagebrhs(): Collection
    {
        return $this->lignepagebrhs;
    }

    public function addLignepagebrh(Lignepagebrh $lignepagebrh): static
    {
        if (!$this->lignepagebrhs->contains($lignepagebrh)) {
            $this->lignepagebrhs->add($lignepagebrh);
            $lignepagebrh->setExercice($this);
        }

        return $this;
    }

    public function removeLignepagebrh(Lignepagebrh $lignepagebrh): static
    {
        if ($this->lignepagebrhs->removeElement($lignepagebrh)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebrh->getExercice() === $this) {
                $lignepagebrh->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Attribution>
     */
    public function getAttributions(): Collection
    {
        return $this->attributions;
    }

    public function addAttribution(Attribution $attribution): static
    {
        if (!$this->attributions->contains($attribution)) {
            $this->attributions->add($attribution);
            $attribution->setExercice($this);
        }

        return $this;
    }

    public function removeAttribution(Attribution $attribution): static
    {
        if ($this->attributions->removeElement($attribution)) {
            // set the owning side to null (unless already changed)
            if ($attribution->getExercice() === $this) {
                $attribution->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Reprise>
     */
    public function getReprises(): Collection
    {
        return $this->reprises;
    }

    public function addReprise(Reprise $reprise): static
    {
        if (!$this->reprises->contains($reprise)) {
            $this->reprises->add($reprise);
            $reprise->setExercice($this);
        }

        return $this;
    }

    public function removeReprise(Reprise $reprise): static
    {
        if ($this->reprises->removeElement($reprise)) {
            // set the owning side to null (unless already changed)
            if ($reprise->getExercice() === $this) {
                $reprise->setExercice(null);
            }
        }

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
            $lignepagecp->setCodeExercice($this);
        }

        return $this;
    }

    public function removeLignepagecp(Lignepagecp $lignepagecp): static
    {
        if ($this->lignepagecps->removeElement($lignepagecp)) {
            // set the owning side to null (unless already changed)
            if ($lignepagecp->getCodeExercice() === $this) {
                $lignepagecp->setCodeExercice(null);
            }
        }

        return $this;
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
            $lignepagebcbp->setExercice($this);
        }

        return $this;
    }

    public function removeLignepagebcbp(Lignepagebcbp $lignepagebcbp): static
    {
        if ($this->lignepagebcbps->removeElement($lignepagebcbp)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebcbp->getExercice() === $this) {
                $lignepagebcbp->setExercice(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SuiviDoc>
     */
    public function getSuiviDocs(): Collection
    {
        return $this->suiviDocs;
    }

    public function addSuiviDoc(SuiviDoc $suiviDoc): static
    {
        if (!$this->suiviDocs->contains($suiviDoc)) {
            $this->suiviDocs->add($suiviDoc);
            $suiviDoc->setExercice($this);
        }

        return $this;
    }

    public function removeSuiviDoc(SuiviDoc $suiviDoc): static
    {
        if ($this->suiviDocs->removeElement($suiviDoc)) {
            // set the owning side to null (unless already changed)
            if ($suiviDoc->getExercice() === $this) {
                $suiviDoc->setExercice(null);
            }
        }

        return $this;
    }
}
