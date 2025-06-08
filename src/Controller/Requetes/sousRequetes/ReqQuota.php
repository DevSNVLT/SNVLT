<?php

namespace App\Controller\Requetes\sousRequetes;

use App\Entity\Admin\Exercice;
use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\References\Essence;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Entity\References\TypeForet;
use App\Entity\Requetes\CubageEssences;
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

class ReqQuota extends AbstractController
{
    #[Route('/snvlt/req/exploitation/quota', name: 'app_req_quota')]
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

                return $this->render('requetes/sous_requetes/quota/index.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'exploitants'=>$registry->getRepository(Exploitant::class)->findBy([],['raison_sociale_exploitant'=>'ASC']),
                    'exercices'=>$registry->getRepository(Exercice::class)->findBy([],['id'=>'DESC'])
                ]);

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/req/exploitation/quota/all/{id_exercice}', name: 'rechercher_tous_les_quotas')]
    public function rechercher_tous_les_quotas(
        Request $request,
        ManagerRegistry $registry,
        int $id_exercice
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF') )
            {

                $response = array();

                $exercice = $registry->getRepository(Exercice::class)->find($id_exercice);
                if ($exercice){


                    $attributions = $registry->getRepository(Attribution::class)->findBy(['exercice'=>$exercice]);

                    foreach ($attributions as $attribution){
                        $volume = 0;
                        $nb_billes = 0;
                        $quota = 0;
                        $pourcentage = 0;
                        $exploitant = "-";
                        $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                        foreach ($reprises as $reprise){
                            $docbrhs = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                            foreach ($docbrhs as $docbrh){
                                $pages = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$docbrh]);
                                foreach ($pages as $page){
                                    $lignepages = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page]);
                                    foreach ($lignepages as $lignepage){
                                        $volume = $volume + $lignepage->getCubageLignepagebrh();
                                        $nb_billes = $nb_billes + 1;
                                    }
                                }
                            }
                        }
                        if ($attribution->getCodeExploitant()){
                            if ($attribution->getCodeExploitant()->getSigle()){
                                $exploitant = $attribution->getCodeExploitant()->getSigle();
                            } else {
                                $exploitant = $attribution->getCodeExploitant()->getRaisonSocialeExploitant();
                            }
                        }
                        $numero_foret = "-";
                        $superficie = 0;
                        if ($attribution->getCodeForet()){
                            $foret = $attribution->getCodeForet();
                            $numero_foret = $foret->getDenomination();
                            $superficie = $foret->getSuperficie();
                            if ($foret->getSuperficie()){
                                $quota =  $foret->getSuperficie() / 4;
                            }
                        }

                        if ($quota > 0){
                            $pourcentage =  round(($volume / $quota) * 100, 0);
                        }
                        $response[] = array(
                            'numero_foret'=>$numero_foret,
                            'superficie'=>$superficie,
                            'exploitant'=>$exploitant,
                            'volume'=>round($volume, 3),
                            'nb_billes'=>$nb_billes,
                            'pourcentage'=>$pourcentage,
                            'quota'=>$quota
                        );
                    }

                }

                rsort($response);
                return  new JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('/snvlt/req/exploitation/quota/exploitant/{id_exploitant}/{id_exercice}', name: 'rechercher_quotas_exploitants')]
    public function rechercher_quotas_exploitants(
        Request $request,
        ManagerRegistry $registry,
        int $id_exercice,
        int $id_exploitant
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF') )
            {

                $response = array();

                $exercice = $registry->getRepository(Exercice::class)->find($id_exercice);
                $exloitant = $registry->getRepository(Exploitant::class)->find($id_exploitant);
                if ($exercice && $exloitant){

                    $attributions = $registry->getRepository(Attribution::class)->findBy(['exercice'=>$exercice, 'code_exploitant'=>$exloitant]);

                    foreach ($attributions as $attribution){
                        $volume = 0;
                        $nb_billes = 0;
                        $quota = 0;
                        $pourcentage = 0;
                        $exploitant = "-";
                        $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                        foreach ($reprises as $reprise){
                            $docbrhs = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                            foreach ($docbrhs as $docbrh){
                                $pages = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$docbrh]);
                                foreach ($pages as $page){
                                    $lignepages = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page]);
                                    foreach ($lignepages as $lignepage){
                                        $volume = $volume + $lignepage->getCubageLignepagebrh();
                                        $nb_billes = $nb_billes + 1;
                                    }
                                }
                            }
                        }
                        if ($attribution->getCodeExploitant()){
                            if ($attribution->getCodeExploitant()->getSigle()){
                                $exploitant = $attribution->getCodeExploitant()->getSigle();
                            } else {
                                $exploitant = $attribution->getCodeExploitant()->getRaisonSocialeExploitant();
                            }
                        }
                        $numero_foret = "-";
                        $superficie = 0;
                        if ($attribution->getCodeForet()){
                            $foret = $attribution->getCodeForet();
                            $numero_foret = $foret->getDenomination();
                            $superficie = $foret->getSuperficie();
                            if ($foret->getSuperficie()){
                                $quota =  $foret->getSuperficie() / 4;
                            }
                        }

                        if ($quota > 0){
                            $pourcentage =  round(($volume / $quota) * 100, 0);
                        }
                        $response[] = array(
                            'numero_foret'=>$numero_foret,
                            'superficie'=>$superficie,
                            'exploitant'=>$exploitant,
                            'volume'=>$volume,
                            'nb_billes'=>$nb_billes,
                            'pourcentage'=>$pourcentage,
                            'quota'=>$quota
                        );
                    }

                }

                rsort($response);
                return  new JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

}
