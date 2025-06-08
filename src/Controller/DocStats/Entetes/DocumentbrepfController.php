<?php

namespace App\Controller\DocStats\Entetes;

use App\Entity\Autorisation\AgreementExportateur;
use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\AutorisationExportateur;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentbrepf;
use App\Entity\DocStats\Pages\Pagebrepf;
use App\Entity\DocStats\Saisie\Lignepagebrepf;
use App\Entity\References\Cantonnement;
use App\Entity\References\Exportateur;
use App\Entity\References\Foret;
use App\Entity\References\Usine;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DocStats\Entetes\DocumentbrepfRepository;
use App\Repository\DocStats\Pages\PagebrepfRepository;
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

class DocumentbrepfController extends AbstractController
{
    public function __construct(private ManagerRegistry $m)
    {
    }

    #[Route('/doc/stats/entetes/docbrepf', name: 'app_op_docbrepf')]
    public function index(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbrepfRepository $docs_brepf,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPORTATEUR') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('doc_stats/entetes/documentbrepf/index.html.twig', [
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

    #[Route('/snvlt/docbrepf/brepf/pages/{id_brepf}', name: 'affichage_brepf_json')]
    public function affiche_brepf(
        Request $request,
        int $id_brepf,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbrepfRepository $docs_brepf,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPORTATEUR') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $numerodoc = "";

                $documentbrepf = $registry->getRepository(Documentbrepf::class)->find($id_brepf);
                if($documentbrepf){$numerodoc = $documentbrepf->getNumeroDocbrepf();}

                return $this->render('doc_stats/entetes/documentbrepf/affiche_brepf.html.twig', [
                    'document_name'=>$documentbrepf,
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

    #[Route('/snvlt/docbrepf/op', name: 'app_docs_brepf_json')]
    public function my_doc_brepf(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbrepfRepository $docs_brepf,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPORTATEUR') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')  )
                {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $mes_docs_brepf = array();
                //------------------------- Filtre les BREPF par type Opérateur ------------------------------------- //

                //------------------------- Filtre les BREPF ADMIN ------------------------------------- //
                if($user->getCodeGroupe()->getId() == 1){
                    $documents_brepf = $registry->getRepository(Documentbrepf::class)->findBy(['transmission'=>true],['created_at'=>'DESC']);
                    foreach ($documents_brepf as $document_brepf){

                            $exportateur = $document_brepf->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeExportateur() . '-'. $document_brepf->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getRaisonSocialeExportateur();
                            if($document_brepf->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef())
                            {
                                $ddef =  $document_brepf->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef()->getNomDdef();
                            } else {
                                $ddef = "-";
                            }
                        $mes_docs_brepf[] = array(
                            'id_doc_brepf'=>$document_brepf->getId(),
                            'numero_docbrepf'=>$document_brepf->getNumeroDocbrepf(),
                            'cantonnement'=>$document_brepf->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getNomCantonnement(),
                            'dr'=>$document_brepf->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                            'ddef'=>$ddef,
                            'date_delivrance'=>$document_brepf->getDelivreDocbrepf()->format("d m Y"),
                            'etat'=>$document_brepf->isEtat(),
                            'exportateur'=>$exportateur
                        );
                    }
                    //------------------------- Filtre les brepf DR ------------------------------------- //
                } elseif ($user->getCodeDr()){

                        $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_dr'=>$user->getCodeDr()]);
                        foreach ($cantonnements as $cantonnement){

                            $exportateurs = $registry->getRepository(Exportateur::class)->findBy(['code_cantonnement'=>$cantonnement]);
                            foreach ($exportateurs as $exportateur){
                                $agreements = $registry->getRepository(AgreementExportateur::class)->findBy(['statut'=>true, 'code_exportateur'=>$exportateur]);

                                         foreach ($agreements as $agreement) {
                                             $autorisations = $registry->getRepository(AutorisationExportateur::class)->findBy(['code_agreement'=>$agreement]);

                                             foreach ($autorisations as $autorisation)
                                             {
                                                 $documents = $registry->getRepository(Documentbrepf::class)->findBy(['transmission'=>true, 'code_autorisation_exportateur'=>$autorisation]);

                                                    foreach ($documents as $document){
                                                        $exportateur = $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeExportateur() . '-'. $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getRaisonSocialeExportateur();
                                                        if($document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef())
                                                        {
                                                            $ddef =  $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef()->getNomDdef();
                                                        } else {
                                                            $ddef = "-";
                                                        }
                                                        $mes_docs_brepf[] = array(
                                                            'id_doc_brepf'=>$document->getId(),
                                                            'numero_docbrepf'=>$document->getNumeroDocbrepf(),
                                                            'cantonnement'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getNomCantonnement(),
                                                            'dr'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                                            'ddef'=>$ddef,
                                                            'date_delivrance'=>$document->getDelivreDocbrepf()->format("d m Y"),
                                                            'etat'=>$document->isEtat(),
                                                            'exportateur'=>$exportateur
                                                        );
                                                    }
                                                }


                                        }

                                    }
                                }
                                //------------------------- Filtre les brepf DD ------------------------------------- //
                            } elseif ($user->getCodeDdef()){
                                $cantonnements = $registry->getRepository(Cantonnement::class)->findBy(['code_dr'=>$user->getCodeDdef()]);
                                foreach ($cantonnements as $cantonnement){

                                    $exportateurs = $registry->getRepository(Exportateur::class)->findBy(['code_cantonnement'=>$cantonnement]);
                                    foreach ($exportateurs as $exportateur){
                                        $agreements = $registry->getRepository(AgreementExportateur::class)->findBy(['statut'=>true, 'code_exportateur'=>$exportateur]);

                                        foreach ($agreements as $agreement) {
                                            $autorisations = $registry->getRepository(AutorisationExportateur::class)->findBy(['code_agreement'=>$agreement]);

                                            foreach ($autorisations as $autorisation)
                                            {
                                                $documents = $registry->getRepository(Documentbrepf::class)->findBy(['transmission'=>true, 'code_autorisation_exportateur'=>$autorisation]);

                                                foreach ($documents as $document){
                                                    $exportateur = $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeExportateur() . '-'. $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getRaisonSocialeExportateur();
                                                    if($document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef())
                                                    {
                                                        $ddef =  $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef()->getNomDdef();
                                                    } else {
                                                        $ddef = "-";
                                                    }
                                                    $mes_docs_brepf[] = array(
                                                        'id_doc_brepf'=>$document->getId(),
                                                        'numero_docbrepf'=>$document->getNumeroDocbrepf(),
                                                        'cantonnement'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getNomCantonnement(),
                                                        'dr'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                                        'ddef'=>$ddef,
                                                        'date_delivrance'=>$document->getDelivreDocbrepf()->format("d m Y"),
                                                        'etat'=>$document->isEtat(),
                                                        'exportateur'=>$exportateur
                                                    );
                                                }
                                            }


                                        }

                                    }
                                }

                        //------------------------- Filtre les brepf CANTONNEMENT ------------------------------------- //
                    } elseif ($user->getCodeCantonnement()){
                            $exportateurs = $registry->getRepository(Exportateur::class)->findBy(['code_cantonnement'=>$user->getCodeCantonnement()]);
                            foreach ($exportateurs as $exportateur){
                                $agreements = $registry->getRepository(AgreementExportateur::class)->findBy(['statut'=>true, 'code_exportateur'=>$exportateur]);

                                foreach ($agreements as $agreement) {
                                    $autorisations = $registry->getRepository(AutorisationExportateur::class)->findBy(['code_agreement'=>$agreement]);

                                    foreach ($autorisations as $autorisation)
                                    {
                                        $documents = $registry->getRepository(Documentbrepf::class)->findBy(['transmission'=>true, 'code_autorisation_exportateur'=>$autorisation]);

                                        foreach ($documents as $document){
                                            $exportateur = $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeExportateur() . '-'. $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getRaisonSocialeExportateur();
                                            if($document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef())
                                            {
                                                $ddef =  $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef()->getNomDdef();
                                            } else {
                                                $ddef = "-";
                                            }
                                            $mes_docs_brepf[] = array(
                                                'id_doc_brepf'=>$document->getId(),
                                                'numero_docbrepf'=>$document->getNumeroDocbrepf(),
                                                'cantonnement'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getNomCantonnement(),
                                                'dr'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                                'ddef'=>$ddef,
                                                'date_delivrance'=>$document->getDelivreDocbrepf()->format("d m Y"),
                                                'etat'=>$document->isEtat(),
                                                'exportateur'=>$exportateur
                                            );
                                        }
                                    }


                                }

                            }


                        //------------------------- Filtre les BREPF INDUSTRIELS------------------------------------- //
                    }  elseif ($user->getCodeExportateur()){
                    $agreements = $registry->getRepository(AgreementExportateur::class)->findBy(['statut'=>true, 'code_exportateur'=>$user->getCodeExportateur()]);

                    foreach ($agreements as $agreement) {
                        $autorisations = $registry->getRepository(AutorisationExportateur::class)->findBy(['code_agreement'=>$agreement]);

                        foreach ($autorisations as $autorisation)
                        {
                            $documents = $registry->getRepository(Documentbrepf::class)->findBy(['code_autorisation_exportateur'=>$autorisation, 'signature_cef'=>true, 'signature_dr'=>true],['created_at'=>'DESC']);

                            foreach ($documents as $document){
                                $exportateur = $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeExportateur() . '-'. $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getRaisonSocialeExportateur();
                                if($document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef())
                                {
                                    $ddef =  $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef()->getNomDdef();
                                } else {
                                    $ddef = "-";
                                }
                                $mes_docs_brepf[] = array(
                                    'id_doc_brepf'=>$document->getId(),
                                    'numero_docbrepf'=>$document->getNumeroDocbrepf(),
                                    'cantonnement'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getNomCantonnement(),
                                    'dr'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                    'ddef'=>$ddef,
                                    'date_delivrance'=>$document->getDelivreDocbrepf()->format("d m Y"),
                                    'etat'=>$document->isEtat(),
                                    'exportateur'=>$exportateur
                                );
                            }
                        }


                    }
                    }

                    return new JsonResponse(json_encode($mes_docs_brepf));
                }else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

            }

        }
}
