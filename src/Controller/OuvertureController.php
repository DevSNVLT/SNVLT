<?php

namespace App\Controller;

use App\Entity\Admin\Exercice;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OuvertureController extends AbstractController
{
    #[Route('snvlt/login/0', name: 'app_ouverture')]
    public function index(ManagerRegistry $registry,Request $request): Response
    {
        if (!$request->getSession()->has('user_session') or $request->getSession()->has("exercice")){
            return  $this->redirectToRoute("app_tdb_admin");
        } else {
            $rallonge = $registry->getRepository(Exercice::class)->findOneBy(['rallonge'=>true], ['id'=>'DESC']);
            $aujourdhui = new  \DateTime();
            $isRallonge = false;
            if ($rallonge){
                if ($rallonge->getDateExpirationRallonge() >= $aujourdhui){
                    $isRallonge = true;
                }
            }
            return $this->render('ouverture/index.html.twig', [
                'exercices' => $registry->getRepository(Exercice::class)->findBy([], ['annee'=>'DESC']),
                'exo_en_cours'=>$registry->getRepository(Exercice::class)->findOneBy([], ['annee'=>'DESC']),
                'rallonge'=>$rallonge,
                'is_rallonge'=>$isRallonge
            ]);
        }
    }

    #[Route('snvlt/ck_lg/{exo}', name: 'cklog')]
    public function cklog(ManagerRegistry $registry, Request $request, int $exo): Response
    {
        $reponse = array();
        $exercice = $registry->getRepository(Exercice::class)->findOneBy(['annee'=>$exo]);
        if ($exercice){
            $request->getSession()->set("exercice",$exercice->getId());
            $reponse[] = array(
                'code'=>'SUCCESS'
            );

        } else {
            $reponse[] = array(
                'code'=>'ERROR'
            );

        }
        return new JsonResponse(json_encode($reponse));
    }

}
