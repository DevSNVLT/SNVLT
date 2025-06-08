<?php

namespace App\Entity\Admin;

use App\Entity\Transformation\Contrat;
use App\Repository\Admin\CouponRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\Table(name: 'admin.coupon')]
#[ORM\Entity(repositoryClass: CouponRepository::class)]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'coupons')]
    private ?Contrat $code_contrat = null;

    #[ORM\Column]
    private ?int $nb_jours = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $created_at = null;

    #[ORM\Column(length: 255)]
    private ?string $created_by = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $updated_by = null;

    #[ORM\Column(length: 10)]
    private ?string $code_coupon = null;

    #[ORM\OneToMany(mappedBy: 'code_coupon', targetEntity: DocumentsCoupon::class)]
    private Collection $documentsCoupons;

    #[ORM\Column(nullable: true)]
    private ?bool $finalise = null;

    public function __construct()
    {
        $this->documentsCoupons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeContrat(): ?Contrat
    {
        return $this->code_contrat;
    }

    public function setCodeContrat(?Contrat $code_contrat): static
    {
        $this->code_contrat = $code_contrat;

        return $this;
    }

    public function getNbJours(): ?int
    {
        return $this->nb_jours;
    }

    public function setNbJours(int $nb_jours): static
    {
        $this->nb_jours = $nb_jours;

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

    public function getCodeCoupon(): ?string
    {
        return $this->code_coupon;
    }

    public function setCodeCoupon(string $code_coupon): static
    {
        $this->code_coupon = $code_coupon;

        return $this;
    }

    /**
     * @return Collection<int, DocumentsCoupon>
     */
    public function getDocumentsCoupons(): Collection
    {
        return $this->documentsCoupons;
    }

    public function addDocumentsCoupon(DocumentsCoupon $documentsCoupon): static
    {
        if (!$this->documentsCoupons->contains($documentsCoupon)) {
            $this->documentsCoupons->add($documentsCoupon);
            $documentsCoupon->setCodeCoupon($this);
        }

        return $this;
    }

    public function removeDocumentsCoupon(DocumentsCoupon $documentsCoupon): static
    {
        if ($this->documentsCoupons->removeElement($documentsCoupon)) {
            // set the owning side to null (unless already changed)
            if ($documentsCoupon->getCodeCoupon() === $this) {
                $documentsCoupon->setCodeCoupon(null);
            }
        }

        return $this;
    }

    public function isFinalise(): ?bool
    {
        return $this->finalise;
    }

    public function setFinalise(?bool $finalise): static
    {
        $this->finalise = $finalise;

        return $this;
    }
}
