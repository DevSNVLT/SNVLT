<?php

namespace App\Controller\Administration\ValidationCircuit;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\Utils;
use App\Entity\Administration\Notification;
use App\Entity\References\CircuitCommunication;
use App\Entity\References\Exploitant;
use App\Entity\References\Exportateur;
use App\Entity\References\Usine;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\CircuitCommunicationRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ValidationCircuitDemandeController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator, private Utils $utils, private AdministrationService $administrationService)
    {

    }
    #[Route('/validate/circuit/request/{id_notification?0}', name: 'app_validation_circuit_demande')]
    public function index(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        UserRepository $userRepository,
        User $user = null,
        Notification $notification = null,
        CircuitCommunication $communication = null,
        CircuitCommunicationRepository $communicationRepository = null,
        int $id_notification,
        NotificationRepository $notificationRepository,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $infoOperator = [];
                $notification = $notificationRepository->find($id_notification);
                $communication = new CircuitCommunication();
                if($notification){

                    if ($notification->getRelatedToEntity() && $notification->getRelatedToId() && !$notification->isCloture()){
                        /*$notification->getRelatedToId() est le ID du circuit*/
                        $communication = $communicationRepository->find($notification->getRelatedToId());
                        $cantonnement = "-";
                        if ($communication->getCodeDemandeOperateur()->getCodeOperateur()->getId() == 2){
                            $nomCantonnement = $communication->getCodeDemandeOperateur()->getDemandeur()->getCodeexploitant()->getCodeCantonnement();
                            if($nomCantonnement){
                                $cantonnement = $nomCantonnement->getNomCantonnement();
                            }
                            $infoOperator = [
                                'rs'=>$registry->getRepository(Exploitant::class)->find($communication->getCodeDemandeOperateur()->getDemandeur()->getCodeexploitant())->getRaisonSocialeExploitant(),
                                'code'=>$registry->getRepository(Exploitant::class)->find($communication->getCodeDemandeOperateur()->getDemandeur()->getCodeexploitant())->getNumeroExploitant(),
                                'cantonnement'=>$cantonnement
                            ];
                        } elseif($communication->getCodeDemandeOperateur()->getCodeOperateur()->getId() == 3){
                            $nomCantonnement = $communication->getCodeDemandeOperateur()->getDemandeur()->getCodeindustriel()->getCodeCantonnement();
                            if($nomCantonnement){
                                $cantonnement = $nomCantonnement->getNomCantonnement();
                            }
                            $infoOperator = [
                                'rs'=>$registry->getRepository(Usine::class)->find($communication->getCodeDemandeOperateur()->getDemandeur()->getCodeindustriel())->getRaisonSocialeUsine(),
                                'code'=>$registry->getRepository(Usine::class)->find($communication->getCodeDemandeOperateur()->getDemandeur()->getCodeindustriel())->getNumeroUsine(),
                                'cantonnement'=>$cantonnement
                            ];
                        }elseif($communication->getCodeDemandeOperateur()->getCodeOperateur()->getId() == 4){
                            $nomCantonnement = $communication->getCodeDemandeOperateur()->getDemandeur()->getCodeExportateur()->getCodeCantonnement();
                            if($nomCantonnement){
                                $cantonnement = $nomCantonnement->getNomCantonnement();
                            }
                            $infoOperator = [
                                'rs'=>$registry->getRepository(Exportateur::class)->find($communication->getCodeDemandeOperateur()->getDemandeur()->getCodeExportateur())->getRaisonSocialeExportateur(),
                                'code'=>$registry->getRepository(Exportateur::class)->find($communication->getCodeDemandeOperateur()->getDemandeur()->getCodeExportateur())->getCodeExportateur(),
                                'cantonnement'=>$cantonnement
                            ];
                        }

                        //dd($communication);
                        return $this->render('validation_circuit/circuit_demandes_docs_stats.html.twig', [
                            'notification' =>$notification,
                            'liste_menus'=>$menus->findOnlyParent(),
                            "all_menus"=>$menus->findAll(),
                            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                            'mes_notifs'=>$notificationRepository->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                            'groupe'=>$code_groupe,
                            'liste_parent'=>$permissions,
                            'circuit'=>$communication,
                            'operateur'=>$infoOperator
                        ]);
                    }else {
                        return $this->redirectToRoute('app_no_permission_user_active');
                    }

                }else {
                    return $this->redirectToRoute('app_no_permission_user_active');
                }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
    //Routine de validation du circuit
    #[Route('snvlt/validation/circuit/request/acc/{id_circuit}/{id_notification}', name: 'accept_validation_circuit_demande')]
    public function acceptValidation(
        int $id_circuit,
        int $id_notification,
        CircuitCommunication $communication = null,
        CircuitCommunicationRepository $communicationRepository = null,
        ManagerRegistry $manager,
        User $user = null,
        NotificationRepository $notifs,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        Request $request
    )
    {
        $dateMaj = new \DateTimeImmutable();



        //Vérifie la session de l'utilisateur
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            //Filtrer les rôles pour cette action
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $service_user = "";
                if($user->getCodeService() && $user->getCodeDirection()){
                    $service_user = $user->getCodeService()->getLibelleService();
                } elseif(!$user->getCodeService() && $user->getCodeDirection()) {
                    $service_user = $user->getCodeDirection()->getDenomination();
                }

                //Récupération du circuit en cours
                $communication = $communicationRepository->find($id_circuit);

                //Si le circuit existe et l'utilisateur clique sur le bouton VALIDER
                if ($communication){
                            //Persistence du circuit
                            $communication->setStatut('VALIDE');
                            $communication->setValide(true);
                            $communication->setUpdatedBy($user);
                            $communication->setUpdatedAt($dateMaj);


                            $manager->getManager()->persist($communication);



                            //Recherche le nombre d'entités i Circuits du document
                            $countCircuits = $communicationRepository->findBy(['code_demande_operateur'=>$communication->getCodeDemandeOperateur()]);
                            $i = 0;
                            foreach ($countCircuits as $circuit)
                            {
                                $i++;
                            }

                            //compare l'index circuit courant au nombre de circuits
                            if($communication->getNumSeq() < $i){
                                $operateur = "";
                                $nextCircuit = new CircuitCommunication();
                                $nextCircuit = $communicationRepository->find($communication->getId() + 1);

                                //Mise a jour du circuit suivant
                                $nextCircuit->setStatut('EN COURS');
                                $manager->getManager()->persist($nextCircuit);

                                $nextResponsable = new User();

                                //Envoi d'une notification au Responsable du service concerné
                                        //Recherche de l'email Responsable dus ervice ou de la Direction
                                        if($nextCircuit->getTypeService() == "SERVICE"){
                                            $nextResponsableEmail = $nextCircuit->getCodeService()->getEmailPersonneRessource();

                                        } else {
                                            $nextResponsableEmail = $nextCircuit->getCodeDirection()->getEmailPersonneRessource();
                                        }

                                        //Récupération de l'entité USER RESPONSABLE
                                        $nextResponsable = $manager->getRepository(User::class)->findBy(['email'=>$nextResponsableEmail])[0];

                                        //Recherche de la raison sociale de l'opérateur via le type Opérateur
                                        if($nextCircuit->getCodeDemandeOperateur()->getCodeOperateur()->getId() == 2){
                                            $operateur = $manager->getRepository(Exploitant::class)->find($nextCircuit->getCodeDemandeOperateur()->getDemandeur()->getCodeexploitant())->getRaisonSocialeExploitant();
                                        } elseif ($nextCircuit->getCodeDemandeOperateur()->getCodeOperateur()->getId()== 3){
                                            $operateur = $manager->getRepository(Usine::class)->find($nextCircuit->getCodeDemandeOperateur()->getDemandeur()->getCodeindustriel())->getRaisonSocialeUsine();
                                        }elseif ($nextCircuit->getCodeDemandeOperateur()->getCodeOperateur()->getId() == 4){
                                            $operateur = $manager->getRepository(Exportateur::class)->find($nextCircuit->getCodeDemandeOperateur()->getDemandeur()->getCodeExportateur())->getRaisonSocialeExportateur();
                                        }

                                //Envoi de la notification au responsabler
                                $this->utils->envoiNotification(
                                    $manager,
                                    $this->translator->trans("New statistical document request for validation"),
                                    $this->translator->trans("The Operator ") . $operateur .  $this->translator->trans(" has sent to you a new statistical document request. Please click on this notification to display the content."),
                                    $nextResponsable,
                                    $user->getId(),
                                    'app_validation_circuit_demande',
                                    "DOCUMENT_OPERATOR",
                                    $nextCircuit->getId()

                                );

                               //Enregistrement dans le LOG SNVLT
                                $this->administrationService->save_action(
                                    $user,
                                    "VALIDATION DEMANDE_OPERATEUR",
                                    "MODIFICATION",
                                    new \DateTimeImmutable(),
                                    "Demande de ". $communication->getCodeDemandeOperateur()->getQte() . " " . $communication->getCodeDemandeOperateur()->getDocStat()->getAbv() . " validée par " . $communication->getCodeDemandeOperateur()->getDemandeur()->getPrenomsUtilisateur(). " " . $communication->getCodeDemandeOperateur()->getDemandeur()->getNomUtilisateur() . " validée par l'agent " . $user . " [" . $service_user . "]"
                                );

                            } elseif ($communication->getNumSeq() == $i)
                            {

                                //Persistence du circuit
                                $communication->setStatut('VALIDE');
                                $communication->setValide(true);
                                $communication->setUpdatedBy($user);
                                $communication->setUpdatedAt($dateMaj);
                                $manager->getManager()->persist($communication);

                                // Mise à jour du statut Document et cloture
                                $communication->getCodeDemandeOperateur()->setStatut("APPROUVE");
                                $communication->getCodeDemandeOperateur()->setUpdatedAt($dateMaj);
                                $communication->getCodeDemandeOperateur()->setUpdatedBy($user);

                                $manager->getManager()->persist($communication);


                                //Rechercher le Responsable de la structure émettrice par type Opérateur
                                $responsableOperateur = new User();
                                $emailResponsableOperateur = "";
                                if($communication->getCodeDemandeOperateur()->getCodeOperateur()->getId()  == 2){
                                    $emailResponsableOperateur = $manager->getRepository(Exploitant::class)->find($communication->getCodeDemandeOperateur()->getDemandeur()->getCodeexploitant())->getEmailPersonneRessource();
                                }elseif($communication->getCodeDemandeOperateur()->getCodeOperateur()->getId()  == 3){
                                    $emailResponsableOperateur = $manager->getRepository(Usine::class)->find($communication->getCodeDemandeOperateur()->getDemandeur()->getCodeindustriel())->getEmailPersonneRessource();
                                }elseif($communication->getCodeDemandeOperateur()->getCodeOperateur()->getId()  ==4){
                                    $emailResponsableOperateur = $manager->getRepository(Exportateur::class)->find($communication->getCodeDemandeOperateur()->getDemandeur()->getCodeExportateur())->getEmailPersonneRessource();
                                }
                                $responsableOperateur = $manager->getRepository(User::class)->findBy(['email'=>$emailResponsableOperateur])[0];

                                //Envoi d'une notification au Responsable Opérateur
                                $this->utils->envoiNotification(
                                    $manager,
                                    $this->translator->trans("Information about your statistical document request"),
                                    $this->translator->trans("Hi ") . $responsableOperateur->getPrenomsUtilisateur(). " ". $responsableOperateur->getNomUtilisateur() .  $this->translator->trans(". Congratulations! We are pleased to tell you that your document request ".$communication->getCodeDemandeOperateur()->getDocStat()->getDenomination() . " has been accepted by the whole Ministry Administrators. Please, click on this link to view details"),
                                    $responsableOperateur,
                                    $user->getId(),
                                    'app_validation_circuit_demande',
                                    "REQUEST_OPERATOR",
                                    $communication->getId()
                                );
                                //Enregistrement dans le LOG SNVLT
                                $this->administrationService->save_action(
                                    $user,
                                    "VALIDATION DEMANDE_OPERATEUR",
                                    "MODIFICATION",
                                    new \DateTimeImmutable(),
                                    "Demande de ". $communication->getCodeDemandeOperateur()->getQte() . " " . $communication->getCodeDemandeOperateur()->getDocStat()->getAbv() . "validée et cloturée par " . $communication->getCodeDemandeOperateur()->getDemandeur()->getPrenomsUtilisateur(). " " . $communication->getCodeDemandeOperateur()->getDemandeur()->getNomUtilisateur() . " validée par l'agent " . $user . " [" . $service_user . "]"
                            );
                            }

                    //Mise à jour de la notification
                    $notification = new Notification();
                    $notif = new Notification();
                    $notification = $manager->getRepository(Notification::class)->findBy(['related_to_id'=>$communication->getId()])[0]->setLu(true);
                    $manager->getManager()->persist($notification);

                    //Recherche la notif courante et cloturer
                    $notif = $notifs->find($id_notification);

                    if($notif){
                        $notif->setCloture(true);
                        $manager->getManager()->persist($notif);
                    }

                    $manager->getManager()->flush();


                    $response = $this->translator->trans("Notification accepted by")." ". $user;



                    return new JsonResponse(json_encode($response));
                }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }


    //Routine de validation du circuit
    #[Route('snvlt/validation/circuit/request/ref/{id_circuit}/{id_notification}/{motif}', name: 'refusde_validation_circuit_demande')]
    public function refuseValidation(
        int $id_circuit,
        int $id_notification,
        string $motif,
        CircuitCommunication $communication = null,
        CircuitCommunicationRepository $communicationRepository = null,
        ManagerRegistry $manager,
        User $user = null,
        NotificationRepository $notifs,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        Request $request
    )
    {
        $dateMaj = new \DateTimeImmutable();

        //Vérifie la session de l'utilisateur
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            //Filtrer les rôles pour cette action
            if ($this->isGranted('ROLE_MINEF')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                //Récupération du circuit en cours
                $communication = $communicationRepository->find($id_circuit);

                //Si le circuit existe et l'utilisateur clique sur le bouton VALIDER
                 if ($communication){

                    //Persistence du circuit
                    $communication->setStatut('REJETTE');
                    $communication->setValide(false);
                    $communication->getCodeDemandeOperateur()->setMotifVerification($motif);

                    $communication->setUpdatedBy($user);
                    $communication->setUpdatedAt($dateMaj);
                    $manager->getManager()->persist($communication);

                    // Mise à jour du statut Document et cloture



                    $communication->getCodeDemandeOperateur()->setStatut("REJETTE");
                    $communication->getCodeDemandeOperateur()->setUpdatedAt($dateMaj);
                    $communication->getCodeDemandeOperateur()->setUpdatedBy($user);
                    $manager->getManager()->persist($communication);

                    //Rechercher le Responsable de la structure émettrice par type Opérateur
                    $responsableOperateur = new User();
                    $emailResponsableOperateur = "";
                    if($communication->getCodeDemandeOperateur()->getCodeOperateur()->getId()  == 2){
                        $emailResponsableOperateur = $manager->getRepository(Exploitant::class)->find($communication->getCodeDemandeOperateur()->getDemandeur()->getCodeexploitant())->getEmailPersonneRessource();
                    }elseif($communication->getCodeDemandeOperateur()->getCodeOperateur()->getId()  == 3){
                        $emailResponsableOperateur = $manager->getRepository(Usine::class)->find($communication->getCodeDemandeOperateur()->getDemandeur()->getCodeindustriel())->getEmailPersonneRessource();
                    }elseif($communication->getCodeDemandeOperateur()->getCodeOperateur()->getId()  ==4){
                        $emailResponsableOperateur = $manager->getRepository(Exportateur::class)->find($communication->getCodeDemandeOperateur()->getDemandeur()->getCodeExportateur())->getEmailPersonneRessource();
                    }
                    $responsableOperateur = $manager->getRepository(User::class)->findBy(['email'=>$emailResponsableOperateur])[0];

                    //Envoi d'une notification au Responsable Opérateur
                    $this->utils->envoiNotification(
                        $manager,
                        $this->translator->trans("Information about your statistical document request"),
                        $this->translator->trans("Hello ") . $responsableOperateur->getPrenomsUtilisateur(). " ". $responsableOperateur->getNomUtilisateur() .  $this->translator->trans(" We are sorry to tell you that your statistical document request ".$communication->getCodeDemandeOperateur()->getDocStat()->getDenomination() . " has been rejected by the Ministry Administrators. Please, click on this link to view details"),
                        $responsableOperateur,
                        $user->getId(),
                        'app_validation_circuit_demande',
                        "REQUEST_OPERATOR",
                        $communication->getId()
                    );

                    //Log SNVLT

                     $service_user = "";
                     if($user->getCodeService() && $user->getCodeDirection()){
                         $service_user = $user->getCodeService()->getLibelleService();
                     } elseif(!$user->getCodeService() && $user->getCodeDirection()) {
                         $service_user = $user->getCodeDirection()->getDenomination();
                     }

                     //Enregistrement dans le LOG SNVLT
                     $this->administrationService->save_action(
                         $user,
                         "VALIDATION DEMANDE_OPERATEUR",
                         "MODIFICATION",
                         new \DateTimeImmutable(),
                         "Demande de ". $communication->getCodeDemandeOperateur()->getQte() . " " . $communication->getCodeDemandeOperateur()->getDocStat()->getAbv() . " rejettée par " . $communication->getCodeDemandeOperateur()->getDemandeur()->getPrenomsUtilisateur(). " " . $communication->getCodeDemandeOperateur()->getDemandeur()->getNomUtilisateur() . " validée par l'agent " . $user . " [" . $service_user . "]"
                        );
                     //Mise à jour de la notification
                     $notification = new Notification();
                     $notif = new Notification();
                     $notification = $manager->getRepository(Notification::class)->findBy(['related_to_id'=>$communication->getId()])[0]->setLu(true);
                     $manager->getManager()->persist($notification);

                     //Recherche la motif courante et cloturer
                     $notif = $notifs->find($id_notification);

                     if($notif){
                         $notif->setCloture(true);
                         $manager->getManager()->persist($notif);
                     }

                     $manager->getManager()->flush();


                     $response = $this->translator->trans("Notification refused by")." ". $user;



                     return $this->redirectToRoute('app_tdb_admin');
                 }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
}
