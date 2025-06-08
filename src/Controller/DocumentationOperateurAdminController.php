<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentationOperateurAdminController extends AbstractController
{
    #[Route('/documentation/operateur/admin', name: 'app_documentation_operateur_admin')]
    public function index(): Response
    {
        return $this->render('documentation_operateur_admin/index.html.twig', [
            'controller_name' => 'DocumentationOperateurAdminController',
        ]);
    }
}
