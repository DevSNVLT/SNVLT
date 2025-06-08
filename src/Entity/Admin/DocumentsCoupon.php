<?php

namespace App\Entity\Admin;

use App\Entity\References\DocumentOperateur;
use App\Repository\Admin\DocumentsCouponRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentsCouponRepository::class)]
#[ORM\Table(name: 'admin.documents_coupon')]
class DocumentsCoupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'documentsCoupons')]
    private ?DocumentOperateur $code_doc_op = null;

    #[ORM\ManyToOne(inversedBy: 'documentsCoupons')]
    private ?Coupon $code_coupon = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeDocOp(): ?DocumentOperateur
    {
        return $this->code_doc_op;
    }

    public function setCodeDocOp(?DocumentOperateur $code_doc_op): static
    {
        $this->code_doc_op = $code_doc_op;

        return $this;
    }

    public function getCodeCoupon(): ?Coupon
    {
        return $this->code_coupon;
    }

    public function setCodeCoupon(?Coupon $code_coupon): static
    {
        $this->code_coupon = $code_coupon;

        return $this;
    }
}
