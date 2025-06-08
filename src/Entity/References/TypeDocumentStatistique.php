<?php

namespace App\Entity\References;

use App\Entity\Administration\DemandeOperateur;
use App\Entity\Administration\DocStatsGen;
use App\Entity\Administration\StockDoc;
use App\Entity\DocStats\Entetes\Documentbcbp;
use App\Entity\DocStats\Entetes\Documentbcburb;
use App\Entity\DocStats\Entetes\Documentbrepf;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Entetes\Documentbtgu;
use App\Entity\DocStats\Entetes\Documentbth;
use App\Entity\DocStats\Entetes\Documentcp;
use App\Entity\DocStats\Entetes\Documentdmp;
use App\Entity\DocStats\Entetes\Documentdmv;
use App\Entity\DocStats\Entetes\Documentetatb;
use App\Entity\DocStats\Entetes\Documentetate2;
use App\Entity\DocStats\Entetes\Documentetatg;
use App\Entity\DocStats\Entetes\Documentetath;
use App\Entity\DocStats\Entetes\Documentfp;
use App\Entity\DocStats\Entetes\Documentlje;
use App\Entity\DocStats\Entetes\Documentpdtdrv;
use App\Entity\DocStats\Entetes\Documentrsdpf;
use App\Entity\DocStats\Entetes\SuiviDoc;
use App\Entity\DocStats\Saisie\Lignepagelje;
use App\Repository\References\TypeDocumentStatistiqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'metier.type_document_statistique')]
#[ORM\Entity(repositoryClass: TypeDocumentStatistiqueRepository::class)]
#[UniqueEntity(fields: ['abv'], message: 'There is already a document with this name')]
class TypeDocumentStatistique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $abv = null;

    #[ORM\Column(length: 100)]
    private ?string $denomination = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    private ?string $statut = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_type_doc_stat', targetEntity: StockDoc::class)]
    private Collection $stockDocs;

    #[ORM\OneToMany(mappedBy: 'docname', targetEntity: DocStatsGen::class)]
    private Collection $docStatsGens;

    #[ORM\Column(nullable: true)]
    private ?int $nb_pages = null;

    #[ORM\Column(nullable: true)]
    private ?int $tarif = null;

    #[ORM\OneToMany(mappedBy: 'doc_stat', targetEntity: DemandeOperateur::class)]
    private Collection $demandeOperateurs;

    #[ORM\ManyToOne(inversedBy: 'typeDocumentStatistiques')]
    private ?TypeOperateur $code_type_operateur = null;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentcp::class)]
    private Collection $documentcps;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentbrh::class)]
    private Collection $documentbrhs;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentlje::class)]
    private Collection $documentljes;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentbtgu::class)]
    private Collection $documentbtgus;

    #[ORM\OneToMany(mappedBy: 'code_type_doc', targetEntity: Lignepagelje::class)]
    private Collection $lignepageljes;

    #[ORM\Column(nullable: true)]
    private ?int $stock_alert = null;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentfp::class)]
    private Collection $documentfps;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentbcbp::class)]
    private Collection $documentbcbps;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentetate2::class)]
    private Collection $documentetate2s;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentetatg::class)]
    private Collection $documentetatgs;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentetath::class)]
    private Collection $documentetaths;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentdmp::class)]
    private Collection $documentdmps;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentdmv::class)]
    private Collection $documentdmvs;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentpdtdrv::class)]
    private Collection $documentpdtdrvs;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentbcburb::class)]
    private Collection $documentbcburbs;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentbrepf::class)]
    private Collection $documentbrepfs;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentrsdpf::class)]
    private Collection $documentrsdpfs;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentbth::class)]
    private Collection $documentbths;

    #[ORM\OneToMany(mappedBy: 'type_document', targetEntity: Documentetatb::class)]
    private Collection $documentetatbs;

    #[ORM\OneToMany(mappedBy: 'document_type', targetEntity: SuiviDoc::class)]
    private Collection $suiviDocs;

    #[ORM\Column(nullable: true)]
    private ?bool $lettre = null;


    public function __construct()
    {
        $this->stockDocs = new ArrayCollection();
        $this->docStatsGens = new ArrayCollection();
        $this->demandeOperateurs = new ArrayCollection();
        $this->documentcps = new ArrayCollection();
        $this->documentbrhs = new ArrayCollection();
        $this->documentljes = new ArrayCollection();
        $this->documentbtgus = new ArrayCollection();
        $this->lignepageljes = new ArrayCollection();
        $this->documentfps = new ArrayCollection();
        $this->documentbcbps = new ArrayCollection();
        $this->documentetate2s = new ArrayCollection();
        $this->documentetatgs = new ArrayCollection();
        $this->documentetaths = new ArrayCollection();
        $this->documentdmps = new ArrayCollection();
        $this->documentdmvs = new ArrayCollection();
        $this->documentpdtdrvs = new ArrayCollection();
        $this->documentbcburbs = new ArrayCollection();
        $this->documentbrepfs = new ArrayCollection();
        $this->documentrsdpfs = new ArrayCollection();
        $this->documentbths = new ArrayCollection();
        $this->documentetatbs = new ArrayCollection();
        $this->suiviDocs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbv(): ?string
    {
        return $this->abv;
    }

    public function setAbv(string $abv): static
    {
        $this->abv = $abv;

        return $this;
    }

    public function getDenomination(): ?string
    {
        return $this->denomination;
    }

    public function setDenomination(string $denomination): static
    {
        $this->denomination = $denomination;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
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

    /**
     * @return Collection<int, StockDoc>
     */
    public function getStockDocs(): Collection
    {
        return $this->stockDocs;
    }

    public function addStockDoc(StockDoc $stockDoc): static
    {
        if (!$this->stockDocs->contains($stockDoc)) {
            $this->stockDocs->add($stockDoc);
            $stockDoc->setCodeTypeDocStat($this);
        }

        return $this;
    }

    public function removeStockDoc(StockDoc $stockDoc): static
    {
        if ($this->stockDocs->removeElement($stockDoc)) {
            // set the owning side to null (unless already changed)
            if ($stockDoc->getCodeTypeDocStat() === $this) {
                $stockDoc->setCodeTypeDocStat(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->abv;
    }

    /**
     * @return Collection<int, DocStatsGen>
     */
    public function getDocStatsGens(): Collection
    {
        return $this->docStatsGens;
    }

    public function addDocStatsGen(DocStatsGen $docStatsGen): static
    {
        if (!$this->docStatsGens->contains($docStatsGen)) {
            $this->docStatsGens->add($docStatsGen);
            $docStatsGen->setDocname($this);
        }

        return $this;
    }

    public function removeDocStatsGen(DocStatsGen $docStatsGen): static
    {
        if ($this->docStatsGens->removeElement($docStatsGen)) {
            // set the owning side to null (unless already changed)
            if ($docStatsGen->getDocname() === $this) {
                $docStatsGen->setDocname(null);
            }
        }

        return $this;
    }

    public function getNbPages(): ?int
    {
        return $this->nb_pages;
    }

    public function setNbPages(?int $nb_pages): static
    {
        $this->nb_pages = $nb_pages;

        return $this;
    }

    public function getTarif(): ?int
    {
        return $this->tarif;
    }

    public function setTarif(?int $tarif): static
    {
        $this->tarif = $tarif;

        return $this;
    }

    /**
     * @return Collection<int, DemandeOperateur>
     */
    public function getDemandeOperateurs(): Collection
    {
        return $this->demandeOperateurs;
    }

    public function addDemandeOperateur(DemandeOperateur $demandeOperateur): static
    {
        if (!$this->demandeOperateurs->contains($demandeOperateur)) {
            $this->demandeOperateurs->add($demandeOperateur);
            $demandeOperateur->setDocStat($this);
        }

        return $this;
    }

    public function removeDemandeOperateur(DemandeOperateur $demandeOperateur): static
    {
        if ($this->demandeOperateurs->removeElement($demandeOperateur)) {
            // set the owning side to null (unless already changed)
            if ($demandeOperateur->getDocStat() === $this) {
                $demandeOperateur->setDocStat(null);
            }
        }

        return $this;
    }

    public function getCodeTypeOperateur(): ?TypeOperateur
    {
        return $this->code_type_operateur;
    }

    public function setCodeTypeOperateur(?TypeOperateur $code_type_operateur): static
    {
        $this->code_type_operateur = $code_type_operateur;

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
            $documentcp->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentcp(Documentcp $documentcp): static
    {
        if ($this->documentcps->removeElement($documentcp)) {
            // set the owning side to null (unless already changed)
            if ($documentcp->getTypeDocument() === $this) {
                $documentcp->setTypeDocument(null);
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
            $documentbrh->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentbrh(Documentbrh $documentbrh): static
    {
        if ($this->documentbrhs->removeElement($documentbrh)) {
            // set the owning side to null (unless already changed)
            if ($documentbrh->getTypeDocument() === $this) {
                $documentbrh->setTypeDocument(null);
            }
        }

        return $this;
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
            $documentlje->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentlje(Documentlje $documentlje): static
    {
        if ($this->documentljes->removeElement($documentlje)) {
            // set the owning side to null (unless already changed)
            if ($documentlje->getTypeDocument() === $this) {
                $documentlje->setTypeDocument(null);
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
            $documentbtgu->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentbtgu(Documentbtgu $documentbtgu): static
    {
        if ($this->documentbtgus->removeElement($documentbtgu)) {
            // set the owning side to null (unless already changed)
            if ($documentbtgu->getTypeDocument() === $this) {
                $documentbtgu->setTypeDocument(null);
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
            $lignepagelje->setCodeTypeDoc($this);
        }

        return $this;
    }

    public function removeLignepagelje(Lignepagelje $lignepagelje): static
    {
        if ($this->lignepageljes->removeElement($lignepagelje)) {
            // set the owning side to null (unless already changed)
            if ($lignepagelje->getCodeTypeDoc() === $this) {
                $lignepagelje->setCodeTypeDoc(null);
            }
        }

        return $this;
    }

    public function getStockAlert(): ?int
    {
        return $this->stock_alert;
    }

    public function setStockAlert(?int $stock_alert): static
    {
        $this->stock_alert = $stock_alert;

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
            $documentfp->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentfp(Documentfp $documentfp): static
    {
        if ($this->documentfps->removeElement($documentfp)) {
            // set the owning side to null (unless already changed)
            if ($documentfp->getTypeDocument() === $this) {
                $documentfp->setTypeDocument(null);
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
            $documentbcbp->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentbcbp(Documentbcbp $documentbcbp): static
    {
        if ($this->documentbcbps->removeElement($documentbcbp)) {
            // set the owning side to null (unless already changed)
            if ($documentbcbp->getTypeDocument() === $this) {
                $documentbcbp->setTypeDocument(null);
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
            $documentetate2->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentetate2(Documentetate2 $documentetate2): static
    {
        if ($this->documentetate2s->removeElement($documentetate2)) {
            // set the owning side to null (unless already changed)
            if ($documentetate2->getTypeDocument() === $this) {
                $documentetate2->setTypeDocument(null);
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
            $documentetatg->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentetatg(Documentetatg $documentetatg): static
    {
        if ($this->documentetatgs->removeElement($documentetatg)) {
            // set the owning side to null (unless already changed)
            if ($documentetatg->getTypeDocument() === $this) {
                $documentetatg->setTypeDocument(null);
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
            $documentetath->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentetath(Documentetath $documentetath): static
    {
        if ($this->documentetaths->removeElement($documentetath)) {
            // set the owning side to null (unless already changed)
            if ($documentetath->getTypeDocument() === $this) {
                $documentetath->setTypeDocument(null);
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
            $documentdmp->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentdmp(Documentdmp $documentdmp): static
    {
        if ($this->documentdmps->removeElement($documentdmp)) {
            // set the owning side to null (unless already changed)
            if ($documentdmp->getTypeDocument() === $this) {
                $documentdmp->setTypeDocument(null);
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
            $documentdmv->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentdmv(Documentdmv $documentdmv): static
    {
        if ($this->documentdmvs->removeElement($documentdmv)) {
            // set the owning side to null (unless already changed)
            if ($documentdmv->getTypeDocument() === $this) {
                $documentdmv->setTypeDocument(null);
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
            $documentpdtdrv->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentpdtdrv(Documentpdtdrv $documentpdtdrv): static
    {
        if ($this->documentpdtdrvs->removeElement($documentpdtdrv)) {
            // set the owning side to null (unless already changed)
            if ($documentpdtdrv->getTypeDocument() === $this) {
                $documentpdtdrv->setTypeDocument(null);
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
            $documentbcburb->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentbcburb(Documentbcburb $documentbcburb): static
    {
        if ($this->documentbcburbs->removeElement($documentbcburb)) {
            // set the owning side to null (unless already changed)
            if ($documentbcburb->getTypeDocument() === $this) {
                $documentbcburb->setTypeDocument(null);
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
            $documentbrepf->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentbrepf(Documentbrepf $documentbrepf): static
    {
        if ($this->documentbrepfs->removeElement($documentbrepf)) {
            // set the owning side to null (unless already changed)
            if ($documentbrepf->getTypeDocument() === $this) {
                $documentbrepf->setTypeDocument(null);
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
            $documentrsdpf->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentrsdpf(Documentrsdpf $documentrsdpf): static
    {
        if ($this->documentrsdpfs->removeElement($documentrsdpf)) {
            // set the owning side to null (unless already changed)
            if ($documentrsdpf->getTypeDocument() === $this) {
                $documentrsdpf->setTypeDocument(null);
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
            $documentbth->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentbth(Documentbth $documentbth): static
    {
        if ($this->documentbths->removeElement($documentbth)) {
            // set the owning side to null (unless already changed)
            if ($documentbth->getTypeDocument() === $this) {
                $documentbth->setTypeDocument(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Documentetatb>
     */
    public function getDocumentetatbs(): Collection
    {
        return $this->documentetatbs;
    }

    public function addDocumentetatb(Documentetatb $documentetatb): static
    {
        if (!$this->documentetatbs->contains($documentetatb)) {
            $this->documentetatbs->add($documentetatb);
            $documentetatb->setTypeDocument($this);
        }

        return $this;
    }

    public function removeDocumentetatb(Documentetatb $documentetatb): static
    {
        if ($this->documentetatbs->removeElement($documentetatb)) {
            // set the owning side to null (unless already changed)
            if ($documentetatb->getTypeDocument() === $this) {
                $documentetatb->setTypeDocument(null);
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
            $suiviDoc->setDocumentType($this);
        }

        return $this;
    }

    public function removeSuiviDoc(SuiviDoc $suiviDoc): static
    {
        if ($this->suiviDocs->removeElement($suiviDoc)) {
            // set the owning side to null (unless already changed)
            if ($suiviDoc->getDocumentType() === $this) {
                $suiviDoc->setDocumentType(null);
            }
        }

        return $this;
    }

    public function isLettre(): ?bool
    {
        return $this->lettre;
    }

    public function setLettre(?bool $lettre): static
    {
        $this->lettre = $lettre;

        return $this;
    }
}
