<?php

namespace App\Entity\References;

use App\Entity\Administration\InventaireForestier;
use App\Entity\DocStats\Pages\Pagebcbp;
use App\Entity\DocStats\Saisie\Lignepagebcbp;
use App\Entity\DocStats\Saisie\Lignepagebrepf;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\DocStats\Saisie\Lignepagebtgu;
use App\Entity\DocStats\Saisie\Lignepagecp;
use App\Entity\DocStats\Saisie\Lignepagedmp;
use App\Entity\DocStats\Saisie\Lignepagedmv;
use App\Entity\DocStats\Saisie\Lignepageetate;
use App\Entity\DocStats\Saisie\Lignepageetate2;
use App\Entity\DocStats\Saisie\Lignepageetatg;
use App\Entity\DocStats\Saisie\Lignepageetath;
use App\Entity\DocStats\Saisie\Lignepagefp;
use App\Entity\DocStats\Saisie\Lignepagelje;
use App\Entity\Transformation\Colis;
use App\Entity\Transformation\Contrat;
use App\Entity\Transformation\Details2Transfo;
use App\Entity\Transformation\Elements;
use App\Entity\Transformation\FicheJourTransfo;
use App\Repository\References\EssenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'metier.essence')]
#[ORM\Entity(repositoryClass: EssenceRepository::class)]
#[UniqueEntity(fields: ['nom_vernaculaire'], message: 'There is already a name with this forest species')]
class Essence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 4)]
    private ?string $numero_essence = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_vernaculaire = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $famille_essence = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nom_scientifique = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $categorie_essence = null;

    #[ORM\Column(nullable: true)]
    private ?int $taxe_abattage = null;

    #[ORM\Column]
    private ?int $dm_minima = null;

    #[ORM\Column(nullable: true)]
    private ?int $taxe_preservation = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'code_essence', targetEntity: InventaireForestier::class)]
    private Collection $inventaireForestiers;

    #[ORM\OneToMany(mappedBy: 'nom_essencecp', targetEntity: Lignepagecp::class)]
    private Collection $lignepagecps;

    #[ORM\OneToMany(mappedBy: 'nom_essencebrh', targetEntity: Lignepagebrh::class)]
    private Collection $lignepagebrhs;

    #[ORM\OneToMany(mappedBy: 'essence', targetEntity: Lignepagelje::class)]
    private Collection $lignepageljes;

    #[ORM\OneToMany(mappedBy: 'essence', targetEntity: Lignepagebtgu::class)]
    private Collection $lignepagebtgus;

    #[ORM\OneToMany(mappedBy: 'essence', targetEntity: Lignepagefp::class)]
    private Collection $lignepagefps;

    #[ORM\OneToMany(mappedBy: 'essence', targetEntity: Pagebcbp::class)]
    private Collection $pagebcbps;

    #[ORM\OneToMany(mappedBy: 'essence', targetEntity: Lignepageetate::class)]
    private Collection $lignepageetates;

    #[ORM\OneToMany(mappedBy: 'essence', targetEntity: Lignepageetate2::class)]
    private Collection $lignepageetate2s;

    #[ORM\OneToMany(mappedBy: 'code_essence', targetEntity: Lignepageetatg::class)]
    private Collection $lignepageetatgs;

    #[ORM\OneToMany(mappedBy: 'essence', targetEntity: Lignepageetath::class)]
    private Collection $lignepageetaths;

    #[ORM\OneToMany(mappedBy: 'esssence', targetEntity: Lignepagedmp::class)]
    private Collection $lignepagedmps;

    #[ORM\OneToMany(mappedBy: 'essence', targetEntity: Lignepagedmv::class)]
    private Collection $lignepagedmvs;

    #[ORM\OneToMany(mappedBy: 'essence', targetEntity: FicheJourTransfo::class)]
    private Collection $ficheJourTransfos;

    #[ORM\OneToMany(mappedBy: 'essence', targetEntity: Lignepagebrepf::class)]
    private Collection $lignepagebrepfs;

    #[ORM\ManyToMany(targetEntity: Contrat::class, mappedBy: 'essence')]
    private Collection $contrats;

    #[ORM\OneToMany(mappedBy: 'code_essence', targetEntity: Colis::class)]
    private Collection $colis;

    #[ORM\OneToMany(mappedBy: 'essence', targetEntity: Lignepagebcbp::class)]
    private Collection $lignepagebcbps;

    #[ORM\OneToMany(mappedBy: 'code_essence', targetEntity: Details2Transfo::class)]
    private Collection $details2Transfos;

    #[ORM\OneToMany(mappedBy: 'code_essence', targetEntity: Elements::class)]
    private Collection $elements;

    #[ORM\Column(nullable: true)]
    private ?bool $autorisation = null;

    public function __construct()
    {
        $this->inventaireForestiers = new ArrayCollection();
        $this->lignepagecps = new ArrayCollection();
        $this->lignepagebrhs = new ArrayCollection();
        $this->lignepageljes = new ArrayCollection();
        $this->lignepagebtgus = new ArrayCollection();
        $this->lignepagefps = new ArrayCollection();
        $this->pagebcbps = new ArrayCollection();
        $this->lignepageetates = new ArrayCollection();
        $this->lignepageetate2s = new ArrayCollection();
        $this->lignepageetatgs = new ArrayCollection();
        $this->lignepageetaths = new ArrayCollection();
        $this->lignepagedmps = new ArrayCollection();
        $this->lignepagedmvs = new ArrayCollection();
        $this->ficheJourTransfos = new ArrayCollection();
        $this->lignepagebrepfs = new ArrayCollection();
        $this->contrats = new ArrayCollection();
        $this->colis = new ArrayCollection();
        $this->lignepagebcbps = new ArrayCollection();
        $this->details2Transfos = new ArrayCollection();
        $this->elements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroEssence(): ?string
    {
        return $this->numero_essence;
    }

    public function setNumeroEssence(string $numero_essence): static
    {
        $this->numero_essence = $numero_essence;

        return $this;
    }

    public function getNomVernaculaire(): ?string
    {
        return $this->nom_vernaculaire;
    }

    public function setNomVernaculaire(string $nom_vernaculaire): static
    {
        $this->nom_vernaculaire = $nom_vernaculaire;

        return $this;
    }

    public function getFamilleEssence(): ?string
    {
        return $this->famille_essence;
    }

    public function setFamilleEssence(?string $famille_essence): static
    {
        $this->famille_essence = $famille_essence;

        return $this;
    }

    public function getNomScientifique(): ?string
    {
        return $this->nom_scientifique;
    }

    public function setNomScientifique(?string $nom_scientifique): static
    {
        $this->nom_scientifique = $nom_scientifique;

        return $this;
    }

    public function getCategorieEssence(): ?string
    {
        return $this->categorie_essence;
    }

    public function setCategorieEssence(?string $categorie_essence): static
    {
        $this->categorie_essence = $categorie_essence;

        return $this;
    }

    public function getTaxeAbattage(): ?int
    {
        return $this->taxe_abattage;
    }

    public function setTaxeAbattage(?int $taxe_abattage): static
    {
        $this->taxe_abattage = $taxe_abattage;

        return $this;
    }

    public function getDmMinima(): ?int
    {
        return $this->dm_minima;
    }

    public function setDmMinima(int $dm_minima): static
    {
        $this->dm_minima = $dm_minima;

        return $this;
    }

    public function getTaxePreservation(): ?int
    {
        return $this->taxe_preservation;
    }

    public function setTaxePreservation(?int $taxe_preservation): static
    {
        $this->taxe_preservation = $taxe_preservation;

        return $this;
    }
    public function __toString(): string
    {
        return $this->nom_vernaculaire;
    }

    /**
     * @return Collection<int, InventaireForestier>
     */
    public function getInventaireForestiers(): Collection
    {
        return $this->inventaireForestiers;
    }

    public function addInventaireForestier(InventaireForestier $inventaireForestier): static
    {
        if (!$this->inventaireForestiers->contains($inventaireForestier)) {
            $this->inventaireForestiers->add($inventaireForestier);
            $inventaireForestier->setCodeEssence($this);
        }

        return $this;
    }

    public function removeInventaireForestier(InventaireForestier $inventaireForestier): static
    {
        if ($this->inventaireForestiers->removeElement($inventaireForestier)) {
            // set the owning side to null (unless already changed)
            if ($inventaireForestier->getCodeEssence() === $this) {
                $inventaireForestier->setCodeEssence(null);
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
            $lignepagecp->setNomEssencecp($this);
        }

        return $this;
    }

    public function removeLignepagecp(Lignepagecp $lignepagecp): static
    {
        if ($this->lignepagecps->removeElement($lignepagecp)) {
            // set the owning side to null (unless already changed)
            if ($lignepagecp->getNomEssencecp() === $this) {
                $lignepagecp->setNomEssencecp(null);
            }
        }

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
            $lignepagebrh->setNomEssencebrh($this);
        }

        return $this;
    }

    public function removeLignepagebrh(Lignepagebrh $lignepagebrh): static
    {
        if ($this->lignepagebrhs->removeElement($lignepagebrh)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebrh->getNomEssencebrh() === $this) {
                $lignepagebrh->setNomEssencebrh(null);
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
            $lignepagelje->setEssence($this);
        }

        return $this;
    }

    public function removeLignepagelje(Lignepagelje $lignepagelje): static
    {
        if ($this->lignepageljes->removeElement($lignepagelje)) {
            // set the owning side to null (unless already changed)
            if ($lignepagelje->getEssence() === $this) {
                $lignepagelje->setEssence(null);
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
            $lignepagebtgu->setEssence($this);
        }

        return $this;
    }

    public function removeLignepagebtgu(Lignepagebtgu $lignepagebtgu): static
    {
        if ($this->lignepagebtgus->removeElement($lignepagebtgu)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebtgu->getEssence() === $this) {
                $lignepagebtgu->setEssence(null);
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
            $lignepagefp->setEssence($this);
        }

        return $this;
    }

    public function removeLignepagefp(Lignepagefp $lignepagefp): static
    {
        if ($this->lignepagefps->removeElement($lignepagefp)) {
            // set the owning side to null (unless already changed)
            if ($lignepagefp->getEssence() === $this) {
                $lignepagefp->setEssence(null);
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
            $pagebcbp->setEssence($this);
        }

        return $this;
    }

    public function removePagebcbp(Pagebcbp $pagebcbp): static
    {
        if ($this->pagebcbps->removeElement($pagebcbp)) {
            // set the owning side to null (unless already changed)
            if ($pagebcbp->getEssence() === $this) {
                $pagebcbp->setEssence(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lignepageetate>
     */
    public function getLignepageetates(): Collection
    {
        return $this->lignepageetates;
    }

    public function addLignepageetate(Lignepageetate $lignepageetate): static
    {
        if (!$this->lignepageetates->contains($lignepageetate)) {
            $this->lignepageetates->add($lignepageetate);
            $lignepageetate->setEssence($this);
        }

        return $this;
    }

    public function removeLignepageetate(Lignepageetate $lignepageetate): static
    {
        if ($this->lignepageetates->removeElement($lignepageetate)) {
            // set the owning side to null (unless already changed)
            if ($lignepageetate->getEssence() === $this) {
                $lignepageetate->setEssence(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lignepageetate2>
     */
    public function getLignepageetate2s(): Collection
    {
        return $this->lignepageetate2s;
    }

    public function addLignepageetate2(Lignepageetate2 $lignepageetate2): static
    {
        if (!$this->lignepageetate2s->contains($lignepageetate2)) {
            $this->lignepageetate2s->add($lignepageetate2);
            $lignepageetate2->setEssence($this);
        }

        return $this;
    }

    public function removeLignepageetate2(Lignepageetate2 $lignepageetate2): static
    {
        if ($this->lignepageetate2s->removeElement($lignepageetate2)) {
            // set the owning side to null (unless already changed)
            if ($lignepageetate2->getEssence() === $this) {
                $lignepageetate2->setEssence(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lignepageetatg>
     */
    public function getLignepageetatgs(): Collection
    {
        return $this->lignepageetatgs;
    }

    public function addLignepageetatg(Lignepageetatg $lignepageetatg): static
    {
        if (!$this->lignepageetatgs->contains($lignepageetatg)) {
            $this->lignepageetatgs->add($lignepageetatg);
            $lignepageetatg->setCodeEssence($this);
        }

        return $this;
    }

    public function removeLignepageetatg(Lignepageetatg $lignepageetatg): static
    {
        if ($this->lignepageetatgs->removeElement($lignepageetatg)) {
            // set the owning side to null (unless already changed)
            if ($lignepageetatg->getCodeEssence() === $this) {
                $lignepageetatg->setCodeEssence(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lignepageetath>
     */
    public function getLignepageetaths(): Collection
    {
        return $this->lignepageetaths;
    }

    public function addLignepageetath(Lignepageetath $lignepageetath): static
    {
        if (!$this->lignepageetaths->contains($lignepageetath)) {
            $this->lignepageetaths->add($lignepageetath);
            $lignepageetath->setEssence($this);
        }

        return $this;
    }

    public function removeLignepageetath(Lignepageetath $lignepageetath): static
    {
        if ($this->lignepageetaths->removeElement($lignepageetath)) {
            // set the owning side to null (unless already changed)
            if ($lignepageetath->getEssence() === $this) {
                $lignepageetath->setEssence(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lignepagedmp>
     */
    public function getLignepagedmps(): Collection
    {
        return $this->lignepagedmps;
    }

    public function addLignepagedmp(Lignepagedmp $lignepagedmp): static
    {
        if (!$this->lignepagedmps->contains($lignepagedmp)) {
            $this->lignepagedmps->add($lignepagedmp);
            $lignepagedmp->setEsssence($this);
        }

        return $this;
    }

    public function removeLignepagedmp(Lignepagedmp $lignepagedmp): static
    {
        if ($this->lignepagedmps->removeElement($lignepagedmp)) {
            // set the owning side to null (unless already changed)
            if ($lignepagedmp->getEsssence() === $this) {
                $lignepagedmp->setEsssence(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lignepagedmv>
     */
    public function getLignepagedmvs(): Collection
    {
        return $this->lignepagedmvs;
    }

    public function addLignepagedmv(Lignepagedmv $lignepagedmv): static
    {
        if (!$this->lignepagedmvs->contains($lignepagedmv)) {
            $this->lignepagedmvs->add($lignepagedmv);
            $lignepagedmv->setEssence($this);
        }

        return $this;
    }

    public function removeLignepagedmv(Lignepagedmv $lignepagedmv): static
    {
        if ($this->lignepagedmvs->removeElement($lignepagedmv)) {
            // set the owning side to null (unless already changed)
            if ($lignepagedmv->getEssence() === $this) {
                $lignepagedmv->setEssence(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FicheJourTransfo>
     */
    public function getFicheJourTransfos(): Collection
    {
        return $this->ficheJourTransfos;
    }

    public function addFicheJourTransfo(FicheJourTransfo $ficheJourTransfo): static
    {
        if (!$this->ficheJourTransfos->contains($ficheJourTransfo)) {
            $this->ficheJourTransfos->add($ficheJourTransfo);
            $ficheJourTransfo->setEssence($this);
        }

        return $this;
    }

    public function removeFicheJourTransfo(FicheJourTransfo $ficheJourTransfo): static
    {
        if ($this->ficheJourTransfos->removeElement($ficheJourTransfo)) {
            // set the owning side to null (unless already changed)
            if ($ficheJourTransfo->getEssence() === $this) {
                $ficheJourTransfo->setEssence(null);
            }
        }

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
            $lignepagebrepf->setEssence($this);
        }

        return $this;
    }

    public function removeLignepagebrepf(Lignepagebrepf $lignepagebrepf): static
    {
        if ($this->lignepagebrepfs->removeElement($lignepagebrepf)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebrepf->getEssence() === $this) {
                $lignepagebrepf->setEssence(null);
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
            $contrat->addEssence($this);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): static
    {
        if ($this->contrats->removeElement($contrat)) {
            $contrat->removeEssence($this);
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
            $coli->setCodeEssence($this);
        }

        return $this;
    }

    public function removeColi(Colis $coli): static
    {
        if ($this->colis->removeElement($coli)) {
            // set the owning side to null (unless already changed)
            if ($coli->getCodeEssence() === $this) {
                $coli->setCodeEssence(null);
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
            $lignepagebcbp->setEssence($this);
        }

        return $this;
    }

    public function removeLignepagebcbp(Lignepagebcbp $lignepagebcbp): static
    {
        if ($this->lignepagebcbps->removeElement($lignepagebcbp)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebcbp->getEssence() === $this) {
                $lignepagebcbp->setEssence(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Details2Transfo>
     */
    public function getDetails2Transfos(): Collection
    {
        return $this->details2Transfos;
    }

    public function addDetails2Transfo(Details2Transfo $details2Transfo): static
    {
        if (!$this->details2Transfos->contains($details2Transfo)) {
            $this->details2Transfos->add($details2Transfo);
            $details2Transfo->setCodeEssence($this);
        }

        return $this;
    }

    public function removeDetails2Transfo(Details2Transfo $details2Transfo): static
    {
        if ($this->details2Transfos->removeElement($details2Transfo)) {
            // set the owning side to null (unless already changed)
            if ($details2Transfo->getCodeEssence() === $this) {
                $details2Transfo->setCodeEssence(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Elements>
     */
    public function getElements(): Collection
    {
        return $this->elements;
    }

    public function addElement(Elements $element): static
    {
        if (!$this->elements->contains($element)) {
            $this->elements->add($element);
            $element->setCodeEssence($this);
        }

        return $this;
    }

    public function removeElement(Elements $element): static
    {
        if ($this->elements->removeElement($element)) {
            // set the owning side to null (unless already changed)
            if ($element->getCodeEssence() === $this) {
                $element->setCodeEssence(null);
            }
        }

        return $this;
    }

    public function isAutorisation(): ?bool
    {
        return $this->autorisation;
    }

    public function setAutorisation(?bool $autorisation): static
    {
        $this->autorisation = $autorisation;

        return $this;
    }
}
