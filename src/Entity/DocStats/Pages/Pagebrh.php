<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\References\Cantonnement;
use App\Entity\References\PageDocGen;
use App\Entity\References\Usine;
use App\Repository\DocStats\Pages\PagebrhRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.pagebrh')]
#[ORM\Entity(repositoryClass: PagebrhRepository::class)]
class Pagebrh
{
    const PAGE_BRH_EDITED_SUCCESSFULLY = 'PAGE_BRH_EDITED_SUCCESSFULLY';
    const PAGE_BRH_BAD_DATA = 'PAGE_BRH_BAD_DATA';
    const PAGE_BRH_ACCEPTED = 'PAGE_BRH_LOADING_DATA_ACCEPTED';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_pagebrh = null;

    #[ORM\Column]
    private ?int $index_pagebrh = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_chargementbrh = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $destination_pagebrh = null;

    #[ORM\ManyToOne(inversedBy: 'pagebrhs')]
    private ?Usine $parc_usine_brh = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $village_pagebrh = null;

    #[ORM\ManyToOne(inversedBy: 'pagebrhs')]
    private ?Cantonnement $cantonnement_pagebrh = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $chauffeurbrh = null;

    #[ORM\Column(nullable: true)]
    private ?int $cout_transportbrh = null;

    #[ORM\Column(length: 20, nullable: true)]
    private ?string $immatcamion = null;

    #[ORM\Column(nullable: true)]
    private ?int $exercice = null;

    #[ORM\Column(nullable: true)]
    private ?bool $fini = null;

    #[ORM\Column(nullable: true)]
    private ?bool $confirmation_usine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $motivation_rejet = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'pagebrhs')]
    private ?Documentbrh $code_docbrh = null;

    #[ORM\OneToMany(mappedBy: 'code_pagebrh', targetEntity: Lignepagebrh::class)]
    private Collection $lignepagebrhs;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\ManyToOne(inversedBy: 'pagebrhs')]
    private ?PageDocGen $code_generation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $entre_lje = null;

    #[ORM\Column(nullable: true)]
    private ?bool $soumettre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    public function __construct()
    {
        $this->lignepagebrhs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroPagebrh(): ?string
    {
        return $this->numero_pagebrh;
    }

    public function setNumeroPagebrh(string $numero_pagebrh): static
    {
        $this->numero_pagebrh = $numero_pagebrh;

        return $this;
    }

    public function getindex_page(): ?int
    {
        return $this->index_pagebrh;
    }

    public function setindex_page(int $index_pagebrh): static
    {
        $this->index_pagebrh = $index_pagebrh;

        return $this;
    }

    public function getDateChargementbrh(): ?\DateTimeInterface
    {
        return $this->date_chargementbrh;
    }

    public function setDateChargementbrh(\DateTimeInterface $date_chargementbrh): static
    {
        $this->date_chargementbrh = $date_chargementbrh;

        return $this;
    }

    public function getDestinationPagebrh(): ?string
    {
        return $this->destination_pagebrh;
    }

    public function setDestinationPagebrh(?string $destination_pagebrh): static
    {
        $this->destination_pagebrh = $destination_pagebrh;

        return $this;
    }

    public function getParcUsineBrh(): ?Usine
    {
        return $this->parc_usine_brh;
    }

    public function setParcUsineBrh(?Usine $parc_usine_brh): static
    {
        $this->parc_usine_brh = $parc_usine_brh;

        return $this;
    }

    public function getVillagePagebrh(): ?string
    {
        return $this->village_pagebrh;
    }

    public function setVillagePagebrh(?string $village_pagebrh): static
    {
        $this->village_pagebrh = $village_pagebrh;

        return $this;
    }

    public function getCantonnementPagebrh(): ?Cantonnement
    {
        return $this->cantonnement_pagebrh;
    }

    public function setCantonnementPagebrh(?Cantonnement $cantonnement_pagebrh): static
    {
        $this->cantonnement_pagebrh = $cantonnement_pagebrh;

        return $this;
    }

    public function getChauffeurbrh(): ?string
    {
        return $this->chauffeurbrh;
    }

    public function setChauffeurbrh(?string $chauffeurbrh): static
    {
        $this->chauffeurbrh = $chauffeurbrh;

        return $this;
    }

    public function getCoutTransportbrh(): ?int
    {
        return $this->cout_transportbrh;
    }

    public function setCoutTransportbrh(?int $cout_transportbrh): static
    {
        $this->cout_transportbrh = $cout_transportbrh;

        return $this;
    }

    public function getImmatcamion(): ?string
    {
        return $this->immatcamion;
    }

    public function setImmatcamion(?string $immatcamion): static
    {
        $this->immatcamion = $immatcamion;

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

    public function isFini(): ?bool
    {
        return $this->fini;
    }

    public function setFini(?bool $fini): static
    {
        $this->fini = $fini;

        return $this;
    }

    public function isConfirmationUsine(): ?bool
    {
        return $this->confirmation_usine;
    }

    public function setConfirmationUsine(?bool $confirmation_usine): static
    {
        $this->confirmation_usine = $confirmation_usine;

        return $this;
    }

    public function getMotivationRejet(): ?string
    {
        return $this->motivation_rejet;
    }

    public function setMotivationRejet(?string $motivation_rejet): static
    {
        $this->motivation_rejet = $motivation_rejet;

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


    public function getCodeDocbrh(): ?Documentbrh
    {
        return $this->code_docbrh;
    }

    public function setCodeDocbrh(?Documentbrh $code_docbrh): static
    {
        $this->code_docbrh = $code_docbrh;

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
            $lignepagebrh->setCodePagebrh($this);
        }

        return $this;
    }

    public function removeLignepagebrh(Lignepagebrh $lignepagebrh): static
    {
        if ($this->lignepagebrhs->removeElement($lignepagebrh)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebrh->getCodePagebrh() === $this) {
                $lignepagebrh->setCodePagebrh(null);
            }
        }

        return $this;
    }

    public function getUniqueDoc(): ?string
    {
        return $this->unique_doc;
    }

    public function setUniqueDoc(?string $unique_doc): static
    {
        $this->unique_doc = $unique_doc;

        return $this;
    }

    public function getCodeGeneration(): ?PageDocGen
    {
        return $this->code_generation;
    }

    public function setCodeGeneration(?PageDocGen $code_generation): static
    {
        $this->code_generation = $code_generation;

        return $this;
    }

    public function isEntreLje(): ?bool
    {
        return $this->entre_lje;
    }

    public function setEntreLje(?bool $entre_lje): static
    {
        $this->entre_lje = $entre_lje;

        return $this;
    }

    public function isSoumettre(): ?bool
    {
        return $this->soumettre;
    }

    public function setSoumettre(?bool $soumettre): static
    {
        $this->soumettre = $soumettre;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): static
    {
        $this->photo = $photo;

        return $this;
    }
}
