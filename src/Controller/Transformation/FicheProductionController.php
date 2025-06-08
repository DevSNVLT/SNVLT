<?php

namespace App\Controller\Transformation;

use App\Entity\DocStats\Saisie\Lignepagelje;
use App\Entity\References\Essence;
use App\Entity\Transformation\Billon;
use App\Entity\Transformation\Elements;
use App\Entity\Transformation\FicheLot;
use App\Entity\Transformation\FicheLotProd;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DocStats\Pages\PagebrhRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\Transformation\BillonRepository;
use App\Repository\Transformation\FicheJourTransfoRepository;
use App\Repository\UserRepository;
use App\Services\PdfService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FicheProductionController extends AbstractController
{
    #[Route('/transformation/ficheProductionJournaliere', name: 'fiche_prod_jour')]
    public function index(ManagerRegistry $registry,
                          BillonRepository $billonRepository,
                          FicheJourTransfoRepository $fichesRepository,
                          Request $request,
                          MenuPermissionRepository $permissions,
                          MenuRepository $menus,
                          GroupeRepository $groupeRepository,
                          UserRepository $userRepository,
                          User $user = null,
                          NotificationRepository $notification): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or  $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATTIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                if ($this->isGranted('ROLE_INDUSTRIEL')){
                    $fiches_lots = $registry->getRepository(FicheLotProd::class)->findBy(['code_usine'=>$user->getCodeindustriel()], ['date_fiche'=>'DESC']);
                } else {
                    $fiches_lots = $registry->getRepository(FicheLotProd::class)->findBy([], ['date_fiche'=>'DESC']);
                }

                return $this->render('transformation/fiche_production/index.html.twig', [
                    'essences' => $registry->getRepository(Essence::class)->findBy([],['nom_vernaculaire'=>'ASC']),
                    'billes_non_decoupes'=>$registry->getRepository(Lignepagelje::class)->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'fiches'=>$fiches_lots
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    #[Route('/snvlt/prod/ficheProd/{id_fiche}', name: 'affiche_elts_prod')]
    public function affiche_elts_prod(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_fiche,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or  $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATTIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $infos_fiche = array();


                $fiche= $registry->getRepository(FicheLotProd::class)->find($id_fiche);

                if ($fiche){

                    $elements = $registry->getRepository(Elements::class)->findBy(['code_fiche_prod'=>$fiche]);

                    foreach ($elements as $element){
                        $type_transfo = $element->getCodeTypeTransfo();
                        $liste_billons = array();
                        //$billons = "";

                        if ($type_transfo->getId() == 1 or $type_transfo->getId() == 3){
                            $billons_prod = $element->getCodeBillon()->getNumeroBillon();
                        } else {
                            $billons = $registry->getRepository(Billon::class)->findBy([
                                'code_lot'=>$element->getCodeTypeTransfo()
                            ]);
                            foreach($billons as $bill){
                                $liste_billons[] = array(
                                    'numero_billon'=>$bill->getNumeroBillon()
                                );
                            }
                            $billons_prod = $liste_billons;
                            //dd($liste_billons);
                        }
                        $infos_fiche[] = array(
                            'id_elt'=>$element->getId(),
                            'lng'=>$element->getLng(),
                            'lrg'=>$element->getLrg(),
                            'ep'=>$element->getEp(),
                            'nb'=>$element->getNombre(),
                            'vol'=>$element->getVolume(),
                            'type_transfo'=>$type_transfo->getLibelle(),
                            'essence'=>$element->getCodeEssence()->getNomVernaculaire(),
                            'billons'=>$billons_prod
                        );
                    }



                }



                return  new JsonResponse(json_encode($infos_fiche));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/PrintFicheProd/{id_fiche}', name: 'print_elts_prod')]
    public function print_elts_prod(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_fiche,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or  $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATTIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $infos_fiche = array();
                $liste_billons = array();
                $volume = 0;
                $nb_elts = 0;
                $fiche= $registry->getRepository(FicheLotProd::class)->find($id_fiche);

                if ($fiche){

                    $elements = $registry->getRepository(Elements::class)->findBy(['code_fiche_prod'=>$fiche]);

                    foreach ($elements as $element){
                        $type_transfo = $element->getCodeTypeTransfo();
                        //$billons = "";

                        if ($type_transfo->getId() == 1 or $type_transfo->getId() == 3){
                            $billons_prod = $element->getCodeBillon()->getNumeroBillon();
                        } else {
                            $billons = $registry->getRepository(Billon::class)->findBy([
                                'code_lot'=>$element->getCodeTypeTransfo()
                            ]);
                            foreach($billons as $bill){
                                $liste_billons[] = array(
                                    'numero_billon'=>$bill->getNumeroBillon()
                                );
                            }
                            $billons_prod = $liste_billons;
                            //dd($liste_billons);
                        }
                        $volume = $volume + $element->getVolume();
                        $nb_elts = $nb_elts + $element->getNombre();
                        $infos_fiche[] = array(
                            'id_elt'=>$element->getId(),
                            'numero_fiche'=>$fiche->getNumero() ." du ". $fiche->getDateFiche()->format('d/m/Y'),
                            'lng'=>$element->getLng(),
                            'lrg'=>$element->getLrg(),
                            'ep'=>$element->getEp(),
                            'nb'=>$element->getNombre(),
                            'vol'=>$element->getVolume(),
                            'type_transfo'=>$type_transfo->getLibelle(),
                            'essence'=>$element->getCodeEssence()->getNomVernaculaire(),
                            'billons'=>$billons_prod
                        );
                    }



                }



                return $this->render('transformation/print/ficheprod.html.twig', [
                    'fiche' => $infos_fiche,
                    'rs'=>$fiche->getCodeUsine()->getRaisonSocialeUsine(),
                    'code'=>$fiche->getCodeUsine()->getNumeroUsine(),
                    'volume'=>round($volume, 3),
                    'nb_elts'=>$nb_elts
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/PrintPdf/FicheProd/{id_fiche}', name: 'print_fiche_prod')]
    public function print_fiche_prod(
        Request $request,
        UserRepository $userRepository,
        int $id_fiche,
        ManagerRegistry $registry,
        PdfService $pdfService
    ):Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or  $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATTIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $infos_fiche = array();
                $liste_billons = array();

                $fiche= $registry->getRepository(FicheLotProd::class)->find($id_fiche);

                if ($fiche){


                    $elements = $registry->getRepository(Elements::class)->findBy(['code_fiche_prod'=>$fiche]);

                    foreach ($elements as $element){
                        $type_transfo = $element->getCodeTypeTransfo();
                        //$billons = "";

                        if ($type_transfo->getId() == 1 or $type_transfo->getId() == 3){
                            $billons_prod = $element->getCodeBillon()->getNumeroBillon();
                        } else {
                            $billons = $registry->getRepository(Billon::class)->findBy([
                                'code_lot'=>$element->getCodeTypeTransfo()
                            ]);
                            foreach($billons as $bill){
                                $liste_billons[] = array(
                                    'numero_billon'=>$bill->getNumeroBillon()
                                );
                            }
                            $billons_prod = $liste_billons;
                            //dd($liste_billons);
                        }
                        $infos_fiche[] = array(
                            'id_elt'=>$element->getId(),
                            'numero_fiche'=>$fiche->getNumero() ." du ". $fiche->getDateFiche()->format('d/m/Y'),
                            'lng'=>$element->getLng(),
                            'lrg'=>$element->getLrg(),
                            'ep'=>$element->getEp(),
                            'nb'=>$element->getNombre(),
                            'vol'=>$element->getVolume(),
                            'type_transfo'=>$type_transfo->getLibelle(),
                            'essence'=>$element->getCodeEssence()->getNomVernaculaire(),
                            'billons'=>$billons_prod
                        );
                    }


                    $html = $this->render('transformation/print/ficheprod.html.twig', [
                        'fiche' => $infos_fiche
                    ]);
                    $titre = "Fiche Production N° ". $fiche->getNumero() ." du ". $fiche->getDateFiche()->format('d/m/Y');
                    $pdfService->showPdfFile($html, $titre);
                }

        }
    }
    }
}
