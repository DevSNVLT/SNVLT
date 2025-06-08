<?php

namespace App\Entity\DocStats\Saisie;

use App\Entity\Admin\Exercice;
use App\Entity\DocStats\Pages\Pagebcbgfh;
use App\Entity\References\Essence;
use App\Entity\References\ZoneHemispherique;
use App\Repository\DocStats\Saisie\LignepagebcbgfhRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.lignepagebcbgfh')]
#[ORM\Entity(repositoryClass: LignepagebcbgfhRepository::class)]
class Lignepagebcbgfh
{
    const LIGNE_BCBGFH_ADDED_SUCCESSFULLY = 'LIGNE_BCBGFH_ADDED_SUCCESSFULLY';
    const LIGNE_BCBGFH_IVALID_DATA= 'LIGNE_BCBGFH_IVALID_DATA';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagebcbgfhs')]
    private ?Essence $nom_essencebcbgfh = null;

    #[ORM\Column]
    private ?int $numero_lignepagebcbgfh = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagebcbgfhs')]
    private ?ZoneHemispherique $zh_lignepagebcbgfh = null;

    #[ORM\Column(nullable: true)]
    private ?float $x_lignepagebcbgfh = null;

    #[ORM\Column(nullable: true)]
    private ?float $y_lignepagebcbgfh = null;

    #[ORM\Column(length: 1)]
    private ?string $lettre_lignepagebcbgfh = null;

    #[ORM\Column]
    private ?int $longeur_lignepagebcbgfh = null;

    #[ORM\Column]
    private ?int $diametre_lignepagebcbgfh = null;

    #[ORM\Column]
    private ?float $cubage_lignepagebcbgfh = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observationbcbgfh = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagebcbgfhs')]
    private ?Pagebcbgfh $code_pagebcbgfh = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(nullable: true)]
    private ?bool $entre_lje = null;

    #[ORM\Column(nullable: true)]
    private ?bool $transformation = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagebcbgfhs')]
    private ?Exercice $exercice = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagebcbgfhs')]
    private ?Lignepagecp $code_ligne_cp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEssencebcbgfh(): ?Essence
    {
        return $this->nom_essencebcbgfh;
    }

    public function setNomEssencebcbgfh(?Essence $nom_essencebcbgfh): static
    {
        $this->nom_essencebcbgfh = $nom_essencebcbgfh;

        return $this;
    }

    public function getNumeroLignepagebcbgfh(): ?int
    {
        return $this->numero_lignepagebcbgfh;
    }

    public function setNumeroLignepagebcbgfh(int $numero_lignepagebcbgfh): static
    {
        $this->numero_lignepagebcbgfh = $numero_lignepagebcbgfh;

        return $this;
    }

    public function getZhLignepagebcbgfh(): ?ZoneHemispherique
    {
        return $this->zh_lignepagebcbgfh;
    }

    public function setZhLignepagebcbgfh(?ZoneHemispherique $zh_lignepagebcbgfh): static
    {
        $this->zh_lignepagebcbgfh = $zh_lignepagebcbgfh;

        return $this;
    }

    public function getXLignepagebcbgfh(): ?float
    {
        return $this->x_lignepagebcbgfh;
    }

    public function setXLignepagebcbgfh(?float $x_lignepagebcbgfh): static
    {
        $this->x_lignepagebcbgfh = $x_lignepagebcbgfh;

        return $this;
    }

    public function getYLignepagebcbgfh(): ?float
    {
        return $this->y_lignepagebcbgfh;
    }

    public function setYLignepagebcbgfh(?float $y_lignepagebcbgfh): static
    {
        $this->y_lignepagebcbgfh = $y_lignepagebcbgfh;

        return $this;
    }

    public function getLettreLignepagebcbgfh(): ?string
    {
        return $this->lettre_lignepagebcbgfh;
    }

    public function setLettreLignepagebcbgfh(string $lettre_lignepagebcbgfh): static
    {
        $this->lettre_lignepagebcbgfh = $lettre_lignepagebcbgfh;

        return $this;
    }

    public function getLongeurLignepagebcbgfh(): ?int
    {
        return $this->longeur_lignepagebcbgfh;
    }

    public function setLongeurLignepagebcbgfh(int $longeur_lignepagebcbgfh): static
    {
        $this->longeur_lignepagebcbgfh = $longeur_lignepagebcbgfh;

        return $this;
    }

    public function getDiametreLignepagebcbgfh(): ?int
    {
        return $this->diametre_lignepagebcbgfh;
    }

    public function setDiametreLignepagebcbgfh(int $diametre_lignepagebcbgfh): static
    {
        $this->diametre_lignepagebcbgfh = $diametre_lignepagebcbgfh;

        return $this;
    }

    public function getCubageLignepagebcbgfh(): ?float
    {
        return $this->cubage_lignepagebcbgfh;
    }

    public function setCubageLignepagebcbgfh(float $cubage_lignepagebcbgfh): static
    {
        $this->cubage_lignepagebcbgfh = $cubage_lignepagebcbgfh;

        return $this;
    }

    public function getObservationbcbgfh(): ?string
    {
        return $this->observationbcbgfh;
    }

    public function setObservationbcbgfh(?string $observationbcbgfh): static
    {
        $this->observationbcbgfh = $observationbcbgfh;

        return $this;
    }

    public function getCodePagebcbgfh(): ?Pagebcbgfh
    {
        return $this->code_pagebcbgfh;
    }

    public function setCodePagebcbgfh(?Pagebcbgfh $code_pagebcbgfh): static
    {
        $this->code_pagebcbgfh = $code_pagebcbgfh;

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

    public function isEntreLje(): ?bool
    {
        return $this->entre_lje;
    }

    public function setEntreLje(?bool $entre_lje): static
    {
        $this->entre_lje = $entre_lje;

        return $this;
    }

    public function isTransformation(): ?bool
    {
        return $this->transformation;
    }

    public function setTransformation(?bool $transformation): static
    {
        $this->transformation = $transformation;

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

    public function getCodeLigneCp(): ?Lignepagecp
    {
        return $this->code_ligne_cp;
    }

    public function setCodeLigneCp(?Lignepagecp $code_ligne_cp): static
    {
        $this->code_ligne_cp = $code_ligne_cp;

        return $this;
    }
}
