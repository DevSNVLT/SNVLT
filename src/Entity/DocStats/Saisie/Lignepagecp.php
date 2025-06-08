<?php

namespace App\Entity\DocStats\Saisie;

use App\Entity\Admin\Exercice;
use App\Entity\Administration\InventaireForestier;
use App\Entity\DocStats\Pages\Pagecp;
use App\Entity\References\Essence;
use App\Entity\References\ZoneHemispherique;
use App\Repository\DocStats\Saisie\LignepagecpRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.lignepagecp')]
#[ORM\Entity(repositoryClass: LignepagecpRepository::class)]
class Lignepagecp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numero_arbrecp = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagecps')]
    private ?ZoneHemispherique $zh_arbrecp = null;

    #[ORM\Column]
    private ?float $x_arbrecp = null;

    #[ORM\Column]
    private ?float $y_arbrecp = null;

    #[ORM\Column(nullable: true)]
    private ?int $jour_abattage = null;

    #[ORM\Column(nullable: true)]
    private ?int $numero_essencecp = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagecps')]
    private ?Essence $nom_essencecp = null;

    #[ORM\Column]
    private ?int $longeur_arbrecp = null;

    #[ORM\Column]
    private ?int $diametre_arbrecp = null;

    #[ORM\Column]
    private ?float $volume_arbrecp = null;

    #[ORM\Column]
    private ?int $longeura_billecp = null;

    #[ORM\Column]
    private ?int $diametrea_billecp = null;

    #[ORM\Column]
    private ?float $volumea_billecp = null;

    #[ORM\Column(nullable: true)]
    private ?int $longeurb_billecp = null;

    #[ORM\Column(nullable: true)]
    private ?int $diametreb_billecp = null;

    #[ORM\Column(nullable: true)]
    private ?float $volumeb_billecp = null;

    #[ORM\Column(nullable: true)]
    private ?int $longeurc_billecp = null;

    #[ORM\Column(nullable: true)]
    private ?int $diametrec_billecp = null;

    #[ORM\Column(nullable: true)]
    private ?float $volumec_billecp = null;

    #[ORM\Column(nullable: true)]
    private ?bool $a_utlise = null;

    #[ORM\Column(nullable: true)]
    private ?bool $b_utilise = null;

    #[ORM\Column(nullable: true)]
    private ?bool $c_utilise = null;

    #[ORM\Column(nullable: true)]
    private ?bool $a_abandon = null;

    #[ORM\Column(nullable: true)]
    private ?bool $b_abandon = null;

    #[ORM\Column(nullable: true)]
    private ?bool $c_abandon = null;

    #[ORM\Column(nullable: true)]
    private ?bool $fut_abandon = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motivation = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagecps')]
    private ?Pagecp $code_pagecp = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(nullable: true)]
    private ?int $exercice = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagecps')]
    private ?InventaireForestier $code_inv = null;

    #[ORM\Column(nullable: true)]
    private ?bool $charge = null;

    #[ORM\OneToMany(mappedBy: 'code_ligne_cp', targetEntity: Lignepagebrh::class)]
    private Collection $lignepagebrhs;

    #[ORM\ManyToOne(inversedBy: 'lignepagecps')]
    private ?Exercice $code_exercice = null;

    public function __construct()
    {
        $this->lignepagebrhs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroArbrecp(): ?int
    {
        return $this->numero_arbrecp;
    }

    public function setNumeroArbrecp(int $numero_arbrecp): static
    {
        $this->numero_arbrecp = $numero_arbrecp;

        return $this;
    }

    public function getZhArbrecp(): ?ZoneHemispherique
    {
        return $this->zh_arbrecp;
    }

    public function setZhArbrecp(?ZoneHemispherique $zh_arbrecp): static
    {
        $this->zh_arbrecp = $zh_arbrecp;

        return $this;
    }

    public function getXArbrecp(): ?float
    {
        return $this->x_arbrecp;
    }

    public function setXArbrecp(float $x_arbrecp): static
    {
        $this->x_arbrecp = $x_arbrecp;

        return $this;
    }

    public function getYArbrecp(): ?float
    {
        return $this->y_arbrecp;
    }

    public function setYArbrecp(float $y_arbrecp): static
    {
        $this->y_arbrecp = $y_arbrecp;

        return $this;
    }

    public function getJourAbattage(): ?int
    {
        return $this->jour_abattage;
    }

    public function setJourAbattage(?int $jour_abattage): static
    {
        $this->jour_abattage = $jour_abattage;

        return $this;
    }

    public function getNumeroEssencecp(): ?int
    {
        return $this->numero_essencecp;
    }

    public function setNumeroEssencecp(?int $numero_essencecp): static
    {
        $this->numero_essencecp = $numero_essencecp;

        return $this;
    }

    public function getNomEssencecp(): ?Essence
    {
        return $this->nom_essencecp;
    }

    public function setNomEssencecp(?Essence $nom_essencecp): static
    {
        $this->nom_essencecp = $nom_essencecp;

        return $this;
    }

    public function getLongeurArbrecp(): ?int
    {
        return $this->longeur_arbrecp;
    }

    public function setLongeurArbrecp(int $longeur_arbrecp): static
    {
        $this->longeur_arbrecp = $longeur_arbrecp;

        return $this;
    }

    public function getDiametreArbrecp(): ?int
    {
        return $this->diametre_arbrecp;
    }

    public function setDiametreArbrecp(int $diametre_arbrecp): static
    {
        $this->diametre_arbrecp = $diametre_arbrecp;

        return $this;
    }

    public function getVolumeArbrecp(): ?float
    {
        return $this->volume_arbrecp;
    }

    public function setVolumeArbrecp(float $volume_arbrecp): static
    {
        $this->volume_arbrecp = $volume_arbrecp;

        return $this;
    }

    public function getLongeuraBillecp(): ?int
    {
        return $this->longeura_billecp;
    }

    public function setLongeuraBillecp(int $longeura_billecp): static
    {
        $this->longeura_billecp = $longeura_billecp;

        return $this;
    }

    public function getDiametreaBillecp(): ?int
    {
        return $this->diametrea_billecp;
    }

    public function setDiametreaBillecp(int $diametrea_billecp): static
    {
        $this->diametrea_billecp = $diametrea_billecp;

        return $this;
    }

    public function getVolumeaBillecp(): ?float
    {
        return $this->volumea_billecp;
    }

    public function setVolumeaBillecp(float $volumea_billecp): static
    {
        $this->volumea_billecp = $volumea_billecp;

        return $this;
    }

    public function getLongeurbBillecp(): ?int
    {
        return $this->longeurb_billecp;
    }

    public function setLongeurbBillecp(?int $longeurb_billecp): static
    {
        $this->longeurb_billecp = $longeurb_billecp;

        return $this;
    }

    public function getDiametrebBillecp(): ?int
    {
        return $this->diametreb_billecp;
    }

    public function setDiametrebBillecp(?int $diametreb_billecp): static
    {
        $this->diametreb_billecp = $diametreb_billecp;

        return $this;
    }

    public function getVolumebBillecp(): ?float
    {
        return $this->volumeb_billecp;
    }

    public function setVolumebBillecp(?float $volumeb_billecp): static
    {
        $this->volumeb_billecp = $volumeb_billecp;

        return $this;
    }

    public function getLongeurcBillecp(): ?int
    {
        return $this->longeurc_billecp;
    }

    public function setLongeurcBillecp(?int $longeurc_billecp): static
    {
        $this->longeurc_billecp = $longeurc_billecp;

        return $this;
    }

    public function getDiametrecBillecp(): ?int
    {
        return $this->diametrec_billecp;
    }

    public function setDiametrecBillecp(?int $diametrec_billecp): static
    {
        $this->diametrec_billecp = $diametrec_billecp;

        return $this;
    }

    public function getVolumecBillecp(): ?float
    {
        return $this->volumec_billecp;
    }

    public function setVolumecBillecp(?float $volumec_billecp): static
    {
        $this->volumec_billecp = $volumec_billecp;

        return $this;
    }

    public function isAUtlise(): ?bool
    {
        return $this->a_utlise;
    }

    public function setAUtlise(?bool $a_utlise): static
    {
        $this->a_utlise = $a_utlise;

        return $this;
    }

    public function isBUtilise(): ?bool
    {
        return $this->b_utilise;
    }

    public function setBUtilise(?bool $b_utilise): static
    {
        $this->b_utilise = $b_utilise;

        return $this;
    }

    public function isCUtilise(): ?bool
    {
        return $this->c_utilise;
    }

    public function setCUtilise(?bool $c_utilise): static
    {
        $this->c_utilise = $c_utilise;

        return $this;
    }

    public function isAAbandon(): ?bool
    {
        return $this->a_abandon;
    }

    public function setAAbandon(?bool $a_abandon): static
    {
        $this->a_abandon = $a_abandon;

        return $this;
    }

    public function isBAbandon(): ?bool
    {
        return $this->b_abandon;
    }

    public function setBAbandon(?bool $b_abandon): static
    {
        $this->b_abandon = $b_abandon;

        return $this;
    }

    public function isCAbandon(): ?bool
    {
        return $this->c_abandon;
    }

    public function setCAbandon(?bool $c_abandon): static
    {
        $this->c_abandon = $c_abandon;

        return $this;
    }

    public function isFutAbandon(): ?bool
    {
        return $this->fut_abandon;
    }

    public function setFutAbandon(?bool $fut_abandon): static
    {
        $this->fut_abandon = $fut_abandon;

        return $this;
    }

    public function getMotivation(): ?string
    {
        return $this->motivation;
    }

    public function setMotivation(?string $motivation): static
    {
        $this->motivation = $motivation;

        return $this;
    }

    public function getCodePagecp(): ?Pagecp
    {
        return $this->code_pagecp;
    }

    public function setCodePagecp(?Pagecp $code_pagecp): static
    {
        $this->code_pagecp = $code_pagecp;

        return $this;
    }

    public function getExercice(): ?int
    {
        return $this->exercice;
    }

    public function setExercice(?int $exercice): static
    {
        $this->exercice = $exercice;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    /**
     * @param \DateTimeInterface|null $created_at
     */
    public function setCreatedAt(?\DateTimeInterface $created_at): void
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

    public function getCodeInv(): ?InventaireForestier
    {
        return $this->code_inv;
    }

    public function setCodeInv(?InventaireForestier $code_inv): static
    {
        $this->code_inv = $code_inv;

        return $this;
    }

    public function isCharge(): ?bool
    {
        return $this->charge;
    }

    public function setCharge(?bool $charge): static
    {
        $this->charge = $charge;

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
            $lignepagebrh->setCodeLigneCp($this);
        }

        return $this;
    }

    public function removeLignepagebrh(Lignepagebrh $lignepagebrh): static
    {
        if ($this->lignepagebrhs->removeElement($lignepagebrh)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebrh->getCodeLigneCp() === $this) {
                $lignepagebrh->setCodeLigneCp(null);
            }
        }

        return $this;
    }

    public function getCodeExercice(): ?Exercice
    {
        return $this->code_exercice;
    }

    public function setCodeExercice(?Exercice $code_exercice): static
    {
        $this->code_exercice = $code_exercice;

        return $this;
    }


}
