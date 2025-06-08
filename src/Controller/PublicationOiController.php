<?php

namespace App\Controller;

use App\Entity\Observateur\PublicationRapport;
use App\Repository\Observateur\PublicationRapportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PublicationOiController extends AbstractController
{
    #[Route('/publication/oi', name: 'app_publication_oi')]
    public function index(PublicationRapportRepository $rapport): Response
    {
        return $this->render('publication_oi/index.html.twig', [
            'publications' => $rapport->findBy([], ['created_at'=>'DESC']),
        ]);
    }
}
