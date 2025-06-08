<?php

namespace App\Controller\Administration;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\SendSMS;
use App\Controller\Services\Utils;
use App\Entity\Admin\Exercice;
use App\Entity\Administration\DemandeOperateur;
use App\Entity\Administration\DocStatsGen;
use App\Entity\Autorisation\AutorisationExportateur;
use App\Entity\Autorisation\AutorisationPs;
use App\Entity\Autorisation\AutorisationPv;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentbcbp;
use App\Entity\DocStats\Entetes\Documentbcburb;
use App\Entity\DocStats\Entetes\Documentbrepf;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Entetes\Documentbtgu;
use App\Entity\DocStats\Entetes\Documentbth;
use App\Entity\DocStats\Entetes\Documentcp;
use App\Entity\DocStats\Entetes\Documentdmp;
use App\Entity\DocStats\Entetes\Documentdmv;
use App\Entity\DocStats\Entetes\Documentetatb;
use App\Entity\DocStats\Entetes\Documentetate;
use App\Entity\DocStats\Entetes\Documentetate2;
use App\Entity\DocStats\Entetes\Documentetatg;
use App\Entity\DocStats\Entetes\Documentetath;
use App\Entity\DocStats\Entetes\Documentfp;
use App\Entity\DocStats\Entetes\Documentlje;
use App\Entity\DocStats\Entetes\Documentpdtdrv;
use App\Entity\DocStats\Entetes\Documentrsdpf;
use App\Entity\DocStats\Pages\Pagebcbp;
use App\Entity\DocStats\Pages\Pagebcburb;
use App\Entity\DocStats\Pages\Pagebrepf;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Pages\Pagebtgu;
use App\Entity\DocStats\Pages\Pagebth;
use App\Entity\DocStats\Pages\Pagecp;
use App\Entity\DocStats\Pages\Pagedmp;
use App\Entity\DocStats\Pages\Pagedmv;
use App\Entity\DocStats\Pages\Pageetatb;
use App\Entity\DocStats\Pages\Pageetate;
use App\Entity\DocStats\Pages\Pageetate2;
use App\Entity\DocStats\Pages\Pageetatg;
use App\Entity\DocStats\Pages\Pageetath;
use App\Entity\DocStats\Pages\Pagefp;
use App\Entity\DocStats\Pages\Pagelje;
use App\Entity\DocStats\Pages\Pagepdtdrv;
use App\Entity\DocStats\Pages\Pagersdpf;
use App\Entity\References\Commercant;
use App\Entity\References\Exploitant;
use App\Entity\References\Exportateur;
use App\Entity\References\PageDocGen;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\References\TypeOperateur;
use App\Entity\References\Usine;
use App\Entity\User;
use App\Events\References\AddDemandeOperateurEvent;
use App\Form\Administration\ValidationDemandeType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\Administration\StockDocRepository;
use App\Repository\DemandeOperateurRepository;
use App\Repository\DocumentOperateurRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\CircuitCommunicationRepository;
use App\Repository\References\ExploitantRepository;
use App\Repository\References\TypeOperateurRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;


