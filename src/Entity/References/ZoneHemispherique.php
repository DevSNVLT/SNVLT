<?php

namespace App\Entity\References;

use App\Entity\Administration\InventaireForestier;
use App\Entity\DocStats\Saisie\Lignepagebcbp;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\DocStats\Saisie\Lignepagebtgu;
use App\Entity\DocStats\Saisie\Lignepagecp;
use App\Entity\DocStats\Saisie\Lignepagelje;
use App\Repository\References\ZoneHemispheriqueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'metier.zone_hemispherique')]
#[ORM\Entity(repositoryClass: ZoneHemispheriqueRepository::class)]
class ZoneHemispherique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 4, nullable: true)]
    private ?string $zone = null;

    #[ORM\OneToMany(mappedBy: 'zoneh', targetEntity: InventaireForestier::class)]
    private Collection $inventaireForestiers;

    #[ORM\OneToMany(mappedBy: 'zh_arbrecp', targetEntity: Lignepagecp::class)]
    private Collection $lignepagecps;

    #[ORM\OneToMany(mappedBy: 'zh_lignepagebrh', targetEntity: Lignepagebrh::class)]
    private Collection $lignepagebrhs;

    #[ORM\OneToMany(mappedBy: 'zh', targetEntity: Lignepagelje::class)]
    private Collection $lignepageljes;

    #[ORM\OneToMany(mappedBy: 'zh', targetEntity: Lignepagebtgu::class)]
    private Collection $lignepagebtgus;

    #[ORM\OneToMany(mappedBy: 'zh_lignepagebcbp', targetEntity: Lignepagebcbp::class)]
    private Collection $lignepagebcbps;

    public function __construct()
    {
        $this->inventaireForestiers = new ArrayCollection();
        $this->lignepagecps = new ArrayCollection();
        $this->lignepagebrhs = new ArrayCollection();
        $this->lignepageljes = new ArrayCollection();
        $this->lignepagebtgus = new ArrayCollection();
        $this->lignepagebcbps = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getZone(): ?string
    {
        return $this->zone;
    }

    public function setZone(?string $zone): static
    {
        $this->zone = $zone;

        return $this;
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
            $inventaireForestier->setZoneh($this);
        }

        return $this;
    }

    public function removeInventaireForestier(InventaireForestier $inventaireForestier): static
    {
        if ($this->inventaireForestiers->removeElement($inventaireForestier)) {
            // set the owning side to null (unless already changed)
            if ($inventaireForestier->getZoneh() === $this) {
                $inventaireForestier->setZoneh(null);
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
            $lignepagecp->setZhArbrecp($this);
        }

        return $this;
    }

    public function removeLignepagecp(Lignepagecp $lignepagecp): static
    {
        if ($this->lignepagecps->removeElement($lignepagecp)) {
            // set the owning side to null (unless already changed)
            if ($lignepagecp->getZhArbrecp() === $this) {
                $lignepagecp->setZhArbrecp(null);
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
            $lignepagebrh->setZhLignepagebrh($this);
        }

        return $this;
    }

    public function removeLignepagebrh(Lignepagebrh $lignepagebrh): static
    {
        if ($this->lignepagebrhs->removeElement($lignepagebrh)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebrh->getZhLignepagebrh() === $this) {
                $lignepagebrh->setZhLignepagebrh(null);
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
            $lignepagelje->setZh($this);
        }

        return $this;
    }

    public function removeLignepagelje(Lignepagelje $lignepagelje): static
    {
        if ($this->lignepageljes->removeElement($lignepagelje)) {
            // set the owning side to null (unless already changed)
            if ($lignepagelje->getZh() === $this) {
                $lignepagelje->setZh(null);
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
            $lignepagebtgu->setZh($this);
        }

        return $this;
    }

    public function removeLignepagebtgu(Lignepagebtgu $lignepagebtgu): static
    {
        if ($this->lignepagebtgus->removeElement($lignepagebtgu)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebtgu->getZh() === $this) {
                $lignepagebtgu->setZh(null);
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
            $lignepagebcbp->setZhLignepagebcbp($this);
        }

        return $this;
    }

    public function removeLignepagebcbp(Lignepagebcbp $lignepagebcbp): static
    {
        if ($this->lignepagebcbps->removeElement($lignepagebcbp)) {
            // set the owning side to null (unless already changed)
            if ($lignepagebcbp->getZhLignepagebcbp() === $this) {
                $lignepagebcbp->setZhLignepagebcbp(null);
            }
        }

        return $this;
    }
}
