<?php

namespace App\Controller\Administration;

use App\Controller\Services\Utils;
use App\Entity\Administration\Notification;
use App\Entity\Groupe;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DemandeOperateurRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ValidationAdhesionController extends AbstractController
{
    public function __construct(
        private TranslatorInterface $translator,
        private ManagerRegistry $managerRegistry,
        private LoggerInterface $logger,
        private EmailVerifier $emailVerifier,
        private Utils $utils)
    {
    }

    #[Route('snvlt/admin/validation/adh/{id_notification}', name: 'app_administration_validation_adhesion')]
    public function index(Request $request,
                          MenuRepository $menus,
                          MenuPermissionRepository $permissions,
                          GroupeRepository $groupeRepository,
                          UserRepository $userRepository,
                          Notification $notifEntity = null,
                          ManagerRegistry $registry,
                          User $user = null,
                          User $userFrom = null,
                          int $id_notification,
                          NotificationRepository $notification,
                          DemandeOperateurRepository $demandes): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {

                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $notifEntity = $notification->find($id_notification);
                $userFromId = (int) $notifEntity->getFromUser();
                $userFrom = $registry->getRepository(User::class)->find($userFromId);

                $notifEntity->setLu(true);


                $registry->getManager()->persist($notifEntity);
                $registry->getManager()->flush();
        }
        return $this->render('administration/validation_adhesion/index.html.twig', [
            'liste_menus'=>$menus->findOnlyParent(),
            "all_menus"=>$menus->findAll(),
            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
            'groupe'=>$code_groupe,
            'liste_parent'=>$permissions,
            'notif_en_cours'=>$notifEntity,
            'sender'=>$userFrom
        ]);
    }

    #[Route('snvlt/admin/notifs/all', name: 'app_notifs')]
    public function liste_notifications(Request $request,
                          MenuRepository $menus,
                          MenuPermissionRepository $permissions,
                          GroupeRepository $groupeRepository,
                          UserRepository $userRepository,
                          ManagerRegistry $registry,
                          User $user = null,
                          NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {

            $user = $userRepository->find($this->getUser());
            $code_groupe = $user->getCodeGroupe()->getId();
            $membre = $registry->getRepository(User::class);

        }
        return $this->render('administration/validation_adhesion/all.html.twig', [
            'liste_menus'=>$menus->findOnlyParent(),
            "all_menus"=>$menus->findAll(),
            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],['created_at'=>'DESC'],5,0),
            'all_notifs'=>$notification->findBy(['to_user'=>$user],['created_at'=>'DESC']),
            'groupe'=>$code_groupe,
            'liste_parent'=>$permissions,
            'recherche_user'=>$membre
        ]);
    }

    #[Route('snvlt/admin/validation/adh/add/{id_notification}', name: 'app_administration_validation_adhesion_add')]
    public function save_user(
        int $id_notification,
        User $user = null,
        ManagerRegistry $registry,
        Request $request,
        NotificationRepository $notification
    ):Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            $notifEntity = $notification->find($id_notification);
            $userFromId = (int) $notifEntity->getFromUser();
            $userFrom = $registry->getRepository(User::class)->find($userFromId);

            $userFrom->setStatut(true);
            $userFrom->setActif(true);
            $userFrom->setNouveau(true);
            $userFrom->setIsVerified(true);
            //$userFrom->setIsVerified(true);

           /* $code_groupe = 0;
            if ($notifEntity->getToUser()->getCodeOperateur()->getId() == 2){
                $code_groupe = 2;

            }elseif ($notifEntity->getToUser()->getCodeOperateur()->getId() == 3){
                $code_groupe = 4;
            }elseif ($notifEntity->getToUser()->getCodeOperateur()->getId() == 4){
                $code_groupe = 6;
            }elseif ($notifEntity->getToUser()->getCodeOperateur()->getId() == 5){
                $code_groupe = 8;
            }elseif ($notifEntity->getToUser()->getCodeOperateur()->getId() == 6){
                $code_groupe = 10;
            }elseif ($notifEntity->getToUser()->getCodeOperateur()->getId() == 7){
                $code_groupe = 12;
            }elseif ($notifEntity->getToUser()->getCodeOperateur()->getId() == 10){
                $code_groupe = 14;
            }

            $userFrom->setCodeGroupe($registry->getRepository(Groupe::class)->find($code_groupe));*/

            $registry->getManager()->persist($userFrom);
            $registry->getManager()->flush();


            // generate a signed url and email it to the user

            $sujet = "Félicitation, vous avez été accepté dans SNVLT!";

            $this->utils->sendEmail(
                $userFrom->getEmail(),
                $sujet,
                "Vous avez été accepté par votre administrateur. Merci de vous connecter"
            );

            /*
            $this->emailVerifier->sendEmailRespoConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('snvlt@system2is.com', 'SNVLT INFOS'))
                    ->to($user->getEmail())
                    ->subject($sujet)
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );*/

            $this->addFlash('success', $this->translator->trans('The requester has been added to your organization'));

            return $this->redirectToRoute('app_my_users');
        }
    }

    #[Route('snvlt/admin/validation/notif_lu/{id_notification}', name: 'app_notif_lu')]
    public function lu_notif(
        int $id_notification,
        User $user = null,
        Notification $notification = null,
        ManagerRegistry $registry,
        Request $request,
        NotificationRepository $notifications,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        DemandeOperateurRepository $demandes

    ):Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {

                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                if ($id_notification){
                    $notification = $notifications->find($id_notification);
                        if($notification){

                            $notification->setLu(true);
                            $registry->getManager()->persist($notification);
                            $registry->getManager()->flush();

                            $this->logger->info('Notification readed');

                            /////////////////////////////////////////////////////////////////
                            /// /////////////////////////////////////////////////////////////
                            ///    TARGET A DEFINIR PAR TYPE DE NOTIFICATION            /////
                            ///     REVOIR LES TARGETS                                  /////
                            /// /////////////////////////////////////////////////////////////

                            return new JsonResponse(json_encode('Notification readed'));
                        } else {

                            $this->logger->info('Problem detected in Notification readed');
                            return new JsonResponse(json_encode('Problem detected in Notification readed'));
                        }

                } else {

                    $this->logger->info('Problem detected in Notification readed');
                    return new JsonResponse(json_encode('Problem detected in Notification readed'));
                }



        }
    }
}
