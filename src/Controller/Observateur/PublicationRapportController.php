<?php

namespace App\Controller\Observateur;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\Utils;
use App\Entity\MenuPermission;
use App\Entity\Observateur\AnalyseRapport;
use App\Entity\Observateur\PublicationRapport;
use App\Entity\Observateur\Ticket;
use App\Entity\References\Caroi;
use App\Entity\References\Direction;
use App\Entity\References\Dr;
use App\Entity\References\Oi;
use App\Entity\User;
use App\Form\Observateur\AnalyseRapportAdminType;
use App\Form\Observateur\AnalyseRapportRecommendationType;
use App\Form\Observateur\AnalyseRapportType;
use App\Form\Observateur\PublicationRapportType;
use App\Form\Observateur\TicketType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\Observateur\TicketRepository;
use App\Repository\References\DrRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Snappy\Pdf;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Contracts\Translation\TranslatorInterface;

class PublicationRapportController extends AbstractController
{
    public function __construct(
        TranslatorInterface $translator,
        private SluggerInterface $slugger,
        private Utils $utils,
        private MailerInterface  $mailer,
        private AdministrationService  $administrationService,
    )
    {
        $this->translator = $translator;
    }

    #[Route('/snvlt/oi/publication/rapport', name: 'app_publication_rapport')]
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
            if ($this->isGranted('ROLE_OI'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

        return $this->render('observateur/rapport/index.html.twig', [
            'ref_drs' => $drs->findAll(),
            'liste_menus'=>$menus->findOnlyParent(),
            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
            "all_menus"=>$menus->findAll(),
            'liste_drs' => $drs->findAll(),
            'liste_parent'=>$permissions,
            'mes_rapports'=>$registry->getRepository(PublicationRapport::class)->findBy(['code_oi'=>$user->getCodeOi()])
        ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/rp/oi/publication', name: 'rp_oi')]
    public function listing(
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
            if ($this->isGranted('ROLE_OI'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('observateur/rapport/index.html.twig', [
                    'ref_drs' => $drs->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    "all_menus"=>$menus->findAll(),
                    'liste_drs' => $drs->findAll(),
                    'liste_parent'=>$permissions,
                    'mes_rapports'=>$registry->getRepository(PublicationRapport::class)->findAll()
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/oi/publication/rapport/infos/{id_notification}', name: 'infos_admin_fiche_oi')]
    public function infos_admin_fiche_oi(
        DrRepository $drs,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        ManagerRegistry $registry,
        Request $request,
        UserRepository $userRepository,
        NotificationRepository $notification,
        int $id_notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_OI') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $notif = $notification->find($id_notification);

        return $this->render('observateur/rapport/infos.html.twig', [
            'ref_drs' => $drs->findAll(),
            'liste_menus'=>$menus->findOnlyParent(),
            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
            "all_menus"=>$menus->findAll(),
            'liste_drs' => $drs->findAll(),
            'liste_parent'=>$permissions,
            'rapport_oi'=>$registry->getRepository(PublicationRapport::class)->find($notif->getRelatedToId())
        ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/oi/publication/rapport/analyse', name: 'infos_admin_analyse_fiche_oi')]
    public function infos_admin_analyse_fiche_oi(
        DrRepository $drs,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        ManagerRegistry $registry,
        Request $request,
        UserRepository $userRepository,
        NotificationRepository $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_OI') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('observateur/rapport/analyses.html.twig', [
                    'ref_drs' => $drs->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    "all_menus"=>$menus->findAll(),
                    'liste_drs' => $drs->findAll(),
                    'liste_parent'=>$permissions,
                    'rapports'=>$registry->getRepository(PublicationRapport::class)->findBy([], ['id'=>'DESC']),
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/oi/{id_rapport}', name: 'affiche_oi_rapport_portail')]
    public function affiche_oi_rapport_portail(
        ManagerRegistry $registry,
        Request $request,
        int $id_rapport ): Response
    {
                $rapport = $registry->getRepository(PublicationRapport::class)->find($id_rapport);
                $derniere_analyse = $registry->getRepository(AnalyseRapport::class)->findOneBy(['code_rapport'=>$rapport],['id'=>'DESC']);


                return $this->render('observateur/rapport/affiche_dernier_rapport.html.twig', [
                    'rapport'=>$rapport,
                    'derniere_analyse'=>$derniere_analyse
                ]);


    }

    #[Route('/snvlt/oi/publication/rapport/analyse/edit/{id_rapport?O}', name: 'infos_admin_analyse_fiche_oi_edit')]
    public function infos_admin_analyse_fiche_oi_edit(
        DrRepository $drs,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        ManagerRegistry $registry,
        Request $request,
        UserRepository $userRepository,
        NotificationRepository $notification,
        int $id_rapport
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $rapport = $registry->getRepository(PublicationRapport::class)->find($id_rapport);

                if ($rapport){
                    $derniere_analyse = $registry->getRepository(AnalyseRapport::class)->findOneBy(['code_rapport'=>$rapport], ['id'=>'DESC']);
                    $analyse = new AnalyseRapport();
                    $analyse->setCodeRapport($rapport);

                    $form = $this->createForm(AnalyseRapportRecommendationType::class, $analyse);

                    $form->handleRequest($request);

                    if ( $form->isSubmitted() && $form->isValid() ) {

                        $fichier = $form->get('fichierRecommande')->getData();

                        if ($fichier) {
                            $originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                            // this is needed to safely include the file name as part of the URL
                            $safeFilename = $this->slugger->slug($originalFilename);
                            $newFilename = $this->utils->uniqidReal(25) . '.' . $fichier->guessExtension();

                            // Move the file to the directory where brochures are stored
                            try {
                                $fichier->move(
                                    $this->getParameter('recommendations_caroi_directory'),
                                    $newFilename
                                );
                            } catch (FileException $e) {
                                // ... handle exception if something happens during file upload
                            }

                            // updates the 'brochureFilename' property to store the PDF file name
                            // instead of its contents

                            $analyse->setFichierRecommande($newFilename);
                        }
                            $analyse->setCreatedAt(new \DateTime());
                            $analyse->setCreatedBy($user);
                            $analyse->getCodeRapport()->setStatut($analyse->getStatut());


                            $description = "";
                            if ($form->get('statut')->getData() == "Analyse"){
                                $analyse->setResume("Analyse en cours");
                                $description = "Le CAROI vient d'envoyer une nouvelle recommendation à l'OI ". $analyse->getCodeRapport()->getCodeOi()->getSigle();
                            } elseif ($analyse->getStatut() == "Transmission des observations"){
                                $analyse->setResume("Transmission des observations");
                                $description = "Nouvelles Observations pour le rapport OI N° ". $analyse->getCodeRapport()->getId() . " de la structure ". $analyse->getCodeRapport()->getCodeOi()->getSigle();
                            } elseif ($analyse->getStatut() == "Prise en Compte des propositions"){
                                $analyse->setResume("Prise en Compte des propositions");
                                $description = "Le CAROI a pris en compte les propositions du rapport OI N° ". $analyse->getCodeRapport()->getId() . " de la structure ". $analyse->getCodeRapport()->getCodeOi()->getSigle();
                            } elseif ($analyse->getStatut() == "Publication"){
                                $analyse->setResume("Proposition publiée");
                                $description = "Le CAROI vient de publier le rapport OI N° ". $analyse->getCodeRapport()->getId() . " de la structure ". $analyse->getCodeRapport()->getCodeOi()->getSigle();
                            }


                            $registry->getManager()->persist($analyse);
                            $registry->getManager()->flush();



                            // Mise à Jour Log SNVLT
                            $this->administrationService->save_action(
                                $user,
                                'PUBLICATION_RAPPORT_OI',
                                'RECOMMENDATION CAROI',
                                new \DateTimeImmutable(),
                                $description
                            );


                            /*la derniere analyse*/
                            //Envoi de notification à l'OI


                            // Recupere le destinataire
                            $destinataire = $registry->getRepository(User::class)->findOneBy(['email' => $rapport->getCodeOi()->getEmailPersonneRessource()]);
                            //dd($destinataire);
                            if ($destinataire) {
                                $message = "Le rapport ". $rapport->getLibelle(). " a été analysé par le CAROI. Merci de valider les recommendatrions";
                                //envoi d'une notification à l'OI
                                $this->utils->envoiNotification(
                                    $registry,
                                    'Vous avez une nouvelle recommendation CAROI',
                                    "Le rapport " . $rapport->getLibelle() . " a été analysé par le CAROI. Merci de valider les recommendatrions",
                                    $registry->getRepository(User::class)->findOneBy(['email' => $rapport->getCodeOi()->getEmailPersonneRessource()]),
                                    $user->getId(),
                                    'app_valide_analyse',
                                    'ANALYSE CAROI',
                                    $analyse->getId()
                                );

                                $email = (new TemplatedEmail())
                                    ->from(new Address('snvlt@system2is.com', 'Snvlt Infos'))
                                    ->to($destinataire->getEmail())
                                    ->subject('Vous avez une nouvelle recommendation CAROI')
                                    ->htmlTemplate('observateur/rapport/email.html.twig')
                                    ->context([
                                        'titre'=>'Vous avez une nouvelle recommendation CAROI',
                                        'message' => $message,
                                        'destinataire'=>$destinataire,
                                        'url'=>$this->generateUrl('app_publication_rapport'),
                                        'rapport'=>$rapport
                                    ])
                                ;

                                $this->mailer->send(
                                    $email
                                );
                            }


                            //Envoi d'une notification APP au CAROI
                            $membre_caroi = $registry->getRepository(Caroi::class)->findAll();
                            foreach($membre_caroi as $membre){
                                $message = "Le rapport ". $rapport->getLibelle(). " a été analysé par le CAROI.";
                                $this->utils->envoiNotification(
                                    $registry,
                                    'Nouvelle recommendation CAROI',
                                    $message,
                                    $membre->getCodeUser(),
                                    $user->getId(),
                                    'infos_admin_fiche_oi',
                                    'RAPPORT OI',
                                    $rapport->getId()
                                );

                                $email = (new TemplatedEmail())
                                    ->from(new Address('snvlt@system2is.com', 'Snvlt Infos'))
                                    ->to($membre->getCodeUser()->getEmail())
                                    ->subject('Nouvelle recommendation CAROI')
                                    ->htmlTemplate('observateur/rapport/email.html.twig')
                                    ->context([
                                        'titre'=>'Vous avez une nouvelle recommendation CAROI',
                                        'message' => $message,
                                        'destinataire'=>$membre->getCodeUser(),
                                        'url'=>$this->generateUrl('app_publication_rapport'),
                                        'rapport'=>$rapport
                                    ])
                                ;

                                $this->mailer->send(
                                    $email
                                );

                            }

                            //Envoi d'une notification APP et emails aux drs
                            $dr = $rapport->getCodeDr();
                            //dd($dr);

                            foreach ($dr as $single_dr){
                                if ($single_dr && $single_dr->getEmailPersonneRessource()){
                                    $responsable_dr = $registry->getRepository(User::class)->findOneBy(['email'=>$single_dr->getEmailPersonneRessource()]);
                                    if($responsable_dr){
                                        $message = "Le rapport ". $rapport->getLibelle(). " a été analysé par le CAROI.";
                                        $this->utils->envoiNotification(
                                            $registry,
                                            'Nouvelle recommendation CAROI',
                                            $message,
                                            $responsable_dr,
                                            $user->getId(),
                                            'infos_admin_fiche_oi',
                                            'RAPPORT OI',
                                            $rapport->getId()
                                        );

                                        $email = (new TemplatedEmail())
                                            ->from(new Address('snvlt@system2is.com', 'Snvlt Infos'))
                                            ->to($responsable_dr->getEmail())
                                            ->subject('Nouvelle recommendation CAROI')
                                            ->htmlTemplate('observateur/rapport/email.html.twig')
                                            ->context([
                                                'titre'=>'Vous avez une nouvelle recommendation CAROI',
                                                'message' => $message,
                                                'destinataire'=>$responsable_dr,
                                                'url'=>$this->generateUrl('app_publication_rapport'),
                                                'rapport'=>$rapport
                                            ])
                                        ;

                                        $this->mailer->send(
                                            $email
                                        );
                                    }
                                }
                            }




                            $this->addFlash('success', $this->translator->trans("La recommendation vient d'être traitée par ") . " " . $user);
                            return $this->redirectToRoute("infos_admin_analyse_fiche_oi");

                    } else {
                        return $this->render('observateur/rapport/analyses_edit.html.twig', [
                            'form'=>$form,
                            'ref_drs' => $drs->findAll(),
                            'liste_menus'=>$menus->findOnlyParent(),
                            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                            "all_menus"=>$menus->findAll(),
                            'liste_drs' => $drs->findAll(),
                            'liste_parent'=>$permissions,
                            'rapport'=>$rapport,
                            'lignes'=>$registry->getRepository(AnalyseRapport::class)->findBy(['code_rapport'=>$rapport], ['id'=>'ASC']),
                            'derniere_analyse'=>$derniere_analyse
                        ]);
                    }

                } else {
                    return $this->redirectToRoute('app_no_page_found');
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

 #[Route('/snvlt/oi/publication/rapport/analyse/validate/{id_notification}', name: 'app_valide_analyse')]
    public function app_valide_analyse(
        DrRepository $drs,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        ManagerRegistry $registry,
        Request $request,
        UserRepository $userRepository,
        NotificationRepository $notification,
        int $id_notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_OI'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $notif = $notification->find($id_notification);

                if ($notif){
                    $analyse = $registry->getRepository(AnalyseRapport::class)->find($notif->getRelatedToId());
                    if ($analyse){
                        $derniere_analyse = $registry->getRepository(AnalyseRapport::class)->findOneBy(['code_rapport'=>$analyse->getCodeRapport()], ['id'=>'DESC']);

                        $form = $this->createForm(AnalyseRapportType::class, $analyse);

                        $form->handleRequest($request);

                        if ( $form->isSubmitted() && $form->isValid() ) {

                            $fichier = $form->get('fichier')->getData();

                            /*if ($fichier) {*/
                                $originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                                // this is needed to safely include the file name as part of the URL
                                $safeFilename = $this->slugger->slug($originalFilename);
                                $newFilename = $this->utils->uniqidReal(25) . '.' . $fichier->guessExtension();

                                // Move the file to the directory where brochures are stored
                                try {
                                    $fichier->move(
                                        $this->getParameter('reports_directory'),
                                        $newFilename
                                    );
                                } catch (FileException $e) {
                                    // ... handle exception if something happens during file upload
                                }

                                // updates the 'brochureFilename' property to store the PDF file name
                                // instead of its contents

                                $analyse->setFichier($newFilename);
                                $registry->getManager()->persist($analyse);
                                $registry->getManager()->flush();

                                //Envoi de notification au CAROI
                                    // Recupere les membres du CAROI
                                        $membres_caroi = $registry->getRepository(Caroi::class)->findAll();
                                        foreach ($membres_caroi as $membre){
                                            //Notification App
                                            $message = 'La recommendation N° '. $analyse->getNumeroLigne() . " [Rapport OI N° ". $analyse->getCodeRapport()->getId(). "] a été mis à jour par l'OI ". $analyse->getCodeRapport()->getCodeOi()->getRaisonSociale();
                                            $this->utils->envoiNotification(
                                                $registry,
                                                'Retour sur recommendations OI',
                                                $message,
                                                $membre->getCodeUser(),
                                                $user->getId(),
                                                'infos_admin_fiche_oi',
                                                'ANALYSE CAROI',
                                                $analyse->getCodeRapport()->getId()
                                            );

                                            $email = (new TemplatedEmail())
                                                ->from(new Address('snvlt@system2is.com', 'Snvlt Infos'))
                                                ->to($membre->getCodeUser()->getEmail())
                                                ->subject('La recommendation N° '. $analyse->getNumeroLigne() . " [Rapport OI N° ". $analyse->getCodeRapport()->getId(). "] a été mis à jour par l'OI ". $analyse->getCodeRapport()->getCodeOi()->getRaisonSociale())
                                                ->htmlTemplate('observateur/rapport/email_recommendation.html.twig')
                                                ->context([
                                                    'titre'=>'La recommendation N° '. $analyse->getNumeroLigne() . " [Rapport OI N° ". $analyse->getCodeRapport()->getId(). "] a été mis à jour par l'OI ". $analyse->getCodeRapport()->getCodeOi()->getRaisonSociale(),
                                                    'message' => $message,
                                                    'destinataire'=>$membre->getCodeUser(),
                                                    'url'=>$this->generateUrl('app_publication_rapport'),
                                                    'rapport'=>$analyse
                                                ])
                                            ;

                                            $this->mailer->send(
                                                $email
                                            );
                                        }

                            //Envoi d'une notification APP et emails aux drs
                            $rapport = $analyse->getCodeRapport();
                            $dr = $rapport->getCodeDr();
                            //dd($dr);
                            foreach ($dr as $single_dr){
                                if ($single_dr && $single_dr->getEmailPersonneRessource()){
                                    $responsable_dr = $registry->getRepository(User::class)->findOneBy(['email'=>$single_dr->getEmailPersonneRessource()]);
                                    if($responsable_dr){
                                        $message = 'La structure OI dénommée '. $rapport->getCodeOi()->getSigle() . " vous a envoyé un nouvelle proposition de rapport";
                                        $this->utils->envoiNotification(
                                            $registry,
                                            'Retour sur recommendations OI',
                                            $message,
                                            $responsable_dr,
                                            $user->getId(),
                                            'infos_admin_fiche_oi',
                                            'ANALYSE CAROI',
                                            $rapport->getId()
                                        );

                                        $email = (new TemplatedEmail())
                                            ->from(new Address('snvlt@system2is.com', 'Snvlt Infos'))
                                            ->to($responsable_dr->getEmail())
                                            ->subject('Vous avez une nouvelle proposition de rapport OI')
                                            ->htmlTemplate('observateur/rapport/email_recommendation.html.twig')
                                            ->context([
                                                'titre'=>'Vous avez une nouvelle proposition de rapport OI',
                                                'message' => $message,
                                                'destinataire'=>$responsable_dr,
                                                'url'=>$this->generateUrl('app_publication_rapport'),
                                                'rapport'=>$analyse
                                            ])
                                        ;

                                        $this->mailer->send(
                                            $email
                                        );
                                    }
                                }
                            }

                                $this->addFlash('success',$this->translator->trans("La recommendation vient d'être traitée par ")." ". $user);
                                return $this->redirectToRoute("app_publication_rapport");
                           /* }*/

                        } else{

                            return $this->render('observateur/rapport/analyses_add_file_oi.html.twig', [
                                'ref_drs' => $drs->findAll(),
                                'liste_menus'=>$menus->findOnlyParent(),
                                'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                                "all_menus"=>$menus->findAll(),
                                'liste_drs' => $drs->findAll(),
                                'liste_parent'=>$permissions,
                                'analyse'=>$analyse,
                                'dernier_analyse'=> $derniere_analyse,
                                'form'=>$form
                            ]);
                        }


                    }


                } else {
                    return $this->redirectToRoute('app_no_page_found');
                }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/oi/publication/rapport/analyse/validate/admin/{id_notification}', name: 'app_valide_analyse_admin')]
    public function app_valide_analyse_admin(
        DrRepository $drs,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        ManagerRegistry $registry,
        Request $request,
        UserRepository $userRepository,
        NotificationRepository $notification,
        int $id_notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_OI'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $notif = $notification->find($id_notification);

                if ($notif){
                    $analyse = $registry->getRepository(AnalyseRapport::class)->find($notif->getRelatedToId());
                    if ($analyse){
                        $form = $this->createForm(AnalyseRapportAdminType::class, $analyse);

                        $form->handleRequest($request);

                        if ( $form->isSubmitted() && $form->isValid() ) {

                            $fichier = $form->get('fichier')->getData();

                            if ($fichier) {
                                $originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                                // this is needed to safely include the file name as part of the URL
                                $safeFilename = $this->slugger->slug($originalFilename);
                                $newFilename = $this->utils->uniqidReal(25) . '.' . $fichier->guessExtension();

                                // Move the file to the directory where brochures are stored
                                try {
                                    $fichier->move(
                                        $this->getParameter('reports_directory'),
                                        $newFilename
                                    );
                                } catch (FileException $e) {
                                    // ... handle exception if something happens during file upload
                                }

                                // updates the 'brochureFilename' property to store the PDF file name
                                // instead of its contents

                                $analyse->setFichier($newFilename);
                                $registry->getManager()->persist($analyse);
                                $registry->getManager()->flush();

                                //Envoi de notification au CAROI
                                // Recupere les membres du CAROI
                                $membres_caroi = $registry->getRepository(Caroi::class)->findAll();
                                foreach ($membres_caroi as $membre){
                                    $message = 'La recommendation N° '. $analyse->getNumeroLigne() . " [Rapport OI N° ". $analyse->getCodeRapport()->getId(). "] a été mis à jour par l'OI ". $analyse->getCodeRapport()->getCodeOi()->getRaisonSociale();

                                    //Notification App
                                    $this->utils->envoiNotification(
                                        $registry,
                                        'Corrections OI sur recommendations OI',
                                        'La recommendation N° '. $analyse->getNumeroLigne() . " [Rapport OI N° ". $analyse->getCodeRapport()->getId(). "] a été mis à jour par l'OI ". $analyse->getCodeRapport()->getCodeOi()->getRaisonSociale(),
                                        $membre->getCodeUser(),
                                        $user->getId(),
                                        'app_valide_analyse_admin',
                                        'ANALYSE CAROI',
                                        $analyse->getId()
                                    );
                                    //Email au CAROI
                                    $email = (new TemplatedEmail())
                                        ->from(new Address('snvlt@system2is.com', 'Snvlt Infos'))
                                        ->to($membre->getCodeUser()->getEmail())
                                        ->subject('Corrections OI sur recommendations OI')
                                        ->htmlTemplate('observateur/rapport/email.html.twig')
                                        ->context([
                                            'titre'=>'Retour sur recommendations OI',
                                            'message' => $message,
                                            'destinataire'=>$membre->getCodeUser(),
                                            'url'=>$this->generateUrl('app_publication_rapport')
                                        ])
                                    ;

                                    $this->mailer->send(
                                        $email
                                    );
                                }








                                $this->addFlash('success',$this->translator->trans("La recommendation vient d'être traitée par ")." ". $user);
                                return $this->redirectToRoute("app_publication_oi");
                            }

                        } else{
                            return $this->render('observateur/rapport/analyses_add_file_oi.html.twig', [
                                'ref_drs' => $drs->findAll(),
                                'liste_menus'=>$menus->findOnlyParent(),
                                'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                                "all_menus"=>$menus->findAll(),
                                'liste_drs' => $drs->findAll(),
                                'liste_parent'=>$permissions,
                                'analyse'=>$analyse,
                                'form'=>$form
                            ]);
                        }


                    }


                }else {
                    return $this->redirectToRoute('app_no_page_found');
                }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/oi/publication/rapport/analyse/respond/{id_rapport?O}', name: 'infos_admin_analyse_fiche_oi_respond')]
    public function infos_admin_analyse_fiche_oi_respond(
        DrRepository $drs,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        ManagerRegistry $registry,
        Request $request,
        UserRepository $userRepository,
        NotificationRepository $notification,
        int $id_rapport,
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_OI'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $rapport = $registry->getRepository(PublicationRapport::class)->find($id_rapport);
                $derniere_analyse = $registry->getRepository(AnalyseRapport::class)->findOneBy(['code_rapport'=>$rapport], ['id'=>'DESC']);

                if ($derniere_analyse){


                    $form = $this->createForm(AnalyseRapportType::class, $derniere_analyse);

                    $form->handleRequest($request);

                    if ( $form->isSubmitted() && $form->isValid() ) {

                        $fichier = $form->get('fichier')->getData();


                        /*if ($fichier) {*/
                        $originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                        // this is needed to safely include the file name as part of the URL
                        $safeFilename = $this->slugger->slug($originalFilename);
                        $newFilename = $this->utils->uniqidReal(25) . '.' . $fichier->guessExtension();

                        // Move the file to the directory where brochures are stored
                        try {
                            $fichier->move(
                                $this->getParameter('reports_directory'),
                                $newFilename
                            );
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }

                        // updates the 'brochureFilename' property to store the PDF file name
                        // instead of its contents

                        $derniere_analyse->setFichier($newFilename);
                        $registry->getManager()->persist($derniere_analyse);
                        $registry->getManager()->flush();

                        //Envoi de notification au CAROI
                        // Recupere les membres du CAROI
                        $membres_caroi = $registry->getRepository(Caroi::class)->findAll();
                        foreach ($membres_caroi as $membre){
                            //Notification App
                            $message = 'La recommendation N° '. $derniere_analyse->getNumeroLigne() . " [Rapport OI N° ". $derniere_analyse->getCodeRapport()->getId(). "] a été mis à jour par l'OI ". $derniere_analyse->getCodeRapport()->getCodeOi()->getRaisonSociale();
                            $this->utils->envoiNotification(
                                $registry,
                                'Retour sur recommendations OI',
                                $message,
                                $membre->getCodeUser(),
                                $user->getId(),
                                'infos_admin_fiche_oi',
                                'ANALYSE CAROI',
                                $derniere_analyse->getCodeRapport()->getId()
                            );

                            $email = (new TemplatedEmail())
                                ->from(new Address('snvlt@system2is.com', 'Snvlt Infos'))
                                ->to($membre->getCodeUser()->getEmail())
                                ->subject('La recommendation N° '. $derniere_analyse->getNumeroLigne() . " [Rapport OI N° ". $derniere_analyse->getCodeRapport()->getId(). "] a été mis à jour par l'OI ". $derniere_analyse->getCodeRapport()->getCodeOi()->getRaisonSociale())
                                ->htmlTemplate('observateur/rapport/email_recommendation.html.twig')
                                ->context([
                                    'titre'=>'La recommendation N° '. $derniere_analyse->getNumeroLigne() . " [Rapport OI N° ". $derniere_analyse->getCodeRapport()->getId(). "] a été mis à jour par l'OI ". $derniere_analyse->getCodeRapport()->getCodeOi()->getRaisonSociale(),
                                    'message' => $message,
                                    'destinataire'=>$membre->getCodeUser(),
                                    'url'=>$this->generateUrl('app_publication_rapport'),
                                    'rapport'=>$derniere_analyse
                                ])
                            ;

                            $this->mailer->send(
                                $email
                            );
                        }

                        //Envoi d'une notification APP et emails aux drs
                        $rapport = $derniere_analyse->getCodeRapport();
                        $dr = $rapport->getCodeDr();
                        //dd($dr);
                        foreach ($dr as $single_dr){
                            if ($single_dr && $single_dr->getEmailPersonneRessource()){
                                $responsable_dr = $registry->getRepository(User::class)->findOneBy(['email'=>$single_dr->getEmailPersonneRessource()]);
                                if($responsable_dr){
                                    $message = 'La structure OI dénommée '. $rapport->getCodeOi()->getSigle() . " vous a envoyé un nouvelle proposition de rapport";
                                    $this->utils->envoiNotification(
                                        $registry,
                                        'Retour sur recommendations OI',
                                        $message,
                                        $responsable_dr,
                                        $user->getId(),
                                        'infos_admin_fiche_oi',
                                        'ANALYSE CAROI',
                                        $rapport->getId()
                                    );

                                    $email = (new TemplatedEmail())
                                        ->from(new Address('snvlt@system2is.com', 'Snvlt Infos'))
                                        ->to($responsable_dr->getEmail())
                                        ->subject('Vous avez une nouvelle proposition de rapport OI')
                                        ->htmlTemplate('observateur/rapport/email_recommendation.html.twig')
                                        ->context([
                                            'titre'=>'Vous avez une nouvelle proposition de rapport OI',
                                            'message' => $message,
                                            'destinataire'=>$responsable_dr,
                                            'url'=>$this->generateUrl('app_publication_rapport'),
                                            'rapport'=>$derniere_analyse
                                        ])
                                    ;

                                    $this->mailer->send(
                                        $email
                                    );
                                }
                            }
                        }

                        $this->addFlash('success',$this->translator->trans("La recommendation vient d'être traitée par ")." ". $user);
                        return $this->redirectToRoute("app_publication_rapport");
                        /* }*/

                    } else{

                        return $this->render('observateur/rapport/analyses_respond_oi.html.twig', [
                            'ref_drs' => $drs->findAll(),
                            'liste_menus'=>$menus->findOnlyParent(),
                            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                            "all_menus"=>$menus->findAll(),
                            'liste_drs' => $drs->findAll(),
                            'liste_parent'=>$permissions,
                            'analyse'=>$derniere_analyse,
                            'dernier_analyse'=> $derniere_analyse,
                            'form'=>$form
                        ]);
                    }


                } else {
                    return $this->redirectToRoute('app_no_page_found');
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/oi/publication/rapport/new/{id_rapport?0}', name: 'add_rapport')]
    public function add_rapport (
        ManagerRegistry $entityManager,
        TranslatorInterface $translator,
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        UserRepository $userRepository,
        NotificationRepository $notification,
        ManagerRegistry $registry,
        int $id_rapport){

        $session = $request->getSession();
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_OI'))
            {
            $user = $userRepository->find($this->getUser());
            $code_groupe = $user->getCodeGroupe()->getId();

            $titre = $translator->trans("Modifier un rapport");

                $rapport = $registry->getRepository(PublicationRapport::class)->find($id_rapport);
                $new = false;
                if(!$rapport){
                    $new = true;
                    $rapport = new PublicationRapport();
                    $rapport->setCreatedAt(new \DateTime());
                    $rapport->setCreatedBy($user);
                }




            $form = $this->createForm(PublicationRapportType::class, $rapport);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){

                $fichier = $form->get('fichier')->getData();

                if ($fichier) {$originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                    // this is needed to safely include the file name as part of the URL
                    $safeFilename = $this->slugger->slug($originalFilename);
                    $newFilename = $this->utils->uniqidReal(25).'.'.$fichier->guessExtension();

                    // Move the file to the directory where brochures are stored
                    try {
                        $fichier->move(
                            $this->getParameter('reports_directory'),
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }

                    // updates the 'brochureFilename' property to store the PDF file name
                    // instead of its contents

                    $rapport->setFichier($newFilename);

                }


                $rapport->setCodeOi($user->getCodeOi());
                $rapport->setValide(false);

                $rapport->setStatut("Soumission");
                $manager = $entityManager->getManager();

                $manager->persist($rapport);


                //Ajout du fichier dans l'analyse OI
                $analyse = new AnalyseRapport();
                $analyse->setFichier($rapport->getFichier());
                $analyse->setStatut(1);
                $analyse->setCodeRapport($rapport);
                $analyse->setNumeroLigne($rapport->getId(). "-".$rapport->getAnalyseRapports()->count() + 1);
                $analyse->setResume("Soumission du rapport");
                $analyse->setCreatedBy($user);
                $analyse->setCreatedAt(new \DateTime());
                $manager->persist($analyse);
                //dd($analyse);
                $manager->flush();


                //Envoi d'une notification APP au CAROI
                    $membre_caroi = $registry->getRepository(Caroi::class)->findAll();
                    foreach($membre_caroi as $membre){
                        $message = 'La structure OI dénommée '. $rapport->getCodeOi()->getSigle() . " vous a envoyé un nouvelle proposition de rapport";
                        $this->utils->envoiNotification(
                            $registry,
                            'Vous avez une nouvelle proposition de rapport OI',
                            $message,
                            $membre->getCodeUser(),
                            $user->getId(),
                            'infos_admin_fiche_oi',
                            'RAPPORT OI',
                            $rapport->getId()
                        );

                        $email = (new TemplatedEmail())
                            ->from(new Address('snvlt@system2is.com', 'Snvlt Infos'))
                            ->to($membre->getCodeUser()->getEmail())
                            ->subject('Vous avez une nouvelle proposition de rapport OI')
                            ->htmlTemplate('observateur/rapport/email.html.twig')
                            ->context([
                                'titre'=>'Vous avez une nouvelle proposition de rapport OI',
                                'message' => $message,
                                'destinataire'=>$membre->getCodeUser(),
                                'url'=>$this->generateUrl('app_publication_rapport'),
                                'rapport'=>$rapport
                            ])
                        ;

                        $this->mailer->send(
                            $email
                        );

                    }
                //Envoi d'une notification APP et emails aux drs
                $dr = $rapport->getCodeDr();
                    //dd($dr);
                    foreach ($dr as $single_dr){
                        if ($single_dr && $single_dr->getEmailPersonneRessource()){
                            $responsable_dr = $registry->getRepository(User::class)->findOneBy(['email'=>$single_dr->getEmailPersonneRessource()]);
                            if($responsable_dr){
                                $message = 'La structure OI dénommée '. $rapport->getCodeOi()->getSigle() . " vous a envoyé un nouvelle proposition de rapport";
                                $this->utils->envoiNotification(
                                    $registry,
                                    'Vous avez une nouvelle proposition de rapport OI',
                                    $message,
                                    $responsable_dr,
                                    $user->getId(),
                                    'infos_admin_fiche_oi',
                                    'RAPPORT OI',
                                    $rapport->getId()
                                );

                                $email = (new TemplatedEmail())
                                    ->from(new Address('snvlt@system2is.com', 'Snvlt Infos'))
                                    ->to($responsable_dr->getEmail())
                                    ->subject('Vous avez une nouvelle proposition de rapport OI')
                                    ->htmlTemplate('observateur/rapport/email.html.twig')
                                    ->context([
                                        'titre'=>'Vous avez une nouvelle proposition de rapport OI',
                                        'message' => $message,
                                        'destinataire'=>$responsable_dr,
                                        'url'=>$this->generateUrl('app_publication_rapport'),
                                        'rapport'=>$rapport
                                    ])
                                ;

                                $this->mailer->send(
                                    $email
                                );
                            }
                        }
                    }


                // Log SNVLT
                $this->administrationService->save_action(
                    $user,
                    'SOUMISSION_RAPPORT_OI',
                    'CREATION',
                    new \DateTimeImmutable(),
                    "le Rapport OI N° " . $rapport->getId() . " [". $rapport->getLibelle() . "] vient d'être créé par ". $user
                );

                //Envoi Email et Notifications aux différents recipiendaires


                //-------------------  CEF -----------------------------//
                $cefs = $rapport->getCodeCef();
                //dd($dr);
                foreach ($cefs as $single_cef){
                    if ($single_cef && $single_cef->getEmailPersonneRessource()){
                        $responsable_cef = $registry->getRepository(User::class)->findOneBy(['email'=>$single_cef->getEmailPersonneRessource()]);
                        if($responsable_cef){
                            $message = 'La structure OI dénommée '. $rapport->getCodeOi()->getSigle() . " vous a envoyé un nouvelle proposition de rapport";
                            $this->utils->envoiNotification(
                                $registry,
                                'Vous avez une nouvelle proposition de rapport OI',
                                $message,
                                $responsable_cef,
                                $user->getId(),
                                'infos_admin_fiche_oi',
                                'RAPPORT OI',
                                $rapport->getId()
                            );

                            $email = (new TemplatedEmail())
                                ->from(new Address('snvlt@system2is.com', 'Snvlt Infos'))
                                ->to($responsable_cef->getEmail())
                                ->subject('Vous avez une nouvelle proposition de rapport OI')
                                ->htmlTemplate('observateur/rapport/email.html.twig')
                                ->context([
                                    'titre'=>'Vous avez une nouvelle proposition de rapport OI',
                                    'message' => $message,
                                    'destinataire'=>$responsable_cef,
                                    'url'=>$this->generateUrl('app_publication_rapport'),
                                    'rapport'=>$rapport
                                ])
                            ;

                            $this->mailer->send(
                                $email
                            );
                        }
                    }
                }

                //-------------------  DR -----------------------------//
                $dirs = $rapport->getCodeDirection();
                //dd($dr);
                foreach ($dirs as $single_dir){
                    if ($single_dir && $single_dir->getEmailPersonneRessource()){
                        $responsable_dir = $registry->getRepository(User::class)->findOneBy(['email'=>$single_dir->getEmailPersonneRessource()]);
                        if($responsable_dir){
                            $message = 'La structure OI dénommée '. $rapport->getCodeOi()->getSigle() . " vous a envoyé un nouvelle proposition de rapport";
                            $this->utils->envoiNotification(
                                $registry,
                                'Vous avez une nouvelle proposition de rapport OI',
                                $message,
                                $responsable_dir,
                                $user->getId(),
                                'infos_admin_fiche_oi',
                                'RAPPORT OI',
                                $rapport->getId()
                            );

                            $email = (new TemplatedEmail())
                                ->from(new Address('snvlt@system2is.com', 'Snvlt Infos'))
                                ->to($responsable_dir->getEmail())
                                ->subject('Vous avez une nouvelle proposition de rapport OI')
                                ->htmlTemplate('observateur/rapport/email.html.twig')
                                ->context([
                                    'titre'=>'Vous avez une nouvelle proposition de rapport OI',
                                    'message' => $message,
                                    'destinataire'=>$responsable_dir,
                                    'url'=>$this->generateUrl('app_publication_rapport'),
                                    'rapport'=>$rapport
                                ])
                            ;

                            $this->mailer->send(
                                $email
                            );
                        }
                    }
                }

                //-------------------  SERVICES MINEF -----------------------------//
                $ser_minef = $rapport->getCodeServiceMinef();
                //dd($dr);
                foreach ($ser_minef as $single_srv){
                    if ($single_srv && $single_srv->getEmailPersonneRessource()){
                        $responsable_srv = $registry->getRepository(User::class)->findOneBy(['email'=>$single_srv->getEmailPersonneRessource()]);
                        if($responsable_srv){
                            $message = 'La structure OI dénommée '. $rapport->getCodeOi()->getSigle() . " vous a envoyé un nouvelle proposition de rapport";
                            $this->utils->envoiNotification(
                                $registry,
                                'Vous avez une nouvelle proposition de rapport OI',
                                $message,
                                $responsable_srv,
                                $user->getId(),
                                'infos_admin_fiche_oi',
                                'RAPPORT OI',
                                $rapport->getId()
                            );

                            $email = (new TemplatedEmail())
                                ->from(new Address('snvlt@system2is.com', 'Snvlt Infos'))
                                ->to($responsable_srv->getEmail())
                                ->subject('Vous avez une nouvelle proposition de rapport OI')
                                ->htmlTemplate('observateur/rapport/email.html.twig')
                                ->context([
                                    'titre'=>'Vous avez une nouvelle proposition de rapport OI',
                                    'message' => $message,
                                    'destinataire'=>$responsable_srv,
                                    'url'=>$this->generateUrl('app_publication_rapport'),
                                    'rapport'=>$rapport
                                ])
                            ;

                            $this->mailer->send(
                                $email
                            );
                        }
                    }
                }

                $this->addFlash('success',$this->translator->trans("Rapport publié avec succès"));
                return $this->redirectToRoute("app_publication_rapport");
            } else {
                return $this->render('observateur/rapport/add.html.twig', [

                    'liste_menus'=>$menus->findOnlyParent(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'form' =>$form->createView(),
                    "all_menus"=>$menus->findAll(),
                    'liste_parent'=>$permissions,
                    'mes_tickets'=>$registry->getRepository(Ticket::class)->findAll()
                ]);
            }
        } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }


    //Ajouter une analyse
    #[Route('/snvlt/analyse/caroi/add/{data}', name: 'add_analyse_rapport')]
    public function add_analyse_rapport(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        string $data,
        NotificationRepository $notification,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());

                if($data){
                    //Decoder le JSON
                    $arraydata = json_decode($data);
                    $reponse = array();

                    $rapport = $registry->getRepository(PublicationRapport::class)->find((int) $arraydata->code_rapport);
                    $last_analyse = $registry->getRepository(AnalyseRapport::class)->findOneBy(['code_rapport'=>$rapport],['id'=>'DESC']);

                    if($last_analyse && !$last_analyse->getFichier()){
                        $reponse[] = array(
                            'code'=>'LAST_ANA_REPORT_NOT_UPLOADED'
                        );
                    } else {
                        if($rapport){
                            $ana = new AnalyseRapport();


                            $ana->setNumeroLigne( $arraydata->numero);
                            $ana->setResume($arraydata->resume);
                            $ana->setStatut($arraydata->statut);
                            $ana->setCodeRapport($rapport);

                            $ana->setCreatedAt(new \DateTime());
                            $ana->setCreatedBy($user);

                            $registry->getManager()->persist($ana);
                            $registry->getManager()->flush();

                            $reponse[] = array(
                                'code'=>'SUCCESS'
                            );

                            $destinataire = $registry->getRepository(User::class)->findOneBy(['email'=>$rapport->getCodeOi()->getEmailPersonneRessource()]);
                            //dd($destinataire);
                            if ($destinataire){
                                //envoi d'une notification à l'OI
                                $this->utils->envoiNotification(
                                    $registry,
                                    'Votre rapport a été analysé par le CAROI',
                                    "Le rapport ". $rapport->getLibelle(). " a été analysé par le CAROI. Merci de valider les recommendatrions",
                                    $registry->getRepository(User::class)->findOneBy(['email'=>$rapport->getCodeOi()->getEmailPersonneRessource()]),
                                    $user->getId(),
                                    'app_valide_analyse',
                                    'ANALYSE CAROI',
                                    $ana->getId()
                                );
                                //envoi d'un email à l'OI
                                try {
                                    $this->utils->sendEmail(
                                        $destinataire->getEmail(),
                                        'Votre rapport a été analysé par le CAROI',
                                        "Le rapport ". $rapport->getLibelle(). " a été analysé par le CAROI. Merci de valider les recommendatrions"
                                    );
                                } catch (FileException $e) {
                                    $reponse[] = array(
                                        'code'=>$e->getCode() . " ". $e->getMessage()
                                    );
                                    // ... handle exception if something happens during file upload
                                }

                            }


                        }
                    }
                }
                return new JsonResponse(json_encode($reponse));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/analyse/caroi/list/{id_rapport}', name: 'add_analyse_rapport_list')]
    public function add_analyse_rapport_list(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        int $id_rapport,
        NotificationRepository $notification,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_OI') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $reponse = array();

                $liste_analyse = $registry->getRepository(AnalyseRapport::class)->findBy(['code_rapport'=>$id_rapport]);

                foreach ($liste_analyse as $ana){
					$date_ana ="-";
                    if ($ana->getDateAnalyse()){
                        $date_ana =$ana->getDateAnalyse()->format('d/m/Y');
                    }
                    $date_oi ="-";
                    if ($ana->getDateOi()){
                        $date_oi =$ana->getDateOi()->format('d/m/Y');
                    }
                    $reponse[] = array(
                        'id_ana'=>$ana->getId(),
                        'numero'=>$ana->getNumeroLigne(),
                        'resume'=>$ana->getResume(),
                        'fichier'=>$ana->getFichier(),
                        'recommendation'=>$ana->getFichierRecommande(),
                        'statut'=>$ana->getStatut(),
                        'emis'=>$ana->getCreatedAt()->format('d/m/Y'),
                        'date_analyse'=>$date_ana,
                        'date_oi'=>$date_oi
                    );
                }
                return new JsonResponse(json_encode($reponse));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/oi/publier/{id_rapport}', name: 'publish_report')]
    public function publish_report(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        int $id_rapport,
        NotificationRepository $notification,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $reponse = array();

                $rapport = $registry->getRepository(PublicationRapport::class)->find($id_rapport);

                if ($rapport){
                    $rapport->setStatut("PUBLIE");
                    $rapport->setValide(true);
                    $rapport->setUpdatedBy($user);
                    $rapport->setCreatedAt(new \DateTime());

                    $registry->getManager()->persist($rapport);
                    $registry->getManager()->flush();
                    $this->addFlash('success', 'Le rapport OI vient d\'être publié');
                }
                $liste_analyse = $registry->getRepository(AnalyseRapport::class)->findBy(['code_rapport'=>$id_rapport]);

                foreach ($liste_analyse as $ana){
                    $reponse[] = array(
                        'code'=>'SUCCESS'
                    );
                }
                return new JsonResponse(json_encode($reponse));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

#[Route('/snvlt/analyse/oi/init/{id_rapport}', name: 'init_fichier')]
    public function init_fichier(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        int $id_rapport,
        NotificationRepository $notification,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_OI'))
            {
                $user = $userRepository->find($this->getUser());
                $reponse = array();

                $rapport = $registry->getRepository(PublicationRapport::class)->find($id_rapport);

                if ($rapport){
                    $derniere_analyse = $registry->getRepository(AnalyseRapport::class)->findOneBy(['code_rapport'=>$rapport], ['id'=>'DESC']);

                    $derniere_analyse->setFichier("");
                    $derniere_analyse->setUpdatedBy($user);
                    $derniere_analyse->setUpdatedAt(new \DateTime());

                    $registry->getManager()->persist($derniere_analyse);
                    $registry->getManager()->flush();
                    $this->addFlash('success', 'Le dernier fichier vient d\'être réinitialisé');
                }
                $liste_analyse = $registry->getRepository(AnalyseRapport::class)->findBy(['code_rapport'=>$id_rapport]);

                foreach ($liste_analyse as $ana){
                    $reponse[] = array(
                        'code'=>'SUCCESS'
                    );
                }
                return new JsonResponse(json_encode($reponse));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }


}
