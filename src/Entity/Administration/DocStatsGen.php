<?php

namespace App\Entity\Administration;

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
use App\Entity\DocStats\Pages\Pagebcbp;
use App\Entity\DocStats\Pages\Pagebth;
use App\Entity\DocStats\Pages\Pagepdtdrv;
use App\Entity\References\PageDocGen;
use App\Entity\References\TypeDocumentStatistique;
use App\Repository\Administration\DocStatsGenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.doc_stats_gen')]
#[ORM\Entity(repositoryClass: DocStatsGenRepository::class)]
class DocStatsGen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 1)]
    private ?string $lettre = null;

    #[ORM\Column]
    private ?int $annee = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_doc = null;

    #[ORM\ManyToOne(inversedBy: 'docStatsGens')]
    private ?TypeDocumentStatistique $docname = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\OneToMany(mappedBy: 'code_doc_gen', targetEntity: PageDocGen::class)]
    private Collection $pageDocGens;

    #[ORM\ManyToOne(inversedBy: 'docStatsGens')]
    private ?StockDoc $code_type_doc = null;

    #[ORM\Column(nullable: true)]
    private ?bool $attribue = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $uniqueDoc = null;

    #[ORM\Column(nullable: true)]
    private ?int $numdoc = null;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentcp::class)]
    private Collection $documentcps;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentbrh::class)]
    private Collection $documentbrhs;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentlje::class)]
    private Collection $documentljes;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentbtgu::class)]
    private Collection $documentbtgus;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentfp::class)]
    private Collection $documentfps;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentbcbp::class)]
    private Collection $documentbcbps;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentetate::class)]
    private Collection $documentetates;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentetate2::class)]
    private Collection $documentetate2s;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentetatg::class)]
    private Collection $documentetatgs;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentetath::class)]
    private Collection $documentetaths;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentdmp::class)]
    private Collection $documentdmps;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentdmv::class)]
    private Collection $documentdmvs;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentpdtdrv::class)]
    private Collection $documentpdtdrvs;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Pagepdtdrv::class)]
    private Collection $pagepdtdrvs;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentbcburb::class)]
    private Collection $documentbcburbs;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentbrepf::class)]
    private Collection $documentbrepfs;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentrsdpf::class)]
    private Collection $documentrsdpfs;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Documentbth::class)]
    private Collection $documentbths;

    public function __construct()
    {
        $this->pageDocGens = new ArrayCollection();
        $this->documentcps = new ArrayCollection();
        $this->documentbrhs = new ArrayCollection();
        $this->documentljes = new ArrayCollection();
        $this->documentbtgus = new ArrayCollection();
        $this->documentfps = new ArrayCollection();
        $this->documentbcbps = new ArrayCollection();
        $this->documentetates = new ArrayCollection();
        $this->documentetate2s = new ArrayCollection();
        $this->documentetatgs = new ArrayCollection();
        $this->documentetaths = new ArrayCollection();
        $this->documentdmps = new ArrayCollection();
        $this->documentdmvs = new ArrayCollection();
        $this->documentpdtdrvs = new ArrayCollection();
        $this->pagepdtdrvs = new ArrayCollection();
        $this->documentbcburbs = new ArrayCollection();
        $this->documentbrepfs = new ArrayCollection();
        $this->documentrsdpfs = new ArrayCollection();
        $this->documentbths = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAnnee(): ?int
    {
        return $this->annee;
    }

    public function setAnnee(int $annee): static
    {
        $this->annee = $annee;

        return $this;
    }

    public function getNumeroDoc(): ?string
    {
        return $this->numero_doc;
    }

    public function setNumeroDoc(string $numero_doc): static
    {
        $this->numero_doc = $numero_doc;

        return $this;
    }

    public function getDocname(): ?TypeDocumentStatistique
    {
        return $this->docname;
    }

    public function setDocname(?TypeDocumentStatistique $docname): static
    {
        $this->docname = $docname;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): static
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
     * @return Collection<int, PageDocGen>
     */
    public function getPageDocGens(): Collection
    {
        return $this->pageDocGens;
    }

    public function addPageDocGen(PageDocGen $pageDocGen): static
    {
        if (!$this->pageDocGens->contains($pageDocGen)) {
            $this->pageDocGens->add($pageDocGen);
            $pageDocGen->setCodeDocGen($this);
        }

        return $this;
    }

    public function removePageDocGen(PageDocGen $pageDocGen): static
    {
        if ($this->pageDocGens->removeElement($pageDocGen)) {
            // set the owning side to null (unless already changed)
            if ($pageDocGen->getCodeDocGen() === $this) {
                $pageDocGen->setCodeDocGen(null);
            }
        }

        return $this;
    }

    public function getCodeTypeDoc(): ?StockDoc
    {
        return $this->code_type_doc;
    }

    public function setCodeTypeDoc(?StockDoc $code_type_doc): static
    {
        $this->code_type_doc = $code_type_doc;

        return $this;
    }

    public function isAttribue(): ?bool
    {
        return $this->attribue;
    }

    public function setAttribue(bool $attribue): static
    {
        $this->attribue = $attribue;

        return $this;
    }

    public function getUniqueDoc(): ?string
    {
        return $this->uniqueDoc;
    }

    public function setUniqueDoc(?string $uniqueDoc): static
    {
        $this->uniqueDoc = $uniqueDoc;

        return $this;
    }

    public function getNumdoc(): ?int
    {
        return $this->numdoc;
    }

    public function setNumdoc(?int $numdoc): static
    {
        $this->numdoc = $numdoc;

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
            $documentcp->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentcp(Documentcp $documentcp): static
    {
        if ($this->documentcps->removeElement($documentcp)) {
            // set the owning side to null (unless already changed)
            if ($documentcp->getCodeGeneration() === $this) {
                $documentcp->setCodeGeneration(null);
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
            $documentbrh->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentbrh(Documentbrh $documentbrh): static
    {
        if ($this->documentbrhs->removeElement($documentbrh)) {
            // set the owning side to null (unless already changed)
            if ($documentbrh->getCodeGeneration() === $this) {
                $documentbrh->setCodeGeneration(null);
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
            $documentlje->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentlje(Documentlje $documentlje): static
    {
        if ($this->documentljes->removeElement($documentlje)) {
            // set the owning side to null (unless already changed)
            if ($documentlje->getCodeGeneration() === $this) {
                $documentlje->setCodeGeneration(null);
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
            $documentbtgu->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentbtgu(Documentbtgu $documentbtgu): static
    {
        if ($this->documentbtgus->removeElement($documentbtgu)) {
            // set the owning side to null (unless already changed)
            if ($documentbtgu->getCodeGeneration() === $this) {
                $documentbtgu->setCodeGeneration(null);
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
            $documentfp->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentfp(Documentfp $documentfp): static
    {
        if ($this->documentfps->removeElement($documentfp)) {
            // set the owning side to null (unless already changed)
            if ($documentfp->getCodeGeneration() === $this) {
                $documentfp->setCodeGeneration(null);
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
            $documentbcbp->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentbcbp(Documentbcbp $documentbcbp): static
    {
        if ($this->documentbcbps->removeElement($documentbcbp)) {
            // set the owning side to null (unless already changed)
            if ($documentbcbp->getCodeGeneration() === $this) {
                $documentbcbp->setCodeGeneration(null);
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
            $documentetate->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentetate(Documentetate $documentetate): static
    {
        if ($this->documentetates->removeElement($documentetate)) {
            // set the owning side to null (unless already changed)
            if ($documentetate->getCodeGeneration() === $this) {
                $documentetate->setCodeGeneration(null);
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
            $documentetate2->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentetate2(Documentetate2 $documentetate2): static
    {
        if ($this->documentetate2s->removeElement($documentetate2)) {
            // set the owning side to null (unless already changed)
            if ($documentetate2->getCodeGeneration() === $this) {
                $documentetate2->setCodeGeneration(null);
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
            $documentetatg->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentetatg(Documentetatg $documentetatg): static
    {
        if ($this->documentetatgs->removeElement($documentetatg)) {
            // set the owning side to null (unless already changed)
            if ($documentetatg->getCodeGeneration() === $this) {
                $documentetatg->setCodeGeneration(null);
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
            $documentetath->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentetath(Documentetath $documentetath): static
    {
        if ($this->documentetaths->removeElement($documentetath)) {
            // set the owning side to null (unless already changed)
            if ($documentetath->getCodeGeneration() === $this) {
                $documentetath->setCodeGeneration(null);
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
            $documentdmp->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentdmp(Documentdmp $documentdmp): static
    {
        if ($this->documentdmps->removeElement($documentdmp)) {
            // set the owning side to null (unless already changed)
            if ($documentdmp->getCodeGeneration() === $this) {
                $documentdmp->setCodeGeneration(null);
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
            $documentdmv->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentdmv(Documentdmv $documentdmv): static
    {
        if ($this->documentdmvs->removeElement($documentdmv)) {
            // set the owning side to null (unless already changed)
            if ($documentdmv->getCodeGeneration() === $this) {
                $documentdmv->setCodeGeneration(null);
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
            $documentpdtdrv->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentpdtdrv(Documentpdtdrv $documentpdtdrv): static
    {
        if ($this->documentpdtdrvs->removeElement($documentpdtdrv)) {
            // set the owning side to null (unless already changed)
            if ($documentpdtdrv->getCodeGeneration() === $this) {
                $documentpdtdrv->setCodeGeneration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pagepdtdrv>
     */
    public function getPagepdtdrvs(): Collection
    {
        return $this->pagepdtdrvs;
    }

    public function addPagepdtdrv(Pagepdtdrv $pagepdtdrv): static
    {
        if (!$this->pagepdtdrvs->contains($pagepdtdrv)) {
            $this->pagepdtdrvs->add($pagepdtdrv);
            $pagepdtdrv->setCodeGeneration($this);
        }

        return $this;
    }

    public function removePagepdtdrv(Pagepdtdrv $pagepdtdrv): static
    {
        if ($this->pagepdtdrvs->removeElement($pagepdtdrv)) {
            // set the owning side to null (unless already changed)
            if ($pagepdtdrv->getCodeGeneration() === $this) {
                $pagepdtdrv->setCodeGeneration(null);
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
            $documentbcburb->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentbcburb(Documentbcburb $documentbcburb): static
    {
        if ($this->documentbcburbs->removeElement($documentbcburb)) {
            // set the owning side to null (unless already changed)
            if ($documentbcburb->getCodeGeneration() === $this) {
                $documentbcburb->setCodeGeneration(null);
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
            $documentbrepf->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentbrepf(Documentbrepf $documentbrepf): static
    {
        if ($this->documentbrepfs->removeElement($documentbrepf)) {
            // set the owning side to null (unless already changed)
            if ($documentbrepf->getCodeGeneration() === $this) {
                $documentbrepf->setCodeGeneration(null);
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
            $documentrsdpf->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentrsdpf(Documentrsdpf $documentrsdpf): static
    {
        if ($this->documentrsdpfs->removeElement($documentrsdpf)) {
            // set the owning side to null (unless already changed)
            if ($documentrsdpf->getCodeGeneration() === $this) {
                $documentrsdpf->setCodeGeneration(null);
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
            $documentbth->setCodeGeneration($this);
        }

        return $this;
    }

    public function removeDocumentbth(Documentbth $documentbth): static
    {
        if ($this->documentbths->removeElement($documentbth)) {
            // set the owning side to null (unless already changed)
            if ($documentbth->getCodeGeneration() === $this) {
                $documentbth->setCodeGeneration(null);
            }
        }

        return $this;
    }

}
