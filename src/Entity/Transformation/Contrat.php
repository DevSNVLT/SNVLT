<?php

namespace App\Entity\Transformation;

use App\Entity\Admin\Coupon;
use App\Entity\Admin\Exercice;
use App\Entity\References\Essence;
use App\Entity\References\Pays;
use App\Entity\References\TypeTransformation;
use App\Entity\References\Usine;
use App\Repository\Transformation\ContratRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'transformation.contrat')]
#[ORM\Entity(repositoryClass: ContratRepository::class)]
class Contrat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $raison_sociale_clt = null;

    #[ORM\ManyToOne(inversedBy: 'contrats')]
    private ?Pays $pays = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $ville = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $destination_colis = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $conditions = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_contrat = null;

    #[ORM\Column(nullable: true)]
    private ?float $volume_demande = null;

    #[ORM\ManyToOne(inversedBy: 'contrats')]
    private ?Exercice $exercice = null;

    #[ORM\ManyToOne(inversedBy: 'contrats')]
    private ?Usine $code_usine = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $personne_resource = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $email_personne_ressource = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $contact_personne_ressource = null;

    #[ORM\Column(nullable: true)]
    private ?bool $cloture = null;

    #[ORM\OneToMany(mappedBy: 'code_contrat', targetEntity: Elements::class)]
    private Collection $elements;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $numero_contrat = null;

    #[ORM\ManyToOne(inversedBy: 'contrats')]
    private ?TypeTransformation $type_transfo = null;

    #[ORM\ManyToMany(targetEntity: Essence::class, inversedBy: 'contrats')]
    private Collection $essence;

    #[ORM\OneToMany(mappedBy: 'code_contrat', targetEntity: Colis::class)]
    private Collection $colis;

    #[ORM\ManyToOne(inversedBy: 'contrats')]
    private ?TypeContrat $type_contrat = null;

    #[ORM\OneToMany(mappedBy: 'code_contrat', targetEntity: Coupon::class)]
    private Collection $coupons;



    public function __construct()
    {
        $this->elements = new ArrayCollection();
        $this->essence = new ArrayCollection();
        $this->colis = new ArrayCollection();
        $this->coupons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaisonSocialeClt(): ?string
    {
        return $this->raison_sociale_clt;
    }

    public function setRaisonSocialeClt(string $raison_sociale_clt): static
    {
        $this->raison_sociale_clt = $raison_sociale_clt;

        return $this;
    }

    public function getPays(): ?Pays
    {
        return $this->pays;
    }

    public function setPays(?Pays $pays): static
    {
        $this->pays = $pays;

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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getDestinationColis(): ?string
    {
        return $this->destination_colis;
    }

    public function setDestinationColis(?string $destination_colis): static
    {
        $this->destination_colis = $destination_colis;

        return $this;
    }


    public function getConditions(): ?string
    {
        return $this->conditions;
    }

    public function setConditions(?string $conditions): static
    {
        $this->conditions = $conditions;

        return $this;
    }

    public function getDateContrat(): ?\DateTimeInterface
    {
        return $this->date_contrat;
    }

    public function setDateContrat(\DateTimeInterface $date_contrat): static
    {
        $this->date_contrat = $date_contrat;

        return $this;
    }

    public function getVolumeDemande(): ?float
    {
        return $this->volume_demande;
    }

    public function setVolumeDemande(?float $volume_demande): static
    {
        $this->volume_demande = $volume_demande;

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

    public function getCodeUsine(): ?Usine
    {
        return $this->code_usine;
    }

    public function setCodeUsine(?Usine $code_usine): static
    {
        $this->code_usine = $code_usine;

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

    public function getPersonneResource(): ?string
    {
        return $this->personne_resource;
    }

    public function setPersonneResource(?string $personne_resource): static
    {
        $this->personne_resource = $personne_resource;

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

    public function getContactPersonneRessource(): ?string
    {
        return $this->contact_personne_ressource;
    }

    public function setContactPersonneRessource(?string $contact_personne_ressource): static
    {
        $this->contact_personne_ressource = $contact_personne_ressource;

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
            $element->setCodeContrat($this);
        }

        return $this;
    }

    public function removeElement(Elements $element): static
    {
        if ($this->elements->removeElement($element)) {
            // set the owning side to null (unless already changed)
            if ($element->getCodeContrat() === $this) {
                $element->setCodeContrat(null);
            }
        }

        return $this;
    }

    public function getNumeroContrat(): ?string
    {
        return $this->numero_contrat;
    }

    public function setNumeroContrat(string $numero_contrat): static
    {
        $this->numero_contrat = $numero_contrat;

        return $this;
    }

    public function getTypeTransfo(): ?TypeTransformation
    {
        return $this->type_transfo;
    }

    public function setTypeTransfo(?TypeTransformation $type_transfo): static
    {
        $this->type_transfo = $type_transfo;

        return $this;
    }

    /**
     * @return Collection<int, Essence>
     */
    public function getEssence(): Collection
    {
        return $this->essence;
    }

    public function addEssence(Essence $essence): static
    {
        if (!$this->essence->contains($essence)) {
            $this->essence->add($essence);
        }

        return $this;
    }

    public function removeEssence(Essence $essence): static
    {
        $this->essence->removeElement($essence);

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
            $coli->setCodeContrat($this);
        }

        return $this;
    }

    public function removeColi(Colis $coli): static
    {
        if ($this->colis->removeElement($coli)) {
            // set the owning side to null (unless already changed)
            if ($coli->getCodeContrat() === $this) {
                $coli->setCodeContrat(null);
            }
        }

        return $this;
    }

    public function getTypeContrat(): ?TypeContrat
    {
        return $this->type_contrat;
    }

    public function setTypeContrat(?TypeContrat $type_contrat): static
    {
        $this->type_contrat = $type_contrat;

        return $this;
    }

    /**
     * @return Collection<int, Coupon>
     */
    public function getCoupons(): Collection
    {
        return $this->coupons;
    }

    public function addCoupon(Coupon $coupon): static
    {
        if (!$this->coupons->contains($coupon)) {
            $this->coupons->add($coupon);
            $coupon->setCodeContrat($this);
        }

        return $this;
    }

    public function removeCoupon(Coupon $coupon): static
    {
        if ($this->coupons->removeElement($coupon)) {
            // set the owning side to null (unless already changed)
            if ($coupon->getCodeContrat() === $this) {
                $coupon->setCodeContrat(null);
            }
        }

        return $this;
    }


}
