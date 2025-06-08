<?php

namespace App\Entity\Administration;

use App\Entity\Autorisation\AutorisationPs;
use App\Entity\Autorisation\AutorisationPv;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentbcbp;
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
use App\Entity\References\CircuitCommunication;
use App\Entity\References\Commercant;
use App\Entity\References\Exportateur;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\References\TypeOperateur;
use App\Entity\References\Usine;
use App\Entity\User;
use App\Repository\DemandeOperateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.demande_operateur')]
#[ORM\Entity(repositoryClass: DemandeOperateurRepository::class)]
class DemandeOperateur
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'demandeOperateurs')]
    private ?TypeDocumentStatistique $doc_stat = null;

    #[ORM\Column(nullable: true)]
    private ?int $qte = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $statut = null;

    #[ORM\Column(nullable: true)]
    private ?bool $transmission = null;

    #[ORM\Column(nullable: true)]
    private ?bool $verification = null;

    #[ORM\Column(nullable: true)]
    private ?bool $delivrance = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_verification = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_delivrance = null;

    #[ORM\ManyToOne(inversedBy: 'demandeOperateurs')]
    private ?User $demandeur = null;

    #[ORM\Column(nullable: true)]
    private ?int $qte_validee = null;

    #[ORM\Column(nullable: true)]
    private ?int $qte_delivree = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $motif_verification = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $motif_delivrance = null;

    #[ORM\ManyToOne(inversedBy: 'demandeOperateurs')]
    private ?TypeOperateur $code_operateur = null;

    #[ORM\Column(nullable: true)]
    private ?int $code_structure = null;

    #[ORM\ManyToOne(inversedBy: 'demandeOperateurs')]
    private ?Reprise $code_reprise = null;

    #[ORM\OneToMany(mappedBy: 'code_demande_operateur', targetEntity: CircuitCommunication::class)]
    private Collection $circuitCommunications;

    #[ORM\Column(nullable: true)]
    private ?bool $docs_generes = null;

    #[ORM\ManyToOne(inversedBy: 'demandeOperateurs')]
    private ?Usine $code_usine = null;

    #[ORM\ManyToOne(inversedBy: 'demandeOperateurs')]
    private ?Exportateur $code_exportateur = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $code = null;

    #[ORM\OneToMany(mappedBy: 'code_demande', targetEntity: Documentbrh::class)]
    private Collection $documentbrhs;

    #[ORM\OneToMany(mappedBy: 'code_demande', targetEntity: Documentcp::class)]
    private Collection $documentcps;

    #[ORM\OneToMany(mappedBy: 'code_demande', targetEntity: Documentlje::class)]
    private Collection $documentljes;

    #[ORM\OneToMany(mappedBy: 'code_demande', targetEntity: Documentbtgu::class)]
    private Collection $documentbtgus;

    #[ORM\OneToMany(mappedBy: 'code_demande', targetEntity: Documentfp::class)]
    private Collection $documentfps;

    #[ORM\OneToMany(mappedBy: 'code_demande', targetEntity: Documentbcbp::class)]
    private Collection $documentbcbps;

    #[ORM\OneToMany(mappedBy: 'code_demande', targetEntity: Documentetate::class)]
    private Collection $documentetates;

    #[ORM\OneToMany(mappedBy: 'code_demande', targetEntity: Documentetate2::class)]
    private Collection $documentetate2s;

    #[ORM\OneToMany(mappedBy: 'code_demande', targetEntity: Documentetatg::class)]
    private Collection $documentetatgs;

    #[ORM\OneToMany(mappedBy: 'code_demande', targetEntity: Documentetath::class)]
    private Collection $documentetaths;

    #[ORM\OneToMany(mappedBy: 'code_demande', targetEntity: Documentdmp::class)]
    private Collection $documentdmps;

    #[ORM\OneToMany(mappedBy: 'code_demande', targetEntity: Documentdmv::class)]
    private Collection $documentdmvs;

    #[ORM\OneToMany(mappedBy: 'code_demande', targetEntity: Documentpdtdrv::class)]
    private Collection $documentpdtdrvs;

    #[ORM\ManyToOne(inversedBy: 'demandeOperateurs')]
    private ?AutorisationPv $code_autorisation_pv = null;

    #[ORM\OneToMany(mappedBy: 'code_demande', targetEntity: Documentbrepf::class)]
    private Collection $documentbrepfs;

    #[ORM\OneToMany(mappedBy: 'code_demande', targetEntity: Documentrsdpf::class)]
    private Collection $documentrsdpfs;

    #[ORM\OneToMany(mappedBy: 'code_demande', targetEntity: Documentbth::class)]
    private Collection $documentbths;

    #[ORM\ManyToOne(inversedBy: 'demandeOperateurs')]
    private ?AutorisationPs $code_autorisationps = null;

    #[ORM\ManyToOne(inversedBy: 'demandeOperateurs')]
    private ?Commercant $code_commercant = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_dr = null;

    #[ORM\Column(nullable: true)]
    private ?bool $signature_cef = null;

    public function __construct()
    {
        $this->circuitCommunications = new ArrayCollection();
        $this->documentbrhs = new ArrayCollection();
        $this->documentcps = new ArrayCollection();
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
        $this->documentbrepfs = new ArrayCollection();
        $this->documentrsdpfs = new ArrayCollection();
        $this->documentbths = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocStat(): ?TypeDocumentStatistique
    {
        return $this->doc_stat;
    }

    public function setDocStat(?TypeDocumentStatistique $doc_stat): static
    {
        $this->doc_stat = $doc_stat;

        return $this;
    }

    public function getQte(): ?int
    {
        return $this->qte;
    }

    public function setQte(?int $qte): static
    {
        $this->qte = $qte;

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

    public function setUpdatedBy(string $updated_by): static
    {
        $this->updated_by = $updated_by;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function isTransmission(): ?bool
    {
        return $this->transmission;
    }

    public function setTransmission(?bool $transmission): static
    {
        $this->transmission = $transmission;

        return $this;
    }

    public function isVerification(): ?bool
    {
        return $this->verification;
    }

    public function setVerification(?bool $verification): static
    {
        $this->verification = $verification;

        return $this;
    }

    public function isDelivrance(): ?bool
    {
        return $this->delivrance;
    }

    public function setDelivrance(?bool $delivrance): static
    {
        $this->delivrance = $delivrance;

        return $this;
    }

    public function getDateVerification(): ?\DateTimeInterface
    {
        return $this->date_verification;
    }

    public function setDateVerification(?\DateTimeInterface $date_verification): static
    {
        $this->date_verification = $date_verification;

        return $this;
    }

    public function getDateDelivrance(): ?\DateTimeInterface
    {
        return $this->date_delivrance;
    }

    public function setDateDelivrance(?\DateTimeInterface $date_delivrance): static
    {
        $this->date_delivrance = $date_delivrance;

        return $this;
    }

    public function getDemandeur(): ?User
    {
        return $this->demandeur;
    }

    public function setDemandeur(?User $demandeur): static
    {
        $this->demandeur = $demandeur;

        return $this;
    }

    public function getQteValidee(): ?int
    {
        return $this->qte_validee;
    }

    public function setQteValidee(?int $qte_validee): static
    {
        $this->qte_validee = $qte_validee;

        return $this;
    }

    public function getQteDelivree(): ?int
    {
        return $this->qte_delivree;
    }

    public function setQteDelivree(?int $qte_delivree): static
    {
        $this->qte_delivree = $qte_delivree;

        return $this;
    }

    public function getMotifVerification(): ?string
    {
        return $this->motif_verification;
    }

    public function setMotifVerification(?string $motif_verification): static
    {
        $this->motif_verification = $motif_verification;

        return $this;
    }

    public function getMotifDelivrance(): ?string
    {
        return $this->motif_delivrance;
    }

    public function setMotifDelivrance(?string $motif_delivrance): static
    {
        $this->motif_delivrance = $motif_delivrance;

        return $this;
    }

    public function getCodeOperateur(): ?TypeOperateur
    {
        return $this->code_operateur;
    }

    public function setCodeOperateur(?TypeOperateur $code_operateur): static
    {
        $this->code_operateur = $code_operateur;

        return $this;
    }

    public function getCodeStructure(): ?int
    {
        return $this->code_structure;
    }

    public function setCodeStructure(?int $code_structure): static
    {
        $this->code_structure = $code_structure;

        return $this;
    }

    public function getCodeReprise(): ?Reprise
    {
        return $this->code_reprise;
    }

    public function setCodeReprise(?Reprise $code_reprise): static
    {
        $this->code_reprise = $code_reprise;

        return $this;
    }

    /**
     * @return Collection<int, CircuitCommunication>
     */
    public function getCircuitCommunications(): Collection
    {
        return $this->circuitCommunications;
    }

    public function addCircuitCommunication(CircuitCommunication $circuitCommunication): static
    {
        if (!$this->circuitCommunications->contains($circuitCommunication)) {
            $this->circuitCommunications->add($circuitCommunication);
            $circuitCommunication->setCodeDemandeOperateur($this);
        }

        return $this;
    }

    public function removeCircuitCommunication(CircuitCommunication $circuitCommunication): static
    {
        if ($this->circuitCommunications->removeElement($circuitCommunication)) {
            // set the owning side to null (unless already changed)
            if ($circuitCommunication->getCodeDemandeOperateur() === $this) {
                $circuitCommunication->setCodeDemandeOperateur(null);
            }
        }

        return $this;
    }

    public function isDocsGeneres(): ?bool
    {
        return $this->docs_generes;
    }

    public function setDocsGeneres(?bool $docs_generes): static
    {
        $this->docs_generes = $docs_generes;

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

    public function getCodeExportateur(): ?Exportateur
    {
        return $this->code_exportateur;
    }

    public function setCodeExportateur(?Exportateur $code_exportateur): static
    {
        $this->code_exportateur = $code_exportateur;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

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
            $documentbrh->setCodeDemande($this);
        }

        return $this;
    }

    public function removeDocumentbrh(Documentbrh $documentbrh): static
    {
        if ($this->documentbrhs->removeElement($documentbrh)) {
            // set the owning side to null (unless already changed)
            if ($documentbrh->getCodeDemande() === $this) {
                $documentbrh->setCodeDemande(null);
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
            $documentcp->setCodeDemande($this);
        }

        return $this;
    }

    public function removeDocumentcp(Documentcp $documentcp): static
    {
        if ($this->documentcps->removeElement($documentcp)) {
            // set the owning side to null (unless already changed)
            if ($documentcp->getCodeDemande() === $this) {
                $documentcp->setCodeDemande(null);
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
            $documentlje->setCodeDemande($this);
        }

        return $this;
    }

    public function removeDocumentlje(Documentlje $documentlje): static
    {
        if ($this->documentljes->removeElement($documentlje)) {
            // set the owning side to null (unless already changed)
            if ($documentlje->getCodeDemande() === $this) {
                $documentlje->setCodeDemande(null);
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
            $documentbtgu->setCodeDemande($this);
        }

        return $this;
    }

    public function removeDocumentbtgu(Documentbtgu $documentbtgu): static
    {
        if ($this->documentbtgus->removeElement($documentbtgu)) {
            // set the owning side to null (unless already changed)
            if ($documentbtgu->getCodeDemande() === $this) {
                $documentbtgu->setCodeDemande(null);
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
            $documentfp->setCodeDemande($this);
        }

        return $this;
    }

    public function removeDocumentfp(Documentfp $documentfp): static
    {
        if ($this->documentfps->removeElement($documentfp)) {
            // set the owning side to null (unless already changed)
            if ($documentfp->getCodeDemande() === $this) {
                $documentfp->setCodeDemande(null);
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
            $documentbcbp->setCodeDemande($this);
        }

        return $this;
    }

    public function removeDocumentbcbp(Documentbcbp $documentbcbp): static
    {
        if ($this->documentbcbps->removeElement($documentbcbp)) {
            // set the owning side to null (unless already changed)
            if ($documentbcbp->getCodeDemande() === $this) {
                $documentbcbp->setCodeDemande(null);
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
            $documentetate->setCodeDemande($this);
        }

        return $this;
    }

    public function removeDocumentetate(Documentetate $documentetate): static
    {
        if ($this->documentetates->removeElement($documentetate)) {
            // set the owning side to null (unless already changed)
            if ($documentetate->getCodeDemande() === $this) {
                $documentetate->setCodeDemande(null);
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
            $documentetate2->setCodeDemande($this);
        }

        return $this;
    }

    public function removeDocumentetate2(Documentetate2 $documentetate2): static
    {
        if ($this->documentetate2s->removeElement($documentetate2)) {
            // set the owning side to null (unless already changed)
            if ($documentetate2->getCodeDemande() === $this) {
                $documentetate2->setCodeDemande(null);
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
            $documentetatg->setCodeDemande($this);
        }

        return $this;
    }

    public function removeDocumentetatg(Documentetatg $documentetatg): static
    {
        if ($this->documentetatgs->removeElement($documentetatg)) {
            // set the owning side to null (unless already changed)
            if ($documentetatg->getCodeDemande() === $this) {
                $documentetatg->setCodeDemande(null);
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
            $documentetath->setCodeDemande($this);
        }

        return $this;
    }

    public function removeDocumentetath(Documentetath $documentetath): static
    {
        if ($this->documentetaths->removeElement($documentetath)) {
            // set the owning side to null (unless already changed)
            if ($documentetath->getCodeDemande() === $this) {
                $documentetath->setCodeDemande(null);
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
            $documentdmp->setCodeDemande($this);
        }

        return $this;
    }

    public function removeDocumentdmp(Documentdmp $documentdmp): static
    {
        if ($this->documentdmps->removeElement($documentdmp)) {
            // set the owning side to null (unless already changed)
            if ($documentdmp->getCodeDemande() === $this) {
                $documentdmp->setCodeDemande(null);
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
            $documentdmv->setCodeDemande($this);
        }

        return $this;
    }

    public function removeDocumentdmv(Documentdmv $documentdmv): static
    {
        if ($this->documentdmvs->removeElement($documentdmv)) {
            // set the owning side to null (unless already changed)
            if ($documentdmv->getCodeDemande() === $this) {
                $documentdmv->setCodeDemande(null);
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
            $documentpdtdrv->setCodeDemande($this);
        }

        return $this;
    }

    public function removeDocumentpdtdrv(Documentpdtdrv $documentpdtdrv): static
    {
        if ($this->documentpdtdrvs->removeElement($documentpdtdrv)) {
            // set the owning side to null (unless already changed)
            if ($documentpdtdrv->getCodeDemande() === $this) {
                $documentpdtdrv->setCodeDemande(null);
            }
        }

        return $this;
    }

    public function getCodeAutorisationPv(): ?AutorisationPv
    {
        return $this->code_autorisation_pv;
    }

    public function setCodeAutorisationPv(?AutorisationPv $code_autorisation_pv): static
    {
        $this->code_autorisation_pv = $code_autorisation_pv;

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
            $documentbrepf->setCodeDemande($this);
        }

        return $this;
    }

    public function removeDocumentbrepf(Documentbrepf $documentbrepf): static
    {
        if ($this->documentbrepfs->removeElement($documentbrepf)) {
            // set the owning side to null (unless already changed)
            if ($documentbrepf->getCodeDemande() === $this) {
                $documentbrepf->setCodeDemande(null);
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
            $documentrsdpf->setCodeDemande($this);
        }

        return $this;
    }

    public function removeDocumentrsdpf(Documentrsdpf $documentrsdpf): static
    {
        if ($this->documentrsdpfs->removeElement($documentrsdpf)) {
            // set the owning side to null (unless already changed)
            if ($documentrsdpf->getCodeDemande() === $this) {
                $documentrsdpf->setCodeDemande(null);
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
            $documentbth->setCodeDemande($this);
        }

        return $this;
    }

    public function removeDocumentbth(Documentbth $documentbth): static
    {
        if ($this->documentbths->removeElement($documentbth)) {
            // set the owning side to null (unless already changed)
            if ($documentbth->getCodeDemande() === $this) {
                $documentbth->setCodeDemande(null);
            }
        }

        return $this;
    }

    public function getCodeAutorisationps(): ?AutorisationPs
    {
        return $this->code_autorisationps;
    }

    public function setCodeAutorisationps(?AutorisationPs $code_autorisationps): static
    {
        $this->code_autorisationps = $code_autorisationps;

        return $this;
    }

    public function getCodeCommercant(): ?Commercant
    {
        return $this->code_commercant;
    }

    public function setCodeCommercant(?Commercant $code_commercant): static
    {
        $this->code_commercant = $code_commercant;

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

    public function isSignatureCef(): ?bool
    {
        return $this->signature_cef;
    }

    public function setSignatureCef(?bool $signature_cef): static
    {
        $this->signature_cef = $signature_cef;

        return $this;
    }
}
