<?php

namespace App\Controller\DocStats\Entetes;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\Utils;
use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentbtgu;
use App\Entity\DocStats\Entetes\Documentcp;
use App\Entity\DocStats\Entetes\Documentlje;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Pages\Pagebtgu;
use App\Entity\DocStats\Pages\Pagecp;
use App\Entity\DocStats\Pages\Pagelje;
use App\Entity\DocStats\Saisie\Lignepagebtgu;
use App\Entity\DocStats\Saisie\Lignepagecp;
use App\Entity\DocStats\Saisie\Lignepagelje;
use App\Entity\References\Cantonnement;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Entity\References\SousPrefecture;
use App\Entity\References\Usine;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DocStats\Entetes\DocumentbtguRepository;
use App\Repository\DocStats\Pages\PagebcbpRepository;
use App\Repository\DocStats\Pages\PagebtguRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentbtguController extends AbstractController
{
    public function __construct(private ManagerRegistry $m, private Utils $utils, private AdministrationService $administrationService)
    {
    }

    #[Route('/doc/stats/entetes/docbtgu', name: 'app_op_docbtgu')]
    public function index(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbtguRepository $docs_btgu,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')  )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('doc_stats/entetes/documentbtgu/index.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    #[Route('/snvlt/docbtgu/btgu/pages/{id_btgu}', name: 'affichage_btgu_json')]
    public function affiche_btgu(
        Request $request,
        int $id_btgu,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbtguRepository $docs_btgu,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $numerodoc = "";

                $documentbtgu = $registry->getRepository(Documentbtgu::class)->find($id_btgu);
                if($documentbtgu){$numerodoc = $documentbtgu->getNumeroDocbtgu();}

                return $this->render('doc_stats/entetes/documentbtgu/affiche_btgu.html.twig', [
                    'document_name'=>$documentbtgu,
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'villes'=>$registry->getRepository(Cantonnement::class)->findBy([],['nom_cantonnement'=>'ASC']),
                    'usines'=>$registry->getRepository(Usine::class)->findAll()
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/docbtgu/op', name: 'app_docs_btgu_json')]
    public function my_doc_btgu(
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')  )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $exercice = $this->administrationService->getAnnee();
                $mes_docs_btgu = array();
                //------------------------- Filtre les btgu par type Opérateur ------------------------------------- //

                //------------------------- Filtre les btgu ADMIN ------------------------------------- //
                if($user->getCodeGroupe()->getId() == 1){
                    $documents_btgu = $registry->getRepository(Documentbtgu::class)->findBy(['exercice'=>$exercice]);
                    foreach ($documents_btgu as $document_btgu){
                        $exploitant = "-";
                        if ($document_btgu->getCodeUsine()) {

                            if ($document_btgu->getCodeUsine()->getCodeExploitant()) {
                                $exploitant = $document_btgu->getCodeUsine()->getCodeExploitant()->getNumeroExploitant() . '-'. $document_btgu->getCodeUsine()->getCodeExploitant()->getMarteauExploitant(). '-'. $document_btgu->getCodeUsine()->getCodeExploitant()->getRaisonSocialeExploitant();
                            }

                            if ($document_btgu->getCodeUsine()->getCodeCantonnement()){
                                $canton = $document_btgu->getCodeUsine()->getCodeCantonnement()->getNomCantonnement();
                                $d = $document_btgu->getCodeUsine()->getCodeCantonnement()->getCodeDr()->getDenomination();
                            }else {
                                $canton = "-";
                                $d = "-";
                            }

                            $mes_docs_btgu[] = array(
                                'id_doc_btgu'=>$document_btgu->getId(),
                                'numero_docbtgu'=>$document_btgu->getNumeroDocbtgu(),
                                'usine'=>$document_btgu->getCodeUsine()->getRaisonSocialeUsine(),
                                'code_usine'=>$document_btgu->getCodeUsine()->getNumeroUsine(),
                                'cantonnement'=>$canton,
                                'dr'=>$d,
                                'date_delivrance'=>$document_btgu->getDelivreDocbtgu()->format("d m Y"),
                                'etat'=>$document_btgu->isEtat(),
                                'exploitant'=>$exploitant,
                                'volume_btgu'=>$this->getVolumebtgu($document_btgu)
                            );
                        }

                    }
                    //------------------------- Filtre les btgu DR ------------------------------------- //
                } elseif ($user->getCodeDr()){
                    //dd($user->getCodeDr());
                    $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_dr'=>$user->getCodeDr()]);
                    foreach ($cantonnements as $cantonnement){

                        $usines = $registry->getRepository(Usine::class)->findBy(['code_cantonnement'=>$cantonnement]);
                        foreach ($usines as $usine){
                            $documents_btgu = $registry->getRepository(Documentbtgu::class)->findBy(['code_usine'=>$usine, 'exercice'=>$exercice]);
                            foreach ($documents_btgu as $document_btgu){
                                $exploitant = "-";
                                if ($document_btgu->getCodeUsine()) {

                                    if ($document_btgu->getCodeUsine()->getCodeExploitant()) {
                                        $exploitant = $document_btgu->getCodeUsine()->getCodeExploitant()->getNumeroExploitant() . '-'. $document_btgu->getCodeUsine()->getCodeExploitant()->getMarteauExploitant(). '-'. $document_btgu->getCodeUsine()->getCodeExploitant()->getRaisonSocialeExploitant();
                                    }

                                    if ($document_btgu->getCodeUsine()->getCodeCantonnement()){
                                        $canton = $document_btgu->getCodeUsine()->getCodeCantonnement()->getNomCantonnement();
                                        $d = $document_btgu->getCodeUsine()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                    }else {
                                        $canton = "-";
                                        $d = "-";
                                    }

                                    $mes_docs_btgu[] = array(
                                        'id_doc_btgu'=>$document_btgu->getId(),
                                        'numero_docbtgu'=>$document_btgu->getNumeroDocbtgu(),
                                        'usine'=>$document_btgu->getCodeUsine()->getRaisonSocialeUsine(),
                                        'code_usine'=>$document_btgu->getCodeUsine()->getNumeroUsine(),
                                        'cantonnement'=>$canton,
                                        'dr'=>$d,
                                        'date_delivrance'=>$document_btgu->getDelivreDocbtgu()->format("d m Y"),
                                        'etat'=>$document_btgu->isEtat(),
                                        'exploitant'=>$exploitant,
                                        'volume_btgu'=>$this->getVolumebtgu($document_btgu)
                                    );
                                }

                            }

                        }
                    }
                    //------------------------- Filtre les btgu DD ------------------------------------- //
                } elseif ($user->getCodeDdef()){
                    //dd($user->getCodeDr());
                    $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_ddef'=>$user->getCodeDdef()]);
                    foreach ($cantonnements as $cantonnement){

                        $usines = $registry->getRepository(Usine::class)->findBy(['code_cantonnement'=>$cantonnement]);
                        foreach ($usines as $usine){
                            $documents_btgu = $registry->getRepository(Documentbtgu::class)->findBy(['code_usine'=>$usine, 'exercice'=>$exercice]);
                            foreach ($documents_btgu as $document_btgu){
                                $exploitant = "-";
                                if ($document_btgu->getCodeUsine()) {

                                    if ($document_btgu->getCodeUsine()->getCodeExploitant()) {
                                        $exploitant = $document_btgu->getCodeUsine()->getCodeExploitant()->getNumeroExploitant() . '-'. $document_btgu->getCodeUsine()->getCodeExploitant()->getMarteauExploitant(). '-'. $document_btgu->getCodeUsine()->getCodeExploitant()->getRaisonSocialeExploitant();
                                    }

                                    if ($document_btgu->getCodeUsine()->getCodeCantonnement()){
                                        $canton = $document_btgu->getCodeUsine()->getCodeCantonnement()->getNomCantonnement();
                                        $d = $document_btgu->getCodeUsine()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                    }else {
                                        $canton = "-";
                                        $d = "-";
                                    }

                                    $mes_docs_btgu[] = array(
                                        'id_doc_btgu'=>$document_btgu->getId(),
                                        'numero_docbtgu'=>$document_btgu->getNumeroDocbtgu(),
                                        'usine'=>$document_btgu->getCodeUsine()->getRaisonSocialeUsine(),
                                        'code_usine'=>$document_btgu->getCodeUsine()->getNumeroUsine(),
                                        'cantonnement'=>$canton,
                                        'dr'=>$d,
                                        'date_delivrance'=>$document_btgu->getDelivreDocbtgu()->format("d m Y"),
                                        'etat'=>$document_btgu->isEtat(),
                                        'exploitant'=>$exploitant,
                                        'volume_btgu'=>$this->getVolumebtgu($document_btgu)
                                    );
                                }

                            }

                        }
                    }

                    //------------------------- Filtre les btgu CANTONNEMENT ------------------------------------- //
                } elseif ($user->getCodeCantonnement()){

                    $usines = $registry->getRepository(Usine::class)->findBy(['code_cantonnement'=>$user->getCodeCantonnement()]);
                    foreach ($usines as $usine){
                        $documents_btgu = $registry->getRepository(Documentbtgu::class)->findBy(['code_usine'=>$usine, 'exercice'=>$exercice]);
                        foreach ($documents_btgu as $document_btgu){
                            $exploitant = "-";
                            if ($document_btgu->getCodeUsine()) {

                                if ($document_btgu->getCodeUsine()->getCodeExploitant()) {
                                    $exploitant = $document_btgu->getCodeUsine()->getCodeExploitant()->getNumeroExploitant() . '-'. $document_btgu->getCodeUsine()->getCodeExploitant()->getMarteauExploitant(). '-'. $document_btgu->getCodeUsine()->getCodeExploitant()->getRaisonSocialeExploitant();
                                }

                                if ($document_btgu->getCodeUsine()->getCodeCantonnement()){
                                    $canton = $document_btgu->getCodeUsine()->getCodeCantonnement()->getNomCantonnement();
                                    $d = $document_btgu->getCodeUsine()->getCodeCantonnement()->getCodeDr()->getDenomination();
                                }else {
                                    $canton = "-";
                                    $d = "-";
                                }

                                $mes_docs_btgu[] = array(
                                    'id_doc_btgu'=>$document_btgu->getId(),
                                    'numero_docbtgu'=>$document_btgu->getNumeroDocbtgu(),
                                    'usine'=>$document_btgu->getCodeUsine()->getRaisonSocialeUsine(),
                                    'code_usine'=>$document_btgu->getCodeUsine()->getNumeroUsine(),
                                    'cantonnement'=>$canton,
                                    'dr'=>$d,
                                    'date_delivrance'=>$document_btgu->getDelivreDocbtgu()->format("d m Y"),
                                    'etat'=>$document_btgu->isEtat(),
                                    'exploitant'=>$exploitant,
                                    'volume_btgu'=>$this->getVolumebtgu($document_btgu)
                                );
                            }

                        }

                    }


                    //------------------------- Filtre les btgu INDUSTRIELS------------------------------------- //
                }  elseif ($user->getCodeindustriel()){
                    $documents_btgu = $registry->getRepository(Documentbtgu::class)->findBy(['code_usine'=>$user->getCodeindustriel(), 'signature_cef'=>true, 'signature_dr'=>true, 'exercice'=>$exercice],['created_at'=>'DESC']);
                    foreach ($documents_btgu as $document_btgu){
                        $exploitant = "-";
                        if ($document_btgu->getCodeUsine()) {

                            if ($document_btgu->getCodeUsine()->getCodeExploitant()) {
                                $exploitant = $document_btgu->getCodeUsine()->getCodeExploitant()->getNumeroExploitant() . '-'. $document_btgu->getCodeUsine()->getCodeExploitant()->getMarteauExploitant(). '-'. $document_btgu->getCodeUsine()->getCodeExploitant()->getRaisonSocialeExploitant();
                            }

                            if ($document_btgu->getCodeUsine()->getCodeCantonnement()){
                                $canton = $document_btgu->getCodeUsine()->getCodeCantonnement()->getNomCantonnement();
                                $d = $document_btgu->getCodeUsine()->getCodeCantonnement()->getCodeDr()->getDenomination();
                            }else {
                                $canton = "-";
                                $d = "-";
                            }

                            $mes_docs_btgu[] = array(
                                'id_doc_btgu'=>$document_btgu->getId(),
                                'numero_docbtgu'=>$document_btgu->getNumeroDocbtgu(),
                                'usine'=>$document_btgu->getCodeUsine()->getRaisonSocialeUsine(),
                                'code_usine'=>$document_btgu->getCodeUsine()->getNumeroUsine(),
                                'cantonnement'=>$canton,
                                'dr'=>$d,
                                'date_delivrance'=>$document_btgu->getDelivreDocbtgu()->format("d m Y"),
                                'etat'=>$document_btgu->isEtat(),
                                'exploitant'=>$exploitant,
                                'volume_btgu'=>$this->getVolumebtgu($document_btgu)
                            );
                        }

                    }
                }

                return new JsonResponse(json_encode($mes_docs_btgu));
            }else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }

    }

    #[Route('/snvlt/docbtgu/op/pages_btgu/{id_btgu}', name: 'affichage_pages_btgu_json')]
    public function affiche_pages_btgu(
        Request $request,
        int $id_btgu,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbtguRepository $docs_btgu,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_MINEF'))
            {
                $doc_btgu = $docs_btgu->find($id_btgu);
                if($doc_btgu){
                    $pages_btgu = $registry->getRepository(Pagebtgu::class)->findBy(['code_docbtgu'=>$doc_btgu], ['id'=>'ASC']);
                    $my_btgu_pages = array();

                    foreach ($pages_btgu as $page){
                        $my_btgu_pages[] = array(
                            'id_page'=>$page->getId(),
                            'numero_page'=>$page->getNumeroPagebtgu()
                        );
                    }
                    return  new JsonResponse(json_encode($my_btgu_pages));
                }else{
                    return  new JsonResponse("NO DATA FOUND");
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/docbtgu/op/pages_btgu/data/{id_page}', name: 'affichage_page_data_btgu_json')]
    public function affiche_page_courante(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagebtguRepository $pages_btgu,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')  )
            {
                $page_btgu = $pages_btgu->find($id_page);
                if($page_btgu){
                    $my_btgu_page = array();

                    $destination = "";
                    $destination_id = 0;
                    $usine = "";
                    $usine_id = 0;
                    $date_chr = "";
                    $date_dep = "";
                    $date_arr = "";

                    if ($page_btgu->getUsineDestinataire()){
                        $usine = $page_btgu->getUsineDestinataire()->getSigle();
                        $usine_id = $page_btgu->getUsineDestinataire()->getId();
                    }


                    if ($page_btgu->getDatechargement()){
                        $date_chr = $page_btgu->getDatechargement()->format('Y-m-d');
                    }
                    if ($page_btgu->getDateDepart()){
                        $date_dep = $page_btgu->getDateDepart()->format('Y-m-d');
                    }
                    if ($page_btgu->getDateArrivee()){
                        $date_arr = $page_btgu->getDateArrivee()->format('Y-m-d');
                    }

                    if ($page_btgu->getDestination()){
                        if ($registry->getRepository(Cantonnement::class)->find($page_btgu->getDestination())){
                            $destination = $registry->getRepository(Cantonnement::class)->find($page_btgu->getDestination())->getNomCantonnement();
                            $destination_id = $registry->getRepository(Cantonnement::class)->find($page_btgu->getDestination())->getId();

                        } else {
                            $destination = $page_btgu->getDestination();
                            $destination_id = 0;
                        }
                    }

                    $my_btgu_page[] = array(
                        'id_page'=>$page_btgu->getId(),
                        'numero_page'=>$page_btgu->getNumeroPagebtgu(),
                        'annee'=>$page_btgu->getAnnee(),
                        'mois'=>$page_btgu->getMois(),
                        'volume_page'=>$this->getVolumebtguByPage($page_btgu),
                        'destination'=>$destination,
                        'destination_id'=>$destination_id,
                        'parc_usine'=>$usine_id,
                        'usine_dest'=>$usine,
                        'transporteur'=>$page_btgu->getTransporteur(),
                        'chauffeur'=>$page_btgu->getChauffeur(),
                        'immatriculation'=>$page_btgu->getImmatriculation(),
                        'date_chargement'=>$date_chr,
                        'date_depart'=>$date_dep,
                        'date_arrivee'=>$date_arr,
                        'fini'=>$page_btgu->isFini(),
                        'soumettre'=>$page_btgu->isSoumettre()
                    );

                    return  new JsonResponse(json_encode($my_btgu_page));
                } else {
                    return  new JsonResponse("NO DATA FOUND");
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/docbtgu/op/pages_btgu/data/edit/{id_page}/{data}', name: 'edit_page_btgu_json')]
    public function edit_page_btgu(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        string $data,
        int $id_page,
        NotificationRepository $notification,
        PagebtguRepository $pages_btgu,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                //$page_btgu = $pages_btgu->find($id_page);
                if($data){
                    $pagebtgu = $registry->getRepository(Pagebtgu::class)->find($id_page);

                    if ($pagebtgu){
                        //Decoder le JSON BRH
                        $arraydata = json_decode($data);

                        //dd($arraydata->numero_lignepagebtgu);
                        $date_jour = new \DateTime();

                        /*
                         *  obj.date_chargementbrh =  $("#date_chargement").val();
                            obj.date_depart =  $("#date_depart").val();
                            obj.date_arrivee =  $("#date_arrivee").val();
                            obj.destination =  $("#destination").val();
                            obj.parc_usine =  $("#parc_usine").val();
                            obj.transporteur =  $("#transporteur").val();
                            obj.chauffeur =  $("#chauffeur").val();
                            obj.mois =  $("#mois").val();
                            obj.annee =  $("#annee").val();
                            obj.immatriculation =  $("#immatriculation").val();
                            obj.code_docbtgu=  {{ document_name.id }};

                         */

                        $pagebtgu->setDestination(strtoupper($arraydata->destination));
                        $pagebtgu->setUsineDestinataire($registry->getRepository(Usine::class)->find((int)  $arraydata->parc_usine));


                        //$date_chargement = new \DateTime();
                        $date_chargement =  $arraydata->date_chargement;
                        $date_depart =  $arraydata->date_depart;
                        $date_arrivee =  $arraydata->date_arrivee;



                        $pagebtgu->setImmatriculation(strtoupper($arraydata->immatriculation));
                        $pagebtgu->setMois((int) $arraydata->mois);
                        $pagebtgu->setAnnee(strtoupper($arraydata->annee));
                        $pagebtgu->setChauffeur(strtoupper($arraydata->chauffeur));
                        $pagebtgu->setTransporteur(strtoupper($arraydata->transporteur));

                        $pagebtgu->setDatechargement(\DateTime::createFromFormat('Y-m-d', $date_chargement));
                        $pagebtgu->setDateDepart(\DateTime::createFromFormat('Y-m-d', $date_depart));
                        $pagebtgu->setDateArrivee(\DateTime::createFromFormat('Y-m-d', $date_arrivee));

                        $pagebtgu->setUpdatedAt(new \DateTime());
                        $pagebtgu->setUpdatedBy($user);

                        $pagebtgu->setCodeDocbtgu($registry->getRepository(Documentbtgu::class)->find((int) $arraydata->code_docbtgu));

                        $registry->getManager()->persist($pagebtgu);
                        $registry->getManager()->flush();

                        return  new JsonResponse([
                            'code'=>'PAGE_BRH_EDITED_SUCCESSFULLY',
                            'html'=>''
                        ]);
                    }

                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    
    

    #[Route('/snvlt/docbtgu/op/lignes_btgu/data/{id_page}', name: 'affichage_ligne_btgu_data_btgu_json')]
    public function affiche_lignes_btgu_courante(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagebtguRepository $pages_btgu,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')  )
            {
                $page_btgu = $pages_btgu->find($id_page);
                if($page_btgu){
                    $lignes_btgu = $registry->getRepository(Lignepagebtgu::class)->findBy(['code_pagebtgu'=>$page_btgu]);
                    $my_btgu_page = array();
                    foreach ($lignes_btgu as $lignebtgu){
                        $my_btgu_page[] = array(
                            'id_ligne'=>$lignebtgu->getId(),
                            'numero_ligne'=>$lignebtgu->getNumeroArbre(),
                            'lettre'=>$lignebtgu->getLettreBille(),
                            'essence'=>$lignebtgu->getEssence()->getNomVernaculaire(),
                            'x'=>$lignebtgu->getX(),
                            'y'=>$lignebtgu->getY(),
                            'zh'=>$lignebtgu->getZh()->getZone(),
                            'lng'=>$lignebtgu->getLng(),
                            'dm'=>$lignebtgu->getDm(),
                            'volume'=>round($lignebtgu->getVolume(),3),
                            'created_at'=>$lignebtgu->getCreatedAt()->format('d/m/Y h:i:s'),
                            'created_by'=>$lignebtgu->getCreatedBy(),
                            'exploitant'=>$registry->getRepository(Exploitant::class)->findOneBy(["numero_exploitant"=>(int) $lignebtgu->getCodeExploitant()])->getSigle()
                        );
                    }


                    return  new JsonResponse(json_encode($my_btgu_page));
                } else {
                    return  new JsonResponse("NO DATA FOUND");
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    function getVolumebtgu(Documentbtgu $documentbtgu):float
    {
        $volumebtgu = 0;
        if($documentbtgu){
            $pagebtgu =$this->m->getRepository(Pagebtgu::class)->findBy(['code_docbtgu'=>$documentbtgu]);
            foreach ($pagebtgu as $page){
                $lignepages = $this->m->getRepository(Lignepagebtgu::class)->findBy(['code_pagebtgu'=>$page]);
                foreach ($lignepages as $ligne){
                    $volumebtgu = $volumebtgu +  $ligne->getVolume();
                }
            }
            return $volumebtgu;
        } else {
            return $volumebtgu;
        }
    }

    function getVolumebtguByPage(Pagebtgu $pagebtgu):float
    {
        $volumebtgu = 0;
        $lignepages = $this->m->getRepository(Lignepagebtgu::class)->findBy(['code_pagebtgu'=>$pagebtgu]);

        foreach ($lignepages as $ligne){
            $volumebtgu = $volumebtgu +  $ligne->getVolume();
        }

        return $volumebtgu;

    }

    #[Route('/snvlt/numlje/dispo', name:'num_lje_dispo')]
    public function num_lje_dispo(
        ManagerRegistry $registry,
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {

            $user = $userRepository->find($this->getUser());
            $code_groupe = $user->getCodeGroupe()->getId();

            $liste_numeros = array();

            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')){
                $docljes = $registry->getRepository(Documentlje::class)->findBy(['code_usine'=>$user->getCodeindustriel()]);

                        foreach ($docljes as $doclje){
                            $pageljes = $registry->getRepository(Pagelje::class)->findBy(['code_doclje'=>$doclje]);

                            foreach ($pageljes as $pagelje){
                                $lignes = $registry->getRepository(Lignepagelje::class)->findBy(['code_pagelje'=>$pagelje, 'tronconnee'=>false]);

                                foreach ($lignes as $ligne){
                                    if (!$ligne->getCodePagebtgu()){
                                        $numero = $ligne->getNumeroArbre().$ligne->getLettre();
                                        if ($ligne->getCodeTypeDoc()->getId() == 2){
                                            $numero = $ligne->getNumeroArbre().$ligne->getLettre()." [".$registry->getRepository(Pagebrh::class)->find($ligne->getCodeFeuillet())->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination()."]";
                                        }
                                        $liste_numeros[] = array(
                                            'id_ligne'=>$ligne->getId(),
                                            'num'=>$numero
                                        );
                                    }

                                }
                            }
                        }
                        //dd($liste_numeros);
                    rsort($liste_numeros);
                    return new JsonResponse(json_encode($liste_numeros));

            }elseif ($this->isGranted('ROLE_ADMIN')){
                $lignes = $registry->getRepository(Lignepagelje::class)->findBy(['tronconnee'=>false]);

                foreach ($lignes as $ligne){
                    $numero = "";
                    if ($ligne->getCodeTypeDoc()->getId() == 2){
                        $numero = $ligne->getNumeroArbre().$ligne->getLettre()." [".$registry->getRepository(Pagebrh::class)->find($ligne->getCodeFeuillet())->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination()."]";
                    }
                    $liste_numeros[] = array(
                        'id_ligne'=>$ligne->getId(),
                        'num'=>$numero
                    );
                }

                rsort($liste_numeros);
                return new JsonResponse(json_encode($liste_numeros));


            }  else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/numbille/lje_infos/{id_bille}', name:'bille_lje_infos')]
    public function bille_lje_infos(
        ManagerRegistry $registry,
        Request $request,
        int $id_bille,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {

            $user = $userRepository->find($this->getUser());
            $code_groupe = $user->getCodeGroupe()->getId();

            $infos_bille = array();

            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')){
                $bille = $registry->getRepository(Lignepagelje::class)->find($id_bille);
                $infos_bille[] = array(
                    'essence'=>$bille->getEssence()->getNomVernaculaire(),
                    'zh'=>$bille->getZh()->getZone(),
                    'x'=>$bille->getX(),
                    'y'=>$bille->getY(),
                    'lng'=>$bille->getLng(),
                    'dm'=>$bille->getDm(),
                    'vol'=>$bille->getVolume(),
                    'type_doc'=>$bille->getCodeTypeDoc()->getAbv(),
                );

                rsort($infos_bille);
                return new JsonResponse(json_encode($infos_bille));


            }  else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/numlje/dispo/info/{id_bille}', name:'lettres_bille_lje')]
    public function lettres_bille_lje(
        ManagerRegistry $registry,
        Request $request,
        int $id_bille,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL')  or $this->isGranted('ROLE_ADMIN') ){

                $infos_bille = array();

                $bille = $registry->getRepository(Lignepagelje::class)->find($id_bille);

                if ($bille){
                    if ($bille->getLongeuraBillecp() && !$bille->isAUtlise()){
                        $infos_bille[] = array(
                            'lettre'=>'A'
                        );
                    }
                    if ($bille->getLongeurbBillecp() && !$bille->isBUtilise()){
                        $infos_bille[] = array(
                            'lettre'=>'B'
                        );
                    }
                    if ($bille->getLongeurcBillecp() && !$bille->isCUtilise()){
                        $infos_bille[] = array(
                            'lettre'=>'C'
                        );
                    }

                    return new JsonResponse(json_encode($infos_bille));

                }
                else {
                    return new JsonResponse(json_encode(false));
                }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
    //snvlt/docbtgu/op/pages_btgu/data/add_lignes/1165/3941
    #[Route('snvlt/docbtgu/op/pages_btgu/data/add_lignes/{id_bille}/{id_pagebtgu}', name:'transferer_bille_lje')]
    public function transferer_bille_lje(
        ManagerRegistry $registry,
        Request $request,
        int $id_bille,
        int $id_pagebtgu,
        UserRepository $userRepository
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL')  or $this->isGranted('ROLE_ADMIN') ){

                $user = $userRepository->find($this->getUser());

                $infos_bille = array();

                $bille = $registry->getRepository(Lignepagelje::class)->find($id_bille);

                if ($bille){
                    $pagebtgu = $registry->getRepository(Pagebtgu::class)->find($id_pagebtgu);
                    if($pagebtgu){
                        $ligne_btgu = new Lignepagebtgu();
                        $ligne_btgu->setEssence($bille->getEssence());
                        $ligne_btgu->setNumeroArbre($bille->getNumeroArbre());
                        $ligne_btgu->setLettreBille($bille->getLettre());
                        $ligne_btgu->setZh($bille->getZh());
                        $ligne_btgu->setX($bille->getX());
                        $ligne_btgu->setY($bille->getY());
                        $ligne_btgu->setLng($bille->getLng());
                        $ligne_btgu->setDm($bille->getDm());
                        $ligne_btgu->setVolume($bille->getVolume());
                        $ligne_btgu->setExercice($this->administrationService->getAnnee());
                        $ligne_btgu->setCreatedAt(new  \DateTimeImmutable());
                        $ligne_btgu->setCreatedBy($user);
                        $ligne_btgu->setUpdatedAt(new  \DateTimeImmutable());
                        $ligne_btgu->setUpdatedBy($user);

                        //Recupère le ID LJE
                        $ligne_btgu->setIdLje($bille->getId());

                        $ligne_btgu->setCodePagebtgu($pagebtgu);
                        $registry->getManager()->persist($ligne_btgu);

                        // Mise à jour de la ligne LJE code_pagebtgu
                        $bille->setCodePagebtgu($pagebtgu);

                        $registry->getManager()->flush();

                        // Log SNVLT
                        $this->administrationService->save_action(
                            $user,
                            "LIGNEPAGE_BTGU",
                            "AJOUT BILLE",
                            new \DateTimeImmutable(),
                            "Le chargement N% ". $ligne_btgu->getNumeroArbre().$ligne_btgu->getLettreBille() . " du connaissement BTGU N° " . $pagebtgu->getNumeroPagebtgu(). " a été enregistré par " . $user
                        );


                        return new JsonResponse(json_encode(true));
                    }
                } else {
                    return new JsonResponse(json_encode(false));
                }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/btgu/validate_form/{id_page}', name: 'validate_page_btgu_json')]
    public function validate_form_page_bTGU(
        Request $request,
        int $id_page,
        UserRepository $userRepository,
        PagebtguRepository $page_btgu,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $page = $page_btgu->find($id_page);
                if($page){
                    if ($page->getLignepagebtgus()->count() > 0)
                    {
                        $page->setFini(true);
                        $page->setEntreLje(false);
                        $page->setSoumettre(true);
                        $page->setConfirmationUsine(false);

                        if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE')){
                            $page->setConfirmationUsine(true);
                        }

                        $registry->getManager()->persist($page);
                        $registry->getManager()->flush();

                        // Log SNVLT
                        $this->administrationService->save_action(
                            $user,
                            "PAGE_BTGU",
                            "VALIDATION CHARGEMENT",
                            new \DateTimeImmutable(),
                            "Le chargement N% ". $page->getNumeroPagebtgu() . " du BTGU " . $page->getCodeDocbtgu()->getNumeroDocbtgu() . " vient d'être validé par l'agent " . $user . " de la structure [" . $user->getCodeindustriel()->getRaisonSocialeUsine() . " ]. Chargement en partance pour l'usine ". $page->getUsineDestinataire()
                        );

                        //Notification App Respo Usine
                        if ($page->getUsineDestinataire()->getEmailPersonneRessource()){
                            $respo_usine = $registry->getRepository(User::class)->findOneBy([
                                'email'=>$page->getUsineDestinataire()->getEmailPersonneRessource()
                            ]);
                            if ($respo_usine){
                                $this->utils->envoiNotification(
                                    $registry,
                                    "Transfert Grumes N° ".  $page->getNumeroPagebtgu() . " [ Usine : " . $user->getCodeindustriel()->getRaisonSocialeUsine() . "] en destination de votre usine " ,
                                    "Le Chargement BTGU". $page->getCodeDocbtgu()->getTypeDocument()->getAbv() . " N° " . $page->getCodeDocbtgu()->getNumeroDocbtgu() . " - Feuillet N° ". $page->getNumeroPagebtgu() . " est en transit vers votre usine  ",
                                    $respo_usine,
                                    $user->getId(),
                                    "app_my_btgu_loadings_notifs",
                                    "PAGE_BTGU",
                                    $page->getId()
                                );
                            }

                        }


                        // Notification Email Respo Usine

                        return  new JsonResponse(json_encode(true));
                    } else {
                        return  new JsonResponse(json_encode(false));
                    }

                }else {
                    return  new JsonResponse(json_encode(false));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('/snvlt/detail_btgu_loading/{id_notification?0}', name:'app_my_btgu_loadings_notifs')]
    public function details_chargements_notifs(
        ManagerRegistry $registry,
        Request $request,
        int $id_notification,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_ADMIN') or
                $this->isGranted('ROLE_ADMINISTRATIF' ) or
                $this->isGranted('ROLE_INDUSTRIEL' ) ){

                $notification = $notifications->find($id_notification);

                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                if ($notification) {

                    $pagebtgu = $registry->getRepository(Pagebtgu::class)->find($notification->getRelatedToId());
                    if ($pagebtgu) {

                        return $this->render('doc_stats/entetes/documentbtgu/details_chargement.html.twig',
                            [
                                'liste_menus'=>$menus->findOnlyParent(),
                                "all_menus"=>$menus->findAll(),
                                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                                'mes_notifs'=>$notifications->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                                'groupe'=>$code_groupe,
                                'chargement'=>$pagebtgu,
                                'liste_parent'=>$permissions
                            ]);

                    } else {
                        return new JsonResponse(json_encode(false));
                    }


                } else {
                    return new JsonResponse(json_encode("BAD NOTIFICATION"));
                }

            }else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/detail_btgu_loading/accept/{id_page?0}', name:'accept_chargement_btgu')]
    public function accepter_chargement_btgu(
        ManagerRegistry $registry,
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ( $this->isGranted('ROLE_INDUSTRIEL' ) ){



                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $pagebtgu = $registry->getRepository(Pagebtgu::class)->find($id_page);
                if ($pagebtgu) {

                    $pagebtgu->setConfirmationUsine(true);
                    $registry->getManager()->persist($pagebtgu);
                    $registry->getManager()->flush();


                    //Save Log

                    $this->administrationService->save_action(
                        $user,
                        "PAGE_BTGU",
                        "ACCEPTAION_CHARGEMENT_USINE",
                        new \DateTimeImmutable(),
                        "Chargement ". $pagebtgu->getCodeDocbtgu()->getTypeDocument()->getDenomination() . " N° " . $pagebtgu->getCodeDocbtgu()->getNumeroDocbtgu() . " - Feuillet N° ". $pagebtgu->getNumeroPagebtgu() . " en provenance de l'usine " . $pagebtgu->getCodeDocbtgu()->getCodeUsine()->getRaisonSocialeUsine() . " a été accepté par l'usine " . $user->getCodeindustriel()->getRaisonSocialeUsine() . " {User : " . $user . "}"
                    );

                    //envoi Notification à l'usine emettrice
                    if ($pagebtgu->getCodeDocbtgu()->getCodeUsine()->getEmailPersonneRessource()){
                        $this->utils->envoiNotification(
                            $registry,
                            "Infos sur le Chargement BTGU N° " . $pagebtgu->getNumeroPagebtgu() . " [" . $pagebtgu->getCodeDocbtgu()->getCodeUsine()->getRaisonSocialeUsine() . "]",
                            "Chargement ". $pagebtgu->getCodeDocbtgu()->getTypeDocument()->getDenomination() . " N° " . $pagebtgu->getCodeDocbtgu()->getNumeroDocbtgu() . " - Feuillet N° ". $pagebtgu->getNumeroPagebtgu() . " en provenance de l'usine " . $pagebtgu->getCodeDocbtgu()->getCodeUsine()->getRaisonSocialeUsine() . " a été accepté par l'usine " . $user->getCodeindustriel()->getRaisonSocialeUsine() . " {User : " . $user . "}",
                            $registry->getRepository(User::class)->findOneBy(['email'=>$pagebtgu->getCodeDocbtgu()->getCodeUsine()->getEmailPersonneRessource()]),
                            $user,
                            "app_my_btgu_loadings_notifs",
                            "PAGE_BTGU",
                            $pagebtgu->getId()
                        );
                    }


                    //envoi Notification à la DR de l'usine
                    $this->utils->envoiNotification(
                        $registry,
                        "Infos sur le Chargement BTGU N° " . $pagebtgu->getNumeroPagebtgu() . " [" . $pagebtgu->getCodeDocbtgu()->getCodeUsine()->getRaisonSocialeUsine() . "]",
                        "Chargement ". $pagebtgu->getCodeDocbtgu()->getTypeDocument()->getDenomination() . " N° " . $pagebtgu->getCodeDocbtgu()->getNumeroDocbtgu() . " - Feuillet N° ". $pagebtgu->getNumeroPagebtgu() . " en provenance de l'usine " . $pagebtgu->getCodeDocbtgu()->getCodeUsine()->getRaisonSocialeUsine() . " a été accepté par l'usine " . $user->getCodeindustriel()->getRaisonSocialeUsine() . " {User : " . $user . "}",
                        $registry->getRepository(User::class)->findOneBy(['email'=>$pagebtgu->getCodeDocbtgu()->getCodeUsine()->getCodeCantonnement()->getCodeDr()->getEmailPersonneRessource()]),
                        $user,
                        "app_my_btgu_loadings_notifs",
                        "PAGE_BTGU",
                        $pagebtgu->getId()
                    );

                    //envoi Notification à la DD de l'usine si elle existe
                    if ($user->getCodeindustriel()->getCodeCantonnement()->getCodeDdef()->getId() > 0){
                        if ($user->getCodeindustriel()->getCodeCantonnement()->getCodeDdef()->getEmailPersonneRessource()){
                            // dd($user->getCodeindustriel()->getCodeCantonnement()->getCodeDdef()->getId());

                            $this->utils->envoiNotification(
                                $registry,
                                "Infos sur le Chargement BTGU N° " . $pagebtgu->getNumeroPagebtgu() . " [" . $pagebtgu->getCodeDocbtgu()->getCodeUsine()->getRaisonSocialeUsine() . "]",
                                "Chargement ". $pagebtgu->getCodeDocbtgu()->getTypeDocument()->getDenomination() . " N° " . $pagebtgu->getCodeDocbtgu()->getNumeroDocbtgu() . " - Feuillet N° ". $pagebtgu->getNumeroPagebtgu() . " en provenance de l'usine " . $pagebtgu->getCodeDocbtgu()->getCodeUsine()->getRaisonSocialeUsine() . " a été accepté par l'usine " . $user->getCodeindustriel()->getRaisonSocialeUsine() . " {User : " . $user . "}",
                                $registry->getRepository(User::class)->findOneBy(['email'=>$pagebtgu->getCodeDocbtgu()->getCodeUsine()->getCodeCantonnement()->getCodeDdef()->getEmailPersonneRessource()]),
                                $user,
                                "app_my_btgu_loadings_notifs",
                                "PAGE_BTGU",
                                $pagebtgu->getId()
                            );
                        }
                    }

                    //envoi Notification au cantonnement de l'usine

                    $this->utils->envoiNotification(
                        $registry,
                        "Infos sur le Chargement BTGU N° " . $pagebtgu->getNumeroPagebtgu() . " [" . $pagebtgu->getCodeDocbtgu()->getCodeUsine()->getRaisonSocialeUsine() . "]",
                        "Chargement ". $pagebtgu->getCodeDocbtgu()->getTypeDocument()->getDenomination() . " N° " . $pagebtgu->getCodeDocbtgu()->getNumeroDocbtgu() . " - Feuillet N° ". $pagebtgu->getNumeroPagebtgu() . " en provenance de l'usine " . $pagebtgu->getCodeDocbtgu()->getCodeUsine()->getRaisonSocialeUsine() . " a été accepté par l'usine " . $user->getCodeindustriel()->getRaisonSocialeUsine() . " {User : " . $user . "}",
                        $registry->getRepository(User::class)->findOneBy(['email'=>$pagebtgu->getCodeDocbtgu()->getCodeUsine()->getCodeCantonnement()->getEmailPersonneRessource()]),
                        $user,
                        "app_my_btgu_loadings_notifs",
                        "PAGE_BTGU",
                        $pagebtgu->getId()
                    );
                    return new JsonResponse(json_encode("SUCCESS"));


                } else {
                    return new JsonResponse(json_encode("BAD NOTIFICATION"));
                }

            }else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/detail_btgu_loading/details/{id_page}', name:'app_my_btgu_loadings')]
    public function my_btgu_loadings(
        ManagerRegistry $registry,
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        UserRepository $userRepository,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_ADMIN') or
                $this->isGranted('ROLE_ADMINISTRATIF' ) or
                $this->isGranted('ROLE_INDUSTRIEL' ) ){


                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                //dd($notification->getRelatedToId());
                $pagebtgu = $registry->getRepository(Pagebtgu::class)->find($id_page);
                if ($pagebtgu) {


                    return $this->render('doc_stats/entetes/documentbtgu/details_chargement.html.twig',
                        [
                            'liste_menus'=>$menus->findOnlyParent(),
                            "all_menus"=>$menus->findAll(),
                            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                            'mes_notifs'=>$notifications->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                            'groupe'=>$code_groupe,
                            'chargement'=>$pagebtgu,
                            'liste_parent'=>$permissions
                        ]);

                } else {
                    return new JsonResponse(json_encode(false));
                }


            }else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


}
