<?php

namespace App\Entity\DocStats\Saisie;

use App\Entity\Admin\Exercice;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\References\Essence;
use App\Entity\References\ZoneHemispherique;
use App\Repository\DocStats\Saisie\LignepagebrhRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.lignepagebrh')]
#[ORM\Entity(repositoryClass: LignepagebrhRepository::class)]
class Lignepagebrh
{
    const LIGNE_BCBGFH_ADDED_SUCCESSFULLY = 'LIGNE_BCBGFH_ADDED_SUCCESSFULLY';
    const LIGNE_BCBGFH_IVALID_DATA= 'LIGNE_BCBGFH_IVALID_DATA';
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagebrhs')]
    private ?Essence $nom_essencebrh = null;

    #[ORM\Column]
    private ?int $numero_lignepagebrh = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagebrhs')]
    private ?ZoneHemispherique $zh_lignepagebrh = null;

    #[ORM\Column(nullable: true)]
    private ?float $x_lignepagebrh = null;

    #[ORM\Column(nullable: true)]
    private ?float $y_lignepagebrh = null;

    #[ORM\Column(length: 1)]
    private ?string $lettre_lignepagebrh = null;

    #[ORM\Column]
    private ?int $longeur_lignepagebrh = null;

    #[ORM\Column]
    private ?int $diametre_lignepagebrh = null;

    #[ORM\Column]
    private ?float $cubage_lignepagebrh = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $observationbrh = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagebrhs')]
    private ?Pagebrh $code_pagebrh = null;

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

    #[ORM\ManyToOne(inversedBy: 'lignepagebrhs')]
    private ?Exercice $exercice = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagebrhs')]
    private ?Lignepagecp $code_ligne_cp = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomEssencebrh(): ?Essence
    {
        return $this->nom_essencebrh;
    }

    public function setNomEssencebrh(?Essence $nom_essencebrh): static
    {
        $this->nom_essencebrh = $nom_essencebrh;

        return $this;
    }

    public function getNumeroLignepagebrh(): ?int
    {
        return $this->numero_lignepagebrh;
    }

    public function setNumeroLignepagebrh(int $numero_lignepagebrh): static
    {
        $this->numero_lignepagebrh = $numero_lignepagebrh;

        return $this;
    }

    public function getZhLignepagebrh(): ?ZoneHemispherique
    {
        return $this->zh_lignepagebrh;
    }

    public function setZhLignepagebrh(?ZoneHemispherique $zh_lignepagebrh): static
    {
        $this->zh_lignepagebrh = $zh_lignepagebrh;

        return $this;
    }

    public function getXLignepagebrh(): ?float
    {
        return $this->x_lignepagebrh;
    }

    public function setXLignepagebrh(?float $x_lignepagebrh): static
    {
        $this->x_lignepagebrh = $x_lignepagebrh;

        return $this;
    }

    public function getYLignepagebrh(): ?float
    {
        return $this->y_lignepagebrh;
    }

    public function setYLignepagebrh(?float $y_lignepagebrh): static
    {
        $this->y_lignepagebrh = $y_lignepagebrh;

        return $this;
    }

    public function getLettreLignepagebrh(): ?string
    {
        return $this->lettre_lignepagebrh;
    }

    public function setLettreLignepagebrh(string $lettre_lignepagebrh): static
    {
        $this->lettre_lignepagebrh = $lettre_lignepagebrh;

        return $this;
    }

    public function getLongeurLignepagebrh(): ?int
    {
        return $this->longeur_lignepagebrh;
    }

    public function setLongeurLignepagebrh(int $longeur_lignepagebrh): static
    {
        $this->longeur_lignepagebrh = $longeur_lignepagebrh;

        return $this;
    }

    public function getDiametreLignepagebrh(): ?int
    {
        return $this->diametre_lignepagebrh;
    }

    public function setDiametreLignepagebrh(int $diametre_lignepagebrh): static
    {
        $this->diametre_lignepagebrh = $diametre_lignepagebrh;

        return $this;
    }

    public function getCubageLignepagebrh(): ?float
    {
        return $this->cubage_lignepagebrh;
    }

    public function setCubageLignepagebrh(float $cubage_lignepagebrh): static
    {
        $this->cubage_lignepagebrh = $cubage_lignepagebrh;

        return $this;
    }

    public function getObservationbrh(): ?string
    {
        return $this->observationbrh;
    }

    public function setObservationbrh(?string $observationbrh): static
    {
        $this->observationbrh = $observationbrh;

        return $this;
    }

    public function getCodePagebrh(): ?Pagebrh
    {
        return $this->code_pagebrh;
    }

    public function setCodePagebrh(?Pagebrh $code_pagebrh): static
    {
        $this->code_pagebrh = $code_pagebrh;

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
