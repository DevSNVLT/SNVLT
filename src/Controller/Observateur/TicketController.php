<?php

namespace App\Controller\Observateur;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\SendSMS;
use App\Controller\Services\Utils;
use App\Entity\Administration\Notification;
use App\Entity\Blog\FichierPublication;
use App\Entity\Observateur\Ticket;
use App\Entity\Observateur\TicketFiles;
use App\Entity\References\Dr;
use App\Entity\User;
use App\Form\Administration\ProfileFormType;
use App\Form\Observateur\TicketType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\Observateur\TicketRepository;
use App\Repository\References\DrRepository;
use App\Repository\UserRepository;
use App\Security\AppCustomAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\Persistence\ManagerRegistry;
use MongoDB\Driver\Manager;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TicketController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private $translator;

    public function __construct(
        TranslatorInterface $translator,
        private SluggerInterface $slugger,
        private Utils $utils,
        private AdministrationService $administrationService,
    private MailerInterface $mailer)
    {
        $this->translator = $translator;
    }
    #[Route('/observateur/tickets', name: 'app_observateur_ticket')]
    public function index(
          DrRepository $drs,
          MenuRepository $menus,
          MenuPermissionRepository $permissions,
          GroupeRepository $groupeRepository,
          ManagerRegistry $registry,
          Request $request,
          UserRepository $userRepository,
          NotificationRepository $notification,
          User $user = null
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_OI') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('observateur/ticket/index.html.twig', [
                    'ref_drs' => $drs->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    "all_menus"=>$menus->findAll(),
                    'liste_drs' => $drs->findAll(),
                    'liste_parent'=>$permissions,
                    'mes_tickets'=>$registry->getRepository(Ticket::class)->findBy(['code_oi'=>$user->getCodeOi()], ['id'=>'DESC'])
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }


    }

    #[Route('/observateur/t/details/{id_ticket?0}', name: 'details_ticket')]
    public function details_ticket(
        DrRepository $drs,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        ManagerRegistry $registry,
        Request $request,
        UserRepository $userRepository,
        NotificationRepository $notification,
        User $user = null,
        int $id_ticket
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_OI') or  $this->isGranted('ROLE_ADMIN') or  $this->isGranted('ROLE_MINEF'))
            {

                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $ticket = $registry->getRepository(Ticket::class)->find($id_ticket);

                if ($ticket){
                    return $this->render('observateur/ticket/details.html.twig', [
                        'ticket' => $ticket,
                        'liste_menus'=>$menus->findOnlyParent(),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        "all_menus"=>$menus->findAll(),
                        'liste_drs' => $drs->findAll(),
                        'liste_parent'=>$permissions
                    ]);
                } else {
                    return $this->redirectToRoute('app_no_page_found');
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }


    }

    #[Route('/observateur/t/details/notif/{id_notification?0}', name: 'details_ticket_notif')]
    public function details_ticket_notif(
        DrRepository $drs,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        ManagerRegistry $registry,
        Request $request,
        UserRepository $userRepository,
        NotificationRepository $notification,
        User $user = null,
        int $id_notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_OI') or  $this->isGranted('ROLE_ADMIN') or  $this->isGranted('ROLE_MINEF'))
            {

                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                $notif = $registry->getRepository(Notification::class)->find($id_notification);
                $ticket = $registry->getRepository(Ticket::class)->find((int) $notif->getRelatedToId());
                //dd($ticket);
                if ($ticket){
                    return $this->redirectToRoute('details_ticket',['id_ticket'=>$ticket->getId()]);

                } else {
                    return $this->redirectToRoute('app_no_page_found');
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }


    }

    #[Route('snvlt/observateur/add_tickets', name: 'add_ticket')]
    public function add_ticket (
                               ManagerRegistry $entityManager,
                               TranslatorInterface $translator,
                               Request $request,
                               MenuRepository $menus,
                               MenuPermissionRepository $permissions,
                               UserRepository $userRepository,
                               TicketRepository $ticketRepository,
                               NotificationRepository $notification,
                               ManagerRegistry $registry
                                ){

        $session = $request->getSession();
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            $user = $userRepository->find($this->getUser());
            $code_groupe = $user->getCodeGroupe()->getId();

                $titre = $translator->trans("Edit my profile");

                    $ticket = new Ticket();

                    $ticket->setCreatedAt(new \DateTime());
                    $ticket->setCreatedBy($user);

                $form = $this->createForm(TicketType::class, $ticket);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){
                    //dd($user);
                    $MajDate = new \DateTimeImmutable();



                    $ticket->setCodeOi($user->getCodeOi());
                    $ticket->setCodeUser($user);



                    $manager = $entityManager->getManager();
                    $manager->persist($ticket);

                    //Mise à jour des fichiers chargés

                    $fichiers = $form->get('fichiers')->getData();
                    //dd($fichiers);
                    foreach ($fichiers as $fichier){

                        /* $newFilename = $this->slugger->slug($fichier) . $fichier->guessExtension()/* .  md5(uniqid(). '.'.  $fichier->guessExtension()) */


                        $originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                        // this is needed to safely include the file name as part of the URL
                        $safeFilename = $this->slugger->slug($originalFilename);
                        $newFilename = $safeFilename.'-'.uniqid().'.'.$fichier->guessExtension();
                        $extension = $fichier->guessExtension();
                        // Move the file to the directory where brochures are stored

                        $fichier->move(
                            $this->getParameter('oi_tickets_directory'),
                            $newFilename
                        );

                        // updates the 'brochureFilename' property to store the PDF file name
                        // instead of its contents
                        $fichierTicket = new TicketFiles();
                        $fichierTicket->setLibelle($originalFilename);
                        $fichierTicket->setFichier($newFilename);
                        $fichierTicket->setExtension($extension);
                        $fichierTicket->setCodeTicket($ticket);
                        $manager->persist($fichierTicket);

                    }

                    $manager->flush();

                    $recipiendaires = array();

                    $drs = $ticket->getCodeDr();
                    foreach ($drs as $dr){
                        $recipiendaires[] = array(
                            'user_email'=>$dr->getEmailResponsable(),
                            'user_entity'=>$registry->getRepository(User::class)->findOneBy(['email' => $dr->getEmailPersonneRessource()])
                        );
                    }




                    // Log SNVLT
                    $this->administrationService->save_action(
                        $user,
                        'TICKET_OI',
                        'CREATION',
                        new \DateTimeImmutable(),
                        "le ticket N° " . $ticket->getId() . " [". $ticket->getSujet() . "] vient d'être créé par ". $user
                    );

                    //Envoi Email et Notifications aux différents recipiendaires

                        //-------------------  DR -----------------------------//
                                $drs = $ticket->getCodeDr();
                                foreach ($drs as $dr){
                                    //envoi mail
                                    $this->utils->envoiNotification(
                                        $registry,
                                        'Nouveau ticket SNVLT',
                                        "L'OI " . $ticket->getCodeOi()->getSigle() . " vient de signaler une situation ",
                                        $registry->getRepository(User::class)->findOneBy(['email' => $dr->getEmailPersonneRessource()]),
                                        $user->getId(),
                                        'details_ticket_notif',
                                        'TICKET OI',
                                        $ticket->getId()
                                    );

                                    $email = (new TemplatedEmail())
                                        ->from(new Address('snvlt@system2is.com', 'Snvlt Infos'))
                                        ->to($registry->getRepository(User::class)->findOneBy(['email' => $dr->getEmailPersonneRessource()])->getEmail())
                                        ->subject('Vous avez un nouveau ticket OI')
                                        ->htmlTemplate('observateur/ticket/email.html.twig')
                                        ->context([
                                            'titre'=>'Vous avez un nouveau ticket OI',
                                            'message' => "L'OI " . $ticket->getCodeOi()->getSigle() . " vient de signaler une situation ",
                                            'destinataire'=>$registry->getRepository(User::class)->findOneBy(['email' => $dr->getEmailPersonneRessource()]),
                                            'url'=>$this->generateUrl('app_login'),
                                            'ticket'=>$ticket
                                        ])
                                    ;

                                    $this->mailer->send(
                                        $email
                                    );

                                }

                    //-------------------  CEF -----------------------------//
                    $cefs = $ticket->getCodeCef();
                    foreach ($cefs as $cef){
                        //envoi mail
                        $this->utils->envoiNotification(
                            $registry,
                            'Nouveau ticket SNVLT',
                            "L'OI " . $ticket->getCodeOi()->getSigle() . " vient de signaler une situation ",
                            $registry->getRepository(User::class)->findOneBy(['email' => $cef->getEmailPersonneRessource()]),
                            $user->getId(),
                            'details_ticket_notif',
                            'TICKET OI',
                            $ticket->getId()
                        );

                        $email = (new TemplatedEmail())
                            ->from(new Address('snvlt@system2is.com', 'Snvlt Infos'))
                            ->to($registry->getRepository(User::class)->findOneBy(['email' => $cef->getEmailPersonneRessource()])->getEmail())
                            ->subject('Vous avez un nouveau ticket OI')
                            ->htmlTemplate('observateur/ticket/email.html.twig')
                            ->context([
                                'titre'=>'Vous avez un nouveau ticket OI',
                                'message' => "L'OI " . $ticket->getCodeOi()->getSigle() . " vient de signaler une situation ",
                                'destinataire'=>$registry->getRepository(User::class)->findOneBy(['email' => $cef->getEmailPersonneRessource()]),
                                'url'=>$this->generateUrl('app_login'),
                                'ticket'=>$ticket
                            ])
                        ;

                        $this->mailer->send(
                            $email
                        );

                    }

                    //-------------------  DR -----------------------------//
                    $directions = $ticket->getCodeDirection();
                    foreach ($directions as $direction){
                        //envoi mail
                        $this->utils->envoiNotification(
                            $registry,
                            'Nouveau ticket SNVLT',
                            "L'OI " . $ticket->getCodeOi()->getSigle() . " vient de signaler une situation ",
                            $registry->getRepository(User::class)->findOneBy(['email' => $direction->getEmailPersonneRessource()]),
                            $user->getId(),
                            'details_ticket_notif',
                            'TICKET OI',
                            $ticket->getId()
                        );

                        $email = (new TemplatedEmail())
                            ->from(new Address('snvlt@system2is.com', 'Snvlt Infos'))
                            ->to($registry->getRepository(User::class)->findOneBy(['email' => $direction->getEmailPersonneRessource()])->getEmail())
                            ->subject('Vous avez un nouveau ticket OI')
                            ->htmlTemplate('observateur/ticket/email.html.twig')
                            ->context([
                                'titre'=>'Vous avez un nouveau ticket OI',
                                'message' => "L'OI " . $ticket->getCodeOi()->getSigle() . " vient de signaler une situation ",
                                'destinataire'=>$registry->getRepository(User::class)->findOneBy(['email' => $direction->getEmailPersonneRessource()]),
                                'url'=>$this->generateUrl('app_login'),
                                'ticket'=>$ticket
                            ])
                        ;

                        $this->mailer->send(
                            $email
                        );

                    }

                    //-------------------  SERVICES MINEF -----------------------------//
                    $services = $ticket->getCodeService();
                    foreach ($services as $service){
                        //envoi mail
                        $this->utils->envoiNotification(
                            $registry,
                            'Nouveau ticket SNVLT',
                            "L'OI " . $ticket->getCodeOi()->getSigle() . " vient de signaler une situation ",
                            $registry->getRepository(User::class)->findOneBy(['email' => $service->getEmailPersonneRessource()]),
                            $user->getId(),
                            'details_ticket_notif',
                            'TICKET OI',
                            $ticket->getId()
                        );

                        $email = (new TemplatedEmail())
                            ->from(new Address('snvlt@system2is.com', 'Snvlt Infos'))
                            ->to($registry->getRepository(User::class)->findOneBy(['email' => $service->getEmailPersonneRessource()])->getEmail())
                            ->subject('Vous avez un nouveau ticket OI')
                            ->htmlTemplate('observateur/ticket/email.html.twig')
                            ->context([
                                'titre'=>'Vous avez un nouveau ticket OI',
                                'message' => "L'OI " . $ticket->getCodeOi()->getSigle() . " vient de signaler une situation ",
                                'destinataire'=>$registry->getRepository(User::class)->findOneBy(['email' => $service->getEmailPersonneRessource()]),
                                'url'=>$this->generateUrl('app_login'),
                                'ticket'=>$ticket
                            ])
                        ;

                        $this->mailer->send(
                            $email
                        );

                    }
                    $this->addFlash('success',$this->translator->trans("Your ticket has been sent successfully "));
                    return $this->redirectToRoute("app_observateur_ticket");
                } else {
                    return $this->render('observateur/ticket/add.html.twig', [

                        'liste_menus'=>$menus->findOnlyParent(),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'form' =>$form->createView(),
                        "all_menus"=>$menus->findAll(),
                        'liste_parent'=>$permissions,
                        'mes_tickets'=>$registry->getRepository(Ticket::class)->findAll()
                    ]);
                }
             }
    }


    #[Route('/observateur/oi/tickets', name: 'all_tickets')]
    public function all_tickets(
        DrRepository $drs,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        ManagerRegistry $registry,
        Request $request,
        UserRepository $userRepository,
        NotificationRepository $notification,
        User $user = null
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('observateur/ticket/all.html.twig', [
                    'ref_drs' => $drs->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    "all_menus"=>$menus->findAll(),
                    'liste_drs' => $drs->findAll(),
                    'liste_parent'=>$permissions,
                    'mes_tickets'=>$registry->getRepository(Ticket::class)->findBy([], ['id'=>'DESC'])
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/oi/statut/alerte/{id_ticket}/{valeur}', name: 'change_statut')]
    public function change_statut(
        DrRepository $drs,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        ManagerRegistry $registry,
        Request $request,
        UserRepository $userRepository,
        NotificationRepository $notification,
        int $id_ticket = null,
        string $valeur = null,
        User $user = null
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $response = array();

                    $ticket = $registry->getRepository(Ticket::class)->find($id_ticket);
                    if ($ticket){
                        $ticket->setStatut($valeur);
                        $registry->getManager()->persist($ticket);
                        $registry->getManager()->flush();

                        $response[] = array(
                            'valeur'=>true
                        );

                        return  new JsonResponse(json_encode($response));
                    } else {
                        $response[] = array(
                            'valeur'=>false
                        );
                        return  new JsonResponse(json_encode($response));
                    }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
