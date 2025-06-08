<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CouponAppController extends AbstractController
{
    #[Route('/coupon/app', name: 'app_coupon_app')]
    public function index(): Response
    {
        return $this->render('coupon_app/index.html.twig');
    }
}
