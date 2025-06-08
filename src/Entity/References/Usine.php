<?php

namespace App\Entity\References;

use App\Entity\Administration\DemandeOperateur;
use App\Entity\DocStats\Entetes\Documentbtgu;
use App\Entity\DocStats\Entetes\Documentdmp;
use App\Entity\DocStats\Entetes\Documentdmv;
use App\Entity\DocStats\Entetes\Documentetate;
use App\Entity\DocStats\Entetes\Documentetate2;
use App\Entity\DocStats\Entetes\Documentetatg;
use App\Entity\DocStats\Entetes\Documentetath;
use App\Entity\DocStats\Entetes\Documentfp;
use App\Entity\DocStats\Entetes\Documentlje;
use App\Entity\DocStats\Entetes\Documentpdtdrv;
use App\Entity\DocStats\Pages\Pagebcbp;
use App\Entity\DocStats\Pages\Pagebcburb;
use App\Entity\DocStats\Pages\Pagebrepf;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Pages\Pagebtgu;
use App\Entity\DocStats\Saisie\Lignepageetate;
use App\Entity\DocStats\Saisie\Lignepageetate2;
use App\Entity\DocStats\Saisie\Lignepagersdpf;
use App\Entity\Transformation\Contrat;
use App\Entity\Transformation\Fiche2Transfo;
use App\Entity\Transformation\FicheLot;
use App\Entity\Transformation\FicheLotProd;
use App\Entity\User;
use App\Repository\References\UsineRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Table(name: 'metier.usine')]
#[ORM\Entity(repositoryClass: UsineRepository::class)]
class Usine
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numero_usine = null;

    #[ORM\Column(length: 255)]
    private ?string $raison_sociale_usine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $personne_ressource = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $cc_usine = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $tel_usine = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $fax_usine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse_usine = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $localisation_usine = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(nullable: true)]
    private ?int $capacite_usine = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $sigle = null;

    #[ORM\ManyToOne(inversedBy: 'usines')]
    private ?Cantonnement $code_cantonnement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_personne_ressource = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $mobile_personne_ressource = null;

    #[ORM\Column(nullable: true)]
    private ?bool $export = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $code_exportateur = null;

    #[ORM\OneToMany(mappedBy: 'codeindustriel', targetEntity: User::class)]
    private Collection $utilisateurs;

    #[ORM\ManyToMany(targetEntity: TypeTransformation::class, inversedBy: 'usines')]
    private Collection $type_transformation;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $created_by = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'parc_usine_brh', targetEntity: Pagebrh::class)]
    private Collection $pagebrhs;

    #[ORM\OneToMany(mappedBy: 'code_usine', targetEntity: Documentlje::class)]
    private Collection $documentljes;

    #[ORM\OneToMany(mappedBy: 'code_usine', targetEntity: Documentbtgu::class)]
    private Collection $documentbtgus;

    #[ORM\OneToMany(mappedBy: 'usine_destinataire', targetEntity: Pagebtgu::class)]
    private Collection $pagebtgus;

    #[ORM\ManyToOne(inversedBy: 'usines')]
    private ?Exploitant $code_exploitant = null;

    #[ORM\OneToMany(mappedBy: 'code_usine', targetEntity: DemandeOperateur::class)]
    private Collection $demandeOperateurs;

    #[ORM\OneToMany(mappedBy: 'code_usin', targetEntity: Documentfp::class)]
    private Collection $documentfps;

    #[ORM\OneToMany(mappedBy: 'parc_usine', targetEntity: Pagebcbp::class)]
    private Collection $pagebcbps;

    #[ORM\OneToMany(mappedBy: 'code_usine', targetEntity: Documentetate::class)]
    private Collection $documentetates;

    #[ORM\OneToMany(mappedBy: 'code_usine', targetEntity: Documentetate2::class)]
    private Collection $documentetate2s;

    #[ORM\OneToMany(mappedBy: 'code_usine', targetEntity: Documentetatg::class)]
    private Collection $documentetatgs;

    #[ORM\OneToMany(mappedBy: 'code_usine', targetEntity: Documentetath::class)]
    private Collection $documentetaths;

    #[ORM\OneToMany(mappedBy: 'code_usine', targetEntity: Documentdmp::class)]
    private Collection $documentdmps;

    #[ORM\OneToMany(mappedBy: 'code_usine', targetEntity: Documentdmv::class)]
    private Collection $documentdmvs;

    #[ORM\OneToMany(mappedBy: 'usine_origine', targetEntity: Lignepageetate::class)]
    private Collection $lignepageetates;

    #[ORM\OneToMany(mappedBy: 'usine_destination', targetEntity: Lignepageetate2::class)]
    private Collection $lignepageetate2s;

    #[ORM\OneToMany(mappedBy: 'code_usine', targetEntity: Contrat::class)]
    private Collection $contrats;

    #[ORM\OneToMany(mappedBy: 'code_usine', targetEntity: Pagebcburb::class)]
    private Collection $pagebcburbs;

    #[ORM\OneToMany(mappedBy: 'usine_origine', targetEntity: Pagebrepf::class)]
    private Collection $pagebrepfs;

    #[ORM\OneToMany(mappedBy: 'usine_origine', targetEntity: Lignepagersdpf::class)]
    private Collection $lignepagersdpfs;

    #[ORM\OneToMany(mappedBy: 'code_usine', targetEntity: Documentpdtdrv::class)]
    private Collection $documentpdtdrvs;

    #[ORM\OneToMany(mappedBy: 'codeindustriel', targetEntity: Fiche2Transfo::class)]
    private Collection $fiche2Transfos;

    #[ORM\OneToMany(mappedBy: 'code_usine', targetEntity: FicheLot::class)]
    private Collection $ficheLots;

    #[ORM\OneToMany(mappedBy: 'code_usine', targetEntity: FicheLotProd::class)]
    private Collection $ficheLotProds;

    #[ORM\Column(nullable: true)]
    private ?bool $agree = null;

    #[ORM\Column(nullable: true)]
    private ?float $latitude = null;

    #[ORM\Column(nullable: true)]
    private ?float $longitude = null;
    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
        $this->type_transformation = new ArrayCollection();
        $this->pagebrhs = new ArrayCollection();
        $this->documentljes = new ArrayCollection();
        $this->documentbtgus = new ArrayCollection();
        $this->pagebtgus = new ArrayCollection();
        $this->demandeOperateurs = new ArrayCollection();
        $this->documentfps = new ArrayCollection();
        $this->pagebcbps = new ArrayCollection();
        $this->documentetates = new ArrayCollection();
        $this->documentetate2s = new ArrayCollection();
        $this->documentetatgs = new ArrayCollection();
        $this->documentetaths = new ArrayCollection();
        $this->documentdmps = new ArrayCollection();
        $this->documentdmvs = new ArrayCollection();
        $this->lignepageetates = new ArrayCollection();
        $this->lignepageetate2s = new ArrayCollection();
        $this->contrats = new ArrayCollection();
        $this->pagebcburbs = new ArrayCollection();
        $this->pagebrepfs = new ArrayCollection();
        $this->lignepagersdpfs = new ArrayCollection();
        $this->documentpdtdrvs = new ArrayCollection();
        $this->fiche2Transfos = new ArrayCollection();
        $this->ficheLots = new ArrayCollection();
        $this->ficheLotProds = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroUsine(): ?int
    {
        return $this->numero_usine;
    }

    public function setNumeroUsine(int $numero_usine): static
    {
        $this->numero_usine = $numero_usine;

        return $this;
    }

    public function getRaisonSocialeUsine(): ?string
    {
        return $this->raison_sociale_usine;
    }

    public function setRaisonSocialeUsine(string $raison_sociale_usine): static
    {
        $this->raison_sociale_usine = $raison_sociale_usine;

        return $this;
    }

    public function getPersonneRessource(): ?string
    {
        return $this->personne_ressource;
    }

    public function setPersonneRessource(?string $personne_ressource): static
    {
        $this->personne_ressource = $personne_ressource;

        return $this;
    }

    public function getCcUsine(): ?string
    {
        return $this->cc_usine;
    }

    public function setCcUsine(?string $cc_usine): static
    {
        $this->cc_usine = $cc_usine;

        return $this;
    }

    public function getTelUsine(): ?string
    {
        return $this->tel_usine;
    }

    public function setTelUsine(?string $tel_usine): static
    {
        $this->tel_usine = $tel_usine;

        return $this;
    }

    public function getFaxUsine(): ?string
    {
        return $this->fax_usine;
    }

    public function setFaxUsine(?string $fax_usine): static
    {
        $this->fax_usine = $fax_usine;

        return $this;
    }

    public function getAdresseUsine(): ?string
    {
        return $this->adresse_usine;
    }

    public function setAdresseUsine(?string $adresse_usine): static
    {
        $this->adresse_usine = $adresse_usine;

        return $this;
    }

    public function getLocalisationUsine(): ?string
    {
        return $this->localisation_usine;
    }

    public function setLocalisationUsine(?string $localisation_usine): static
    {
        $this->localisation_usine = $localisation_usine;

        return $this;
    }


    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCapaciteUsine(): ?int
    {
        return $this->capacite_usine;
    }

    public function setCapaciteUsine(?int $capacite_usine): static
    {
        $this->capacite_usine = $capacite_usine;

        return $this;
    }

    public function getSigle(): ?string
    {
        return $this->sigle;
    }

    public function setSigle(?string $sigle): static
    {
        $this->sigle = $sigle;

        return $this;
    }

    public function getCodeCantonnement(): ?Cantonnement
    {
        return $this->code_cantonnement;
    }

    public function setCodeCantonnement(?Cantonnement $code_cantonnement): static
    {
        $this->code_cantonnement = $code_cantonnement;

        return $this;
    }

    public function getEmailPersonneRessource(): ?string
    {
        return $this->email_personne_ressource;
    }

    public function setEmailPersonneRessource(?string $email_personne_ressource): static
    {
        $this->email_personne_ressource = $email_personne_ressource;

        return $this;
    }

    public function getMobilePersonneRessource(): ?string
    {
        return $this->mobile_personne_ressource;
    }

    public function setMobilePersonneRessource(?string $mobile_personne_ressource): static
    {
        $this->mobile_personne_ressource = $mobile_personne_ressource;

        return $this;
    }

    public function isExport(): ?bool
    {
        return $this->export;
    }

    public function setExport(?bool $export): static
    {
        $this->export = $export;

        return $this;
    }

    public function getCodeExportateur(): ?string
    {
        return $this->code_exportateur;
    }

    public function setCodeExportateur(?string $code_exportateur): static
    {
        $this->code_exportateur = $code_exportateur;

        return $this;
    }
    public function __toString(): string
    {
        return $this->raison_sociale_usine;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUser(User $utilisateur): static
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->add($utilisateur);
            $utilisateur->setCodeindustriel($this);
        }

        return $this;
    }

    public function removeUser(User $utilisateur): static
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getCodeindustriel() === $this) {
                $utilisateur->setCodeindustriel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TypeTransformation>
     */
    public function getTypeTransformation(): Collection
    {
        return $this->type_transformation;
    }

    public function addTypeTransformation(TypeTransformation $typeTransformation): static
    {
        if (!$this->type_transformation->contains($typeTransformation)) {
            $this->type_transformation->add($typeTransformation);
        }

        return $this;
    }

    public function removeTypeTransformation(TypeTransformation $typeTransformation): static
    {
        $this->type_transformation->removeElement($typeTransformation);

        return $this;
    }

    /**
     * @return ArrayCollection|Collection
     */
    public function getUtilisateurs(): ArrayCollection|Collection
    {
        return $this->utilisateurs;
    }

    /**
     * @param ArrayCollection|Collection $utilisateurs
     */
    public function setUtilisateurs(ArrayCollection|Collection $utilisateurs): void
    {
        $this->utilisateurs = $utilisateurs;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @param \DateTimeImmutable|null $created_at
     */
    public function setCreatedAt(?\DateTimeImmutable $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return string|null
     */
    public function getCreatedBy(): ?string
    {
        return $this->created_by;
    }

    /**
     * @param string|null $created_by
     */
    public function setCreatedBy(?string $created_by): void
    {
        $this->created_by = $created_by;
    }

    /**
     * @return string|null
     */
    public function getUpdatedBy(): ?string
    {
        return $this->updated_by;
    }

    /**
     * @param string|null $updated_by
     */
    public function setUpdatedBy(?string $updated_by): void
    {
        $this->updated_by = $updated_by;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * @param \DateTimeInterface|null $updated_at
     */
    public function setUpdatedAt(?\DateTimeInterface $updated_at): void
    {
        $this->updated_at = $updated_at;
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
            $pagebrh->setParcUsineBrh($this);
        }

        return $this;
    }

    public function removePagebrh(Pagebrh $pagebrh): static
    {
        if ($this->pagebrhs->removeElement($pagebrh)) {
            // set the owning side to null (unless already changed)
            if ($pagebrh->getParcUsineBrh() === $this) {
                $pagebrh->setParcUsineBrh(null);
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
            $documentlje->setCodeUsine($this);
        }

        return $this;
    }

    public function removeDocumentlje(Documentlje $documentlje): static
    {
        if ($this->documentljes->removeElement($documentlje)) {
            // set the owning side to null (unless already changed)
            if ($documentlje->getCodeUsine() === $this) {
                $documentlje->setCodeUsine(null);
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
            $documentbtgu->setCodeUsine($this);
        }

        return $this;
    }

    public function removeDocumentbtgu(Documentbtgu $documentbtgu): static
    {
        if ($this->documentbtgus->removeElement($documentbtgu)) {
            // set the owning side to null (unless already changed)
            if ($documentbtgu->getCodeUsine() === $this) {
                $documentbtgu->setCodeUsine(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Pagebtgu>
     */
    public function getPagebtgus(): Collection
    {
        return $this->pagebtgus;
    }

    public function addPagebtgu(Pagebtgu $pagebtgu): static
    {
        if (!$this->pagebtgus->contains($pagebtgu)) {
            $this->pagebtgus->add($pagebtgu);
            $pagebtgu->setUsineDestinataire($this);
        }

        return $this;
    }

    public function removePagebtgu(Pagebtgu $pagebtgu): static
    {
        if ($this->pagebtgus->removeElement($pagebtgu)) {
            // set the owning side to null (unless already changed)
            if ($pagebtgu->getUsineDestinataire() === $this) {
                $pagebtgu->setUsineDestinataire(null);
            }
        }

        return $this;
    }

    public function getCodeExploitant(): ?Exploitant
    {
        return $this->code_exploitant;
    }

    public function setCodeExploitant(?Exploitant $code_exploitant): static
    {
        $this->code_exploitant = $code_exploitant;

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
            $demandeOperateur->setCodeUsine($this);
        }

        return $this;
    }

    public function removeDemandeOperateur(DemandeOperateur $demandeOperateur): static
    {
        if ($this->demandeOperateurs->removeElement($demandeOperateur)) {
            // set the owning side to null (unless already changed)
            if ($demandeOperateur->getCodeUsine() === $this) {
                $demandeOperateur->setCodeUsine(null);
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
            $documentfp->setCodeUsin($this);
        }

        return $this;
    }

    public function removeDocumentfp(Documentfp $documentfp): static
    {
        if ($this->documentfps->removeElement($documentfp)) {
            // set the owning side to null (unless already changed)
            if ($documentfp->getCodeUsin() === $this) {
                $documentfp->setCodeUsin(null);
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
            $pagebcbp->setParcUsine($this);
        }

        return $this;
    }

    public function removePagebcbp(Pagebcbp $pagebcbp): static
    {
        if ($this->pagebcbps->removeElement($pagebcbp)) {
            // set the owning side to null (unless already changed)
            if ($pagebcbp->getParcUsine() === $this) {
                $pagebcbp->setParcUsine(null);
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
            $documentetate->setCodeUsine($this);
        }

        return $this;
    }

    public function removeDocumentetate(Documentetate $documentetate): static
    {
        if ($this->documentetates->removeElement($documentetate)) {
            // set the owning side to null (unless already changed)
            if ($documentetate->getCodeUsine() === $this) {
                $documentetate->setCodeUsine(null);
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
            $documentetate2->setCodeUsine($this);
        }

        return $this;
    }

    public function removeDocumentetate2(Documentetate2 $documentetate2): static
    {
        if ($this->documentetate2s->removeElement($documentetate2)) {
            // set the owning side to null (unless already changed)
            if ($documentetate2->getCodeUsine() === $this) {
                $documentetate2->setCodeUsine(null);
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
            $documentetatg->setCodeUsine($this);
        }

        return $this;
    }

    public function removeDocumentetatg(Documentetatg $documentetatg): static
    {
        if ($this->documentetatgs->removeElement($documentetatg)) {
            // set the owning side to null (unless already changed)
            if ($documentetatg->getCodeUsine() === $this) {
                $documentetatg->setCodeUsine(null);
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
            $documentetath->setCodeUsine($this);
        }

        return $this;
    }

    public function removeDocumentetath(Documentetath $documentetath): static
    {
        if ($this->documentetaths->removeElement($documentetath)) {
            // set the owning side to null (unless already changed)
            if ($documentetath->getCodeUsine() === $this) {
                $documentetath->setCodeUsine(null);
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
            $documentdmp->setCodeUsine($this);
        }

        return $this;
    }

    public function removeDocumentdmp(Documentdmp $documentdmp): static
    {
        if ($this->documentdmps->removeElement($documentdmp)) {
            // set the owning side to null (unless already changed)
            if ($documentdmp->getCodeUsine() === $this) {
                $documentdmp->setCodeUsine(null);
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
            $documentdmv->setCodeUsine($this);
        }

        return $this;
    }

    public function removeDocumentdmv(Documentdmv $documentdmv): static
    {
        if ($this->documentdmvs->removeElement($documentdmv)) {
            // set the owning side to null (unless already changed)
            if ($documentdmv->getCodeUsine() === $this) {
                $documentdmv->setCodeUsine(null);
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
            $lignepageetate->setUsineOrigine($this);
        }

        return $this;
    }

    public function removeLignepageetate(Lignepageetate $lignepageetate): static
    {
        if ($this->lignepageetates->removeElement($lignepageetate)) {
            // set the owning side to null (unless already changed)
            if ($lignepageetate->getUsineOrigine() === $this) {
                $lignepageetate->setUsineOrigine(null);
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
            $lignepageetate2->setUsineDestination($this);
        }

        return $this;
    }

    public function removeLignepageetate2(Lignepageetate2 $lignepageetate2): static
    {
        if ($this->lignepageetate2s->removeElement($lignepageetate2)) {
            // set the owning side to null (unless already changed)
            if ($lignepageetate2->getUsineDestination() === $this) {
                $lignepageetate2->setUsineDestination(null);
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
            $contrat->setCodeUsine($this);
        }

        return $this;
    }

    public function removeContrat(Contrat $contrat): static
    {
        if ($this->contrats->removeElement($contrat)) {
            // set the owning side to null (unless already changed)
            if ($contrat->getCodeUsine() === $this) {
                $contrat->setCodeUsine(null);
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
            $pagebcburb->setCodeUsine($this);
        }

        return $this;
    }

    public function removePagebcburb(Pagebcburb $pagebcburb): static
    {
        if ($this->pagebcburbs->removeElement($pagebcburb)) {
            // set the owning side to null (unless already changed)
            if ($pagebcburb->getCodeUsine() === $this) {
                $pagebcburb->setCodeUsine(null);
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
            $pagebrepf->setUsineOrigine($this);
        }

        return $this;
    }

    public function removePagebrepf(Pagebrepf $pagebrepf): static
    {
        if ($this->pagebrepfs->removeElement($pagebrepf)) {
            // set the owning side to null (unless already changed)
            if ($pagebrepf->getUsineOrigine() === $this) {
                $pagebrepf->setUsineOrigine(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Lignepagersdpf>
     */
    public function getLignepagersdpfs(): Collection
    {
        return $this->lignepagersdpfs;
    }

    public function addLignepagersdpf(Lignepagersdpf $lignepagersdpf): static
    {
        if (!$this->lignepagersdpfs->contains($lignepagersdpf)) {
            $this->lignepagersdpfs->add($lignepagersdpf);
            $lignepagersdpf->setUsineOrigine($this);
        }

        return $this;
    }

    public function removeLignepagersdpf(Lignepagersdpf $lignepagersdpf): static
    {
        if ($this->lignepagersdpfs->removeElement($lignepagersdpf)) {
            // set the owning side to null (unless already changed)
            if ($lignepagersdpf->getUsineOrigine() === $this) {
                $lignepagersdpf->setUsineOrigine(null);
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
            $documentpdtdrv->setCodeUsine($this);
        }

        return $this;
    }

    public function removeDocumentpdtdrv(Documentpdtdrv $documentpdtdrv): static
    {
        if ($this->documentpdtdrvs->removeElement($documentpdtdrv)) {
            // set the owning side to null (unless already changed)
            if ($documentpdtdrv->getCodeUsine() === $this) {
                $documentpdtdrv->setCodeUsine(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Fiche2Transfo>
     */
    public function getFiche2Transfos(): Collection
    {
        return $this->fiche2Transfos;
    }

    public function addFiche2Transfo(Fiche2Transfo $fiche2Transfo): static
    {
        if (!$this->fiche2Transfos->contains($fiche2Transfo)) {
            $this->fiche2Transfos->add($fiche2Transfo);
            $fiche2Transfo->setCodeindustriel($this);
        }

        return $this;
    }

    public function removeFiche2Transfo(Fiche2Transfo $fiche2Transfo): static
    {
        if ($this->fiche2Transfos->removeElement($fiche2Transfo)) {
            // set the owning side to null (unless already changed)
            if ($fiche2Transfo->getCodeindustriel() === $this) {
                $fiche2Transfo->setCodeindustriel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FicheLot>
     */
    public function getFicheLots(): Collection
    {
        return $this->ficheLots;
    }

    public function addFicheLot(FicheLot $ficheLot): static
    {
        if (!$this->ficheLots->contains($ficheLot)) {
            $this->ficheLots->add($ficheLot);
            $ficheLot->setCodeUsine($this);
        }

        return $this;
    }

    public function removeFicheLot(FicheLot $ficheLot): static
    {
        if ($this->ficheLots->removeElement($ficheLot)) {
            // set the owning side to null (unless already changed)
            if ($ficheLot->getCodeUsine() === $this) {
                $ficheLot->setCodeUsine(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FicheLotProd>
     */
    public function getFicheLotProds(): Collection
    {
        return $this->ficheLotProds;
    }

    public function addFicheLotProd(FicheLotProd $ficheLotProd): static
    {
        if (!$this->ficheLotProds->contains($ficheLotProd)) {
            $this->ficheLotProds->add($ficheLotProd);
            $ficheLotProd->setCodeUsine($this);
        }

        return $this;
    }

    public function removeFicheLotProd(FicheLotProd $ficheLotProd): static
    {
        if ($this->ficheLotProds->removeElement($ficheLotProd)) {
            // set the owning side to null (unless already changed)
            if ($ficheLotProd->getCodeUsine() === $this) {
                $ficheLotProd->setCodeUsine(null);
            }
        }

        return $this;
    }

    public function isAgree(): ?bool
    {
        return $this->agree;
    }

    public function setAgree(?bool $agree): static
    {
        $this->agree = $agree;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(?float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(?float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }
}
