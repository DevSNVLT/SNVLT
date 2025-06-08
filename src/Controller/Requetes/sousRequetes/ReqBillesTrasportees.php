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

class ReqBillesTrasportees extends AbstractController
{
    #[Route('/snvlt/req/exploitation/chr', name: 'app_req_billes')]
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

            return $this->render('requetes/sous_requetes/billes/index.html.twig', [
                'liste_menus'=>$menus->findOnlyParent(),
                "all_menus"=>$menus->findAll(),
                'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                'groupe'=>$code_groupe,
                'liste_parent'=>$permissions,
                'forets'=>$registry->getRepository(Foret::class)->findBy(['code_type_foret'=>$registry->getRepository(TypeForet::class)->find(1)],['denomination'=>'ASC']),
                'exercices'=>$registry->getRepository(Exercice::class)->findBy([],['id'=>'DESC'])
            ]);

        } else {
            return $this->redirectToRoute('app_no_permission_user_active');
        }
       }
    }

    #[Route('/snvlt/req/exploitation/billes/details/{date_debut}/{date_fin}', name: 'rechercher_details_billes')]
    public function rechercher_details_billes(
        Request $request,
        string $date_debut,
        string $date_fin,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {

                $response = array();
                $nom_exploitant = "-";

                if ($date_debut && $date_fin){
                            $new_date_debut = \DateTime::createFromFormat("Y-m-d", $date_debut);
                            $new_date_fin = \DateTime::createFromFormat("Y-m-d", $date_fin);
                                $pages = $registry->getRepository(Pagebrh::class)->findAll();
                                foreach ($pages as $page){
                                    $documentbrh = "-";
                                    $foret = "-";
                                    $exploitant = "-";
                                    if ($page->getCodeDocbrh()) {
                                        $documentbrh = $page->getCodeDocbrh()->getNumeroDocbrh();
                                        if ($page->getCodeDocbrh()->getCodeReprise()) {
                                            if ($page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()) {
                                                if ($page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()) {
                                                    $foret = $page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination();
                                                }
                                                if ($page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()) {
                                                    if ($page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle()){
                                                        $exploitant = $page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle();
                                                    } else {
                                                        $exploitant = $page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if ($page->getDateChargementbrh() >= $new_date_debut && $page->getDateChargementbrh() <= $new_date_fin) {
                                        if ($page->getParcUsineBrh()){
                                            if ($page->getParcUsineBrh()->getSigle()){
                                                $usine = $page->getParcUsineBrh()->getSigle();
                                            } else {
                                                $usine = $page->getParcUsineBrh()->getRaisonSocialeUsine();
                                            }
                                        } else {
                                            $usine = '-';
                                        }
                                        if ($page->getDateChargementbrh()){
                                            $date_chr  = $page->getDateChargementbrh()->format('d/m/Y');
                                        } else {
                                            $date_chr = '-';
                                        }
                                        if ($page->getImmatcamion() == "" or $page->getImmatcamion() == null){
                                            $immat = '-';
                                        } else {
                                            $immat  = $page->getImmatcamion();
                                        }
                                        if ($page->getChauffeurbrh() == "" or $page->getChauffeurbrh() == null){
                                            $chauffeur = '-';
                                        } else {
                                            $chauffeur  = $page->getChauffeurbrh();
                                        }
                                        $lignes = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page]);
                                        foreach ($lignes as $ligne){

                                            if ($ligne->getNomEssencebrh()) { $essence = $ligne->getNomEssencebrh()->getNomVernaculaire();} else {$essence = "-";}
                                            if ($ligne->getZhLignepagebrh()) { $zh = $ligne->getZhLignepagebrh()->getZone();} else {$zh = "-";}

                                            $response[] = array(
                                                'numero_bille'=>$ligne->getNumeroLignepagebrh(). $ligne->getLettreLignepagebrh(),
                                                'essence'=>$essence,
                                                'zh'=>$zh,
                                                'x'=>$ligne->getXLignepagebrh(),
                                                'y'=>$ligne->getYLignepagebrh(),
                                                'lng'=>$ligne->getLongeurLignepagebrh(),
                                                'dm'=>$ligne->getDiametreLignepagebrh(),
                                                'volume'=>round($ligne->getCubageLignepagebrh(),3),
                                                'numero_chr'=>$page->getNumeroPagebrh(),
                                                'date_chr'=>$date_chr,
                                                'destination'=>$page->getDestinationPagebrh(),
                                                'parc_usine'=>$usine,
                                                'annee'=>$ligne->getExercice()->getAnnee(),
                                                'immatriculation'=>$immat,
                                                'chauffeur'=>$chauffeur,
                                                'exploitant'=>$exploitant,
                                                'foret'=>$foret,
                                                'numero_doc'=>$documentbrh,
                                                'index_page'=>$page->getindex_page()
                                            );
                                        }
                                    }
                                }
                            }


                rsort($response);
                return  new JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('/snvlt/req/exploitation/billes/details/foret/{date_debut}/{date_fin}/{id_foret}', name: 'rechercher_details_billes_foret')]
    public function rechercher_details_billes_foret(
        Request $request,
        string $date_debut,
        string $date_fin,
        int $id_foret,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {

                $response = array();
                $nom_exploitant = "-";

                $foret = $registry->getRepository(Foret::class)->find($id_foret);



                if ($date_debut && $date_fin && $foret){
                    $new_date_debut = \DateTime::createFromFormat("Y-m-d", $date_debut);
                    $new_date_fin = \DateTime::createFromFormat("Y-m-d", $date_fin);

                    $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret]);

                    foreach ($attributions as $attribution){

                        if($attribution->getCodeExploitant()->getSigle()) { $nom_exploitant = $attribution->getCodeExploitant()->getSigle(); } else { $nom_exploitant = $attribution->getCodeExploitant()->getRaisonSocialeExploitant();}
                        $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                        foreach ($reprises as $reprise){
                            $docbrhs = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                            foreach ($docbrhs as $docbrh){
                                $pages = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$docbrh]);
                                foreach ($pages as $page){
                                    $documentbrh = "-";
                                    $foret = "-";
                                    $exploitant = "-";
                                    if ($page->getCodeDocbrh()) {
                                        $documentbrh = $page->getCodeDocbrh()->getNumeroDocbrh();
                                        if ($page->getCodeDocbrh()->getCodeReprise()) {
                                            if ($page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()) {
                                                if ($page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()) {
                                                    $foret = $page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination();
                                                }
                                                if ($page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()) {
                                                    if ($page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle()){
                                                        $exploitant = $page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle();
                                                    } else {
                                                        $exploitant = $page->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant();
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if ($page->getDateChargementbrh() >= $new_date_debut && $page->getDateChargementbrh() <= $new_date_fin) {
                                        if ($page->getParcUsineBrh()){
                                            if ($page->getParcUsineBrh()->getSigle()){
                                                $usine = $page->getParcUsineBrh()->getSigle();
                                            } else {
                                                $usine = $page->getParcUsineBrh()->getRaisonSocialeUsine();
                                            }
                                        } else {
                                            $usine = '-';
                                        }
                                        if ($page->getDateChargementbrh()){
                                            $date_chr  = $page->getDateChargementbrh()->format('d/m/Y');
                                        } else {
                                            $date_chr = '-';
                                        }
                                        if ($page->getImmatcamion() == "" or $page->getImmatcamion() == null){
                                            $immat = '-';
                                        } else {
                                            $immat  = $page->getImmatcamion();
                                        }
                                        if ($page->getChauffeurbrh() == "" or $page->getChauffeurbrh() == null){
                                            $chauffeur = '-';
                                        } else {
                                            $chauffeur  = $page->getChauffeurbrh();
                                        }
                                        $lignes = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$page]);
                                        foreach ($lignes as $ligne){

                                            if ($ligne->getNomEssencebrh()) { $essence = $ligne->getNomEssencebrh()->getNomVernaculaire();} else {$essence = "-";}
                                            if ($ligne->getZhLignepagebrh()) { $zh = $ligne->getZhLignepagebrh()->getZone();} else {$zh = "-";}

                                            $response[] = array(
                                                'numero_bille'=>$ligne->getNumeroLignepagebrh(). $ligne->getLettreLignepagebrh(),
                                                'essence'=>$essence,
                                                'zh'=>$zh,
                                                'x'=>$ligne->getXLignepagebrh(),
                                                'y'=>$ligne->getYLignepagebrh(),
                                                'lng'=>$ligne->getLongeurLignepagebrh(),
                                                'dm'=>$ligne->getDiametreLignepagebrh(),
                                                'volume'=>round($ligne->getCubageLignepagebrh(),3),
                                                'numero_chr'=>$page->getNumeroPagebrh(),
                                                'date_chr'=>$date_chr,
                                                'destination'=>$page->getDestinationPagebrh(),
                                                'parc_usine'=>$usine,
                                                'annee'=>$ligne->getExercice()->getAnnee(),
                                                'immatriculation'=>$immat,
                                                'chauffeur'=>$chauffeur,
                                                'exploitant'=>$exploitant,
                                                'foret'=>$foret,
                                                'numero_doc'=>$documentbrh,
                                                'index_page'=>$page->getindex_page()
                                            );
                                        }
                                    }
                                }

                            }
                        }
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
