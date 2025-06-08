<?php

namespace App\Entity\DocStats\Pages;

use App\Entity\DocStats\Entetes\Documentbcbgfh;
use App\Entity\DocStats\Saisie\Lignepagebcbgfh;
use App\Entity\References\Cantonnement;
use App\Entity\References\PageDocGen;
use App\Entity\References\Usine;
use App\Repository\DocStats\Pages\PagebcbgfhRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.pagebcbgfh')]
#[ORM\Entity(repositoryClass: PagebcbgfhRepository::class)]
class Pagebcbgfh
{
    const PAGE_BCBGFH_EDITED_SUCCESSFULLY = 'PAGE_BCBGFH_EDITED_SUCCESSFULLY';
    const PAGE_BCBGFH_BAD_DATA = 'PAGE_BCBGFH_BAD_DATA';
    const PAGE_BCBGFH_ACCEPTED = 'PAGE_BCBGFH_LOADING_DATA_ACCEPTED';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $numero_pagebcbgfh = null;

    #[ORM\Column]
    private ?int $index_pagebcbgfh = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_chargementbcbgfh = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $destination_pagebcbgfh = null;

    #[ORM\ManyToOne(inversedBy: 'pagebcbgfhs')]
    private ?Usine $parc_usine_bcbgfh = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $village_pagebcbgfh = null;

    #[ORM\ManyToOne(inversedBy: 'pagebcbgfhs')]
    private ?Cantonnement $cantonnement_pagebcbgfh = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $chauffeurbcbgfh = null;

    #[ORM\Column(nullable: true)]
    private ?int $cout_transportbcbgfh = null;

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

    #[ORM\ManyToOne(inversedBy: 'pagebcbgfhs')]
    private ?Documentbcbgfh $code_docbcbgfh = null;

    #[ORM\OneToMany(mappedBy: 'code_pagebcbgfh', targetEntity: Lignepagebcbgfh::class)]
    private Collection $lignepagebcbgfhs;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $unique_doc = null;

    #[ORM\ManyToOne(inversedBy: 'pagebcbgfhs')]
    private ?PageDocGen $code_generation = null;

    #[ORM\Column(nullable: true)]
    private ?bool $entre_lje = null;

    #[ORM\Column(nullable: true)]
    private ?bool $soumettre = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $photo = null;

    public function __construct()
    {
        $this->lignepagebcbgfhs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroPagebcbgfh(): ?string
    {
        return $this->numero_pagebcbgfh;
    }

    public function setNumeroPagebcbgfh(string $numero_pagebcbgfh): static
    {
        $this->numero_pagebcbgfh = $numero_pagebcbgfh;

        return $this;
    }

    public function getindex_page(): ?int
    {
        return $this->index_pagebcbgfh;
    }

    public function setindex_page(int $index_pagebcbgfh): static
    {
        $this->index_pagebcbgfh = $index_pagebcbgfh;

        return $this;
    }

    public function getDateChargementbcbgfh(): ?\DateTimeInterface
    {
        return $this->date_chargementbcbgfh;
    }

    public function setDateChargementbcbgfh(\DateTimeInterface $date_chargementbcbgfh): static
    {
        $this->date_chargementbcbgfh = $date_chargementbcbgfh;

        return $this;
    }

    public function getDestinationPagebcbgfh(): ?string
    {
        return $this->destination_pagebcbgfh;
    }

    public function setDestinationPagebcbgfh(?string $destination_pagebcbgfh): static
    {
        $this->destination_pagebcbgfh = $destination_pagebcbgfh;

        return $this;
    }

    public function getParcUsineBcbgfh(): ?Usine
    {
        return $this->parc_usine_bcbgfh;
    }

    public function setParcUsineBcbgfh(?Usine $parc_usine_bcbgfh): static
    {
        $this->parc_usine_bcbgfh = $parc_usine_bcbgfh;

        return $this;
    }

    public function getVillagePagebcbgfh(): ?string
    {
        return $this->village_pagebcbgfh;
    }

    public function setVillagePagebcbgfh(?string $village_pagebcbgfh): static
    {
        $this->village_pagebcbgfh = $village_pagebcbgfh;

        return $this;
    }

    public function getCantonnementPagebcbgfh(): ?Cantonnement
    {
        return $this->cantonnement_pagebcbgfh;
    }

    public function setCantonnementPagebcbgfh(?Cantonnement $cantonnement_pagebcbgfh): static
    {
        $this->cantonnement_pagebcbgfh = $cantonnement_pagebcbgfh;

        return $this;
    }

    public function getChauffeurbcbgfh(): ?string
    {
        return $this->chauffeurbcbgfh;
    }

    public function setChauffeurbcbgfh(?string $chauffeurbcbgfh): static
    {
        $this->chauffeurbcbgfh = $chauffeurbcbgfh;

        return $this;
    }

    public function getCoutTransportbcbgfh(): ?int
    {
        return $this->cout_transportbcbgfh;
    }

    public function setCoutTransportbcbgfh(?int $cout_transportbcbgfh): static
    {
        $this->cout_transportbcbgfh = $cout_transportbcbgfh;

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


    public function getCodeDocbcbgfh(): ?Documentbcbgfh
    {
        return $this->code_docbcbgfh;
    }

    public function setCodeDocbcbgfh(?Documentbcbgfh $code_docbcbgfh): static
    {
        $this->code_docbcbgfh = $code_docbcbgfh;

        return $this;
    }

    /**
     * @return Collection<int, Lignepagebcbgfh>
     */
    public function getLignepagebcbgfhs(): Collection
    {
        return $this->lignepagebcbgfhs;
    }

    public function addLignepagebcbgfh(Lignepagebcbgfh $lignepagebcbgfh): static
    {
        if (!$this->lignepagebcbgfhs->contains($lignepagebcbgfh)) {
            $this->lignepagebcbgfhs->add($lignepagebcbgfh);
            $lignepagebcbgfh->setCodePagebcbgfh($this);
        }

        return $this;
    }

    public function removeLignepagebcbgfh(Lignepagebcbgfh $lignepagebcbgfh): static
    {
        if ($this->lignepagebcbgfhs->removeElement($lignepagebcbgfh)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebcbgfh->getCodePagebcbgfh() === $this) {
                $lignepagebcbgfh->setCodePagebcbgfh(null);
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