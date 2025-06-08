<?php

namespace App\Controller\DocStats\Entetes;

use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentetatb;
use App\Entity\DocStats\Pages\Pageetatb;
use App\Entity\DocStats\Saisie\Lignepageetatb;
use App\Entity\References\Cantonnement;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DocStats\Entetes\DocumentetatbRepository;
use App\Repository\DocStats\Pages\PageetatbRepository;
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

class DocumentetatbController extends AbstractController
{

    public function __construct(private ManagerRegistry $m)
    {
    }

    #[Route('/doc/stats/entetes/docetatb', name: 'app_op_docetatb')]
    public function index(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentetatbRepository $docs_etatb,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('doc_stats/entetes/documentetatb/index.html.twig', [
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

    #[Route('/snvlt/docetatb/etatb/pages/{id_etatb}', name: 'affichage_etatb_json')]
    public function affiche_etatb(
        Request $request,
        int $id_etatb,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentetatbRepository $docs_etatb,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $numerodoc = "";

                $documentetatb = $registry->getRepository(Documentetatb::class)->find($id_etatb);
                if($documentetatb){$numerodoc = $documentetatb->getNumeroDocetatb();}

                return $this->render('doc_stats/entetes/documentetatb/affiche_etatb.html.twig', [
                    'document_name'=>$documentetatb,
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

    #[Route('/snvlt/docetatb/op', name: 'app_docs_etatb_json')]
    public function my_doc_etatb(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentetatbRepository $docs_etatb,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $mes_docs_etatb = array();
                //------------------------- Filtre les etatb par type Opérateur ------------------------------------- //

                //------------------------- Filtre les ETATs B ADMIN ------------------------------------- //
                if($user->getCodeGroupe()->getId() == 1){
                    $documents_etatb = $registry->getRepository(Documentetatb::class)->findAll();
                    foreach ($documents_etatb as $document_etatb){
                        $mes_docs_etatb[] = array(
                            'id_document_etatb'=>$document_etatb->getId(),
                            'numero_docetatb'=>$document_etatb->getNumeroDocetatb(),
                             'cantonnement'=>$document_etatb->getCodeExploitant()->getCodeCantonnement()->getNomCantonnement(),
                            'dr'=>$document_etatb->getCodeExploitant()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                            'date_delivrance'=>$document_etatb->getDelivreDocetatb()->format("d m Y"),
                            'etat'=>$document_etatb->isEtat(),
                            'exploitant'=>$document_etatb->getCodeExploitant()->getRaisonSocialeExploitant(),
                            'code_exploitant'=>$document_etatb->getCodeExploitant()->getNumeroExploitant(),
                            'volume_etatb'=>$this->getVolumeetatb($document_etatb)
                        );
                    }
                    //------------------------- Filtre les ETATs B DR ------------------------------------- //
                } else {
                    if ($user->getCodeDr()){
                        //dd($user->getCodeDr());
                        $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_dr'=>$user->getCodeDr()]);
                        foreach ($cantonnements as $cantonnement){
                            $exploitants = $registry->getRepository(Exploitant::class)->findBy(['code_cantonnement'=>$cantonnement]);

                            foreach ($exploitants as $exploitant){
                               $documents_etatb = $registry->getRepository(Documentetatb::class)->findBy(['code_exploitant'=>$exploitant]);
                                        foreach ($documents_etatb as $document_etatb){
                                            $mes_docs_etatb[] = array(
                                                'id_document_etatb'=>$document_etatb->getId(),
                                                'numero_docetatb'=>$document_etatb->getNumeroDocetatb(),
                                                'cantonnement'=>$cantonnement->getNomCantonnement(),
                                                'dr'=>$document_etatb->getCodeExploitant()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                                'date_delivrance'=>$document_etatb->getDelivreDocetatb()->format("d m Y"),
                                                'etat'=>$document_etatb->isEtat(),
                                                'exploitant'=>$document_etatb->getCodeExploitant()->getRaisonSocialeExploitant(),
                                                'code_exploitant'=>$document_etatb->getCodeExploitant()->getNumeroExploitant()
                                            );
                                        }

                                    }
                                }

                        //------------------------- Filtre les ETATs B DD ------------------------------------- //
                    } elseif ($user->getCodeDdef()){
                        $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_ddef'=>$user->getCodeDdef()]);
                        foreach ($cantonnements as $cantonnement){
                            $exploitants = $registry->getRepository(Exploitant::class)->findBy(['code_cantonnement'=>$cantonnement]);

                            foreach ($exploitants as $exploitant){
                                $documents_etatb = $registry->getRepository(Documentetatb::class)->findBy(['code_exploitant'=>$exploitant]);
                                foreach ($documents_etatb as $document_etatb){
                                    $mes_docs_etatb[] = array(
                                        'id_document_etatb'=>$document_etatb->getId(),
                                        'numero_docetatb'=>$document_etatb->getNumeroDocetatb(),
                                        'cantonnement'=>$cantonnement->getNomCantonnement(),
                                        'dr'=>$document_etatb->getCodeExploitant()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                        'date_delivrance'=>$document_etatb->getDelivreDocetatb()->format("d m Y"),
                                        'etat'=>$document_etatb->isEtat(),
                                        'exploitant'=>$document_etatb->getCodeExploitant()->getRaisonSocialeExploitant(),
                                        'code_exploitant'=>$document_etatb->getCodeExploitant()->getNumeroExploitant()
                                    );
                                }

                            }
                        }

                        //------------------------- Filtre les ETATs B CANTONNEMENT ------------------------------------- //
                    } elseif ($user->getCodeCantonnement()){$exploitants = $registry->getRepository(Exploitant::class)->findBy(['code_cantonnement'=>$user->getCodeCantonnement()]);

                        foreach ($exploitants as $exploitant){
                            $documents_etatb = $registry->getRepository(Documentetatb::class)->findBy(['code_exploitant'=>$exploitant]);
                            foreach ($documents_etatb as $document_etatb){
                                $mes_docs_etatb[] = array(
                                    'id_document_etatb'=>$document_etatb->getId(),
                                    'numero_docetatb'=>$document_etatb->getNumeroDocetatb(),
                                    'cantonnement'=>$cantonnement->getNomCantonnement(),
                                    'dr'=>$document_etatb->getCodeExploitant()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                    'date_delivrance'=>$document_etatb->getDelivreDocetatb()->format("d m Y"),
                                    'etat'=>$document_etatb->isEtat(),
                                    'exploitant'=>$document_etatb->getCodeExploitant()->getRaisonSocialeExploitant(),
                                    'code_exploitant'=>$document_etatb->getCodeExploitant()->getNumeroExploitant()
                                );
                            }

                        }

                        //------------------------- Filtre les ETATs B POSTE CONTROLE ------------------------------------- //
                    } elseif ($user->getCodePosteControle()){
                        $exploitants = $registry->getRepository(Exploitant::class)->findBy(['code_cantonnement'=>$user->getCodePosteControle()->getCodeCantonnement()]);

                        foreach ($exploitants as $exploitant){
                            $documents_etatb = $registry->getRepository(Documentetatb::class)->findBy(['code_exploitant'=>$exploitant]);
                            foreach ($documents_etatb as $document_etatb){
                                $mes_docs_etatb[] = array(
                                    'id_document_etatb'=>$document_etatb->getId(),
                                    'numero_docetatb'=>$document_etatb->getNumeroDocetatb(),
                                    'cantonnement'=>$cantonnement->getNomCantonnement(),
                                    'dr'=>$document_etatb->getCodeExploitant()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                    'date_delivrance'=>$document_etatb->getDelivreDocetatb()->format("d m Y"),
                                    'etat'=>$document_etatb->isEtat(),
                                    'exploitant'=>$document_etatb->getCodeExploitant()->getRaisonSocialeExploitant(),
                                    'code_exploitant'=>$document_etatb->getCodeExploitant()->getNumeroExploitant()
                                );
                            }

                        }
                        //------------------------- Filtre les ETATs B EXPLOITANT------------------------------------- //
                    } elseif ($user->getCodeexploitant()){
                        $documents_etatb = $registry->getRepository(Documentetatb::class)->findBy(['code_exploitant'=>$user->getCodeexploitant(), 'signature_cef'=>true, 'signature_dr'=>true],['created_at'=>'DESC']);
                        foreach ($documents_etatb as $document_etatb){
                            $mes_docs_etatb[] = array(
                                'id_document_etatb'=>$document_etatb->getId(),
                                'numero_docetatb'=>$document_etatb->getNumeroDocetatb(),
                                'cantonnement'=>$user->getCodeexploitant()->getCodeCantonnement()->getNomCantonnement(),
                                'dr'=>$document_etatb->getCodeExploitant()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                'date_delivrance'=>$document_etatb->getDelivreDocetatb()->format("d m Y"),
                                'etat'=>$document_etatb->isEtat(),
                                'code_exploitant'=>$document_etatb->getCodeExploitant()->getNumeroExploitant()
                            );
                        }
                    }


                }
                return new JsonResponse(json_encode($mes_docs_etatb));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }



    }

    #[Route('/snvlt/docetatb/op/pages_etatb/{id_etatb}', name: 'affichage_pages_etatb_json')]
    public function affiche_pages_etatb(
        Request $request,
        int $id_etatb,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentetatbRepository $docs_etatb,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $doc_etatb = $docs_etatb->find($id_etatb);
                if($doc_etatb){
                    $pages_etatb = $registry->getRepository(Pageetatb::class)->findBy(['code_docetatb'=>$doc_etatb], ['id'=>'ASC']);
                    $my_etatb_pages = array();

                    foreach ($pages_etatb as $page){
                        $my_etatb_pages[] = array(
                            'id_page'=>$page->getId(),
                            'numero_page'=>$page->getNumeroPageetatb()
                        );
                    }
                    return  new JsonResponse(json_encode($my_etatb_pages));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/docetatb/op/pages_etatb/data/{id_page}', name: 'affichage_page_data_etatb_json')]
    public function affiche_page_courante(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PageetatbRepository $pages_etatb,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $page_etatb = $pages_etatb->find($id_page);
                if($page_etatb){
                    $my_etatb_page = array();
                    $my_etatb_page[] = array(
                        'id_page'=>$page_etatb->getId(),
                        'numero_page'=>$page_etatb->getNumeroPageetatb(),
                        'date_chargement'=>$page_etatb->getDateChargementetatb()->format("d m Y"),
                        'destination'=>$page_etatb->getDestinationPageetatb(),
                        'parc_usine'=>$page_etatb->getParcUsineBrh()->getId(),
                        'transporteur'=>$page_etatb->getChauffeuretatb(),
                        'cout'=>$page_etatb->getCoutTransportetatb(),
                        'village'=>$page_etatb->getVillagePageetatb(),
                    );

                    return  new JsonResponse(json_encode($my_etatb_page));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/docetatb/op/lignes_etatb/data/{id_page}', name: 'affichage_ligne_etatb_data_etatb_json')]
    public function affiche_lignes_etatb_courante(
        Request $request,
        int $id_page,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PageetatbRepository $pages_etatb,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $page_etatb = $pages_etatb->find($id_page);
                if($page_etatb){
                    $lignes_etatb = $registry->getRepository(Lignepageetatb::class)->findBy(['code_pageetatb'=>$page_etatb]);
                    $my_etatb_page = array();
                    foreach ($lignes_etatb as $ligneetatb){
                        $my_etatb_page[] = array(
                            'id_ligne'=>$ligneetatb->getId(),
                            'numero_ligne'=>$ligneetatb->getNumeroLignepageetatb(),
                            'essence'=>$ligneetatb->getNomEssenceetatb()->getNomVernaculaire(),
                            'x_etatb'=>$ligneetatb->getXLignepageetatb(),
                            'y_etatb'=>$ligneetatb->getYLignepageetatb(),
                            'zh_etatb'=>$ligneetatb->getZhLignepageetatb()->getZone(),
                            'lng_etatb'=>$ligneetatb->getLongeurLignepageetatb(),
                            'dm_etatb'=>$ligneetatb->getDiametreLignepageetatb(),
                            'cubage_etatb'=>$ligneetatb->getCubageLignepageetatb()
                        );
                    }


                    return  new JsonResponse(json_encode($my_etatb_page));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    function getVolumeetatb(Documentetatb $documentetatb):float
    {
        $volumeetatb = 0;
        if($documentetatb){
            $pageetatb =$this->m->getRepository(Pageetatb::class)->findBy(['code_docetatb'=>$documentetatb]);
            foreach ($pageetatb as $page){
                $lignepages = $this->m->getRepository(Lignepageetatb::class)->findBy(['code_pageetatb'=>$page]);
                foreach ($lignepages as $ligne){
                    $volumeetatb = $volumeetatb +  $ligne->getCubageLignepageetatb();
                }
            }
            return $volumeetatb;
        } else {
            return $volumeetatb;
        }
    }
}
