<?php

namespace App\Controller;

use App\Controller\Services\AdministrationService;
use App\Repository\Autorisations\RepriseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Admin\Exercice;
use App\Entity\Autorisation\Reprise;
use App\Entity\Autorisation\Attribution;

class RepriseController extends AbstractController
{
    public function __construct(private AdministrationService $administrationService)
    {

    }

    #[Route('/reprises', name: 'liste_reprises')]
    public function index(ManagerRegistry $registry): Response
    {

        $exercice = $registry->getRepository(Exercice::class)->findOneBy([], ['id' => 'DESC']);
        
        $reprises = [];
        $totalRepriseExercice = $registry->getRepository(Reprise::class)->findBy(['exercice'=>$exercice]);
        foreach ($totalRepriseExercice as $reprise) {

            $attribution = $registry->getRepository(Attribution::class)->findOneBy(['id'=>$reprise->getCodeAttribution()]);

            $reprises[] = [
                'raisonSocial'=> $attribution->getCodeExploitant()->getRaisonSocialeExploitant(),
                'autorisation'=> $reprise->getNumeroAutorisation(). " du ". $reprise->getDateAutorisation()->format('d/m/Y'),
                'pef' => $attribution->getCodeForet()->getNumeroForet(),
                'marteau' => $attribution->getCodeexploitant()->getMarteauExploitant(),
                'code' => $attribution->getCodeexploitant()->getNumeroExploitant(),

            ];
        }

        return $this->render('reprises/index.html.twig', [
            'reprises' => $reprises,
            'exo'=>$exercice
        ]);
    }

    #[Route('/pdf_reprise', name: 'liste_reprise_pdf')]
    public function liste(ManagerRegistry $registry): Response
    {
        $exercice = $registry->getRepository(Exercice::class)->findOneBy([], ['id' => 'DESC']);
        
        $reprises = [];
        $totalRepriseExercice = $registry->getRepository(Reprise::class)->findBy(['exercice'=>$exercice]);
        foreach ($totalRepriseExercice as $reprise) {

            $attribution = $registry->getRepository(Attribution::class)->findOneBy(['id'=>$reprise->getCodeAttribution()]);

            $reprises[] = [
                'raisonSocial'=> $attribution->getCodeExploitant()->getRaisonSocialeExploitant(),
                'autorisation'=> $reprise->getNumeroAutorisation(). " du ". $reprise->getDateAutorisation()->format('d/m/Y'),
                'pef' => $attribution->getCodeForet()->getNumeroForet(),
                'marteau' => $attribution->getCodeexploitant()->getMarteauExploitant(),
                'code' => $attribution->getCodeexploitant()->getNumeroExploitant(),

            ];
        }


        return $this->render('reprises/liste.html.twig', [
            'reprises'=>$reprises
        ]);

    }
}
