<?php

namespace App\Entity\References;

use App\Entity\Administration\DocStatsGen;
use App\Entity\DocStats\Pages\Pagebcbp;
use App\Entity\DocStats\Pages\Pagebcburb;
use App\Entity\DocStats\Pages\Pagebrepf;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Pages\Pagebth;
use App\Entity\DocStats\Pages\Pagecp;
use App\Entity\DocStats\Pages\Pagedmp;
use App\Entity\DocStats\Pages\Pagedmv;
use App\Entity\DocStats\Pages\Pageetatb;
use App\Entity\DocStats\Pages\Pageetate;
use App\Entity\DocStats\Pages\Pageetate2;
use App\Entity\DocStats\Pages\Pageetatg;
use App\Entity\DocStats\Pages\Pageetath;
use App\Entity\DocStats\Pages\Pagefp;
use App\Entity\DocStats\Pages\Pagersdpf;
use App\Entity\DocStats\Saisie\Lignepageetate2;
use App\Entity\DocStats\Saisie\Lignepagepdtdrv;
use App\Repository\References\PageDocGenRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.page_doc_gen')]
#[ORM\Entity(repositoryClass: PageDocGenRepository::class)]
class PageDocGen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_page = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\ManyToOne(inversedBy: 'pageDocGens')]
    private ?DocStatsGen $code_doc_gen = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $numpage = null;

    #[ORM\Column(nullable: true)]
    private ?int $doctype = null;

    #[ORM\Column(nullable: true)]
    private ?int $seqPage = null;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Pagecp::class)]
    private Collection $pagecps;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Pagebrh::class)]
    private Collection $pagebrhs;

    #[ORM\Column(nullable: true)]
    private ?bool $attribue = null;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Pagefp::class)]
    private Collection $pagefps;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Pageetate::class)]
    private Collection $pageetates;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Pageetatg::class)]
    private Collection $pageetatgs;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Pagedmp::class)]
    private Collection $pagedmps;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Pagedmv::class)]
    private Collection $pagedmvs;

    #[ORM\OneToMany(mappedBy: 'code_page_pdtdrv', targetEntity: Lignepagepdtdrv::class)]
    private Collection $lignepagepdtdrvs;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Pageetate2::class)]
    private Collection $pageetate2s;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Pageetath::class)]
    private Collection $pageetaths;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Pageetatb::class)]
    private Collection $pageetatbs;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Pagebcburb::class)]
    private Collection $pagebcburbs;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Pagebrepf::class)]
    private Collection $pagebrepfs;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Pagersdpf::class)]
    private Collection $pagersdpfs;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Pagebth::class)]
    private Collection $pagebths;

    #[ORM\OneToMany(mappedBy: 'code_generation', targetEntity: Pagebcbp::class)]
    private Collection $pagebcbps;

    public function __construct()
    {
        $this->pagecps = new ArrayCollection();
        $this->pagebrhs = new ArrayCollection();
        $this->pagefps = new ArrayCollection();
        $this->pageetates = new ArrayCollection();
        $this->pageetatgs = new ArrayCollection();
        $this->pagedmps = new ArrayCollection();
        $this->pagedmvs = new ArrayCollection();
        $this->lignepagepdtdrvs = new ArrayCollection();
        $this->pageetate2s = new ArrayCollection();
        $this->pageetaths = new ArrayCollection();
        $this->pageetatbs = new ArrayCollection();
        $this->pagebcburbs = new ArrayCollection();
        $this->pagebrepfs = new ArrayCollection();
        $this->pagersdpfs = new ArrayCollection();
        $this->pagebths = new ArrayCollection();
        $this->pagebcbps = new ArrayCollection();
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

    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    public function setUpdatedBy(?string $updated_by): static
    {
        $this->updated_by = $updated_by;

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

    public function getCodeDocGen(): ?DocStatsGen
    {
        return $this->code_doc_gen;
    }

    public function setCodeDocGen(?DocStatsGen $code_doc_gen): static
    {
        $this->code_doc_gen = $code_doc_gen;

        return $this;
    }

    public function getNumpage(): ?string
    {
        return $this->numpage;
    }

    public function setNumpage(?string $numpage): static
    {
        $this->numpage = $numpage;

        return $this;
    }

    public function getDoctype(): ?int
    {
        return $this->doctype;
    }

    public function setDoctype(?int $doctype): static
    {
        $this->doctype = $doctype;

        return $this;
    }

    public function getSeqPage(): ?int
    {
        return $this->seqPage;
    }

    public function setSeqPage(?int $seqPage): static
    {
        $this->seqPage = $seqPage;

        return $this;
    }

    /**
     * @return Collection<int, Pagecp>
     */
    public function getPagecps(): Collection
    {
        return $this->pagecps;
    }

    public function addPagecp(Pagecp $pagecp): static
    {
        if (!$this->pagecps->contains($pagecp)) {
            $this->pagecps->add($pagecp);
            $pagecp->setCodeGeneration($this);
        }

        return $this;
    }

    public function removePagecp(Pagecp $pagecp): static
    {
        if ($this->pagecps->removeElement($pagecp)) {
            // set the owning side to null (unless already changed)
            if ($pagecp->getCodeGeneration() === $this) {
                $pagecp->setCodeGeneration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pagebrh>
     */
    public function getPagebrhs(): Collection
    {
        return $this->pagebrhs;
    }

    public function addPagebrh(Pagebrh $pagebrh): static
    {
        if (!$this->pagebrhs->contains($pagebrh)) {
            $this->pagebrhs->add($pagebrh);
            $pagebrh->setCodeGeneration($this);
        }

        return $this;
    }

    public function removePagebrh(Pagebrh $pagebrh): static
    {
        if ($this->pagebrhs->removeElement($pagebrh)) {
            // set the owning side to null (unless already changed)
            if ($pagebrh->getCodeGeneration() === $this) {
                $pagebrh->setCodeGeneration(null);
            }
        }

        return $this;
    }

    public function isAttribue(): ?bool
    {
        return $this->attribue;
    }

    public function setAttribue(?bool $attribue): static
    {
        $this->attribue = $attribue;

        return $this;
    }

    /**
     * @return Collection<int, Pagefp>
     */
    public function getPagefps(): Collection
    {
        return $this->pagefps;
    }

    public function addPagefp(Pagefp $pagefp): static
    {
        if (!$this->pagefps->contains($pagefp)) {
            $this->pagefps->add($pagefp);
            $pagefp->setCodeGeneration($this);
        }

        return $this;
    }

    public function removePagefp(Pagefp $pagefp): static
    {
        if ($this->pagefps->removeElement($pagefp)) {
            // set the owning side to null (unless already changed)
            if ($pagefp->getCodeGeneration() === $this) {
                $pagefp->setCodeGeneration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pageetate>
     */
    public function getPageetates(): Collection
    {
        return $this->pageetates;
    }

    public function addPageetate(Pageetate $pageetate): static
    {
        if (!$this->pageetates->contains($pageetate)) {
            $this->pageetates->add($pageetate);
            $pageetate->setCodeGeneration($this);
        }

        return $this;
    }

    public function removePageetate(Pageetate $pageetate): static
    {
        if ($this->pageetates->removeElement($pageetate)) {
            // set the owning side to null (unless already changed)
            if ($pageetate->getCodeGeneration() === $this) {
                $pageetate->setCodeGeneration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pageetatg>
     */
    public function getPageetatgs(): Collection
    {
        return $this->pageetatgs;
    }

    public function addPageetatg(Pageetatg $pageetatg): static
    {
        if (!$this->pageetatgs->contains($pageetatg)) {
            $this->pageetatgs->add($pageetatg);
            $pageetatg->setCodeGeneration($this);
        }

        return $this;
    }

    public function removePageetatg(Pageetatg $pageetatg): static
    {
        if ($this->pageetatgs->removeElement($pageetatg)) {
            // set the owning side to null (unless already changed)
            if ($pageetatg->getCodeGeneration() === $this) {
                $pageetatg->setCodeGeneration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pagedmp>
     */
    public function getPagedmps(): Collection
    {
        return $this->pagedmps;
    }

    public function addPagedmp(Pagedmp $pagedmp): static
    {
        if (!$this->pagedmps->contains($pagedmp)) {
            $this->pagedmps->add($pagedmp);
            $pagedmp->setCodeGeneration($this);
        }

        return $this;
    }

    public function removePagedmp(Pagedmp $pagedmp): static
    {
        if ($this->pagedmps->removeElement($pagedmp)) {
            // set the owning side to null (unless already changed)
            if ($pagedmp->getCodeGeneration() === $this) {
                $pagedmp->setCodeGeneration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pagedmv>
     */
    public function getPagedmvs(): Collection
    {
        return $this->pagedmvs;
    }

    public function addPagedmv(Pagedmv $pagedmv): static
    {
        if (!$this->pagedmvs->contains($pagedmv)) {
            $this->pagedmvs->add($pagedmv);
            $pagedmv->setCodeGeneration($this);
        }

        return $this;
    }

    public function removePagedmv(Pagedmv $pagedmv): static
    {
        if ($this->pagedmvs->removeElement($pagedmv)) {
            // set the owning side to null (unless already changed)
            if ($pagedmv->getCodeGeneration() === $this) {
                $pagedmv->setCodeGeneration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lignepagepdtdrv>
     */
    public function getLignepagepdtdrvs(): Collection
    {
        return $this->lignepagepdtdrvs;
    }

    public function addLignepagepdtdrv(Lignepagepdtdrv $lignepagepdtdrv): static
    {
        if (!$this->lignepagepdtdrvs->contains($lignepagepdtdrv)) {
            $this->lignepagepdtdrvs->add($lignepagepdtdrv);
            $lignepagepdtdrv->setCodePagePdtdrv($this);
        }

        return $this;
    }

    public function removeLignepagepdtdrv(Lignepagepdtdrv $lignepagepdtdrv): static
    {
        if ($this->lignepagepdtdrvs->removeElement($lignepagepdtdrv)) {
            // set the owning side to null (unless already changed)
            if ($lignepagepdtdrv->getCodePagePdtdrv() === $this) {
                $lignepagepdtdrv->setCodePagePdtdrv(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pageetate2>
     */
    public function getPageetate2s(): Collection
    {
        return $this->pageetate2s;
    }

    public function addPageetate2(Pageetate2 $pageetate2): static
    {
        if (!$this->pageetate2s->contains($pageetate2)) {
            $this->pageetate2s->add($pageetate2);
            $pageetate2->setCodeGeneration($this);
        }

        return $this;
    }

    public function removePageetate2(Pageetate2 $pageetate2): static
    {
        if ($this->pageetate2s->removeElement($pageetate2)) {
            // set the owning side to null (unless already changed)
            if ($pageetate2->getCodeGeneration() === $this) {
                $pageetate2->setCodeGeneration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pageetath>
     */
    public function getPageetaths(): Collection
    {
        return $this->pageetaths;
    }

    public function addPageetath(Pageetath $pageetath): static
    {
        if (!$this->pageetaths->contains($pageetath)) {
            $this->pageetaths->add($pageetath);
            $pageetath->setCodeGeneration($this);
        }

        return $this;
    }

    public function removePageetath(Pageetath $pageetath): static
    {
        if ($this->pageetaths->removeElement($pageetath)) {
            // set the owning side to null (unless already changed)
            if ($pageetath->getCodeGeneration() === $this) {
                $pageetath->setCodeGeneration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pageetatb>
     */
    public function getPageetatbs(): Collection
    {
        return $this->pageetatbs;
    }

    public function addPageetatb(Pageetatb $pageetatb): static
    {
        if (!$this->pageetatbs->contains($pageetatb)) {
            $this->pageetatbs->add($pageetatb);
            $pageetatb->setCodeGeneration($this);
        }

        return $this;
    }

    public function removePageetatb(Pageetatb $pageetatb): static
    {
        if ($this->pageetatbs->removeElement($pageetatb)) {
            // set the owning side to null (unless already changed)
            if ($pageetatb->getCodeGeneration() === $this) {
                $pageetatb->setCodeGeneration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pagebcburb>
     */
    public function getPagebcburbs(): Collection
    {
        return $this->pagebcburbs;
    }

    public function addPagebcburb(Pagebcburb $pagebcburb): static
    {
        if (!$this->pagebcburbs->contains($pagebcburb)) {
            $this->pagebcburbs->add($pagebcburb);
            $pagebcburb->setCodeGeneration($this);
        }

        return $this;
    }

    public function removePagebcburb(Pagebcburb $pagebcburb): static
    {
        if ($this->pagebcburbs->removeElement($pagebcburb)) {
            // set the owning side to null (unless already changed)
            if ($pagebcburb->getCodeGeneration() === $this) {
                $pagebcburb->setCodeGeneration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pagebrepf>
     */
    public function getPagebrepfs(): Collection
    {
        return $this->pagebrepfs;
    }

    public function addPagebrepf(Pagebrepf $pagebrepf): static
    {
        if (!$this->pagebrepfs->contains($pagebrepf)) {
            $this->pagebrepfs->add($pagebrepf);
            $pagebrepf->setCodeGeneration($this);
        }

        return $this;
    }

    public function removePagebrepf(Pagebrepf $pagebrepf): static
    {
        if ($this->pagebrepfs->removeElement($pagebrepf)) {
            // set the owning side to null (unless already changed)
            if ($pagebrepf->getCodeGeneration() === $this) {
                $pagebrepf->setCodeGeneration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pagersdpf>
     */
    public function getPagersdpfs(): Collection
    {
        return $this->pagersdpfs;
    }

    public function addPagersdpf(Pagersdpf $pagersdpf): static
    {
        if (!$this->pagersdpfs->contains($pagersdpf)) {
            $this->pagersdpfs->add($pagersdpf);
            $pagersdpf->setCodeGeneration($this);
        }

        return $this;
    }

    public function removePagersdpf(Pagersdpf $pagersdpf): static
    {
        if ($this->pagersdpfs->removeElement($pagersdpf)) {
            // set the owning side to null (unless already changed)
            if ($pagersdpf->getCodeGeneration() === $this) {
                $pagersdpf->setCodeGeneration(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, Pagebth>
     */
    public function getPagebths(): Collection
    {
        return $this->pagebths;
    }

    public function addPagebth(Pagebth $pagebth): static
    {
        if (!$this->pagebths->contains($pagebth)) {
            $this->pagebths->add($pagebth);
            $pagebth->setCodeGeneration($this);
        }

        return $this;
    }

    public function removePagebth(Pagebth $pagebth): static
    {
        if ($this->pagebths->removeElement($pagebth)) {
            // set the owning side to null (unless already changed)
            if ($pagebth->getCodeGeneration() === $this) {
                $pagebth->setCodeGeneration(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pagebcbp>
     */
    public function getPagebcbps(): Collection
    {
        return $this->pagebcbps;
    }

    public function addPagebcbp(Pagebcbp $pagebcbp): static
    {
        if (!$this->pagebcbps->contains($pagebcbp)) {
            $this->pagebcbps->add($pagebcbp);
            $pagebcbp->setCodeGeneration($this);
        }

        return $this;
    }

    public function removePagebcbp(Pagebcbp $pagebcbp): static
    {
        if ($this->pagebcbps->removeElement($pagebcbp)) {
            // set the owning side to null (unless already changed)
            if ($pagebcbp->getCodeGeneration() === $this) {
                $pagebcbp->setCodeGeneration(null);
            }
        }

        return $this;
    }
}
