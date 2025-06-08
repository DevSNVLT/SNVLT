<?php

namespace App\Controller\Requetes\sousRequetes;

use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\References\Essence;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReqSousDiametresController extends AbstractController
{
    #[Route('/requetes/sous/requetes/req/sous/diametres', name: 'app_req_sd')]
    public function app_req_sd(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        User $user = null,
        NotificationRepository $notification): Response
    { if(!$request->getSession()->has('user_session')){
        return $this->redirectToRoute('app_login');
    } else {
        if (
            $this->isGranted('ROLE_ADMIN') and $this->isGranted('ROLE_DPIF')
        ) {
            $user = $userRepository->find($this->getUser());
            $code_groupe = $user->getCodeGroupe()->getId();


                // essences et cubages
                $liste_essences = $registry->getRepository(Essence::class)->findAll();

                foreach($liste_essences as $ess){
                    $linge_brh_essences_sd = $registry->getRepository(Lignepagebrh::class)->findBy(['nom_essencebrh'=>$ess, 'lettre_lignepagebrh'=>'A']);
                    $cubage_sd = false;
                    $date_chargement = "-";
                    $date_saisie = "-";
                    $exploitant = "-";


                    foreach($linge_brh_essences_sd as $essence_brh){
                        if ($ess->getDmMinima() > $essence_brh->getDiametreLignepagebrh()){

                            if($essence_brh->getCodePagebrh()->getDateChargementbrh()){
                                $date_chargement = $essence_brh->getCodePagebrh()->getDateChargementbrh()->format('d/m/Y');
                            }
                            if($essence_brh->getCreatedAt()){
                                $date_saisie = $essence_brh->getCreatedAt()->format('d/m/Y');
                            }
                            if ($essence_brh->getCodePagebrh()->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()){
                                if ($essence_brh->getCodePagebrh()->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle()){
                                    $exploitant = $essence_brh->getCodePagebrh()->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle();
                                } else {
                                    $exploitant = $essence_brh->getCodePagebrh()->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant();
                                }
                            }
                            if ($essence_brh->getCodePagebrh()->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()){
                                if ($essence_brh->getCodePagebrh()->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()){
                                        $foret = $essence_brh->getCodePagebrh()->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination();
                                        } else {
                                        $foret = "-";
                                }
                            }

                            $liste_essences_sd[] = array(
                                'created_at'=>$essence_brh->getCreatedAt()->format("d/m/Y"),
                                'essence'=>$ess->getNomVernaculaire(),
                                'numero'=>$essence_brh->getNumeroLignepagebrh(). $essence_brh->getLettreLignepagebrh(),
                                'x'=>$essence_brh->getXLignepagebrh(),
                                'y'=>$essence_brh->getYLignepagebrh(),
                                'zh'=>$essence_brh->getZhLignepagebrh(),
                                'lng'=>$essence_brh->getLongeurLignepagebrh(),
                                'dm'=>$essence_brh->getDiametreLignepagebrh(),
                                'volume'=>round($essence_brh->getCubageLignepagebrh(),3),
                                'dm_min'=>$ess->getDmMinima(),
                                'ecart'=>$ess->getDmMinima() - $essence_brh->getDiametreLignepagebrh(),
                                'brh'=>$essence_brh->getCodePagebrh()->getCodeDocbrh()->getNumeroDocbrh(). " - Feuillet N° ". $essence_brh->getCodePagebrh()->getNumeroPagebrh(),
                                'date_chargement'=>$date_chargement,
                                'date_saisie'=>$date_saisie,
                                'foret'=>$foret,
                                'exploitant'=>$exploitant
                            );
                        }
                    }
                }
                arsort($liste_essences_sd);

        return $this->render('requetes/sous_requetes/req_sous_diametres/index.html.twig', [
            'liste_menus'=>$menus->findOnlyParent(),
            "all_menus"=>$menus->findAll(),
            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
            'groupe'=>$code_groupe,
            'liste_parent'=>$permissions,
            'dm_minima'=>array_slice($liste_essences_sd, 0, 1000)

        ]);

        } else {
            return $this->redirectToRoute('app_no_permission_user_active');
        }
      }
    }
}