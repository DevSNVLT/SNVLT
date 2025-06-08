<?php

namespace App\Controller\References;

use App\Entity\Admin\Exercice;
use App\Entity\DocStats\Saisie\Lignepagecp;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AbresAbattuController extends AbstractController
{
    #[Route('/references/abres/abattu', name: 'app_references_abres_abattu')]
    public function index(ManagerRegistry $registry): Response
    {   
        $totalArbreAbattu = [];
        $exerciceEnAnnee = $registry->getRepository(Exercice::class)->findOneBy([],['id'=>'DESC'])->getAnnee();
        $totalArbresExercice = $registry->getRepository(Lignepagecp::class)
               ->findBy(['exercice'=>$exerciceEnAnnee]);

        foreach ($totalArbresExercice as $abre){
            $totalArbreAbattu[] = [
                'numeroArbre' => $abre->getNumeroArbrecp(),
                'nomVernaculaire' =>$abre->getNomEssencecp()->getNomVernaculaire(),
                'x'=>$abre->getXArbrecp(),
                'y'=>$abre->getYArbrecp(),
                'long'=>$abre->getLongeurArbrecp(),
                'diam'=>$abre->getDiametreArbrecp(),
            ];
        }

        return $this->render('references/abres_abattu/index.html.twig', [
            'arbresAbattu' => $totalArbreAbattu,
        ]);
    }
}
