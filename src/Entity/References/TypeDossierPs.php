<?php

namespace App\Entity\References;

use App\Entity\Autorisation\AgreementPs;
use App\Entity\Autorisation\AutorisationPs;
use App\Repository\References\TypeDossierPsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metier.type_dossier_ps')]
#[ORM\Entity(repositoryClass: TypeDossierPsRepository::class)]
class TypeDossierPs
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $libelle = null;

    #[ORM\Column(nullable: true)]
    private ?int $montant_agreement = null;

    #[ORM\OneToMany(mappedBy: 'type_dossier_ps', targetEntity: NaturePs::class)]
    private Collection $naturePs;

    #[ORM\OneToMany(mappedBy: 'code_type_dossier', targetEntity: AgreementPs::class)]
    private Collection $agreementPs;

    public function __construct()
    {
        $this->naturePs = new ArrayCollection();
        $this->agreementPs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getMontantAgreement(): ?int
    {
        return $this->montant_agreement;
    }

    public function setMontantAgreement(?int $montant_agreement): static
    {
        $this->montant_agreement = $montant_agreement;

        return $this;
    }

    /**
     * @return Collection<int, NaturePs>
     */
    public function getNaturePs(): Collection
    {
        return $this->naturePs;
    }

    public function addNatureP(NaturePs $natureP): static
    {
        if (!$this->naturePs->contains($natureP)) {
            $this->naturePs->add($natureP);
            $natureP->setTypeDossierPs($this);
        }

        return $this;
    }

    public function removeNatureP(NaturePs $natureP): static
    {
        if ($this->naturePs->removeElement($natureP)) {
            // set the owning side to null (unless already changed)
            if ($natureP->getTypeDossierPs() === $this) {
                $natureP->setTypeDossierPs(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AgreementPs>
     */
    public function getAgreementPs(): Collection
    {
        return $this->agreementPs;
    }

    public function addAgreementP(AgreementPs $agreementP): static
    {
        if (!$this->agreementPs->contains($agreementP)) {
            $this->agreementPs->add($agreementP);
            $agreementP->setCodeTypeDossier($this);
        }

        return $this;
    }

    public function removeAgreementP(AgreementPs $agreementP): static
    {
        if ($this->agreementPs->removeElement($agreementP)) {
            // set the owning side to null (unless already changed)
            if ($agreementP->getCodeTypeDossier() === $this) {
                $agreementP->setCodeTypeDossier(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return  $this->libelle;
    }
}
