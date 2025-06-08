<?php

namespace App\Entity\DocStats\Saisie;

use App\Entity\Admin\Exercice;
use App\Entity\DocStats\Pages\Pagebcbp;
use App\Entity\References\Essence;
use App\Entity\References\ZoneHemispherique;
use App\Repository\DocStats\Saisie\LignepagebcbpRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.lignepagebcbp')]
#[ORM\Entity(repositoryClass: LignepagebcbpRepository::class)]
class Lignepagebcbp
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $numero_bille = null;

    #[ORM\Column(length: 1)]
    private ?string $lettre = null;



    #[ORM\Column]
    private ?float $x = null;

    #[ORM\Column]
    private ?float $y = null;

    #[ORM\Column]
    private ?float $lng = null;

    #[ORM\Column]
    private ?float $dm = null;

    #[ORM\Column]
    private ?float $volume = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagebcbps')]
    private ?ZOneHemispherique $zh_lignepagebcbp = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagebcbps')]
    private ?Pagebcbp $code_pagebcbp = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagebcbps')]
    private ?Essence $essence = null;

    #[ORM\ManyToOne(inversedBy: 'lignepagebrhs')]
    private ?Exercice $exercice = null;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroBille(): ?int
    {
        return $this->numero_bille;
    }

    public function setNumeroBille(int $numero_bille): static
    {
        $this->numero_bille = $numero_bille;

        return $this;
    }

    public function getLettre(): ?string
    {
        return $this->lettre;
    }

    public function setLettre(string $lettre): static
    {
        $this->lettre = $lettre;

        return $this;
    }

    public function getX(): ?float
    {
        return $this->x;
    }

    public function setX(float $x): static
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): ?float
    {
        return $this->y;
    }

    public function setY(float $y): static
    {
        $this->y = $y;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(float $lng): static
    {
        $this->lng = $lng;

        return $this;
    }

    public function getDm(): ?float
    {
        return $this->dm;
    }

    public function setDm(float $dm): static
    {
        $this->dm = $dm;

        return $this;
    }

    public function getVolume(): ?float
    {
        return $this->volume;
    }

    public function setVolume(float $volume): static
    {
        $this->volume = $volume;

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

    public function getZhLignepagebcbp(): ?ZOneHemispherique
    {
        return $this->zh_lignepagebcbp;
    }

    public function setZhLignepagebcbp(?ZOneHemispherique $zh_lignepagebcbp): static
    {
        $this->zh_lignepagebcbp = $zh_lignepagebcbp;

        return $this;
    }

    public function getCodePagebcbp(): ?Pagebcbp
    {
        return $this->code_pagebcbp;
    }

    public function setCodePagebcbp(?Pagebcbp $code_pagebcbp): static
    {
        $this->code_pagebcbp = $code_pagebcbp;

        return $this;
    }

    public function getEssence(): ?Essence
    {
        return $this->essence;
    }

    public function setEssence(?Essence $essence): static
    {
        $this->essence = $essence;

        return $this;
    }


    /**
     * @return Exercice|null
     */
    public function getExercice(): ?Exercice
    {
        return $this->exercice;
    }

    /**
     * @param Exercice|null $exercice
     */
    public function setExercice(?Exercice $exercice): void
    {
        $this->exercice = $exercice;
    }


}
