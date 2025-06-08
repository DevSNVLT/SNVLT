<?php

namespace App\Controller\Requetes\sousRequetes;

use App\Entity\Admin\Exercice;
use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\References\Foret;
use App\Entity\References\TypeForet;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReqListeArbresForetController extends AbstractController
{
    #[Route('/snvlt/req/arbres/liste', name: 'app_req_arbres_foret')]
    public function index(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
        if (
            $this->isGranted('ROLE_ADMINISTRATIF') or
            $this->isGranted('ROLE_MINEF') or
            $this->isGranted('ROLE_ADMIN')
        ) {
            $user = $userRepository->find($this->getUser());
            $code_groupe = $user->getCodeGroupe()->getId();
            $typeforet= $registry->getRepository(TypeForet::class)->find(1);
            return $this->render('requetes/sous_requetes/lst_arbres_foret/index.html.twig', [
                'liste_menus'=>$menus->findOnlyParent(),
                "all_menus"=>$menus->findAll(),
                'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                'groupe'=>$code_groupe,
                'liste_parent'=>$permissions,
                'forets'=>$registry->getRepository(Foret::class)->findBy(['code_type_foret'=>$typeforet],['denomination'=>'ASC']),
                'exercices'=>$registry->getRepository(Exercice::class)->findBy([],['id'=>'DESC']),
            ]);

        } else {
            return $this->redirectToRoute('app_no_permission_user_active');
        }
       }
    }

    #[Route('/snvlt/req/arbres/liste-foret/{id_foret}/{id_exercice}', name: 'rechercher_arbres_foret')]
    public function rechercher_arbres_foret(
        Request $request,
        int $id_foret,
        int $id_exercice,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {

                $foret = $registry->getRepository(Foret::class)->find($id_foret);
                $exercice = $registry->getRepository(Exercice::class)->find($id_exercice);
                $response = array();
                $exploitant = "-";
                if ($foret && $exercice){
                    $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret, 'exercice'=>$exercice]);
                    foreach ($attributions as $attribution){
                        if ($attribution->getCodeExploitant()){
                            if ($attribution->getCodeExploitant()->getSigle()){
                                $exploitant = $attribution->getCodeExploitant()->getSigle();
                            } else {
                                $exploitant = $attribution->getCodeExploitant()->getRaisonSocialeExploitant();
                            }
                        }
                        $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                        foreach ($reprises as $reprise){
                            $docbrhs = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                            foreach ($docbrhs as $docbrh){
                                $pages = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$docbrh]);
                                foreach ($pages as $page){
                                    $lignes = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page, 'lettre_lignepagebrh'=>'A']);
                                    foreach ($lignes as $ligne){
                                        if ($ligne->getNomEssencebrh()){$essence = $ligne->getNomEssencebrh()->getNomVernaculaire();} else {$essence = "-";}
                                        if ($ligne->getZhLignepagebrh()){$zh = $ligne->getZhLignepagebrh()->getZone();} else {$zh = "-";}

                                        $response[] = array(
                                            'numero'=>$ligne->getNumeroLignepagebrh(),
                                            'essence'=>$essence,
                                            'zh'=>$zh,
                                            'x'=>$ligne->getXLignepagebrh(),
                                            'y'=>$ligne->getYLignepagebrh(),
                                            'foret'=>$foret->getNumeroForet(),
                                            'annee'=>$exercice->getAnnee(),
                                            'exploitant'=>$exploitant
                                        );
                                    }
                                }
                            }
                        }
                    }
                }

                sort($response);
                return  new JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

}