class DemandeOperateurController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
        private Utils $utils,
        private EventDispatcherInterface $dispatcher,
        private SendSMS $sendSMS,
        private LoggerInterface $logger,
        private AdministrationService $administrationService)
    {
    }

    #[Route('snvlt/demop', name: 'app_demande_operateur')]
    public function index(
        Request $request,
        MenuRepository $menus,
        CircuitCommunicationRepository $circuitCommunicationRepository,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DemandeOperateurRepository $demandes): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $code_structure = 0;
                if($user->getCodeOperateur()->getId() == 2){
                    $code_structure = $user->getCodeexploitant()->getId();
                }elseif($user->getCodeOperateur()->getId() == 3){
                    $code_structure = $user->getCodeindustriel()->getId();
                }elseif($user->getCodeOperateur()->getId() == 4){
                    $code_structure = $user->getCodeExportateur()->getId();
                }
                return $this->render('administration/demande_operateur/index.html.twig',
                    [
                        'mes_circuits'=>$circuitCommunicationRepository,
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'groupe'=>$code_groupe,
                        'mes_demandes'=>$demandes->findBy(['code_operateur'=>$user->getCodeOperateur(), 'code_structure'=>$code_structure],['created_at'=>'DESC']),
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/demop/add', name: 'demande.add')]
    public function editDemandeOperateur(
        ManagerRegistry $doctrine,
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        DemandeOperateurRepository $demandes,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        NotificationRepository $notification,
        GroupeRepository $groupeRepository
       /* GroupeRepository $groupeValidation*/
    ): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();



                    return $this->render('administration/demande_operateur/add-demande.html.twig',[
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'groupe'=>$code_groupe,
                        'liste_parent'=>$permissions
                    ]);
                } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/ver_demop', name: 'app_validation_demande')]
    public function validation_document(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DemandeOperateurRepository $demandes): Response
    {
        //dd($request);
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                return $this->render('administration/verification_demandes/index.html.twig',
                    [

                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'groupe'=>$code_groupe,
                        'mes_demandes'=>$demandes->findBy(['verification'=>false]),
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('snvlt/demop/all', name: 'app_validation_demande_all')]
    public function document_operateur_all(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DemandeOperateurRepository $demandes): Response
    {
        //dd($request);
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                return $this->render('administration/verification_demandes/all.html.twig',
                    [

                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'groupe'=>$code_groupe,
                        'mes_demandes'=>$demandes->findBy([],['verification'=>'ASC','created_at'=>'DESC']),
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('snvlt/ver_demop/apply/{id_demande?0}/{id_notif}', name: 'app_validation_demande_validate')]
    public function validate_document(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DemandeOperateurRepository $demandes,
        int $id_demande,
        ManagerRegistry $doctrine,
        DocumentOperateurRepository $operateurRepository): Response
    {
        //dd($request);
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $titre = $this->translator->trans("Validate the request");
                $demande = $demandes->find($id_demande);


                $form = $this->createForm(ValidationDemandeType::class, $demande);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){

                    $dateMAJ  = new \DateTime();
                    $demande->setUpdatedBy($user);
                    $demande->setUpdatedAt($dateMAJ);
                    $demande->setVerification(true);


                    $manager = $doctrine->getManager();
                    $manager->persist($demande);


                    $manager->flush();
                    $this->administrationService->save_action(
                        $user,
                        "DEMANDE_OPERATEUR",
                        "ENVOI DEMANDE", new \DateTimeImmutable(),
                       "Demande de ". $demande->getQte() . " " . $demande->getDocStat()->getAbv() . " par " . $demande->getDemandeur()->getPrenomsUtilisateur(). " " . $demande->getDemandeur()->getNomUtilisateur()
                    );

                    $this->addFlash('success',$this->translator->trans("The request has just been validated successfully"));
                    return $this->redirectToRoute("app_validation_demande");
                } else {
                    return $this->render('administration/verification_demandes/validation-demande.html.twig',
                        [
                            'form' =>$form->createView(),
                            'titre'=>$titre,
                            'liste_menus'=>$menus->findOnlyParent(),
                            "all_menus"=>$menus->findAll(),
                            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                            'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                            'groupe'=>$code_groupe,
                            'mes_demandes'=>$demandes->findBy(['verification'=>false]),
                            'liste_parent'=>$permissions,
                            'documents_operateur'=>$operateurRepository->findBy(['codeOperateur'=>$demande->getCodeStructure()])
                        ]);
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/ver_demop/delivrance', name: 'app_delivrance_document')]
    public function delivrance_document(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DemandeOperateurRepository $demandes): Response
    {
        //dd($request);
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                return $this->render('administration/verification_demandes/index.html.twig',
                    [

                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'groupe'=>$code_groupe,
                        'mes_demandes'=>$demandes->findBy(['verification'=>false]),
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/docs_op_type/{id_operateur}', name: 'docs_op_type.list')]
    public function docstype_operateur(
        Request $request,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        ManagerRegistry $registry,
        User $user = null,
        int $id_operateur,
        TypeOperateur $operateur = null,
        TypeOperateurRepository $operateurRepository,
        ): Response
    {
        //dd($request);
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $response = array();
                $operateur = $operateurRepository->find($id_operateur);
                //dd($operateur);
                if ($operateur){
                    //$documentsOperateur = $operateur->getTypeDocumentStatistiques();
                    $documentsOperateur = $registry->getRepository(TypeDocumentStatistique::class)->findBy(['code_type_operateur'=>$operateur, 'statut'=>'ACTIF'], ['denomination'=>'ASC']);

                    foreach ($documentsOperateur as $doc){
                        $response[] = array(
                            'id_doc'=>$doc->getId(),
                            'libelle'=>$doc->getAbv()
                        );
                    }

                    return new JsonResponse(json_encode($response));
                } else {
                    return new JsonResponse(json_encode("ERROR! THIS OPERATOR IS NOT RECOGNIZED"));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/reprises_operateurs/{id_exploitant}', name: 'reprises_op.list')]
    public function reprises_operateur(
        Request $request,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_exploitant,
        Exploitant $exploitant = null,
        ExploitantRepository $exploitantRepository,
    ): Response
    {
        //dd($request);
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $response = array();
                $exploitant = $exploitantRepository->find($id_exploitant);
                if ($exploitant){
                    $attributions = $exploitant->getAttributions();
                    foreach ($attributions as $attribution){
                        $reprises = $attribution->getReprises();
                        foreach ($reprises as $reprise){
                            $response[] = array(
                                'id_reprise'=>$reprise->getId(),
                                'libelle'=>$reprise->getCodeAttribution()->getCodeForet()->getDenomination()
                            );

                        }
                    }
                    return new JsonResponse(json_encode($response));
                } else {
                    return new JsonResponse(json_encode("ERROR! THIS OPERATOR IS NOT RECOGNIZED"));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/auto_pv_operateurs/{id_exploitant}', name: 'auto_pv_op.list')]
    public function auto_pv_operateur(
        Request $request,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_exploitant,
        Exploitant $exploitant = null,
        ExploitantRepository $exploitantRepository,
    ): Response
    {
        //dd($request);
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $response = array();
                $exploitant = $exploitantRepository->find($id_exploitant);
                if ($exploitant){
                    $attributions = $exploitant->getAttributions();
                    foreach ($attributions as $attribution){
                        $autorisations = $exploitant->getAutorisationPvs();
                        foreach ($autorisations as $autorisation){
                            $response[] = array(
                                'id_autorisation'=>$autorisation->getId(),
                                'libelle'=>$autorisation->getCodeAttributionPv()->getCodeParcelle()->getDenomination()
                            );

                        }
                    }
                    return new JsonResponse(json_encode($response));
                } else {
                    return new JsonResponse(json_encode("ERROR! THIS OPERATOR IS NOT RECOGNIZED"));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('snvlt/demandes_op/save/{id_type_doc}/{id_reprise?0}/{id_autorisation_pv?0}/{id_usine?0}/{id_exportateur?0}/{id_commercant?0}/{qte}', name: 'reprises_op.add')]
    public function add_demande_operateur(
        Request $request,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_type_doc,
        int $qte,
        int $id_reprise,
        int $id_usine,
        int $id_exportateur,
        int $id_autorisation_pv,
        int $id_commercant,
        Exploitant $exploitant = null,
        Usine $usine = null,
        Exportateur $exportateur = null,
        ExploitantRepository $exploitantRepository,
        ManagerRegistry $registry,
    ): Response
    {
        //dd($request);
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                if ($id_type_doc && $qte){
                    $user = $userRepository->find($this->getUser());
                    $code_groupe = $user->getCodeGroupe()->getId();


                    $date_creation = new \DateTimeImmutable();

                    $demande = new DemandeOperateur();

                    $demande->setCreatedAt($date_creation);
                    $demande->setCreatedBy($this->getUser());
                    $demande->setStatut('EN ATTENTE');
                    $demande->setTransmission(false);
                    $demande->setVerification(false);
                    $demande->setDelivrance(false);
                    $demande->setDocsGeneres(false);
                    $demande->setSignatureDr(false);
                    $demande->setSignatureCef(false);


                    $demande->setDemandeur($user);
                    $code = $this->utils->uniqidReal(8). $user->getId();
                    $demande->setCode(strtoupper($code));

                    $demande->setQte($qte);
                    $demande->setDocStat($registry->getRepository(TypeDocumentStatistique::class)->find($id_type_doc));

                    if($user->getCodeOperateur()->getId() == 2){
                        $demande->setCodeStructure($user->getCodeexploitant()->getId());
                        if ($id_reprise){
                            $reprise = $registry->getRepository(Reprise::class)->find($id_reprise);
                            $demande->setCodeReprise($reprise);
                        }

                    } elseif($user->getCodeOperateur()->getId() == 3){
                        $demande->setCodeStructure($user->getCodeindustriel()->getId());
                        if ($id_usine){
                            $usine= $registry->getRepository(Usine::class)->find($id_usine);
                            $demande->setCodeUsine($usine);
                        }
                    }elseif($user->getCodeOperateur()->getId() == 4){
                        $demande->setCodeStructure($user->getCodeExportateur()->getId());
                        if ($id_exportateur){
                            $exportateur= $registry->getRepository(Exportateur::class)->find($id_usine);
                            $demande->setCodeExportateur($exportateur);
                        }
                    }elseif($user->getCodeOperateur()->getId() == 8){
                        $demande->setCodeStructure($user->getCodeCommercant()->getId());
                        if ($id_commercant){
                            $commercant = $registry->getRepository(Commercant::class)->find($id_commercant);
                            $demande->setCodeCommercant($commercant);
                        }
                    }

                    $demande->setCodeOperateur($user->getCodeOperateur());
                    $demande->setDocsGeneres(false);
                    $manager = $registry->getManager();
                    $manager->persist($demande);

                    //Ajout de l'action dans le LOG
                    $this->administrationService->save_action(
                        $user,
                        "DEMANDE_OPERATEUR",
                        "ENVOI",
                        new \DateTimeImmutable(),
                        "Demande de ". $demande->getQte() . " " . $demande->getDocStat()->getAbv() . " envoyéé par " . $demande->getDemandeur()->getPrenomsUtilisateur(). " " . $demande->getDemandeur()->getNomUtilisateur()
                    );
                    $manager->flush();


                    //Crer l'evenement pour la génération de circuit de validation
                    $addDemandeperateurEvent = new AddDemandeOperateurEvent($demande);

                    //Dispatcher l'evenement
                    $this->dispatcher->dispatch($addDemandeperateurEvent, AddDemandeOperateurEvent::ADD_DEMANDE_OPERATEUR_EVENT);

                    $this->addFlash('success',$this->translator->trans("The document request was sent successfully"));
                    return $this->redirectToRoute("app_demande_operateur");
                    }


                    $this->addFlash('success',$this->translator->trans("The document request was sent successfully"));
                    return $this->redirectToRoute("app_demande_operateur");
                } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

            }

        }
    #[Route('snvlt/demop/generate', name: 'app_demande_retrieve')]
    public function retrieve_documents(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DemandeOperateurRepository $demandes): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();



                return $this->render('administration/demande_operateur/reception.twig',
                    [
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'groupe'=>$code_groupe,
                        'mes_demandes'=>$demandes->findBy(['docs_generes'=>false, 'statut'=>'APPROUVE'],['created_at'=>'DESC']),
                        'utils'=>$this->utils,
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/demop/reception', name: 'app_demande_reception')]
    public function reception_documents(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DemandeOperateurRepository $demandes): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();



                return $this->render('administration/demande_operateur/delivrance.twig',
                    [
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'groupe'=>$code_groupe,
                        'mes_demandes'=>$demandes->findBy(['docs_generes'=>true, 'statut'=>'APPROUVE', 'transmission'=>false],['created_at'=>'DESC']),
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('snvlt/demop/stock/{id_demande}', name: 'app_demande_get_docs_stock')]
    public function get_doc_stock(
        Request $request,
        int $id_demande,
        DemandeOperateur $demandeOperateur = null,
        ManagerRegistry $registry,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DemandeOperateurRepository $demandes,
        StockDocRepository $stockDocRepository ): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $demandeOperateur = $registry->getRepository(DemandeOperateur::class)->find($id_demande);
                $nb_docs = 0;
                $reponse = array();
                if($demandeOperateur){
                    $docs = $stockDocRepository->findBy(['code_type_doc_stat'=>$demandeOperateur->getDocStat()]);

                    foreach ($docs as $doc){
                        $nb_docs = $doc->getDocStatsGens()->count();
                        foreach ($doc->getDocStatsGens() as $doc_detail){
                            $code_reprise = 0;
                            $code_autorisation_pv = 0;
                            $code_usine= 0;
                            $code_exportateur = 0;
                            $code_autorisation_ps = 0;
                            $code_commercant = 0;
                            if ($demandeOperateur->getCodeReprise()){
                                $code_reprise = $demandeOperateur->getCodeReprise()->getId();
                            }
                            if ($demandeOperateur->getCodeUsine()){
                                $code_usine = $demandeOperateur->getCodeUsine()->getId();
                            }
                            if ($demandeOperateur->getCodeExportateur()){
                                $code_exportateur = $demandeOperateur->getCodeExportateur()->getId();
                            }
                            if ($demandeOperateur->getCodeAutorisationPv()){
                                $code_autorisation_pv = $demandeOperateur->getCodeAutorisationPv()->getId();
                            }
                            if ($demandeOperateur->getCodeAutorisationps()){
                                $code_autorisation_ps = $demandeOperateur->getCodeAutorisationps()->getId();
                            }
                            if ($demandeOperateur->getCodeCommercant()){
                                $code_commercant = $demandeOperateur->getCodeCommercant()->getId();
                            }
                                if(!$doc_detail->isAttribue()){
                                    $reponse[] = array(
                                        'id_doc'=> $doc_detail->getId(),
                                        'numero_doc'=>$doc_detail->getNumeroDoc(),
                                        'nb'=>$nb_docs,
                                        'qte_livree'=>$demandeOperateur->getQteDelivree(),
                                        'qte_demandee'=>$demandeOperateur->getQte(),
                                        'code_reprise'=>$code_reprise,
                                        'code_usine'=>$code_usine,
                                        'code_exportateur'=>$code_exportateur,
                                        'code_auto_ps'=>$code_autorisation_ps,
                                        'code_commercant'=>$code_commercant,
                                        'code_auto_pv'=>$code_autorisation_pv,
                                        'type_doc'=>$doc_detail->getCodeTypeDoc()->getCodeTypeDocStat()->getId()
                                    );
                                }

                            //dd($reponse);
                            }

                    }

                }
                return new JsonResponse(json_encode($reponse));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    /* ------------------- Routine de Génération de Documents d'Exploitation et Statistiques ----------------------- */
    #[Route('snvlt/demop_cp/gen_from_stock/{id_stock_doc}/{id_reprise?0}/{id_autorisation_parcelle?0}/{id_usine?0}/{id_auto_export?0}/{id_auto_ps?0}/{id_commercant?0}/{id_demande}/{nb_definitif}', name: 'app_demande_gen_doccp')]
    public function generateDocStats(
        Request $request,
        int $id_stock_doc,
        int $id_reprise,
        int $id_autorisation_parcelle,
        int $id_auto_ps,
        int $id_usine,
        int $id_auto_export,
        int $nb_definitif,
        int $id_demande,
        int $id_commercant,
        ManagerRegistry $registry,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $demande = $registry->getRepository(DemandeOperateur::class)->find($id_demande);
                $doc_stock = $registry->getRepository(DocStatsGen::class)->find($id_stock_doc);

                $reponse = "";
                $ref = "";


                $exercice_en_cours = $registry->getRepository(Exercice::class)->findOneBy(['cloture'=>false], ['annee'=>'DESC']);
                $type_doc_stock = 0;
                if($doc_stock){
                    $delivre_doc = new \DateTime();
                    $majDoc = new \DateTime();

                    $type_doc_stock = $doc_stock->getCodeTypeDoc()->getCodeTypeDocStat();
                    $doc_generate = null;
                    $description_log = "";
                    /*----------------------------------------------------------------------------------------------------------------------------------------------
                                            Recherche de l'opérateur à partir de l'id_demande dans le cadre d'envoi de notification
                    ----------------------------------------------------------------------------------------------------------------------------------------------*/
                    /*--*/
                    /*--*/        $type_operateur  = $registry->getRepository(DemandeOperateur::class)->find($id_demande)->getCodeOperateur()->getId();
                    /*--*/        $code_operateur  = $registry->getRepository(DemandeOperateur::class)->find($id_demande)->getCodeStructure();
                    /*--*/        // ...... Recherche email du responsable ...... //
                    /*--*/
                    /*--*/        if ($type_operateur == 2){
                    /*--*/            $emailResponsable = $registry->getRepository(Exploitant::class)->find($code_operateur)->getEmailPersonneRessource();
                    /*--*/            $Responsable = $userRepository->findBy(['email'=>$emailResponsable]);
                    /*--*/        } elseif ($type_operateur == 3){
                    /*--*/             $emailResponsable = $registry->getRepository(Usine::class)->find($code_operateur)->getEmailPersonneRessource();
                    /*--*/            $Responsable = $userRepository->findBy(['email'=>$emailResponsable]);
                    /*--*/        } elseif ($type_operateur == 4){
                    /*--*/            $emailResponsable = $registry->getRepository(Exportateur::class)->find($code_operateur)->getEmailPersonneRessource();
                    /*--*/            $Responsable = $userRepository->findBy(['email'=>$emailResponsable]);
                    /*--*/    }
                    /*-----------------------------------------------------------------------------------------------------------------------------------------------
                    -----------------------------------------------------------------------------------------------------------------------------------------------*/



                    //Routine pour la génération du CP et ses pages depuis le Stock
                    if ($type_doc_stock->getId() == 1){


                        $doc_generate = new Documentcp();
                        $reprise = $registry->getRepository(Reprise::class)->find($id_reprise);
                        $doc_generate->setCodeReprise($reprise);
                        $doc_generate->setNumeroDoccp($doc_stock->getNumeroDoc());
                        $doc_generate->setDelivreDoccp($delivre_doc);
                        $doc_generate->setExercice($exercice_en_cours);
                        $doc_generate->setCreatedAt($majDoc);
                        $doc_generate->setCreatedBy($user);
                        $doc_generate->setTypeDocument($type_doc_stock);
                        $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                        $doc_generate->setCodeGeneration($doc_stock);
                        $doc_generate->setCodeDemande($demande);
                        $doc_generate->setTransmission(false);
                        $doc_generate->setSignatureDr(false);
                        $doc_generate->setSignatureCef(false);

                        $registry->getManager()->persist($doc_generate);
                        $description_log = "Document CP N° " . $doc_generate->getNumeroDoccp() . " généré par " . $user->getPrenomsUtilisateur() . " ". $user->getNomUtilisateur();
                        $document_type = $doc_generate->getTypeDocument()->getDenomination();

                                    //Génétion des pages du document CP

                                    $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                                    foreach ($pages_doc_stock as $page){
                                        $page_cp = new Pagecp();
                                        $page_cp->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                                        $page_cp->setCodeDoccp($doc_generate);
                                        $page_cp->setNumeroPagecp($page->getNumpage());
                                        $page_cp->setCreatedAt(new \DateTime());
                                        $page_cp->setCreatedBy($doc_generate->getCreatedBy());
                                        $page_cp->setIndex($page->getNumeroPage());
                                        $page_cp->setFini(false);
                                        $page_cp->setCodeGeneration($page);

                                        $registry->getManager()->persist($page_cp);
                                        //dd($page_cp);

                                        //Mise à jour de la page stock
                                        $page->setAttribue(true);
                                        $registry->getManager()->persist($page);


                                        $this->logger->info($this->translator->trans("Page No ". $page_cp->getNumeroPagecp() . " " . " from document CP No ". $doc_generate->getNumeroDoccp(). " has been created by ". $doc_generate->getCreatedBy()));
                                    }

                                    $registry->getManager()->flush();
                                    $ref = "app_op_doccp";

                        $reponse = "DOCUMENT CP AND PAGES CP GENERATED";
                        $this->addFlash("success", $this->translator->trans("DOCUMENT CP AND PAGES CP GENERATED"));

                    //Routine pour la génération du BRH et ses pages depuis le Stock
                    } else if ($type_doc_stock->getId() == 2){
                            $doc_generate = new Documentbrh();
                            $reprise = $registry->getRepository(Reprise::class)->find($id_reprise);
                            $doc_generate->setCodeReprise($reprise);
                            $doc_generate->setNumeroDocbrh($doc_stock->getNumeroDoc());
                            $doc_generate->setDelivreDocbrh($delivre_doc);
                            $doc_generate->setExercice($exercice_en_cours);
                            $doc_generate->setCreatedAt($majDoc);
                            $doc_generate->setCreatedBy($user);
                            $doc_generate->setTypeDocument($type_doc_stock);
                            $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                            $doc_generate->setCodeGeneration($doc_stock);
                            $doc_generate->setCodeDemande($demande);
                            $doc_generate->setTransmission(false);
                            $doc_generate->setSignatureDr(false);
                            $doc_generate->setSignatureCef(false);

                            $registry->getManager()->persist($doc_generate);
                            $description_log = "Document BRH N° " . $doc_generate->getNumeroDocbrh() . " généré par " . $user->getPrenomsUtilisateur() . " ". $user->getNomUtilisateur();

                        $document_type = $doc_generate->getTypeDocument()->getDenomination();

                            //Génétion des pages du document BRH

                            $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                            foreach ($pages_doc_stock as $page){
                                $page_brh = new Pagebrh();
                                $page_brh->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                                $page_brh->setCodeDocbrh($doc_generate);
                                $page_brh->setNumeroPagebrh($page->getNumpage());
                                $page_brh->setCreatedAt(new \DateTime());
                                $page_brh->setCreatedBy($doc_generate->getCreatedBy());
                                $page_brh->setindex_page($page->getNumeroPage());
                                $page_brh->setFini(false);
                                $page_brh->setCodeGeneration($page);

                                $registry->getManager()->persist($page_brh);
                                //dd($page_cp);

                                //Mise à jour de la page stock
                                $page->setAttribue(true);
                                $registry->getManager()->persist($page);


                                $this->logger->info($this->translator->trans("Page No ". $page_brh->getNumeroPagebrh() . " " . " from document BRH No ". $doc_generate->getNumeroDocbrh(). " has been created by ". $doc_generate->getCreatedBy()));
                            }

                            $registry->getManager()->flush();
                            $ref = "app_op_docbrh";

                            $reponse = "DOCUMENT BRH AND PAGES BRH GENERATED";
                            $this->addFlash("success", $this->translator->trans("DOCUMENT BRH AND PAGES BRH GENERATED"));

                        //Routine pour la génération du BCBP et ses pages depuis le Stock
                        } else if ($type_doc_stock->getId() == 3){
                        $doc_generate = new Documentbcbp();
                        $autorisation = $registry->getRepository(AutorisationPv::class)->find($id_autorisation_parcelle);
                        $doc_generate->setCodeAutorisationPv($autorisation);
                        $doc_generate->setNumeroDocbcbp($doc_stock->getNumeroDoc());
                        $doc_generate->setDelivreDocbcbp($delivre_doc);
                        $doc_generate->setExercice($exercice_en_cours);
                        $doc_generate->setCreatedAt($majDoc);
                        $doc_generate->setCreatedBy($user);
                        $doc_generate->setTypeDocument($type_doc_stock);
                        $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                        $doc_generate->setCodeGeneration($doc_stock);
                        $doc_generate->setCodeDemande($demande);
                        $doc_generate->setTransmission(false);
                        $doc_generate->setSignatureDr(false);
                        $doc_generate->setSignatureCef(false);

                        $registry->getManager()->persist($doc_generate);
                        $description_log = "Document BCBP N° " . $doc_generate->getNumeroDocbcbp() . " généré par " . $user->getPrenomsUtilisateur() . " ". $user->getNomUtilisateur();

                        $document_type = $doc_generate->getTypeDocument()->getDenomination();

                        //Génétion des pages du document BRH

                        $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                        foreach ($pages_doc_stock as $page){
                            $page_brh = new Pagebcbp();
                            $page_brh->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                            $page_brh->setCodeDocbcbp($doc_generate);
                            $page_brh->setNumeroPagebcbp($page->getNumpage());
                            $page_brh->setCreatedAt(new \DateTime());
                            $page_brh->setCreatedBy($doc_generate->getCreatedBy());
                            $page_brh->setIndexPagebcbp($page->getNumeroPage());
                            $page_brh->setFini(false);
                            $page_brh->setCodeGeneration($page);

                            $registry->getManager()->persist($page_brh);
                            //dd($page_cp);

                            //Mise à jour de la page stock
                            $page->setAttribue(true);
                            $registry->getManager()->persist($page);


                            $this->logger->info($this->translator->trans("Page No ". $page_brh->getNumeroPagebcbp() . " " . " from document BRH No ". $doc_generate->getNumeroDocbcbp(). " has been created by ". $doc_generate->getCreatedBy()));
                        }

                        $registry->getManager()->flush();
                        $ref = "app_op_docbcbp";

                        $reponse = "DOCUMENT BRH AND PAGES BRH GENERATED";
                        $this->addFlash("success", $this->translator->trans("DOCUMENT BCBP AND PAGES BCBP GENERATED"));

                        //Routine pour la génération de l'ETAT B et ses pages depuis le Stock
                    }  else if ($type_doc_stock->getId() == 4){
                        if ($demande->getCodeOperateur()->getId() == 2){
                            $code_exploitant =  $registry->getRepository(Exploitant::class)->find($demande->getCodeStructure());
                            //dd($code_exploitant);
                            $doc_generate = new Documentetatb();

                            $doc_generate->setCodeExploitant($code_exploitant);
                            $doc_generate->setNumeroDocetatb($doc_stock->getNumeroDoc());
                            $doc_generate->setDelivreDocetatb($delivre_doc);
                            $doc_generate->setExercice($exercice_en_cours);
                            $doc_generate->setCreatedAt($majDoc);
                            $doc_generate->setCreatedBy($user);
                            $doc_generate->setTypeDocument($type_doc_stock);
                            $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                            $doc_generate->setCodeGeneration($doc_stock);
                            $doc_generate->setCodeDemande($demande);
                            $doc_generate->setTransmission(false);
                            $doc_generate->setSignatureDr(false);
                            $doc_generate->setSignatureCef(false);

                            $registry->getManager()->persist($doc_generate);
                            $description_log = "Document ETAT B N° " . $doc_generate->getNumeroDocetatb() . " généré par " . $user->getPrenomsUtilisateur() . " ". $user->getNomUtilisateur();


                            $document_type = $doc_generate->getTypeDocument()->getDenomination();

                            //Génétion des pages du document ETAT B

                            $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                            foreach ($pages_doc_stock as $page){
                                $page_etatb = new Pageetatb();
                                $page_etatb->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                                $page_etatb->setCodeDocetatb($doc_generate);
                                $page_etatb->setNumeroPageetatb($page->getNumpage());
                                $page_etatb->setCreatedAt(new \DateTime());
                                $page_etatb->setCreatedBy($doc_generate->getCreatedBy());
                                $page_etatb->setIndexPageetatb($page->getNumeroPage());
                                $page_etatb->setCodeGeneration($page);

                                $registry->getManager()->persist($page_etatb);
                                //dd($page_cp);

                                //Mise à jour de la page stock
                                $page->setAttribue(true);
                                $registry->getManager()->persist($page);


                                $this->logger->info($this->translator->trans("Page No ". $page_etatb->getNumeroPageetatb() . " " . " from document BRH No ". $doc_generate->getNumeroDocetatb(). " has been created by ". $doc_generate->getCreatedBy()));
                            }

                            $registry->getManager()->flush();
                            $ref = "app_op_docetatb";

                            $reponse = "DOCUMENT ETAT B AND PAGES ETAT B GENERATED";
                            $this->addFlash("success", $this->translator->trans("DOCUMENT ETAT B AND PAGES ETAT B GENERATED"));
                        }



                        //Routine pour la génération du LJE et ses pages depuis le Stock
                    } else if ($type_doc_stock->getId() == 5){
                        $doc_generate = new Documentlje();
                        $usine = $registry->getRepository(Usine::class)->find($id_usine);
                        $doc_generate->setCodeUsine($usine);
                        $doc_generate->setNumeroDoclje($doc_stock->getNumeroDoc());
                        $doc_generate->setDelivreDoclje($delivre_doc);
                        $doc_generate->setExercice($exercice_en_cours);
                        $doc_generate->setCreatedAt($majDoc);
                        $doc_generate->setCreatedBy($user);
                        $doc_generate->setTypeDocument($type_doc_stock);
                        $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                        $doc_generate->setCodeGeneration($doc_stock);
                        $doc_generate->setCodeDemande($demande);
                        $doc_generate->setTransmission(false);
                        $doc_generate->setSignatureDr(false);
                        $doc_generate->setSignatureCef(false);

                        $registry->getManager()->persist($doc_generate);
                        $description_log = "Document LJE N° " . $doc_generate->getNumeroDoclje() . " généré par " . $user->getPrenomsUtilisateur() . " ". $user->getNomUtilisateur();

                        $document_type = $doc_generate->getTypeDocument()->getDenomination();

                        //Génétion des pages du document LJE

                        $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                        foreach ($pages_doc_stock as $page){
                            $page_lje = new Pagelje();
                            $page_lje->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                            $page_lje->setCodeDoclje($doc_generate);
                            $page_lje->setNumeroPagelje($page->getNumpage());
                            $page_lje->setCreatedAt(new \DateTime());
                            $page_lje->setCreatedBy($doc_generate->getCreatedBy());
                            $page_lje->setIndexPagelje($page->getNumeroPage());
                            $page_lje->setCodeGeneration($page);

                            $registry->getManager()->persist($page_lje);
                            //dd($page_cp);

                            //Mise à jour de la page stock
                            $page->setAttribue(true);
                            $registry->getManager()->persist($page);


                            $this->logger->info($this->translator->trans("Page No ". $page_lje->getNumeroPagelje() . " " . " from document BRH No ". $doc_generate->getNumeroDoclje(). " has been created by ". $doc_generate->getCreatedBy()));
                        }

                        $registry->getManager()->flush();
                        $ref = "app_op_doclje";


                        $reponse = "DOCUMENT LJE AND PAGES LJE GENERATED";
                        $this->addFlash("success", $this->translator->trans("DOCUMENT BRH LJE PAGES LJE GENERATED"));

                    //Routine pour la génération du BTGU et ses pages depuis le Stock
                    } else if ($type_doc_stock->getId() == 6){
                        $doc_generate = new Documentbtgu();
                        $usine = $registry->getRepository(Usine::class)->find($id_usine);
                        $doc_generate->setCodeUsine($usine);
                        $doc_generate->setNumeroDocbtgu($doc_stock->getNumeroDoc());
                        $doc_generate->setDelivreDocbtgu($delivre_doc);
                        $doc_generate->setExercice($exercice_en_cours);
                        $doc_generate->setCreatedAt($majDoc);
                        $doc_generate->setCreatedBy($user);
                        $doc_generate->setTypeDocument($type_doc_stock);
                        $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                        $doc_generate->setCodeGeneration($doc_stock);
                        $doc_generate->setCodeDemande($demande);
                        $doc_generate->setTransmission(false);
                        $doc_generate->setSignatureDr(false);
                        $doc_generate->setSignatureCef(false);

                        $registry->getManager()->persist($doc_generate);
                        $description_log = "Document BTGU N° " . $doc_generate->getNumeroDocbtgu() . " généré par " . $user->getPrenomsUtilisateur() . " ". $user->getNomUtilisateur();

                        $document_type = $doc_generate->getTypeDocument()->getDenomination();

                        //Génétion des pages du document BRH

                        $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                        foreach ($pages_doc_stock as $page){
                            $page_btgu = new Pagebtgu();
                            $page_btgu->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                            $page_btgu->setCodeDocbtgu($doc_generate);
                            $page_btgu->setNumeroPagebtgu($page->getNumpage());
                            $page_btgu->setCreatedAt(new \DateTime());
                            $page_btgu->setCreatedBy($doc_generate->getCreatedBy());
                            $page_btgu->setIndexPagebtgu($page->getNumeroPage());
                            $page_btgu->setCodeGeneration($page);

                            $registry->getManager()->persist($page_btgu);
                            //dd($page_cp);

                            //Mise à jour de la page stock
                            $page->setAttribue(true);
                            $registry->getManager()->persist($page);


                            $this->logger->info($this->translator->trans("Page No ". $page_btgu->getNumeroPagebtgu() . " " . " from document BRH No ". $doc_generate->getNumeroDocbtgu(). " has been created by ". $doc_generate->getCreatedBy()));
                        }

                        $registry->getManager()->flush();
                        $ref = "app_op_docbtgu";


                        $reponse = "DOCUMENT BTGU AND PAGES BTGU GENERATED";
                        $this->addFlash("success", $this->translator->trans($reponse));

                    //Routine pour la génération de la FIche de Production FP et ses pages depuis le Stock
                    } else if ($type_doc_stock->getId() == 7){
                        $doc_generate = new Documentfp();
                        $usine = $registry->getRepository(Usine::class)->find($id_usine);
                        $doc_generate->setCodeUsin($usine);
                        $doc_generate->setNumeroDocfp($doc_stock->getNumeroDoc());
                        $doc_generate->setDelivreDocfp($delivre_doc);
                        $doc_generate->setExercice($exercice_en_cours);
                        $doc_generate->setCreatedAt($majDoc);
                        $doc_generate->setCreatedBy($user);
                        $doc_generate->setTypeDocument($type_doc_stock);
                        $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                        $doc_generate->setCodeGeneration($doc_stock);
                        $doc_generate->setCodeDemande($demande);
                        $doc_generate->setTransmission(false);
                        $doc_generate->setSignatureDr(false);
                        $doc_generate->setSignatureCef(false);

                        $registry->getManager()->persist($doc_generate);
                        $description_log = "Document FICHE PRODUCTION N° " . $doc_generate->getNumeroDocfp() . " généré par " . $user->getPrenomsUtilisateur() . " ". $user->getNomUtilisateur();

                        $document_type = $doc_generate->getTypeDocument()->getDenomination();

                        //Génétion des pages du document BRH

                        $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                        foreach ($pages_doc_stock as $page){
                            $page_fp= new Pagefp();
                            $page_fp->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                            $page_fp->setCodeDocfp($doc_generate);
                            $page_fp->setNumeroPagefp($page->getNumpage());
                            $page_fp->setCreatedAt(new \DateTime());
                            $page_fp->setCreatedBy($doc_generate->getCreatedBy());
                            $page_fp->setIndexPage($page->getNumeroPage());
                            $page_fp->setCodeGeneration($page);

                            $registry->getManager()->persist($page_fp);
                            //dd($page_cp);

                            //Mise à jour de la page stock
                            $page->setAttribue(true);
                            $registry->getManager()->persist($page);


                            $this->logger->info($this->translator->trans("Page No ". $page_fp->getNumeroPagefp() . " " . " from document BRH No ". $doc_generate->getNumeroDocfp(). " has been created by ". $doc_generate->getCreatedBy()));
                        }

                        $registry->getManager()->flush();
                        $ref = "app_op_docbtgu";


                        $reponse = "DOCUMENT BTGU AND PAGES BTGU GENERATED";
                        $this->addFlash("success", $this->translator->trans($reponse));

                        //Routine pour la génération de l'ETAT E et ses pages depuis le Stock
                    } else if ($type_doc_stock->getId() == 8){
                        $doc_generate = new Documentetate();
                        $usine = $registry->getRepository(Usine::class)->find($id_usine);
                        $doc_generate->setCodeUsine($usine);
                        $doc_generate->setNumeroDocetate($doc_stock->getNumeroDoc());
                        $doc_generate->setDelivreDocetate($delivre_doc);
                        $doc_generate->setExercice($exercice_en_cours);
                        $doc_generate->setCreatedAt($majDoc);
                        $doc_generate->setCreatedBy($user);
                        $doc_generate->setTypeDocument($type_doc_stock);
                        $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                        $doc_generate->setCodeGeneration($doc_stock);
                        $doc_generate->setCodeDemande($demande);
                        $doc_generate->setTransmission(false);
                        $doc_generate->setSignatureDr(false);
                        $doc_generate->setSignatureCef(false);

                        $registry->getManager()->persist($doc_generate);
                        $description_log = "Document ETAT E N° " . $doc_generate->getNumeroDocetate() . " généré par " . $user->getPrenomsUtilisateur() . " ". $user->getNomUtilisateur();

                        $document_type = $doc_generate->getTypeDocument()->getDenomination();

                        //Génétion des pages du document ETAT E

                        $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                        foreach ($pages_doc_stock as $page){
                            $page_etate= new Pageetate();
                            $page_etate->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                            $page_etate->setCodeDocetate($doc_generate);
                            $page_etate->setNumeroPageetate($page->getNumpage());
                            $page_etate->setCreatedAt(new \DateTime());
                            $page_etate->setCreatedBy($doc_generate->getCreatedBy());
                            $page_etate->setIndexPageetate($page->getNumeroPage());
                            $page_etate->setCodeGeneration($page);

                            $registry->getManager()->persist($page_etate);
                            //dd($page_cp);

                            //Mise à jour de la page stock
                            $page->setAttribue(true);
                            $registry->getManager()->persist($page);


                            $this->logger->info($this->translator->trans("Page No ". $page_etate->getNumeroPageetate() . " " . " from document BRH No ". $doc_generate->getNumeroDocetate(). " has been created by ". $doc_generate->getCreatedBy()));
                        }

                        $registry->getManager()->flush();
                        $ref = "app_op_docetate";


                        $reponse = "DOCUMENT ETAT E AND PAGES ETAT E GENERATED";
                        $this->addFlash("success", $this->translator->trans($reponse));


                        //Routine pour la génération de l'ETAT E2 et ses pages depuis le Stock
                    } else if ($type_doc_stock->getId() == 9){
                        $doc_generate = new Documentetate2();
                        $usine = $registry->getRepository(Usine::class)->find($id_usine);
                        $doc_generate->setCodeUsine($usine);
                        $doc_generate->setNumeroDocetate2($doc_stock->getNumeroDoc());
                        $doc_generate->setDelivreDocetate2($delivre_doc);
                        $doc_generate->setExercice($exercice_en_cours);
                        $doc_generate->setCreatedAt($majDoc);
                        $doc_generate->setCreatedBy($user);
                        $doc_generate->setTypeDocument($type_doc_stock);
                        $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                        $doc_generate->setCodeGeneration($doc_stock);
                        $doc_generate->setCodeDemande($demande);
                        $doc_generate->setTransmission(false);
                        $doc_generate->setSignatureDr(false);
                        $doc_generate->setSignatureCef(false);

                        $registry->getManager()->persist($doc_generate);
                        $description_log = "Document ETAT E2 N° " . $doc_generate->getNumeroDocetate2() . " généré par " . $user->getPrenomsUtilisateur() . " ". $user->getNomUtilisateur();

                        $document_type = $doc_generate->getTypeDocument()->getDenomination();

                        //Génétion des pages du document ETAT E2

                        $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                        foreach ($pages_doc_stock as $page){
                            $page_etate2= new Pageetate2();
                            $page_etate2->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                            $page_etate2->setCodeDocetate2($doc_generate);
                            $page_etate2->setNumeroPageetate2($page->getNumpage());
                            $page_etate2->setCreatedAt(new \DateTime());
                            $page_etate2->setCreatedBy($doc_generate->getCreatedBy());
                            $page_etate2->setIndexPageetate2($page->getNumeroPage());
                            $page_etate2->setCodeGeneration($page);

                            $registry->getManager()->persist($page_etate2);
                            //dd($page_cp);

                            //Mise à jour de la page stock
                            $page->setAttribue(true);
                            $registry->getManager()->persist($page);


                            $this->logger->info($this->translator->trans("Page No ". $page_etate2->getNumeroPageetate2() . " " . " from document BRH No ". $doc_generate->getNumeroDocetate2(). " has been created by ". $doc_generate->getCreatedBy()));
                        }

                        $registry->getManager()->flush();
                        $ref = "app_op_docetate2";


                        $reponse = "DOCUMENT ETAT E2 AND PAGES ETAT E2 GENERATED";
                        $this->addFlash("success", $this->translator->trans($reponse));


                        //Routine pour la génération de l'ETAT G1 et ses pages depuis le Stock
                    } else if ($type_doc_stock->getId() == 10){
                        $doc_generate = new Documentetatg();
                        $usine = $registry->getRepository(Usine::class)->find($id_usine);
                        $doc_generate->setCodeUsine($usine);
                        $doc_generate->setNumeroDocetatg($doc_stock->getNumeroDoc());
                        $doc_generate->setDelivreDocetatg($delivre_doc);
                        $doc_generate->setExercice($exercice_en_cours);
                        $doc_generate->setCreatedAt($majDoc);
                        $doc_generate->setCreatedBy($user);
                        $doc_generate->setTypeDocument($type_doc_stock);
                        $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                        $doc_generate->setCodeGeneration($doc_stock);
                        $doc_generate->setCodeDemande($demande);
                        $doc_generate->setTransmission(false);
                        $doc_generate->setSignatureDr(false);
                        $doc_generate->setSignatureCef(false);

                        $registry->getManager()->persist($doc_generate);
                        $description_log = "Document ETAT G1 N° " . $doc_generate->getNumeroDocetatg() . " généré par " . $user->getPrenomsUtilisateur() . " ". $user->getNomUtilisateur();

                        $document_type = $doc_generate->getTypeDocument()->getDenomination();

                        //Génétion des pages du document ETAT G1

                        $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                        foreach ($pages_doc_stock as $page){
                            $page_etatg= new Pageetatg();
                            $page_etatg->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                            $page_etatg->setCodeDocetatg($doc_generate);
                            $page_etatg->setNumeroPageetatg($page->getNumpage());
                            $page_etatg->setCreatedAt(new \DateTime());
                            $page_etatg->setCreatedBy($doc_generate->getCreatedBy());
                            $page_etatg->setIndexPageetag($page->getNumeroPage());
                            $page_etatg->setCodeGeneration($page);

                            $registry->getManager()->persist($page_etatg);
                            //dd($page_cp);

                            //Mise à jour de la page stock
                            $page->setAttribue(true);
                            $registry->getManager()->persist($page);


                            $this->logger->info($this->translator->trans("Page No ". $page_etatg->getNumeroPageetatg() . " " . " from document BRH No ". $doc_generate->getNumeroDocetatg(). " has been created by ". $doc_generate->getCreatedBy()));
                        }

                        $registry->getManager()->flush();
                        $ref = "app_op_docetatg";


                        $reponse = "DOCUMENT ETAT G1 AND PAGES ETAT G1 GENERATED";
                        $this->addFlash("success", $this->translator->trans($reponse));


                        //Routine pour la génération de l'ETAT H et ses pages depuis le Stock
                    } else if ($type_doc_stock->getId() == 11){
                        $doc_generate = new Documentetath();
                        $usine = $registry->getRepository(Usine::class)->find($id_usine);
                        $doc_generate->setCodeUsine($usine);
                        $doc_generate->setNumeroDocetath($doc_stock->getNumeroDoc());
                        $doc_generate->setDelivreDocetath($delivre_doc);
                        $doc_generate->setExercice($exercice_en_cours);
                        $doc_generate->setCreatedAt($majDoc);
                        $doc_generate->setCreatedBy($user);
                        $doc_generate->setTypeDocument($type_doc_stock);
                        $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                        $doc_generate->setCodeGeneration($doc_stock);
                        $doc_generate->setCodeDemande($demande);
                        $doc_generate->setTransmission(false);
                        $doc_generate->setSignatureDr(false);
                        $doc_generate->setSignatureCef(false);

                        $registry->getManager()->persist($doc_generate);
                        $description_log = "Document ETAT H N° " . $doc_generate->getNumeroDocetath() . " généré par " . $user->getPrenomsUtilisateur() . " ". $user->getNomUtilisateur();

                        $document_type = $doc_generate->getTypeDocument()->getDenomination();

                        //Génération des pages du document ETAT H

                        $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                        foreach ($pages_doc_stock as $page){
                            $page_etath= new Pageetath();
                            $page_etath->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                            $page_etath->setCodeDocetath($doc_generate);
                            $page_etath->setNumeroPageetath($page->getNumpage());
                            $page_etath->setCreatedAt(new \DateTime());
                            $page_etath->setCreatedBy($doc_generate->getCreatedBy());
                            $page_etath->setIndexPageetath($page->getNumeroPage());
                            $page_etath->setCodeGeneration($page);

                            $registry->getManager()->persist($page_etath);
                            //dd($page_cp);

                            //Mise à jour de la page stock
                            $page->setAttribue(true);
                            $registry->getManager()->persist($page);


                            $this->logger->info($this->translator->trans("Page No ". $page_etath->getNumeroPageetath() . " " . " from document BRH No ". $doc_generate->getNumeroDocetath(). " has been created by ". $doc_generate->getCreatedBy()));
                        }

                        $registry->getManager()->flush();
                        $ref = "app_op_docetath";


                        $reponse = "DOCUMENT ETAT H AND PAGES ETAT H GENERATED";
                        $this->addFlash("success", $this->translator->trans($reponse));


                        //Routine pour la génération de DMP et ses pages depuis le Stock
                    } else if ($type_doc_stock->getId() == 12){
                        $doc_generate = new Documentdmp();
                        $usine = $registry->getRepository(Usine::class)->find($id_usine);
                        $doc_generate->setCodeUsine($usine);
                        $doc_generate->setNumeroDocdmp($doc_stock->getNumeroDoc());
                        $doc_generate->setDelivreDocdmp($delivre_doc);
                        $doc_generate->setExercice($exercice_en_cours);
                        $doc_generate->setCreatedAt($majDoc);
                        $doc_generate->setCreatedBy($user);
                        $doc_generate->setTypeDocument($type_doc_stock);
                        $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                        $doc_generate->setCodeGeneration($doc_stock);
                        $doc_generate->setCodeDemande($demande);
                        $doc_generate->setTransmission(false);
                        $doc_generate->setSignatureDr(false);
                        $doc_generate->setSignatureCef(false);

                        $registry->getManager()->persist($doc_generate);
                        $description_log = "Document DMP N° " . $doc_generate->getNumeroDocdmp() . " généré par " . $user->getPrenomsUtilisateur() . " ". $user->getNomUtilisateur();

                        $document_type = $doc_generate->getTypeDocument()->getDenomination();

                        //Génétion des pages du document DMP

                        $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                        foreach ($pages_doc_stock as $page){
                            $page_dmp= new Pagedmp();
                            $page_dmp->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                            $page_dmp->setCodeDocdmp($doc_generate);
                            $page_dmp->setNumeroPagedmp($page->getNumpage());
                            $page_dmp->setCreatedAt(new \DateTime());
                            $page_dmp->setCreatedBy($doc_generate->getCreatedBy());
                            $page_dmp->setIndexPagedmp($page->getNumeroPage());
                            $page_dmp->setCodeGeneration($page);

                            $registry->getManager()->persist($page_dmp);
                            //dd($page_cp);

                            //Mise à jour de la page stock
                            $page->setAttribue(true);
                            $registry->getManager()->persist($page);


                            $this->logger->info($this->translator->trans("Page No ". $page_dmp->getNumeroPagedmp() . " " . " from document BRH No ". $doc_generate->getNumeroDocdmp(). " has been created by ". $doc_generate->getCreatedBy()));
                        }

                        $registry->getManager()->flush();
                        $ref = "app_op_docdmp";


                        $reponse = "DOCUMENT DMP AND PAGES DMP GENERATED";
                        $this->addFlash("success", $this->translator->trans($reponse));


                        //Routine pour la génération de DMV et ses pages depuis le Stock
                    } else if ($type_doc_stock->getId() == 13){
                        $doc_generate = new Documentdmv();
                        $usine = $registry->getRepository(Usine::class)->find($id_usine);
                        $doc_generate->setCodeUsine($usine);
                        $doc_generate->setNumeroDocdmv($doc_stock->getNumeroDoc());
                        $doc_generate->setDelivreDocdmv($delivre_doc);
                        $doc_generate->setExercice($exercice_en_cours);
                        $doc_generate->setCreatedAt($majDoc);
                        $doc_generate->setCreatedBy($user);
                        $doc_generate->setTypeDocument($type_doc_stock);
                        $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                        $doc_generate->setCodeGeneration($doc_stock);
                        $doc_generate->setCodeDemande($demande);
                        $doc_generate->setTransmission(false);
                        $doc_generate->setSignatureDr(false);
                        $doc_generate->setSignatureCef(false);

                        $registry->getManager()->persist($doc_generate);
                        $description_log = "Document DMV N° " . $doc_generate->getNumeroDocdmv() . " généré par " . $user->getPrenomsUtilisateur() . " ". $user->getNomUtilisateur();

                        $document_type = $doc_generate->getTypeDocument()->getDenomination();

                        //Génétion des pages du document DMV

                        $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                        foreach ($pages_doc_stock as $page){
                            $page_dmv= new Pagedmv();
                            $page_dmv->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                            $page_dmv->setCodeDocdmv($doc_generate);
                            $page_dmv->setNumeroPagedmv($page->getNumpage());
                            $page_dmv->setCreatedAt(new \DateTime());
                            $page_dmv->setCreatedBy($doc_generate->getCreatedBy());
                            $page_dmv->setIndexPagedmv($page->getNumeroPage());
                            $page_dmv->setCodeGeneration($page);

                            $registry->getManager()->persist($page_dmv);
                            //dd($page_cp);

                            //Mise à jour de la page stock
                            $page->setAttribue(true);
                            $registry->getManager()->persist($page);


                            $this->logger->info($this->translator->trans("Page No ". $page_dmv->getNumeroPagedmv() . " " . " from document BRH No ". $doc_generate->getNumeroDocdmv(). " has been created by ". $doc_generate->getCreatedBy()));
                        }

                        $registry->getManager()->flush();
                        $ref = "app_op_docdmv";


                        $reponse = "DOCUMENT DMV AND PAGES DMV GENERATED";
                        $this->addFlash("success", $this->translator->trans($reponse));


                        //Routine pour la génération de DOCUMENTS BREPF et ses pages depuis le Stock
                    } else if ($type_doc_stock->getId() == 19){
                        $doc_generate = new Documentbrepf();
                        $auto_export = $registry->getRepository(AutorisationExportateur::class)->find($id_auto_export);
                        $doc_generate->setCodeAutorisationExportateur($auto_export);
                        $doc_generate->setNumeroDocbrepf($doc_stock->getNumeroDoc());
                        $doc_generate->setDelivreDocbrepf($delivre_doc);
                        $doc_generate->setExercice($exercice_en_cours);
                        $doc_generate->setCreatedAt($majDoc);
                        $doc_generate->setCreatedBy($user);
                        $doc_generate->setTypeDocument($type_doc_stock);
                        $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                        $doc_generate->setCodeGeneration($doc_stock);
                        $doc_generate->setCodeDemande($demande);
                        $doc_generate->setTransmission(false);
                        $doc_generate->setSignatureDr(false);
                        $doc_generate->setSignatureCef(false);

                        $registry->getManager()->persist($doc_generate);
                        $description_log = "Document BREPF N° " . $doc_generate->getNumeroDocbrepf() . " généré par " . $user->getPrenomsUtilisateur() . " ". $user->getNomUtilisateur();

                        $document_type = $doc_generate->getTypeDocument()->getDenomination();

                        //Génétion des pages du document BREPF

                        $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                        foreach ($pages_doc_stock as $page){
                            $page_pdtdrv= new Pagebrepf();
                            $page_pdtdrv->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                            $page_pdtdrv->setCodeDocbrepf($doc_generate);
                            $page_pdtdrv->setNumeroPagebrepf($page->getNumpage());
                            $page_pdtdrv->setCreatedAt(new \DateTime());
                            $page_pdtdrv->setCreatedBy($doc_generate->getCreatedBy());
                            $page_pdtdrv->setIndexPage($page->getNumeroPage());
                            $page_pdtdrv->setCodeGeneration($page);

                            $registry->getManager()->persist($page_pdtdrv);
                            //dd($page_cp);

                            //Mise à jour de la page stock
                            $page->setAttribue(true);
                            $registry->getManager()->persist($page);


                            $this->logger->info($this->translator->trans("Page No ". $page_pdtdrv->getNumeroPagebrepf() . " " . " from document BRH No ". $doc_generate->getNumeroDocbrepf(). " has been created by ". $doc_generate->getCreatedBy()));
                        }

                        $registry->getManager()->flush();
                        $ref = "app_op_docbrepf";


                        $reponse = "DOCUMENT BREPF AND PAGES BREPF GENERATED";
                        $this->addFlash("success", $this->translator->trans($reponse));

                        //Routine pour la génération de BTH et ses pages depuis le Stock
                    } else if ($type_doc_stock->getId() == 14){
                        $doc_generate = new Documentbth();
                        $auto_export = $registry->getRepository(AutorisationExportateur::class)->find($id_auto_export);
                        $doc_generate->setCodeAutorisationExportateur($auto_export);
                        $doc_generate->setNumeroDocbth($doc_stock->getNumeroDoc());
                        $doc_generate->setDelivreDocbth($delivre_doc);
                        $doc_generate->setExercice($exercice_en_cours);
                        $doc_generate->setCreatedAt($majDoc);
                        $doc_generate->setCreatedBy($user);
                        $doc_generate->setTypeDocument($type_doc_stock);
                        $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                        $doc_generate->setCodeGeneration($doc_stock);
                        $doc_generate->setCodeDemande($demande);
                        $doc_generate->setTransmission(false);
                        $doc_generate->setSignatureDr(false);
                        $doc_generate->setSignatureCef(false);

                        $registry->getManager()->persist($doc_generate);
                        $description_log = "Document BTH N° " . $doc_generate->getNumeroDocbth() . " généré par " . $user->getPrenomsUtilisateur() . " ". $user->getNomUtilisateur();

                        $document_type = $doc_generate->getTypeDocument()->getDenomination();

                        //Génétion des pages du document PRODUITS DERIVES

                        $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                        foreach ($pages_doc_stock as $page){
                            $page_pdtdrv= new Pagebth();
                            $page_pdtdrv->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                            $page_pdtdrv->setCodeDocbth($doc_generate);
                            $page_pdtdrv->setNumeroPagebth($page->getNumpage());
                            $page_pdtdrv->setCreatedAt(new \DateTime());
                            $page_pdtdrv->setCreatedBy($doc_generate->getCreatedBy());
                            $page_pdtdrv->setIndexPage($page->getNumeroPage());
                            $page_pdtdrv->setCodeGeneration($page);

                            $registry->getManager()->persist($page_pdtdrv);
                            //dd($page_cp);

                            //Mise à jour de la page stock
                            $page->setAttribue(true);
                            $registry->getManager()->persist($page);


                            $this->logger->info($this->translator->trans("Page No ". $page_pdtdrv->getNumeroPagebth() . " " . " from document BRH No ". $doc_generate->getNumeroDocbth(). " has been created by ". $doc_generate->getCreatedBy()));
                        }

                        $registry->getManager()->flush();
                        $ref = "app_op_docbth";


                        $reponse = "DOCUMENT BTH AND PAGES BTH GENERATED";
                        $this->addFlash("success", $this->translator->trans($reponse));

                        //Routine pour la génération de PRODUITS DERIVES et ses pages depuis le Stock
                    } else if ($type_doc_stock->getId() == 15){
                        $doc_generate = new Documentpdtdrv();
                        $usine = $registry->getRepository(Usine::class)->find($id_usine);
                        //$doc_generate->setCodeUsine($usine);
                        $doc_generate->setNumeroDocpdtdrv($doc_stock->getNumeroDoc());
                        $doc_generate->setDelivreDocpdtdrv($delivre_doc);
                        $doc_generate->setExercice($exercice_en_cours);
                        $doc_generate->setCreatedAt($majDoc);
                        $doc_generate->setCreatedBy($user);
                        $doc_generate->setTypeDocument($type_doc_stock);
                        $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                        $doc_generate->setCodeGeneration($doc_stock);
                        $doc_generate->setCodeDemande($demande);
                        $doc_generate->setTransmission(false);
                        $doc_generate->setSignatureDr(false);
                        $doc_generate->setSignatureCef(false);

                        $registry->getManager()->persist($doc_generate);
                        $description_log = "Document PRODUITS DERIVES N° " . $doc_generate->getNumeroDocpdtdrv() . " généré par " . $user->getPrenomsUtilisateur() . " ". $user->getNomUtilisateur();

                        $document_type = $doc_generate->getTypeDocument()->getDenomination();

                        //Génétion des pages du document PRODUITS DERIVES

                        $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                        foreach ($pages_doc_stock as $page){
                            $page_pdtdrv= new Pagepdtdrv();
                            $page_pdtdrv->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                            $page_pdtdrv->setCodeDocpdtdrv($doc_generate);
                            $page_pdtdrv->setNumeroPagepdtdrv($page->getNumpage());
                            $page_pdtdrv->setCreatedAt(new \DateTime());
                            $page_pdtdrv->setCreatedBy($doc_generate->getCreatedBy());
                            $page_pdtdrv->setIndexPagepdtdrv($page->getNumeroPage());
                            $page_pdtdrv->setCodeGeneration($page);

                            $registry->getManager()->persist($page_pdtdrv);
                            //dd($page_cp);

                            //Mise à jour de la page stock
                            $page->setAttribue(true);
                            $registry->getManager()->persist($page);


                            $this->logger->info($this->translator->trans("Page No ". $page_pdtdrv->getNumeroPagepdtdrv() . " " . " from document BRH No ". $doc_generate->getNumeroDocpdtdrv(). " has been created by ". $doc_generate->getCreatedBy()));
                        }

                        $registry->getManager()->flush();
                        $ref = "app_op_docpdtdrv";


                        $reponse = "DOCUMENT PRODUITS DERIVES AND PAGES PRODUITS DERIVES GENERATED";
                        $this->addFlash("success", $this->translator->trans($reponse));

                    //Routine pour la génération de BCBURB et ses pages depuis le Stock
                    } else if ($type_doc_stock->getId() == 18){
                        $doc_generate = new Documentbcburb();
                        $auto_ps = $registry->getRepository(AutorisationPs::class)->find($id_auto_ps);
                        $doc_generate->setPermis($auto_ps);
                        $doc_generate->setNumeroDocbcburb($doc_stock->getNumeroDoc());
                        $doc_generate->setDelivreDocbcburb($delivre_doc);
                        $doc_generate->setExercice($exercice_en_cours);
                        $doc_generate->setCreatedAt($majDoc);
                        $doc_generate->setCreatedBy($user);
                        $doc_generate->setTypeDocument($type_doc_stock);
                        $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                        $doc_generate->setCodeGeneration($doc_stock);
                        $doc_generate->setCodeDemande($demande);
                        $doc_generate->setTransmission(false);
                        $doc_generate->setSignatureDr(false);
                        $doc_generate->setSignatureCef(false);

                        $registry->getManager()->persist($doc_generate);
                        $description_log = "Document PRODUITS DERIVES N° " . $doc_generate->getNumeroDocbcburb() . " généré par " . $user->getPrenomsUtilisateur() . " ". $user->getNomUtilisateur();

                        $document_type = $doc_generate->getTypeDocument()->getDenomination();

                        //Génétion des pages du document BCBURB

                        $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                        foreach ($pages_doc_stock as $page){
                            $page_pdtdrv= new Pagebcburb();
                            $page_pdtdrv->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                            $page_pdtdrv->setCodeDocbcburb($doc_generate);
                            $page_pdtdrv->setNumeroPage($page->getNumpage());
                            $page_pdtdrv->setCreatedAt(new \DateTime());
                            $page_pdtdrv->setCreatedBy($doc_generate->getCreatedBy());
                            $page_pdtdrv->setIndexPage($page->getNumeroPage());
                            $page_pdtdrv->setCodeGeneration($page);

                            $registry->getManager()->persist($page_pdtdrv);
                            //dd($page_cp);

                            //Mise à jour de la page stock
                            $page->setAttribue(true);
                            $registry->getManager()->persist($page);


                            $this->logger->info($this->translator->trans("Page No ". $page_pdtdrv->getNumeroPage() . " " . " from document BRH No ". $doc_generate->getNumeroDocbcburb(). " has been created by ". $doc_generate->getCreatedBy()));
                        }

                        $registry->getManager()->flush();
                        $ref = "app_op_docbcburb";


                        $reponse = "DOCUMENT BCBURB AND PAGES BCBURB GENERATED";
                        $this->addFlash("success", $this->translator->trans($reponse));

                        //Routine pour la génération de RSDPF et ses pages depuis le Stock
                    } else if ($type_doc_stock->getId() == 20){
                        $doc_generate = new Documentrsdpf();
                        $commercant = $registry->getRepository(Commercant::class)->find($id_commercant);
                        //$doc_generate->setCodeUsine($usine);
                        $doc_generate->setNumeroDocrsdpf($doc_stock->getNumeroDoc());
                        $doc_generate->setDelivreDocresdpf($delivre_doc);
                        $doc_generate->setExercice($exercice_en_cours);
                        $doc_generate->setCreatedAt($majDoc);
                        $doc_generate->setCreatedBy($user);
                        $doc_generate->setTypeDocument($type_doc_stock);
                        $doc_generate->setUniqueDoc($doc_stock->getUniqueDoc());
                        $doc_generate->setCodeGeneration($doc_stock);
                        $doc_generate->setCodeDemande($demande);
                        $doc_generate->setTransmission(false);
                        $doc_generate->setSignatureDr(false);
                        $doc_generate->setSignatureCef(false);

                        $registry->getManager()->persist($doc_generate);
                        $description_log = "Document RSDPF N° " . $doc_generate->getNumeroDocrsdpf() . " généré par " . $user->getPrenomsUtilisateur() . " ". $user->getNomUtilisateur();

                        $document_type = $doc_generate->getTypeDocument()->getDenomination();

                        //Génétion des pages du document PRODUITS DERIVES

                        $pages_doc_stock = $registry->getRepository(PageDocGen::class)->findBy(['code_doc_gen'=>$doc_stock]);

                        foreach ($pages_doc_stock as $page){
                            $page_pdtdrv= new Pagersdpf();
                            $page_pdtdrv->setUniqueDoc($doc_generate->getUniqueDoc().$page->getNumpage());
                            $page_pdtdrv->setCodeDocrsdpf($doc_generate);
                            $page_pdtdrv->setNumeroPage($page->getNumpage());
                            $page_pdtdrv->setCreatedAt(new \DateTime());
                            $page_pdtdrv->setCreatedBy($doc_generate->getCreatedBy());
                            $page_pdtdrv->setIndexPage($page->getNumeroPage());
                            $page_pdtdrv->setCodeGeneration($page);

                            $registry->getManager()->persist($page_pdtdrv);
                            //dd($page_cp);

                            //Mise à jour de la page stock
                            $page->setAttribue(true);
                            $registry->getManager()->persist($page);


                            $this->logger->info($this->translator->trans("Page No ". $page_pdtdrv->getNumeroPage() . " " . " from document BRH No ". $doc_generate->getNumeroDocrsdpf(). " has been created by ". $doc_generate->getCreatedBy()));
                        }

                        $registry->getManager()->flush();
                        $ref = "app_op_docrsdpf";


                        $reponse = "DOCUMENT RSDPF AND PAGES RSDPF GENERATED";
                        $this->addFlash("success", $this->translator->trans($reponse));


                    }

                    // Mise à jour du document stock
                    $doc_stock->setAttribue(true);
                    $registry->getManager()->persist($doc_stock);

                    //Mise à jour de la demande
                    $demande->setDocsGeneres(true);
                    $demande->setQteValidee($nb_definitif);

                    $registry->getManager()->persist($demande);
                    $registry->getManager()->persist($doc_stock);
                    $registry->getManager()->flush();

                    $this->administrationService->save_action(
                        $user,
                        "GENERATION DOCUMENT",
                        "ENVOI",
                        new \DateTimeImmutable(),
                        $description_log);

                    /*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                                                                                            Envoi de notification à l'opérateur
                    /*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
                    /*--*/
                    /*--*/          /*--*/  /*-------Entêtes et Civilités --------*/
                    /*--*/          $sujet = $this->translator->trans("Document Request");
                    /*--*/          $salutation =$this->translator->trans("Hi")." ". $Responsable[0]->getPrenomsUtilisateur(). " ".$Responsable[0]->getNomUtilisateur()." \n\n";
                    /*--*/          $description = $salutation." ".$this->translator->trans("Your document ") . $document_type . $this->translator->trans(" has been generated to you succesfully. You can collect it at the documents request officer").$this->translator->trans("Cheers!");
                    /*--*/
                    /*--*/          /*-------Envoi notification SNVLT --------*/
                    /*--*/          $user = $this->getUser();
                    /*--*/          $this->utils->envoiNotification(
                    /*--*/             $registry,
                    /*--*/             $sujet,
                    /*--*/            $description,
                    /*--*/             $Responsable[0],
                    /*--*/             $user->getId(),
                    /*--*/             $ref,
                    /*--*/             "DOCUMENT REQUEST",
                    /*--*/             $user->getId()
                    /*--*/          );
                    /*--*/
                    /*--*/          /*-------Envoi notification par mail --------*/
                    /*--*/             $this->utils->sendEmail($Responsable[0]->getEmail(), $sujet, $description);
                    /*--*/
                    /*--*/          /*-------Envoi notification par SMS --------*/
                    /*--*/             /*$this->sendSMS->messagerie($Responsable[0]->getMobile(),$description);*/
                    /*------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
                    /*----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------*/
                    return $this->redirectToRoute('app_demande_retrieve');
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/demop/search/{code_demande}', name: 'demande.search')]
    public function searchDemande(
        ManagerRegistry $doctrine,
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        DemandeOperateurRepository $demandes,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        NotificationRepository $notification,
        GroupeRepository $groupeRepository,
        string $code_demande
    ): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                    $liste_demande = array();
                    $demandes = $doctrine->getRepository(DemandeOperateur::class)->findBy(['code'=>$code_demande, 'statut'=>'APPROUVE', 'transmission'=>false,'docs_generes'=>true ]);
                    $operateur = "";
                    $cantonnement = "";
                    foreach ($demandes as $demande){
                        if($demande->getCodeOperateur()->getId() == 2){
                            if($demande->getDocStat()->getId() == 4){
                                $exp = $doctrine->getRepository(Exploitant::class)->find($demande->getCodeStructure());
                                //dd($exp);
                                $operateur =  $exp->getRaisonSocialeExploitant(). " - Marteau : " . $exp->getMarteauExploitant() . " - Code : " . $exp->getNumeroExploitant();
                                $cantonnement = $exp->getCodeCantonnement()->getNomCantonnement();
                            } else {
                                $operateur = $demande->getDemandeur()->getCodeexploitant()->getRaisonSocialeExploitant(). " - Marteau : " . $demande->getDemandeur()->getCodeexploitant()->getMarteauExploitant() . " - Code : " . $demande->getDemandeur()->getCodeexploitant()->getNumeroExploitant();
                                $cantonnement = $demande->getDemandeur()->getCodeexploitant()->getCodeCantonnement()->getNomCantonnement();
                            }

                        } elseif($demande->getCodeOperateur()->getId() == 3){
                            $operateur = $demande->getCodeUsine()->getRaisonSocialeUsine(). " - Code : " . $demande->getCodeUsine()->getNumeroUsine();
                            $cantonnement = $demande->getCodeUsine()->getCodeCantonnement()->getNomCantonnement();
                        } elseif($demande->getCodeOperateur()->getId() == 4){
                        $operateur = $demande->getCodeExportateur()->getRaisonSocialeExportateur(). " - Code : " . $demande->getCodeExportateur()->getCodeExportateur();
                            $cantonnement = $demande->getCodeExportateur()->getCodeCantonnement()->getNomCantonnement();
                        }


                            $liste_demande[] =array(
                            'id_demande'=>$demande->getId(),
                            'type_operateur'=>$demande->getCodeOperateur()->getLibelleOperateur(),
                            'code_demande'=>$demande->getCode(),
                            'operateur'=>$operateur,
                            'cantonnement'=>$cantonnement,
                            'document_demande'=>$demande->getDocStat()->getAbv(),
                            'qte_demandee'=>$demande->getQte(),
                            'qte_accordee'=>$demande->getQteValidee()
                        );

                    }
                     return new JsonResponse(json_encode($liste_demande));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/demop/search/docs/{id_demande}', name: 'demande.docs')]
    public function DocDemande(
        ManagerRegistry $doctrine,
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        DemandeOperateurRepository $demandes,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        NotificationRepository $notification,
        GroupeRepository $groupeRepository,
        string $id_demande
    ): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                $liste_document = array();
                $documents = null;
                $demande = $doctrine->getRepository(DemandeOperateur::class)->find($id_demande);
                if ($demande){

                    //Recherche la liste des documents par demande
                    if ($demande->getDocStat()->getId() == 1){
                        $documents = $doctrine->getRepository(Documentcp::class)->findBy(['code_demande'=>$demande]);

                        foreach ($documents as $document){
                                $liste_document[] =array(
                                    'id_doc'=>$document->getId(),
                                    'numero_doc'=>$document->getNumeroDoccp()
                                );
                            }

                    } elseif ($demande->getDocStat()->getId() == 2){
                        $documents = $doctrine->getRepository(Documentbrh::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);

                                foreach ($documents as $document){
                                    $liste_document[] =array(
                                        'id_doc'=>$document->getId(),
                                        'numero_doc'=>$document->getNumeroDocbrh()
                                    );
                                }

                    } elseif ($demande->getDocStat()->getId() == 4){
                        $documents = $doctrine->getRepository(Documentetatb::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);

                        foreach ($documents as $document){
                            $liste_document[] =array(
                                'id_doc'=>$document->getId(),
                                'numero_doc'=>$document->getNumeroDocetatb()
                            );
                        }

                    } elseif ($demande->getDocStat()->getId() == 5){
                        $documents = $doctrine->getRepository(Documentlje::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);

                                foreach ($documents as $document){
                                    $liste_document[] =array(
                                        'id_doc'=>$document->getId(),
                                        'numero_doc'=>$document->getNumeroDoclje()
                                    );
                                }

                    }elseif ($demande->getDocStat()->getId() == 6){
                        $documents = $doctrine->getRepository(Documentbtgu::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);

                                foreach ($documents as $document){
                                    $liste_document[] =array(
                                        'id_doc'=>$document->getId(),
                                        'numero_doc'=>$document->getNumeroDocbtgu()
                                    );
                                }

                    }elseif ($demande->getDocStat()->getId() == 7){
                        $documents = $doctrine->getRepository(Documentfp::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);

                                foreach ($documents as $document){
                                    $liste_document[] =array(
                                        'id_doc'=>$document->getId(),
                                        'numero_doc'=>$document->getNumeroDocfp()
                                    );
                                }
                    } elseif ($demande->getDocStat()->getId() == 19){
                        $documents = $doctrine->getRepository(Documentbrepf::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);

                        foreach ($documents as $document){
                            $liste_document[] =array(
                                'id_doc'=>$document->getId(),
                                'numero_doc'=>$document->getNumeroDocbrepf()
                            );
                        }

                    } elseif ($demande->getDocStat()->getId() == 14){
                        $documents = $doctrine->getRepository(Documentbth::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);

                        foreach ($documents as $document){
                            $liste_document[] =array(
                                'id_doc'=>$document->getId(),
                                'numero_doc'=>$document->getNumeroDocbth()
                            );
                        }

                    }

                    return new JsonResponse(json_encode($liste_document));

                } else {
                    return new JsonResponse(json_encode(false));
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/demop/print_doc/sheet/{id_demande}', name: 'print_document_sheet')]
    public function print_document_sheet(
        ManagerRegistry $doctrine,
        Request $request,
        MenuRepository $menus,
        CircuitCommunicationRepository $circuitCommunicationRepository,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_demande,
        NotificationRepository $notification,
        DemandeOperateurRepository $demandes): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $demande = $doctrine->getRepository(DemandeOperateur::class)->find($id_demande);
                $documents = null;
                $operateur =  "";
                $cantonnement = "";
                if ($demande){

                    if ($demande->getDocStat()->getId() == 1){
                        $documents = $doctrine->getRepository(Documentcp::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);
                        foreach ($documents as $document){
                            $document->setTransmission(true);
                            $document->setEtat(true);
                            $doctrine->getManager()->persist($document);
                            $doctrine->getManager()->flush();

                            $this->administrationService->save_action(
                                $user,
                                "DOCUMENT CP",
                                "AJOUT",
                                new \DateTimeImmutable(),
                                "Document CP N° ". $document->getNumeroDoccp(). " généré par ". $user. " pour le compte de ". $demande->getDemandeur()->getCodeExploitant()->getRaisonSocialeExploitant()
                            );
                        }

                    } elseif ($demande->getDocStat()->getId() == 2){
                        $documents = $doctrine->getRepository(Documentbrh::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);
                        foreach ($documents as $document){
                            $document->setTransmission(true);
                            $document->setEtat(true);
                            $doctrine->getManager()->persist($document);
                            $doctrine->getManager()->flush();

                            $this->administrationService->save_action(
                                $user,
                                "DOCUMENT BRH",
                                "AJOUT",
                                new \DateTimeImmutable(),
                                "Document BRH N° ". $document->getNumeroDocbrh(). " généré par ". $user. " pour le compte de ". $demande->getDemandeur()->getCodeExploitant()->getRaisonSocialeExploitant()
                            );
                        }

                    } elseif ($demande->getDocStat()->getId() == 3){
                        $documents = $doctrine->getRepository(Documentbcbp::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);
                        foreach ($documents as $document){
                            $document->setTransmission(true);
                            $document->setEtat(true);
                            $doctrine->getManager()->persist($document);
                            $doctrine->getManager()->flush();

                            $this->administrationService->save_action(
                                $user,
                                "DOCUMENT BCBP",
                                "AJOUT",
                                new \DateTimeImmutable(),
                                "Document BCBP N° ". $document->getNumeroDocbcbp(). " généré par ". $user. " pour le compte de ". $demande->getCodeAutorisationPv()->getCodeExploitant()->getRaisonSocialeExploitant()
                            );
                        }

                    }  elseif ($demande->getDocStat()->getId() == 4){
                        $documents = $doctrine->getRepository(Documentetatb::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);
                        foreach ($documents as $document){
                            $document->setTransmission(true);
                            $document->setEtat(true);
                            $doctrine->getManager()->persist($document);
                            $doctrine->getManager()->flush();

                            $this->administrationService->save_action(
                                $user,
                                "DOCUMENT ETAT B",
                                "AJOUT",
                                new \DateTimeImmutable(),
                                "Document ETAT B N° ". $document->getNumeroDocetatb(). " généré par ". $user. " pour le compte de ". $document->getCodeExploitant()->getRaisonSocialeExploitant()
                            );
                        }

                    } elseif ($demande->getDocStat()->getId() == 5){
                        $documents = $doctrine->getRepository(Documentlje::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);
                        foreach ($documents as $document){
                            $document->setTransmission(true);
                            $document->setEtat(true);
                            $doctrine->getManager()->persist($document);
                            $doctrine->getManager()->flush();

                            $this->administrationService->save_action(
                                $user,
                                "DOCUMENT LJE",
                                "AJOUT",
                                new \DateTimeImmutable(),
                                "Document LJE  N° ". $document->getNumeroDoclje(). " généré par ". $user. " pour le compte de ". $demande->getCodeUsine()->getRaisonSocialeUsine()
                            );
                        }

                    }elseif ($demande->getDocStat()->getId() == 6){
                        $documents = $doctrine->getRepository(Documentbtgu::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);
                        foreach ($documents as $document){
                            $document->setTransmission(true);
                            $document->setEtat(true);
                            $doctrine->getManager()->persist($document);
                            $doctrine->getManager()->flush();

                            $this->administrationService->save_action(
                                $user,
                                "DOCUMENT BTGU",
                                "AJOUT",
                                new \DateTimeImmutable(),
                                "Document BTGU  N° ". $document->getNumeroDocbtgu(). " généré par ". $user. " pour le compte de ". $demande->getCodeUsine()->getRaisonSocialeUsine()
                            );
                        }

                    }elseif ($demande->getDocStat()->getId() == 7){
                        $documents = $doctrine->getRepository(Documentfp::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);
                        foreach ($documents as $document){
                            $document->setTransmission(true);
                            $document->setEtat(true);
                            $doctrine->getManager()->persist($document);
                            $doctrine->getManager()->flush();

                            $this->administrationService->save_action(
                                $user,
                                "DOCUMENT FP",
                                "AJOUT",
                                new \DateTimeImmutable(),
                                "Document FP  N° ". $document->getNumeroDocfp(). " généré par ". $user. " pour le compte de ". $demande->getCodeUsine()->getRaisonSocialeUsine()
                            );
                        }

                    } elseif ($demande->getDocStat()->getId() == 8){
                        $documents = $doctrine->getRepository(Documentetate::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);
                        foreach ($documents as $document){
                            $document->setTransmission(true);
                            $document->setEtat(true);
                            $doctrine->getManager()->persist($document);
                            $doctrine->getManager()->flush();

                            $this->administrationService->save_action(
                                $user,
                                "DOCUMENT ETAT E",
                                "AJOUT",
                                new \DateTimeImmutable(),
                                "Document ETAT E  N° ". $document->getNumeroDocetate(). " généré par ". $user. " pour le compte de ". $demande->getCodeUsine()->getRaisonSocialeUsine()
                            );
                        }

                    }  elseif ($demande->getDocStat()->getId() == 9){
                        $documents = $doctrine->getRepository(Documentetate2::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);
                        foreach ($documents as $document){
                            $document->setTransmission(true);
                            $document->setEtat(true);
                            $doctrine->getManager()->persist($document);
                            $doctrine->getManager()->flush();

                            $this->administrationService->save_action(
                                $user,
                                "DOCUMENT ETAT E2",
                                "AJOUT",
                                new \DateTimeImmutable(),
                                "Document ETAT E2  N° ". $document->getNumeroDocetate2(). " généré par ". $user. " pour le compte de ". $demande->getCodeUsine()->getRaisonSocialeUsine()
                            );
                        }

                    }  elseif ($demande->getDocStat()->getId() == 10){
                        $documents = $doctrine->getRepository(Documentetatg::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);
                        foreach ($documents as $document){
                            $document->setTransmission(true);
                            $document->setEtat(true);
                            $doctrine->getManager()->persist($document);
                            $doctrine->getManager()->flush();

                            $this->administrationService->save_action(
                                $user,
                                "DOCUMENT ETAT G1",
                                "AJOUT",
                                new \DateTimeImmutable(),
                                "Document ETAT G1  N° ". $document->getNumeroDocetatg(). " généré par ". $user. " pour le compte de ". $demande->getCodeUsine()->getRaisonSocialeUsine()
                            );
                        }

                    }  elseif ($demande->getDocStat()->getId() == 11){
                        $documents = $doctrine->getRepository(Documentetath::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);
                        foreach ($documents as $document){
                            $document->setTransmission(true);
                            $document->setEtat(true);
                            $doctrine->getManager()->persist($document);
                            $doctrine->getManager()->flush();

                            $this->administrationService->save_action(
                                $user,
                                "DOCUMENT ETAT H",
                                "AJOUT",
                                new \DateTimeImmutable(),
                                "Document ETAT H  N° ". $document->getNumeroDocetath(). " généré par ". $user. " pour le compte de ". $demande->getCodeUsine()->getRaisonSocialeUsine()
                            );
                        }

                    }  elseif ($demande->getDocStat()->getId() == 12){
                        $documents = $doctrine->getRepository(Documentdmp::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);
                        foreach ($documents as $document){
                            $document->setTransmission(true);
                            $document->setEtat(true);
                            $doctrine->getManager()->persist($document);
                            $doctrine->getManager()->flush();

                            $this->administrationService->save_action(
                                $user,
                                "DOCUMENT DMP",
                                "AJOUT",
                                new \DateTimeImmutable(),
                                "Document DMP  N° ". $document->getNumeroDocdmp(). " généré par ". $user. " pour le compte de ". $demande->getCodeUsine()->getRaisonSocialeUsine()
                            );
                        }

                    }  elseif ($demande->getDocStat()->getId() == 13){
                        $documents = $doctrine->getRepository(Documentdmv::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);
                        foreach ($documents as $document){
                            $document->setTransmission(true);
                            $document->setEtat(true);
                            $doctrine->getManager()->persist($document);
                            $doctrine->getManager()->flush();

                            $this->administrationService->save_action(
                                $user,
                                "DOCUMENT DMV",
                                "AJOUT",
                                new \DateTimeImmutable(),
                                "Document DMV  N° ". $document->getNumeroDocdmv(). " généré par ". $user. " pour le compte de ". $demande->getCodeUsine()->getRaisonSocialeUsine()
                            );
                        }

                    }  elseif ($demande->getDocStat()->getId() == 14){
                        $documents = $doctrine->getRepository(Documentbth::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);
                        foreach ($documents as $document){
                            $document->setTransmission(true);
                            $document->setEtat(true);
                            $doctrine->getManager()->persist($document);
                            $doctrine->getManager()->flush();

                            $this->administrationService->save_action(
                                $user,
                                "DOCUMENT BTH",
                                "AJOUT",
                                new \DateTimeImmutable(),
                                "Document BTH  N° ". $document->getNumeroDocbth(). " généré par ". $user. " pour le compte de ". $demande->getCodeExportateur()->getRaisonSocialeExportateur()
                            );
                        }

                    } elseif ($demande->getDocStat()->getId() == 15 ){
                        $documents = $doctrine->getRepository(Documentpdtdrv::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);
                        foreach ($documents as $document){
                            $document->setTransmission(true);
                            $document->setEtat(true);
                            $doctrine->getManager()->persist($document);
                            $doctrine->getManager()->flush();

                            $this->administrationService->save_action(
                                $user,
                                "DOCUMENT PRODUITS DERIVES",
                                "AJOUT",
                                new \DateTimeImmutable(),
                                "Document PRODUITS DERIVES  N° ". $document->getNumeroDocpdtdrv(). " généré par ". $user. " pour le compte de ". $demande->getCodeUsine()->getRaisonSocialeUsine()
                            );
                        }
                    }  elseif ($demande->getDocStat()->getId() == 18 ){
                        $documents = $doctrine->getRepository(Documentbcburb::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);
                        foreach ($documents as $document){
                            $document->setTransmission(true);
                            $document->setEtat(true);
                            $doctrine->getManager()->persist($document);
                            $doctrine->getManager()->flush();

                            $this->administrationService->save_action(
                                $user,
                                "DOCUMENT BCBURB",
                                "AJOUT",
                                new \DateTimeImmutable(),
                                "Document BCBURB  N° ". $document->getNumeroDocbcburb(). " généré par ". $user. " pour le compte de ". $demande->getCodeCommercant()->getPrenoms(). " " . $demande->getCodeCommercant()->getNom()
                            );
                        }
                    }elseif ($demande->getDocStat()->getId() == 19 ){
                        $documents = $doctrine->getRepository(Documentbrepf::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);
                        foreach ($documents as $document){
                            $document->setTransmission(true);
                            $document->setEtat(true);
                            $doctrine->getManager()->persist($document);
                            $doctrine->getManager()->flush();

                            $this->administrationService->save_action(
                                $user,
                                "DOCUMENT BREPF",
                                "AJOUT",
                                new \DateTimeImmutable(),
                                "Document BREPF  N° ". $document->getNumeroDocbrepf(). " généré par ". $user. " pour le compte de ". $demande->getCodeExportateur()->getRaisonSocialeExportateur()
                            );
                        }
                    }elseif ($demande->getDocStat()->getId() == 20 ){
                            $documents = $doctrine->getRepository(Documentrsdpf::class)->findBy(['code_demande'=>$demande, 'transmission'=>false]);
                            foreach ($documents as $document){
                                $document->setTransmission(true);
                                $document->setEtat(true);
                                $doctrine->getManager()->persist($document);
                                $doctrine->getManager()->flush();

                                $this->administrationService->save_action(
                                    $user,
                                    "DOCUMENT RSDPF",
                                    "AJOUT",
                                    new \DateTimeImmutable(),
                                    "Document RSDPF  N° ". $document->getNumeroDoclje(). " généré par ". $user. " pour le compte de ". $demande->getCodeCommercant()->getPrenoms(). " " . $demande->getCodeCommercant()->getNom()
                                );
                            }
                        }


                    $demande->setTransmission(true);
                    $dateDelivrance = new \DateTime();
                    $demande->setDateDelivrance($dateDelivrance);
                    $demande->setDelivrance(true);


                    $doctrine->getManager()->flush();

                    //Envoi de Notifications au Responsables DR et CANTONNEMENT

                    $emailCantonnement = "";
                    $emailDr = "";
                    $sujet = "";
                    if($demande->getCodeOperateur()->getId() == 2){
                                            if ($demande->getDocStat()->getId() == 4 ){
                                                        $operateur = $demande->getDemandeur()->getCodeexploitant()->getRaisonSocialeExploitant(). " - Marteau : " . $demande->getDemandeur()->getCodeexploitant()->getMarteauExploitant() . " - Code : " . $demande->getDemandeur()->getCodeexploitant()->getNumeroExploitant();
                                                        $cantonnement = $demande->getDemandeur()->getCodeexploitant()->getCodeCantonnement()->getNomCantonnement();
                                                    } else {
                                                        $operateur = $demande->getDemandeur()->getCodeExploitant()->getRaisonSocialeExploitant(). " - Marteau : " . $demande->getDemandeur()->getCodeExploitant()->getMarteauExploitant() . " - Code : " . $demande->getDemandeur()->getCodeExploitant()->getNumeroExploitant();
                                                        $cantonnement = $demande->getDemandeur()->getCodeExploitant()->getCodeCantonnement()->getNomCantonnement();
                                            }

                                            $emailCantonnement = $demande->getDemandeur()->getCodeexploitant()->getCodeCantonnement()->getEmailPersonneRessource();
                                            $emailDr = $demande->getDemandeur()->getCodeexploitant()->getCodeCantonnement()->getCodeDr()->getEmailPersonneRessource();

                                            $sujet = "DEMANDE ". $demande->getDocStat()->getDenomination() . " [". $demande->getDemandeur()->getCodeexploitant()->getRaisonSocialeExploitant() . "]";

                                } elseif($demande->getCodeOperateur()->getId() == 3){
                                            $operateur = $demande->getCodeUsine()->getRaisonSocialeUsine(). " - Code : " . $demande->getCodeUsine()->getNumeroUsine();
                                            $cantonnement = $demande->getCodeUsine()->getCodeCantonnement()->getNomCantonnement();

                                            $emailCantonnement = $demande->getDemandeur()->getCodeindustriel()->getCodeCantonnement()->getEmailPersonneRessource();
                                            $emailDr = $demande->getDemandeur()->getCodeindustriel()->getCodeCantonnement()->getCodeDr()->getEmailPersonneRessource();

                                            $sujet = "DEMANDE ". $demande->getDocStat()->getDenomination() . " [". $demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine() . "]";

                                } elseif($demande->getCodeOperateur()->getId() == 4){
                                            $operateur = $demande->getCodeExportateur()->getRaisonSocialeExportateur(). " - Code : " . $demande->getCodeExportateur()->getCodeExportateur();
                                            $cantonnement = $demande->getCodeExportateur()->getCodeCantonnement()->getNomCantonnement();

                                            $emailCantonnement = $demande->getDemandeur()->getCodeExportateur()->getCodeCantonnement()->getEmailPersonneRessource();
                                            $emailDr = $demande->getDemandeur()->getCodeExportateur()->getCodeCantonnement()->getCodeDr()->getEmailPersonneRessource();

                                            $sujet = "DEMANDE ". $demande->getDocStat()->getDenomination() . " [". $demande->getDemandeur()->getCodeExportateur()->getRaisonSocialeExportateur() . "]";

                                } elseif($demande->getCodeOperateur()->getId() == 8){
                                            $operateur = $demande->getCodeCommercant()->getPrenoms(). " " . $demande->getCodeCommercant()->getNom() . " - Code : " . $demande->getCodeCommercant()->getNumeroCommercant();
                                            $cantonnement = $demande->getCodeCommercant()->getCodeCantonnement()->getNomCantonnement();

                                            $emailCantonnement = $demande->getDemandeur()->getCodeCommercant()->getCodeCantonnement()->getEmailPersonneRessource();
                                            $emailDr = $demande->getDemandeur()->getCodeCommercant()->getCodeCantonnement()->getCodeDr()->getEmailPersonneRessource();

                                            $sujet = "DEMANDE ". $demande->getDocStat()->getDenomination() . " [". $demande->getDemandeur()->getCodeCommercant()->getPrenoms(). " " . $demande->getDemandeur()->getCodeCommercant()->getNom() . "]";

                                }

                                $cef_nom = $doctrine->getRepository(User::class)->findOneBy(['email'=>$emailCantonnement]);
                                $dr_nom = $doctrine->getRepository(User::class)->findOneBy(['email'=>$emailDr]);

                                $description_notif_cef = "Bonjour " . $cef_nom . " Vous avez reçu " . $demande->getQteDelivree() . " " . $demande->getDocStat()->getDenomination() . "(s) pour signature.";
                                $description_notif_dr = "Bonjour " . $dr_nom . " Vous avez reçu " . $demande->getQteDelivree() . " " . $demande->getDocStat()->getDenomination() . "(s) pour signature.";
                                //dd($dr_nom);
                                //Notif Cantonnement

                                $this->utils->envoiNotification(
                                    $doctrine,
                                    $sujet,
                                    $description_notif_cef,
                                    $cef_nom,
                                    $user->getId(),
                                    "validation_signature_doc",
                                    "DOCUMENT OPERATEUR",
                                    $demande->getId()
                                );

                                //Notif DR
                                $this->utils->envoiNotification(
                                    $doctrine,
                                    $sujet,
                                    $description_notif_dr,
                                    $dr_nom,
                                    $user->getId(),
                                    "validation_signature_doc",
                                    "DOCUMENT OPERATEUR",
                                    $demande->getId()
                                );

                    return $this->render('administration/demande_operateur/delivrance_print.twig',
                        [
                            'demande'=>$demande,
                            'documents'=>$documents,
                            'operateur'=>$operateur,
                            'cantonnement'=>$cantonnement
                        ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
       }
   }

       #[Route('/snvlt/demop/sign/{id_notification?0}', name: 'validation_signature_doc')]
       public function signature_doc_generated(
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
               if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF')) {
                   $notification = $notifications->find($id_notification);
                   if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF')) {
                       $user = $userRepository->find($this->getUser());
                       $code_groupe = $user->getCodeGroupe()->getId();

                       //dd($notification->getRelatedToId());
                       $demande = $registry->getRepository(DemandeOperateur::class)->find($notification->getRelatedToId());
                   if ($demande) {

                       if ($user->getCodeCantonnement() or $user->getCodeDr()){

                               return $this->render('administration/demande_operateur/signature.html.twig',
                                   [
                                       'liste_menus'=>$menus->findOnlyParent(),
                                       "all_menus"=>$menus->findAll(),
                                       'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                                       'mes_notifs'=>$notifications->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                                       'groupe'=>$code_groupe,
                                       'demande'=>$demande,
                                       'liste_parent'=>$permissions
                                   ]);
                       } else {
                       return $this->redirectToRoute('app_no_permission_user_active');
                   }
               } else {
                       return new JsonResponse(json_encode(false));
                   }


                   }

               }else {
                   return $this->redirectToRoute('app_no_permission_user_active');
               }

       }
    }

    #[Route('snvlt/demop/sign/cef/{id_demande?0}', name: 'validation_signature_cef_doc')]
    public function signature_cef_doc_generated(
        ManagerRegistry $registry,
        Request $request,
        int $id_demande,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF')) {
                $demande = $registry->getRepository(DemandeOperateur::class)->find($id_demande);
                if ($demande) {
                    $user = $userRepository->find($this->getUser());
                    $code_groupe = $user->getCodeGroupe()->getId();

                    $demande->setSignatureCef(true);
                    $registry->getManager()->persist($demande);
                    $registry->getManager()->flush();;

                    // Mise à jour des documents générés
                    $details = "";
                    if($demande->getDocStat()->getId() == 14){
                        $mes_docs = $registry->getRepository(Documentbth::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureCef(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocbth();
                        }

                        $operateur = "[EXPORTATEUR]".$demande->getCodeExportateur()->getRaisonSocialeExportateur();

                    } elseif($demande->getDocStat()->getId() == 1){
                        $mes_docs = $registry->getRepository(Documentcp::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureCef(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDoccp();
                        }

                        $operateur = "[EXPLOITANT]".$demande->getDemandeur()->getCodeexploitant()->getRaisonSocialeExploitant();
                    } elseif($demande->getDocStat()->getId() == 2){
                        $mes_docs = $registry->getRepository(Documentbrh::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureCef(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocbrh();
                        }

                        $operateur = "[EXPLOITANT]".$demande->getDemandeur()->getCodeexploitant()->getRaisonSocialeExploitant();
                    } elseif($demande->getDocStat()->getId() == 3){
                        $mes_docs = $registry->getRepository(Documentbcbp::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureCef(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocbcbp();
                        }

                        $operateur = "[EXPLOITANT]".$demande->getDemandeur()->getCodeexploitant()->getRaisonSocialeExploitant();
                    } elseif($demande->getDocStat()->getId() == 4){
                        $mes_docs = $registry->getRepository(Documentetatb::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureCef(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocetatb();
                        }

                        $operateur = "[EXPLOITANT]".$demande->getDemandeur()->getCodeexploitant()->getRaisonSocialeExploitant();
                    } elseif($demande->getDocStat()->getId() == 5){
                        $mes_docs = $registry->getRepository(Documentlje::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureCef(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDoclje();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 6){
                        $mes_docs = $registry->getRepository(Documentbtgu::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureCef(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocbtgu();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 7){
                        $mes_docs = $registry->getRepository(Documentfp::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureCef(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocfp();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 8){
                        $mes_docs = $registry->getRepository(Documentetate::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureCef(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocetate();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 9){
                        $mes_docs = $registry->getRepository(Documentetate2::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureCef(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocetate2();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 10){
                        $mes_docs = $registry->getRepository(Documentetatg::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureCef(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocetatg();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 11){
                        $mes_docs = $registry->getRepository(Documentetath::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureCef(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocetath();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 12){
                        $mes_docs = $registry->getRepository(Documentdmp::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureCef(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocdmp();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 13){
                        $mes_docs = $registry->getRepository(Documentdmv::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureCef(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocdmv();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 15){
                        $mes_docs = $registry->getRepository(Documentpdtdrv::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureCef(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocpdtdrv();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 18){
                        $mes_docs = $registry->getRepository(Documentbcburb::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureCef(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocbcburb();
                        }

                        $operateur = "[COMMERCANT]".$demande->getDemandeur()->getCodeCommercant()->getPrenoms() . " " . $demande->getDemandeur()->getCodeCommercant()->getNom();
                    } elseif($demande->getDocStat()->getId() == 19){
                        $mes_docs = $registry->getRepository(Documentbrepf::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureCef(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocbrepf();
                        }

                        $operateur = "[EXPORTATEUR]".$demande->getCodeExportateur()->getRaisonSocialeExportateur();
                    } elseif($demande->getDocStat()->getId() == 20){
                        $mes_docs = $registry->getRepository(Documentrsdpf::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureCef(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocrsdpf();
                        }

                        $operateur = "[COMMERCANT]".$demande->getDemandeur()->getCodeCommercant()->getPrenoms() . " " . $demande->getDemandeur()->getCodeCommercant()->getNom();
                    }


                    //Log
                    $this->administrationService->save_action(
                        $user,
                        "DEMANDE OPERATEUR",
                        "MODIFICATION",
                        new \DateTimeImmutable(),
                        "les documents ". $demande->getDocStat()->getAbv() . " issus de la demande envoyé par l'opérateur " . $operateur . " ont été validés par l'agent " . $user . " du Cantonnement forestier " . $user->getCodeCantonnement()->getNomCantonnement(). ". Détails Documents : ". $details
                    );
                   return new JsonResponse(json_encode(true));
                } else {
                    return new JsonResponse(json_encode(false));
                }
            }else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/demop/sign/dr/{id_demande?0}', name: 'validation_signature_dr_doc')]
    public function signature_dr_doc_generated(
        ManagerRegistry $registry,
        Request $request,
        int $id_demande,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF')) {
                $demande = $registry->getRepository(DemandeOperateur::class)->find($id_demande);
                if ($demande) {
                    $user = $userRepository->find($this->getUser());
                    $code_groupe = $user->getCodeGroupe()->getId();

                    $demande->setSignatureDr(true);
                    $registry->getManager()->persist($demande);
                    $registry->getManager()->flush();;

                    // Mise à jour des documents générés
                    $details = "";
                    if($demande->getDocStat()->getId() == 14){
                        $mes_docs = $registry->getRepository(Documentbth::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureDr(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocbth();
                        }

                        $operateur = "[EXPORTATEUR]".$demande->getCodeExportateur()->getRaisonSocialeExportateur();

                    } elseif($demande->getDocStat()->getId() == 1){
                        $mes_docs = $registry->getRepository(Documentcp::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureDr(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDoccp();
                        }

                        $operateur = "[EXPLOITANT]".$demande->getDemandeur()->getCodeexploitant()->getRaisonSocialeExploitant();
                    } elseif($demande->getDocStat()->getId() == 2){
                        $mes_docs = $registry->getRepository(Documentbrh::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureDr(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocbrh();
                        }

                        $operateur = "[EXPLOITANT]".$demande->getDemandeur()->getCodeexploitant()->getRaisonSocialeExploitant();
                    } elseif($demande->getDocStat()->getId() == 3){
                        $mes_docs = $registry->getRepository(Documentbcbp::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureDr(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocbcbp();
                        }

                        $operateur = "[EXPLOITANT]".$demande->getDemandeur()->getCodeexploitant()->getRaisonSocialeExploitant();
                    } elseif($demande->getDocStat()->getId() == 4){
                        $mes_docs = $registry->getRepository(Documentetatb::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureDr(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocetatb();
                        }

                        $operateur = "[EXPLOITANT]".$demande->getDemandeur()->getCodeexploitant()->getRaisonSocialeExploitant();
                    } elseif($demande->getDocStat()->getId() == 5){
                        $mes_docs = $registry->getRepository(Documentlje::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureDr(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDoclje();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 6){
                        $mes_docs = $registry->getRepository(Documentbtgu::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureDr(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocbtgu();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 7){
                        $mes_docs = $registry->getRepository(Documentfp::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureDr(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocfp();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 8){
                        $mes_docs = $registry->getRepository(Documentetate::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureDr(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocetate();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 9){
                        $mes_docs = $registry->getRepository(Documentetate2::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureDr(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocetate2();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 10){
                        $mes_docs = $registry->getRepository(Documentetatg::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureDr(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocetatg();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 11){
                        $mes_docs = $registry->getRepository(Documentetath::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureDr(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocetath();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 12){
                        $mes_docs = $registry->getRepository(Documentdmp::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureDr(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocdmp();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 13){
                        $mes_docs = $registry->getRepository(Documentdmv::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureDr(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocdmv();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 15){
                        $mes_docs = $registry->getRepository(Documentpdtdrv::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureDr(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocpdtdrv();
                        }

                        $operateur = "[INDUSTRIEL]".$demande->getDemandeur()->getCodeindustriel()->getRaisonSocialeUsine();
                    } elseif($demande->getDocStat()->getId() == 18){
                        $mes_docs = $registry->getRepository(Documentbcburb::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureDr(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocbcburb();
                        }

                        $operateur = "[COMMERCANT]".$demande->getDemandeur()->getCodeCommercant()->getPrenoms() . " " . $demande->getDemandeur()->getCodeCommercant()->getNom();
                    } elseif($demande->getDocStat()->getId() == 19){
                        $mes_docs = $registry->getRepository(Documentbrepf::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureDr(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocbrepf();
                        }

                        $operateur = "[EXPORTATEUR]".$demande->getCodeExportateur()->getRaisonSocialeExportateur();
                    } elseif($demande->getDocStat()->getId() == 20){
                        $mes_docs = $registry->getRepository(Documentrsdpf::class)->findBy(['code_demande'=>$demande]);
                        foreach($mes_docs as $doc){
                            $doc->setSignatureDr(true);
                            $registry->getManager()->persist($doc);
                            $registry->getManager()->flush();;

                            $details = $details . " /" . $doc->getNumeroDocrsdpf();
                        }

                        $operateur = "[COMMERCANT]".$demande->getDemandeur()->getCodeCommercant()->getPrenoms() . " " . $demande->getDemandeur()->getCodeCommercant()->getNom();
                    }


                    //Log
                    $this->administrationService->save_action(
                        $user,
                        "DEMANDE OPERATEUR",
                        "MODIFICATION",
                        new \DateTimeImmutable(),
                        "les documents ". $demande->getDocStat()->getAbv() . " issus de la demande envoyé par l'opérateur " . $operateur . " ont été validés par l'agent " . $user . " de la Direction Régionale " . $user->getCodeDr()->getDenomination(). ". Détails Documents : ". $details
                    );
                    return new JsonResponse(json_encode(true));
                } else {
                    return new JsonResponse(json_encode(false));
                }
            }else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

}
