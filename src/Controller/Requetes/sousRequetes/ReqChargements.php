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

class ReqChargements extends AbstractController
{
    #[Route('/snvlt/req/exploitation/chr', name: 'app_req_chargements')]
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

            return $this->render('requetes/sous_requetes/chargements/index.html.twig', [
                'liste_menus'=>$menus->findOnlyParent(),
                "all_menus"=>$menus->findAll(),
                'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                'groupe'=>$code_groupe,
                'liste_parent'=>$permissions,
                'forets'=>$registry->getRepository(Foret::class)->findBy(['code_type_foret'=>$registry->getRepository(TypeForet::class)->find(1)],['denomination'=>'ASC']),
                'exploitants'=>$registry->getRepository(Exploitant::class)->findBy([],['raison_sociale_exploitant'=>'ASC']),
                'exercices'=>$registry->getRepository(Exercice::class)->findBy([],['id'=>'DESC'])
            ]);

        } else {
            return $this->redirectToRoute('app_no_permission_user_active');
        }
       }
    }

    #[Route('/snvlt/req/exploitation/chr/exploitant/{id_exploitant}/{id_exercice}', name: 'rechercher_chr_exploitant')]
    public function rechercher_chr_exploitant(
        Request $request,
        int $id_exploitant,
        int $id_exercice,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {

                $exploitant = $registry->getRepository(Exploitant::class)->find($id_exploitant);
                $exercice = $registry->getRepository(Exercice::class)->find($id_exercice);

                $response = array();
                $nom_exploitant = "-";

                if ($exploitant && $exercice){



                    if($exploitant->getSigle()) { $nom_exploitant = $exploitant->getSigle(); } else { $nom_exploitant = $exploitant->getRaisonSocialeExploitant();}
                    $attributions = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$exploitant, 'exercice'=>$exercice]);

                    foreach ($attributions as $attribution){
                        $foret = $attribution->getCodeForet()->getDenomination();

                        $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                        foreach ($reprises as $reprise){
                            $docbrhs = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                            foreach ($docbrhs as $docbrh){
                                $pages = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$docbrh], ['date_chargementbrh'=>'DESC']);
                                foreach ($pages as $page){
                                    $vol_chr = 0;
                                    $nb_billes= 0;
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
                                        $vol_chr = $vol_chr + $ligne->getCubageLignepagebrh();
                                        $nb_billes = $nb_billes + 1;
                                    }
                                    if ($page->getParcUsineBrh()){
                                        $response[] = array(
                                            'date_chr'=>$date_chr,
                                            'destination'=>$page->getDestinationPagebrh(),
                                            'foret'=>$foret,
                                            'volume'=>round($vol_chr, 3),
                                            'nb_billes'=>$nb_billes,
                                            'annee'=>$exercice->getAnnee(),
                                            'immatriculation'=>$immat,
                                            'chauffeur'=>$chauffeur,
                                            'parc_usine'=>$usine,
                                            'numero'=>$page->getNumeroPagebrh(),
                                            'exploitant'=>$nom_exploitant,
                                            'numero_doc'=>$page->getCodeDocbrh()->getNumeroDocbrh(),
                                            'index_page'=>$page->getindex_page()
                                        );
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
    #[Route('/snvlt/req/exploitation/chr/foret/{id_foret}/{id_exercice}', name: 'rechercher_chr_foret')]
    public function rechercher_chr_foret(
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
                $nom_foret = "-";

                if ($foret && $exercice){

                    $attributions = $registry->getRepository(Attribution::class)->findBy(['code_foret'=>$foret, 'exercice'=>$exercice]);

                    foreach ($attributions as $attribution){
                        //$foret = $attribution->getCodeForet()->getDenomination();
                        if($attribution->getCodeExploitant()->getSigle()) { $nom_exploitant = $attribution->getCodeExploitant()->getSigle(); } else { $nom_exploitant = $attribution->getCodeExploitant()->getRaisonSocialeExploitant();}
                        $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                        foreach ($reprises as $reprise){
                            $docbrhs = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                            foreach ($docbrhs as $docbrh){
                                $pages = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$docbrh], ['date_chargementbrh'=>'DESC']);
                                foreach ($pages as $page){
                                    $vol_chr = 0;
                                    $nb_billes= 0;
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
                                        $vol_chr = $vol_chr + $ligne->getCubageLignepagebrh();
                                        $nb_billes = $nb_billes + 1;
                                    }
                                    if ($page->getParcUsineBrh()){
                                        $response[] = array(
                                            'date_chr'=>$date_chr,
                                            'destination'=>$page->getDestinationPagebrh(),
                                            'foret'=>$foret->getNumeroForet(),
                                            'volume'=>round($vol_chr, 3),
                                            'nb_billes'=>$nb_billes,
                                            'annee'=>$exercice->getAnnee(),
                                            'immatriculation'=>$immat,
                                            'chauffeur'=>$chauffeur,
                                            'parc_usine'=>$usine,
                                            'numero'=>$page->getNumeroPagebrh(),
                                            'exploitant'=>$nom_exploitant,
                                            'numero_doc'=>$page->getCodeDocbrh()->getNumeroDocbrh(),
                                            'index_page'=>$page->getindex_page()
                                        );
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
    #[Route('/snvlt/req/exploitation/chr/all/{id_exercice}', name: 'rechercher_chr_all')]
    public function rechercher_chr_all(
        Request $request,
        int $id_exercice,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {


                $exercice = $registry->getRepository(Exercice::class)->find($id_exercice);

                $response = array();
                $nom_foret = "-";

                if ($exercice){




                    $attributions = $registry->getRepository(Attribution::class)->findBy(['exercice'=>$exercice]);

                    foreach ($attributions as $attribution){

                        if($attribution->getCodeExploitant()->getSigle()) { $nom_exploitant = $attribution->getCodeExploitant()->getSigle(); } else { $nom_exploitant = $attribution->getCodeExploitant()->getRaisonSocialeExploitant();}
                        $reprises = $registry->getRepository(Reprise::class)->findBy(['code_attribution'=>$attribution]);
                        foreach ($reprises as $reprise){
                            $docbrhs = $registry->getRepository(Documentbrh::class)->findBy(['code_reprise'=>$reprise]);
                            foreach ($docbrhs as $docbrh){
                                $pages = $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$docbrh], ['date_chargementbrh'=>'DESC']);
                                foreach ($pages as $page){
                                    $vol_chr = 0;
                                    $nb_billes= 0;
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
                                        $vol_chr = $vol_chr + $ligne->getCubageLignepagebrh();
                                        $nb_billes = $nb_billes + 1;
                                    }
                                    if ($page->getParcUsineBrh()){
                                        $response[] = array(
                                            'date_chr'=>$date_chr,
                                            'destination'=>$page->getDestinationPagebrh(),
                                            'foret'=>$attribution->getCodeForet()->getNumeroForet(),
                                            'volume'=>round($vol_chr, 3),
                                            'nb_billes'=>$nb_billes,
                                            'annee'=>$exercice->getAnnee(),
                                            'immatriculation'=>$immat,
                                            'chauffeur'=>$chauffeur,
                                            'parc_usine'=>$usine,
                                            'numero'=>$page->getNumeroPagebrh(),
                                            'exploitant'=>$nom_exploitant,
                                            'numero_doc'=>$page->getCodeDocbrh()->getNumeroDocbrh(),
                                            'index_page'=>$page->getindex_page()
                                        );
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
