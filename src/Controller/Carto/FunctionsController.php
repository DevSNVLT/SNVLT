<?php

namespace App\Controller\Carto;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FunctionsController extends AbstractController
{
    #[Route('/carto/functions', name: 'app_carto_functions')]
    public function index(): Response
    {
        return $this->render('carto/functions/index.html.twig', [
            'controller_name' => 'FunctionsController',
        ]);
    }
}
