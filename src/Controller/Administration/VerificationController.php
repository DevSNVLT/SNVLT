<?php

namespace App\Controller\Administration;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VerificationController extends AbstractController
{
    #[Route('snvlt/admin/check', name: 'app_code_verification')]
    public function index(): Response
    {
        return $this->render('administration/verification/index.html.twig');
    }
}
