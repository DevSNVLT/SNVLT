<?php

namespace App\Controller\DocStats\Entetes;

use App\Entity\Autorisation\AgreementExportateur;
use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\AutorisationExportateur;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentbth;
use App\Entity\DocStats\Pages\Pagebth;
use App\Entity\References\Cantonnement;
use App\Entity\References\Exportateur;
use App\Entity\References\Foret;
use App\Entity\References\Usine;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DocStats\Entetes\DocumentbthRepository;
use App\Repository\DocStats\Pages\PagebthRepository;
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

class DocumentbthController extends AbstractController
{

    #[Route('/doc/stats/entetes/docbth', name: 'app_op_docbth')]
    public function index(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbthRepository $docs_bth,
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

                return $this->render('doc_stats/entetes/documentbth/index.html.twig', [
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

    #[Route('/snvlt/docbth/bth/pages/{id_bth}', name: 'affichage_bth_json')]
    public function affiche_bth(
        Request $request,
        int $id_bth,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbthRepository $docs_bth,
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

                $documentbth = $registry->getRepository(Documentbth::class)->find($id_bth);
                if($documentbth){$numerodoc = $documentbth->getNumeroDocbth();}

                return $this->render('doc_stats/entetes/documentbth/affiche_bth.html.twig', [
                    'document_name'=>$documentbth,
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

    #[Route('/snvlt/docbth/op', name: 'app_docs_bth_json')]
    public function my_doc_bth(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DocumentbthRepository $docs_bth,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPORTATEUR') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_MINEF'))
                {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $mes_docs_bth = array();
                //------------------------- Filtre les BTH par type Opérateur ------------------------------------- //

                //------------------------- Filtre les BTH ADMIN ------------------------------------- //
                if($user->getCodeGroupe()->getId() == 1){
                    $documents_bth = $registry->getRepository(Documentbth::class)->findBy(['transmission'=>true],['created_at'=>'DESC']);
                    foreach ($documents_bth as $document_bth){

                            $exportateur = $document_bth->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeExportateur() . '-'. $document_bth->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getRaisonSocialeExportateur();
                            if($document_bth->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef())
                            {
                                $ddef =  $document_bth->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef();
                            } else {
                                $ddef = "-";
                            }
                        $mes_docs_bth[] = array(
                            'id_doc_bth'=>$document_bth->getId(),
                            'numero_docbth'=>$document_bth->getNumeroDocbth(),
                            'cantonnement'=>$document_bth->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getNomCantonnement(),
                            'dr'=>$document_bth->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                            'ddef'=>$ddef,
                            'date_delivrance'=>$document_bth->getDelivreDocbth()->format("d m Y"),
                            'etat'=>$document_bth->isEtat(),
                            'exportateur'=>$exportateur,
                            'code_exportateur'=>$document_bth->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeExportateur(),
                            'autorisation'=>$document_bth->getCodeAutorisationExportateur()->getNumeroAutorisation()
                        );
                    }
                    //------------------------- Filtre les bth DR ------------------------------------- //
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
                                                 $documents = $registry->getRepository(Documentbth::class)->findBy(['transmission'=>true, 'code_autorisation_exportateur'=>$autorisation]);

                                                    foreach ($documents as $document){
                                                        $exportateur = $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeExportateur() . '-'. $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getRaisonSocialeExportateur();
                                                        if($document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef())
                                                        {
                                                            $ddef =  $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef()->getNomDdef();
                                                        } else {
                                                            $ddef = "-";
                                                        }
                                                        $mes_docs_bth[] = array(
                                                            'id_doc_bth'=>$document->getId(),
                                                            'numero_docbth'=>$document->getNumeroDocbth(),
                                                            'cantonnement'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getNomCantonnement(),
                                                            'dr'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                                            'ddef'=>$ddef,
                                                            'date_delivrance'=>$document->getDelivreDocbth()->format("d m Y"),
                                                            'etat'=>$document->isEtat(),
                                                            'exportateur'=>$exportateur,
                                                            'code_exportateur'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeExportateur(),
                                                            'autorisation'=>$document->getCodeAutorisationExportateur()->getNumeroAutorisation()
                                                        );
                                                    }
                                                }


                                        }

                                    }
                                }
                                //------------------------- Filtre les bth DD ------------------------------------- //
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
                                                $documents = $registry->getRepository(Documentbth::class)->findBy(['transmission'=>true, 'code_autorisation_exportateur'=>$autorisation]);

                                                foreach ($documents as $document){
                                                    $exportateur = $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeExportateur() . '-'. $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getRaisonSocialeExportateur();
                                                    if($document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef())
                                                    {
                                                        $ddef =  $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef()->getNomDdef();
                                                    } else {
                                                        $ddef = "-";
                                                    }
                                                    $mes_docs_bth[] = array(
                                                        'id_doc_bth'=>$document->getId(),
                                                        'numero_docbth'=>$document->getNumeroDocbth(),
                                                        'cantonnement'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getNomCantonnement(),
                                                        'dr'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                                        'ddef'=>$ddef,
                                                        'date_delivrance'=>$document->getDelivreDocbth()->format("d m Y"),
                                                        'etat'=>$document->isEtat(),
                                                        'exportateur'=>$exportateur,
                                                        'code_exportateur'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeExportateur(),
                                                        'autorisation'=>$document->getCodeAutorisationExportateur()->getNumeroAutorisation()
                                                    );
                                                }
                                            }


                                        }

                                    }
                                }

                        //------------------------- Filtre les bth CANTONNEMENT ------------------------------------- //
                    } elseif ($user->getCodeCantonnement()){
                            $exportateurs = $registry->getRepository(Exportateur::class)->findBy(['code_cantonnement'=>$user->getCodeCantonnement()]);
                            foreach ($exportateurs as $exportateur){
                                $agreements = $registry->getRepository(AgreementExportateur::class)->findBy(['statut'=>true, 'code_exportateur'=>$exportateur]);

                                foreach ($agreements as $agreement) {
                                    $autorisations = $registry->getRepository(AutorisationExportateur::class)->findBy(['code_agreement'=>$agreement]);

                                    foreach ($autorisations as $autorisation)
                                    {
                                        $documents = $registry->getRepository(Documentbth::class)->findBy(['transmission'=>true, 'code_autorisation_exportateur'=>$autorisation]);

                                        foreach ($documents as $document){
                                            $exportateur = $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeExportateur() . '-'. $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getRaisonSocialeExportateur();
                                            if($document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef())
                                            {
                                                $ddef =  $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef()->getNomDdef();
                                            } else {
                                                $ddef = "-";
                                            }
                                            $mes_docs_bth[] = array(
                                                'id_doc_bth'=>$document->getId(),
                                                'numero_docbth'=>$document->getNumeroDocbth(),
                                                'cantonnement'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getNomCantonnement(),
                                                'dr'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                                'ddef'=>$ddef,
                                                'date_delivrance'=>$document->getDelivreDocbth()->format("d m Y"),
                                                'etat'=>$document->isEtat(),
                                                'exportateur'=>$exportateur,
                                                'code_exportateur'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeExportateur(),
                                                'autorisation'=>$document->getCodeAutorisationExportateur()->getNumeroAutorisation()
                                            );
                                        }
                                    }


                                }

                            }


                        //------------------------- Filtre les BTH EXPORTATEUR------------------------------------- //
                    }  elseif ($user->getCodeExportateur()){
                    $agreements = $registry->getRepository(AgreementExportateur::class)->findBy(['statut'=>true, 'code_exportateur'=>$user->getCodeExportateur()]);

                    foreach ($agreements as $agreement) {
                        $autorisations = $registry->getRepository(AutorisationExportateur::class)->findBy(['code_agreement'=>$agreement]);

                        foreach ($autorisations as $autorisation)
                        {
                            $documents = $registry->getRepository(Documentbth::class)->findBy(['transmission'=>true, 'code_autorisation_exportateur'=>$autorisation, 'signature_cef'=>true, 'signature_dr'=>true],['created_at'=>'DESC']);

                            foreach ($documents as $document){
                                $exportateur = $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeExportateur() . '-'. $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getRaisonSocialeExportateur();
                                if($document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef())
                                {
                                    $ddef =  $document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDdef()->getNomDdef();
                                } else {
                                    $ddef = "-";
                                }
                                $mes_docs_bth[] = array(
                                    'id_doc_bth'=>$document->getId(),
                                    'numero_docbth'=>$document->getNumeroDocbth(),
                                    'cantonnement'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getNomCantonnement(),
                                    'dr'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeCantonnement()->getCodeDr()->getDenomination(),
                                    'ddef'=>$ddef,
                                    'date_delivrance'=>$document->getDelivreDocbth()->format("d m Y"),
                                    'etat'=>$document->isEtat(),
                                    'exportateur'=>$exportateur,
                                    'code_exportateur'=>$document->getCodeAutorisationExportateur()->getCodeAgreement()->getCodeExportateur()->getCodeExportateur(),
                                    'autorisation'=>$document->getCodeAutorisationExportateur()->getNumeroAutorisation()
                                );
                            }
                        }


                    }
                    }

                    return new JsonResponse(json_encode($mes_docs_bth));
                }else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

            }

        }


}
