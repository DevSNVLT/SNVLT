<?php

namespace App\Controller\Administration;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\SendSMS;
use App\Controller\Services\synchro\UserSnvlt1;
use App\Controller\Services\Utils;
use App\Entity\Admin\LogSnvlt;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\Groupe;
use App\Entity\References\Cantonnement;
use App\Entity\References\Commercant;
use App\Entity\References\Ddef;
use App\Entity\References\Direction;
use App\Entity\References\Dr;
use App\Entity\References\Exploitant;
use App\Entity\References\Exportateur;
use App\Entity\References\Oi;
use App\Entity\References\PosteForestier;
use App\Entity\References\ServiceMinef;
use App\Entity\References\Titre;
use App\Entity\References\TypeOperateur;
use App\Entity\References\Usine;
use App\Entity\User;
use App\Events\Administration\AddNotificationEvent;
use App\Events\Administration\SendEmailEvent;
use App\Form\Administration\ProfileFormType;
use App\Form\Administration\SingleUserFormType;
use App\Form\Administration\UtilisateurFormType;
use App\Form\References\ForetType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\TypeAutorisationRepository;
use App\Repository\UserRepository;
use App\Security\AppCustomAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use mysql_xdevapi\Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UtilisateurController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private $translator;

    public function __construct(
        private EventDispatcherInterface $dispatcher,
        EmailVerifier $emailVerifier,
        TranslatorInterface $translator,
        private SluggerInterface $slugger,
        private  Utils $utils,
        private AdministrationService $service)
    {
        $this->emailVerifier = $emailVerifier;
        $this->translator = $translator;
    }
    #[Route('snvlt/admin/utilisateurs', name: 'app_utilisateur')]
    public function index(UserRepository $users,
                          MenuRepository $menus,
                          MenuPermissionRepository $permissions,
                          GroupeRepository $myGroups,
                          Request $request,
                          UserRepository $userRepository,
                          PaginatorInterface $paginator,
                          User $user = null,
                          NotificationRepository $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
            $titre = $this->translator->trans("Structure mamanger");

            $pagination = $paginator->paginate(
                $users->findAll(),
                $request->query->getInt('page', 1),
                10 );

            return $this->render('administration/utilisateur/index.html.twig', [
                'liste_users' => $users->findBy(['isResponsable'=>true]),
                'liste_menus'=>$menus->findOnlyParent(),
                "all_menus"=>$menus->findAll(),
                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                'groupe'=>$code_groupe,
                'titre'=>$titre,
                'mygroups'=>$myGroups->findBy(['groupe_system'=>true],['nom_groupe'=>'ASC']),
                'pagination' => $pagination,
                'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                'liste_parent'=>$permissions
            ]);
        } else {
                return  $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
    #[Route('snvlt/admin/all-users/', name: 'app_users_admin')]
    public function all_users(UserRepository $users,
                          MenuRepository $menus,
                          MenuPermissionRepository $permissions,
                          GroupeRepository $groupeRepository,
						  GroupeRepository $myGroups,
                          Request $request,
                          UserRepository $userRepository,
                          PaginatorInterface $paginator,
                          User $user = null,
                          NotificationRepository $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('administration/utilisateur/all-users.html.twig', [
                    'liste_users' => $users->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
					'mygroups'=>$myGroups->findBy(['groupe_system'=>true],['nom_groupe'=>'ASC']),
                    'groupe'=>$code_groupe,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'liste_parent'=>$permissions
                ]);
            } else {
                return  $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/admin/my_users', name: 'app_my_users')]
    public function my_users(UserRepository $users,
                             MenuRepository $menus,
                             MenuPermissionRepository $permissions,
                             GroupeRepository $groupeRepository,
                             GroupeRepository $myGroups,
                             Request $request,
                             UserRepository $userRepository,
                             User $user = null,
                            NotificationRepository $notification,

    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or
                $this->isGranted('ROLE_INDUSTRIEL') or
                $this->isGranted('ROLE_EXPORTATEUR') or
                $this->isGranted('ROLE_ADMINISTRATIF') or
                $this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_COMMERCANT') or
                $this->isGranted('ROLE_ADMIN')
            )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $myUsers = null;
                $type_operateur = $user->getCodeOperateur()->getId();
                $code_operateur = 0;
                        if ($user->getCodeOperateur()->getId() == 2){
                            $myUsers = $users->findBy(['codeexploitant'=>$user->getCodeexploitant()],['isResponsable'=>'DESC']);
                            $code_operateur = $user->getCodeexploitant()->getId();
                        }elseif ($user->getCodeOperateur()->getId() == 3){
                            $myUsers = $users->findBy(['codeindustriel'=>$user->getCodeindustriel()]);
                            $code_operateur = $user->getCodeindustriel()->getId();
                        }
                        elseif ($user->getCodeOperateur()->getId() == 4){
                            $myUsers = $users->findBy(['code_exportateur'=>$user->getCodeExportateur()]);
                            $code_operateur = $user->getCodeExportateur()->getId();
                        }
                        elseif ($user->getCodeOperateur()->getId() == 5){
                            $myUsers = $users->findBy(['code_dr'=>$user->getCodeDr()]);
                            $code_operateur = $user->getCodeDr()->getId();
                        }
                        elseif ($user->getCodeOperateur()->getId() == 6){
                            $myUsers = $users->findBy(['code_ddef'=>$user->getCodeDdef()]);
                            $code_operateur = $user->getCodeDdef()->getId();
                        }
                        elseif ($user->getCodeOperateur()->getId() == 7){
                            $myUsers = $users->findBy(['code_cantonnement'=>$user->getCodeCantonnement()]);
                            $code_operateur = $user->getCodeCantonnement()->getId();

                        } elseif ($user->getCodeOperateur()->getId() == 8){
                            $myUsers = $users->findBy(['code_commercant'=>$user->getCodeCommercant()]);
                            $code_operateur = $user->getCodeCommercant()->getId();

                        } elseif ($user->getCodeOperateur()->getId() == 1){
                            if ($user->getCodeService() && $user->getCodeDirection()){
                                $myUsers = $users->findBy(['code_service'=>$user->getCodeService()]);
                                $code_operateur = $user->getCodeService()->getId();

                            } elseif (!$user->getCodeService() && $user->getCodeDirection()){
                                $myUsers = $users->findBy(['code_direction'=>$user->getCodeDirection()]);
                                $code_operateur = $user->getCodeDirection()->getId();
                            }

                        }
                        elseif ($user->getCodeOperateur()->getId() == 10){
                            $myUsers = $users->findBy(['code_poste_controle'=>$user->getCodePosteControle()]);
                            $code_operateur = $user->getCodePosteControle()->getId();

                        }elseif ($user->getCodeGroupe()->getId() == 1){
                            $myUsers = $users->findBy(['code_groupe'=>$user->getCodeGroupe()]);
                            $code_operateur = 0;
                        }

                        if(!$user->getCodeGroupe()->getParentGroupe()){
                            $code_groupe = $user->getCodeGroupe()->getId();
                        } else {
                            $code_groupe = $user->getCodeGroupe()->getParentGroupe();
                        }
                        return $this->render('administration/utilisateur/myteam.html.twig', [
                            'liste_users' => $myUsers,
                            'liste_menus'=>$menus->findOnlyParent(),
                            "all_menus"=>$menus->findAll(),
                            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                            'groupe'=>$code_groupe,
                            'liste_parent'=>$permissions,
                            'mygroups'=>$myGroups->findBy(['parent_groupe'=>$code_groupe, 'code_type_operateur'=>$type_operateur, 'code_operateur'=>$code_operateur]),
                            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0)
                        ]);
                } else {
                    return $this->redirectToRoute('app_no_permission_user_active');
                }
            }
     }


    #[Route('/snvlt/secauth/lu/{id_user?0}', name: 'secure_lock_user')]
    public function lockUser(ManagerRegistry $registry, User $user = null, $id_user){
        $user = $registry->getManager()->getRepository(User::class)->find($id_user);
        if ($user){
            $user->setActif(false);
            $registry->getManager()->persist($user);
            $registry->getManager()->flush();

            return true;
        }
    }


    #[Route('/snvlt/secauth/ulu/{id_user?0}', name: 'secure_unlock_user')]
    public function unlockUser(ManagerRegistry $registry, User $user = null, $id_user){
        $user = $registry->getManager()->getRepository(User::class)->find($id_user);
        if ($user){
            $user->setActif(true);
            $registry->getManager()->persist($user);
            $registry->getManager()->flush();

            return true;
        }
    }

    #[Route('/snvlt/user/detail/{id_user?0}', name: 'user_details')]
    public function UserDetail(ManagerRegistry $registry, $id_user){
        $user = $registry->getManager()->getRepository(User::class)->find($id_user);

        $structure = "";
        $derniere = "";


        if ($user){

            //$derniere = $registry->getRepository(LogSnvlt::class)->findOneBy(['created_by'=>$user->getPrenomsUtilisateur(). " " .$user->getNomUtilisateur() ], ['created_at'=>'DESC']);
            switch ($user->getCodeOperateur()->getId()){
            case 1:
                if($user->getCodeService()){
                    $structure = $user->getCodeService()->getLibelleService();
                } elseif ($user->getCodeDirection()) {
                    $structure = $user->getCodeDirection()->getDenomination();
                }

            case 2:
				if($user->getCodeexploitant()){
					$structure = $user->getCodeexploitant()->getRaisonSocialeExploitant();
				}
				
			case 3:
				if($user->getCodeindustriel()){
					$structure = $user->getCodeindustriel()->getRaisonSocialeUsine();
				}
				
			case 4:
				if($user->getCodeExportateur()){
					$structure = $user->getCodeExportateur()->getRaisonSocialeExportateur();
				}
				
			case 5:
				if($user->getCodeDr()){
					$structure =  $user->getCodeDr()->getDenomination();
				}
				
			case 6:
			if($user->getCodeDdef()){
				$structure = $user->getCodeDdef()->getNomDdef();
			}
            
            case 7:
                
				if($user->getCodeCantonnement()){
					$structure = $user->getCodeCantonnement()->getNomCantonnement();
				}
				
			case 8:
                if($user->getCodeCommercant()){
					$structure = "N° Commerçant : " . $user->getCodeCommercant()->getNumeroCommercant(). " - Adresse : " . $user->getCodeCommercant()->getVille() . ", " .$user->getCodeCommercant()->getAdresse() . " | " . $user->getCodeCommercant()->getNationalite() ;
				}
				
			case 9:
				if($user->getCodeOi()){
					$structure = $user->getCodeOi()->getSigle() . " - " . $user->getCodeOi()->getRaisonSociale();
				}
				
			case 10:
				if($user->getCodePosteControle()){
					$structure = $user->getCodePosteControle()->getDenomination() . " / " . $user->getCodePosteControle()->getCodeCantonnement()->getNomCantonnement();
				}
				
			case 11:
                $structure = "SODEFOR";
            }

            //Ecriture du JSON
            $detailUser = json_encode([
                'id' => $user->getId(),
                'nom_prenoms' =>$user->getPrenomsUtilisateur()." ". $user->getNomUtilisateur(),
                'photo' => $user->getPhoto(),
                'email' => $user->getEmail(),
                'mobile' => $user->getMobile(),
                'fonction' => $user->getFonction(),
                'groupe'=>$user->getCodeGroupe()->getNomGroupe(),
                'type_operateur'=>$user->getCodeOperateur()->getLibelleOperateur(),
                'structure'=>$structure,
                'derniere_connexion'=>$derniere
            ]);

            return new Response($detailUser);
        }
    }

    #[Route('/snvlt/auth/add-user', name: 'secure_au_user')]
    public function adduser (UserPasswordHasherInterface $userPasswordHasher,
                             UserAuthenticatorInterface $userAuthenticator,
                             AppCustomAuthenticator $authenticator,
                             ManagerRegistry $entityManager,
                             GroupeRepository $groupe,
                             TranslatorInterface $translator,
                             Request $request,
                             MenuRepository $menus,
                             MenuPermissionRepository $permissions,
                             GroupeRepository $groupeRepository,
                             SendSMS $sendSMS,
                            Utils $utils,
                            NotificationRepository $notification){

        $session = $request->getSession();
        if (!$session->has("user_session")){
            $this->addFlash('error',  $this->translator->trans('You must log in first to access SNVLT'));
            return $this->redirectToRoute('app_login');
        } else {
            //$utils = new Utils();
            $code_groupe = $groupeRepository->find(1);
            $user_encours = $this->getUser();
            $user = new User();
            $datecreation = new \DateTimeImmutable();
            $form = $this->createForm(UtilisateurFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setNomUtilisateur($this->utils->removeSpecialCharacters(strtoupper($form->get('nom_utilisateur')->getData())));
                $user->setPrenomsUtilisateur($this->utils->removeSpecialCharacters(strtoupper($form->get('prenoms_utilisateur')->getData())));
                $user->setEmail($this->utils->removeSpecialCharacters(strtolower($form->get('email')->getData())));

                $mot_passe = $this->utils->uniqidReal(12);
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        strtoupper($mot_passe)
                    )
                );
                $roles = ['ROLE_MINEF', 'ROLE_ADMINISTRATIF'];
                if($form->get('codeexploitant')->getData('id')){
                    $roles = ['ROLE_EXPLOITANT'];
                    //dd($form->get('codeexploitant')->getData('yd'));
                    $utils->MajRespoExploitant(
                        $entityManager,
                        $form->get('codeexploitant')->getData('id'),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur(),
                        $user->getEmail(),
                        $user->getMobile(),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur()
                    );

                } elseif ($form->get('codeindustriel')->getData('id')){
                    $roles = ['ROLE_INDUSTRIEL'];
                    $utils->MajRespoIndustriel(
                        $entityManager,
                        $form->get('codeindustriel')->getData('id'),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur(),
                        $user->getEmail(),
                        $user->getMobile(),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur()
                    );

                }elseif ($form->get('code_service')->getData('id') && $form->get('code_direction')->getData('id')){
                    $roles = ['ROLE_MINEF', 'ROLE_ADMINISTRATIF'];
                    $utils->MajRespoServiceMinef(
                        $entityManager,
                        $form->get('code_service')->getData('id'),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur(),
                        $user->getEmail(),
                        $user->getMobile(),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur()
                    );
                }elseif ($form->get('code_exportateur')->getData('id')){
                    $roles = ['ROLE_EXPORTATEUR'];
                    $utils->MajRespoExportateur(
                        $entityManager,
                        $form->get('code_exportateur')->getData('id'),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur(),
                        $user->getEmail(),
                        $user->getMobile(),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur()
                    );
                }elseif (!$form->get('code_service')->getData('id') && $form->get('code_direction')->getData('id')){
                $roles = ['ROLE_MINEF', 'ROLE_ADMINISTRATIF'];
                    $utils->MajRespoDirectionMinef(
                        $entityManager,
                        $form->get('code_direction')->getData('id'),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur(),
                        $user->getEmail(),
                        $user->getMobile(),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur()
                    );
            }elseif ($form->get('code_cantonnement')->getData('id')){
                    $roles = ['ROLE_ADMINISTRATIF', 'ROLE_MINEF'];
                    $utils->MajRespoCef(
                        $entityManager,
                        $form->get('code_cantonnement')->getData('id'),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur(),
                        $user->getEmail(),
                        $user->getMobile(),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur()
                    );
                }elseif ($form->get('code_dr')->getData('id') ){
                    $roles = ['ROLE_MINEF', 'ROLE_ADMINISTRATIF'];
                    $utils->MajRespoDr(
                        $entityManager,
                        $form->get('code_dr')->getData('id'),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur(),
                        $user->getEmail(),
                        $user->getMobile(),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur()
                    );
                }elseif ($form->get('code_poste_controle')->getData('id')){
                    $roles = ['ROLE_MINEF', 'ROLE_ADMINISTRATIF'];
                    $utils->MajRespoPf(
                        $entityManager,
                        $form->get('code_poste_controle')->getData('id'),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur(),
                        $user->getEmail(),
                        $user->getMobile(),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur()
                    );
                }elseif ($form->get('code_ddef')->getData('id')){
                    $roles = ['ROLE_MINEF', 'ROLE_ADMINISTRATIF'];
                    $utils->MajRespoDdef(
                        $entityManager,
                        $form->get('code_ddef')->getData('id'),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur(),
                        $user->getEmail(),
                        $user->getMobile(),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur()
                    );
                }elseif ($form->get('code_oi')->getData('id')){
                    $roles = ['ROLE_OI'];
                    $utils->MajRespoOi(
                        $entityManager,
                        $form->get('code_oi')->getData('id'),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur(),
                        $user->getEmail(),
                        $user->getMobile(),
                        $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur()
                    );
                }


                $user->setIsVerified(true);
                $user->setIsResponsable(true);
                $user->setActif(true);
                $user->setCreatedBy($user_encours);
                $user->setCreatedAt($datecreation);
                $code = strtoupper($utils->uniqidReal(4));
                $user->setCodeSms($code);
                $user->setRoles($roles);
                $user->setLocale($form->get('locale')->getData());

                $entityManager->getManager()->persist($user);
                $entityManager->getManager()->flush();
                $texteSMS = $translator->trans("Hi ").$user->getPrenomsUtilisateur()." ". $user->getNomUtilisateur(). $this->translator->trans(" Your account has just been created in SNVLT and your verification code is ").  $code. $this->translator->trans(" Your accesses have been sent to you by email. THANKS.");

                //envoi du SMS à l'utilisateur
                /*$sendSMS->messagerie($user->getMobile(), $texteSMS);*/
                // generate a signed url and email it to the user

                $userEvent = new AddNotificationEvent($user);
                    $this->dispatcher->dispatch($userEvent, AddNotificationEvent::ADD_NOTIFICATION_EVENT);

//                $userEvent = new SendEmailEvent($user);
//                $this->dispatcher->dispatch($userEvent, SendEmailEvent::SEND_EMAIL_EVENT);
                try {
                    $this->emailVerifier->sendEmailRespoConfirmation('app_verify_email', $user,
                        (new TemplatedEmail())
                            ->from(new Address('snvlt@system2is.com', 'SNVLT INFOS'))
                            ->to($user->getEmail())
                            ->subject("Merci de confirmer SVP votre mail!")
                            ->htmlTemplate('registration/confirmation_email.html.twig')
                            ->context([
                                'nom_prenoms'=>$user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur(),
                                'mot_passe'=>strtoupper($mot_passe),
                                'email_utilisateur'=>$user->getEmail()
                            ])
                    );
                } catch (\Exception $e) {
                    // Gérer l'erreur
                    $this->addFlash('error', 'Une erreur est survenue durant l\'enregistrement: ' . $e->getMessage());
                    return  $this->redirectToRoute("app_utilisateur");
//                    return new Response('Une erreur est survenue : ' . $e->getMessage());
                }


                // do anything else you need here, like send an email
                $this->addFlash('succes', $this->translator->trans("The structure manager has just been created"));
                $this->addFlash('succes', $this->translator->trans("The logging company has been updated"));
                return  $this->redirectToRoute("app_utilisateur");
            }

            return $this->render('Administration/utilisateur/add-user.html.twig', [
                'responsableForm' => $form->createView(),
                'liste_menus'=>$menus->findOnlyParent(),
                "all_menus"=>$menus->findAll(),
                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                'liste_parent'=>$permissions
            ]);
        }
    }

    #[Route('/snvlt/profile/{id_utilisateur?0}', name: 'my_profile')]
    public function myProfile (UserPasswordHasherInterface $userPasswordHasher,
                             UserAuthenticatorInterface $userAuthenticator,
                             AppCustomAuthenticator $authenticator,
                             ManagerRegistry $entityManager,
                             GroupeRepository $groupe,
                             TranslatorInterface $translator,
                             Request $request,
                             MenuRepository $menus,
                             MenuPermissionRepository $permissions,
                             GroupeRepository $groupeRepository,
                             SendSMS $sendSMS,
                             Utils $utils,
                               int $id_utilisateur,
                               UserRepository $userRepository,
                               User $user = null,
                               User $utilisateur = null,
                               NotificationRepository $notification){

        $session = $request->getSession();
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            $user = $userRepository->find($this->getUser());
            $code_groupe = $user->getCodeGroupe()->getId();

            if ($user->getId() == $id_utilisateur)
            {
                $titre = $translator->trans("Edit my profile");
                $utilisateur = $userRepository->find($id_utilisateur);
                $form = $this->createForm(ProfileFormType::class, $user);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){
                    //dd($user);
                    $MajDate = new \DateTimeImmutable();

                    $photo = $form->get('photo')->getData();

                    if ($photo) {$originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                        // this is needed to safely include the file name as part of the URL
                        $safeFilename = $this->slugger->slug($originalFilename);
                        $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                        // Move the file to the directory where brochures are stored
                        try {
                            $photo->move(
                                $this->getParameter('users_directory'),
                                $newFilename
                            );
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }

                        // updates the 'brochureFilename' property to store the PDF file name
                        // instead of its contents
                        $user->setPhoto($newFilename);
                    }


                    $user->setCreatedAt($MajDate);
                    $user->setUpdatedBy($user->getNomUtilisateur(). " ". $user->getPrenomsUtilisateur());


                    $manager = $entityManager->getManager();
                    $manager->persist($user);
                    $manager->flush();

                    $this->addFlash('success',$this->translator->trans("Your profile has been edited successfully"));
                    return $this->redirect($request->getUri());
                } else {
                    return $this->render('administration/utilisateur/profile.html.twig', [
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'form' =>$form->createView(),
                        'titre'=>$titre,
                        'groupe'=>$code_groupe,
                        'liste_parent'=>$permissions,
                        'mes_infos'=>$user,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0)
                    ]);
                }
            }else {
                return $this->render('exceptions/user-active-pas-de-permissions.html.twig',[
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0)
                ]);
            }
        }
      }

        #[Route('/snvlt/profile/change/{id_utilisateur}/{id_groupe}', name: 'change_profile')]
        public function changeProfile(
            int $id_utilisateur,
            int $id_groupe,
            Request  $request,
            User $user = null,
            User $currentUser = null,
            Groupe $groupe = null,
            UserRepository $userRepository,
            GroupeRepository $groupeRepository,
            ManagerRegistry $registry
        ):Response
        {
            if(!$request->getSession()->has('user_session')){
                return $this->redirectToRoute('app_login');
            } else {
                if (!$this->isGranted('ADMIN')) {

                    $user = $userRepository->find($id_utilisateur);
                    $currentUser = $userRepository->find($this->getUser());
                    $groupe = $groupeRepository->find($id_groupe);

                    if($user){
                        $user->setCodeGroupe($groupe);
                        $registry->getManager()->persist($user);
                        $registry->getManager()->flush();

                        $this->utils->envoiNotification(
                            $registry,
                            "User Profile",
                            "Hi, Your user profile has been changed by your administrator. You have probabily new interfaces",
                            $user,
                            $currentUser->getId(),
                            "app_notifs",
                            "PROFILE",
                            $user->getId()
                        );


                    }
                    return $this->redirectToRoute('app_my_users');
                } else {
                    return $this->redirectToRoute('app_no_permission_user_active');
                }
            }
        }

    #[Route('/snvlt/profile/change_responsable/{id_utilisateur}/{id_groupe}', name: 'change_profile_respo')]
    public function changeProfileRespo(
        int $id_utilisateur,
        int $id_groupe,
        Request  $request,
        User $user = null,
        User $currentUser = null,
        Groupe $groupe = null,
        UserRepository $userRepository,
        GroupeRepository $groupeRepository,
        ManagerRegistry $registry
    ):Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if (!$this->isGranted('ADMIN')) {

                $user = $userRepository->find($id_utilisateur);
                $currentUser = $userRepository->find($this->getUser());
                $groupe = $groupeRepository->find($id_groupe);

                if($user){
                    $user->setCodeGroupe($groupe);
                    $registry->getManager()->persist($user);
                    $registry->getManager()->flush();

                    $this->utils->envoiNotification(
                        $registry,
                        "User Profile",
                        "Hi, Your user profile has been changed by your administrator. You have probabily new interfaces",
                        $user,
                        $currentUser->getId(),
                        "app_notifs",
                        "PROFILE",
                        $user->getId()
                    );


                }
                return $this->redirectToRoute('app_utilisateur');
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/json_user/{id_user}', name: 'user_json.list')]
    public function affiche_user_infos(
        ManagerRegistry $registry,
        TypeAutorisationRepository $type_autorisations,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        User $profil = null,
        int $id_user,
        Request $request,
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {


                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $profil = $userRepository->find($id_user);
                if($profil){

                    /*$liste_docs_attribution = array();

                    foreach ($docs_attribution as $doc) {
                        $liste_docs_attribution[] = array(
                            'id' => $doc->getId(), //ID du document issu de la grille légalité
                            'libelle' => $doc->getLibelleDocument()
                        );
                    }*/

                return new JsonResponse(json_encode($profil));
                }else{
                    return new JsonResponse("BAD_USER_ID");
                }
        }
    }

    #[Route(path: '/before-logout', name: 'app_before_logout')]
    public function before_logout(ManagerRegistry $registry): Response
    {

        $user = $registry->getRepository(User::class)->find($this->getUser());
        $maj =  new \DateTimeImmutable();
        $this->service->save_action(
            $user,
            "UTILISATEUR",
            "DECONNEXION",
            $maj,
            "L'utilisateur ". $user . " vient de se deconnecter a la date du " . $maj->format("d/m/Y h:i:s")
        );
        return $this->redirectToRoute('app_logout');
    }

    #[Route(path: '/register/new/member', name: 'register_new')]
    public function register_new(ManagerRegistry $registry): Response
    {

        return $this->render('administration/utilisateur/register_new.html.twig', [
            'operateurs' => $registry->getRepository(TypeOperateur::class)->findBy([],['libelle_operateur'=>'ASC']),
            'exploitants'=> $registry->getRepository(Exploitant::class)->findOnlyManager(),
            'usines'=> $registry->getRepository(Usine::class)->findOnlyManager(),
            'exportateurs'=> $registry->getRepository(Exportateur::class)->findOnlyManager(),
            'drs'=> $registry->getRepository(Dr::class)->findOnlyManager(),
            'cefs'=> $registry->getRepository(Cantonnement::class)->findOnlyManager(),
            'ddefs'=> $registry->getRepository(Ddef::class)->findOnlyManager(),
            'pfs'=> $registry->getRepository(PosteForestier::class)->findOnlyManager(),
            'directions'=> $registry->getRepository(Direction::class)->findWithResponsable(),

        ]);
    }

    #[Route('snvlt/getusers/{id_user}', name: 'my_users.list')]
    public function getMyUsers(
        ManagerRegistry $registry,
        UserRepository $userRepository,
        int $id_user,
        Request $request,
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {

            $user_respo = $userRepository->find($id_user);
            $my_users = array();

            if ($user_respo){
                $code_operateur = $user_respo->getCodeOperateur();

                if ($code_operateur){
                    if ($code_operateur->getId() == 1){
                        if ($user_respo->getCodeService()){
                            $users = $registry->getRepository(User::class)->findBy(['code_service'=>$user_respo->getCodeService()]);
                            foreach ($users as $uniq_user){
                                $my_users[] = array(
                                    'id_user'=>$uniq_user->getId(),
                                    'nom_prenoms'=>strtoupper($uniq_user->getNomUtilisateur() . " " . $uniq_user->getPrenomsUtilisateur())
                            );
                            }
                        } elseif (!$user_respo->getCodeService() && $user_respo->getCodeDirection()){
                            $users = $registry->getRepository(User::class)->findBy(['code_direction'=>$user_respo->getCodeDirection()]);
                            foreach ($users as $uniq_user){
                                if (!$uniq_user->getCodeService()){
                                    $my_users[] = array(
                                        'id_user'=>$uniq_user->getId(),
                                        'nom_prenoms'=>strtoupper($uniq_user->getNomUtilisateur() . " " . $uniq_user->getPrenomsUtilisateur())
                                        );
                                    }
                                }

                            }
                    }elseif ($code_operateur->getId() == 2){
                        $users = $registry->getRepository(User::class)->findBy(['codeexploitant'=>$user_respo->getCodeexploitant()]);
                        foreach ($users as $uniq_user){
                            $my_users[] = array(
                                'id_user'=>$uniq_user->getId(),
                                'nom_prenoms'=>strtoupper($uniq_user->getNomUtilisateur() . " " . $uniq_user->getPrenomsUtilisateur())
                            );
                        }
                    }elseif ($code_operateur->getId() == 3){
                        $users = $registry->getRepository(User::class)->findBy(['codeindustriel'=>$user_respo->getCodeindustriel()]);
                        foreach ($users as $uniq_user){
                            $my_users[] = array(
                                'id_user'=>$uniq_user->getId(),
                                'nom_prenoms'=>strtoupper($uniq_user->getNomUtilisateur() . " " . $uniq_user->getPrenomsUtilisateur())
                            );
                        }
                    }
                }
            }

                return new JsonResponse(json_encode($my_users));
            } else {
                return  $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/remove_respo/r/{old_respo_id}/{new_respo_id}', name: 'change_respo')]
    public function change_respo(
        ManagerRegistry $registry,
        UserRepository $userRepository,
        int $old_respo_id,
        int $new_respo_id,
        Request $request,
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {

                $old_respo = $userRepository->find($old_respo_id);
                $new_respo = $userRepository->find($new_respo_id);

                $my_users = array();

                if ($old_respo && $new_respo){


                    // Ajouter le statut Responsable sur le nouveau Responsable
                    $new_respo->setIsResponsable(true);
                    $new_respo->setCodeGroupe($old_respo->getCodeGroupe());
                    $registry->getManager()->persist($new_respo);

                    // Enlever le statut Responsable sur l'ancien Responsable
                    $old_respo->setIsResponsable(false);
                    $registry->getManager()->persist($old_respo);


                    //Mise à jour Table concernée
                    if ($old_respo->getCodeOperateur()->getId() == 2){
                        $exploitant = $registry->getRepository(Exploitant::class)->find($old_respo->getCodeexploitant());
                        $exploitant->setPersonneRessource($new_respo->getNomUtilisateur(). " ". $new_respo->getPrenomsUtilisateur());
                        $exploitant->setMobilePersonneRessource($new_respo->getMobile());
                        $exploitant->setEmailPersonneRessource($new_respo->getEmail());
                        $registry->getManager()->persist($exploitant);

                    } elseif ($old_respo->getCodeOperateur()->getId() == 3){
                        $usine = $registry->getRepository(Usine::class)->find($old_respo->getCodeindustriel());
                        $usine->setPersonneRessource($new_respo->getNomUtilisateur(). " ". $new_respo->getPrenomsUtilisateur());
                        $usine->setMobilePersonneRessource($new_respo->getMobile());
                        $usine->setEmailPersonneRessource($new_respo->getEmail());
                        $registry->getManager()->persist($usine);

                    } elseif ($old_respo->getCodeOperateur()->getId() == 4){
                        $exportateur = $registry->getRepository(Exportateur::class)->find($old_respo->getCodeExportateur());
                        $exportateur->setPersonneRessource($new_respo->getNomUtilisateur(). " ". $new_respo->getPrenomsUtilisateur());
                        $exportateur->setMobilePersonneRessource($new_respo->getMobile());
                        $exportateur->setEmailPersonneRessource($new_respo->getEmail());
                        $exportateur->getManager()->persist($exportateur);
                    }
                    elseif ($old_respo->getCodeOperateur()->getId() == 5){
                        $dr = $registry->getRepository(Dr::class)->find($old_respo->getCodeDr());
                        $dr->setPersonneRessource($new_respo->getNomUtilisateur(). " ". $new_respo->getPrenomsUtilisateur());
                        $dr->setMobilePersonneRessource($new_respo->getMobile());
                        $dr->setEmailPersonneRessource($new_respo->getEmail());
                        $dr->getManager()->persist($dr);
                    }
                    elseif ($old_respo->getCodeOperateur()->getId() == 6){
                        $ddef = $registry->getRepository(Ddef::class)->find($old_respo->getCodeDdef());
                        $ddef->setPersonneRessource($new_respo->getNomUtilisateur(). " ". $new_respo->getPrenomsUtilisateur());
                        $ddef->setMobilePersonneRessource($new_respo->getMobile());
                        $ddef->setEmailPersonneRessource($new_respo->getEmail());
                        $ddef->getManager()->persist($ddef);
                    }
                    elseif ($old_respo->getCodeOperateur()->getId() == 7){
                        $cantonnement = $registry->getRepository(Cantonnement::class)->find($old_respo->getCodeCantonnement());
                        $cantonnement->setPersonneRessource($new_respo->getNomUtilisateur(). " ". $new_respo->getPrenomsUtilisateur());
                        $cantonnement->setMobilePersonneRessource($new_respo->getMobile());
                        $cantonnement->setEmailPersonneRessource($new_respo->getEmail());
                        $cantonnement->getManager()->persist($cantonnement);
                    }
                    elseif ($old_respo->getCodeOperateur()->getId() == 1){
                        if ($old_respo->getCodeService()){
                            $serviceminef = $registry->getRepository(ServiceMinef::class)->find($old_respo->getCodeService());
                            $serviceminef->setPersonneRessource($new_respo->getNomUtilisateur(). " ". $new_respo->getPrenomsUtilisateur());
                            $serviceminef->setMobilePersonneRessource($new_respo->getMobile());
                            $serviceminef->setEmailPersonneRessource($new_respo->getEmail());
                            $serviceminef->getManager()->persist($serviceminef);

                        } else if (!$old_respo->getCodeService() && $old_respo->getCodeDirection()){
                            $direction = $registry->getRepository(Direction::class)->find($old_respo->getCodeDirection());
                            $direction->setPersonneRessource($new_respo->getNomUtilisateur(). " ". $new_respo->getPrenomsUtilisateur());
                            $direction->setMobilePersonneRessource($new_respo->getMobile());
                            $direction->setEmailPersonneRessource($new_respo->getEmail());
                            $direction->getManager()->persist($direction);
                        }

                    }

                    //Enregistrement des changements en base
                    $registry->getManager()->flush();

                    $my_users[] = array(
                        'code'=>'SUCCESS'
                    );
                }

                return new JsonResponse(json_encode($my_users));
            } else {
                return  $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/auth/add-single-user/admin', name: 'secure_asu_user')]
    public function addSingleuser (UserPasswordHasherInterface $userPasswordHasher,
                             UserAuthenticatorInterface $userAuthenticator,
                             AppCustomAuthenticator $authenticator,
                             ManagerRegistry $entityManager,
                             GroupeRepository $groupe,
                             TranslatorInterface $translator,
                             Request $request,
                             MenuRepository $menus,
                             MenuPermissionRepository $permissions,
                             GroupeRepository $groupeRepository,
                             SendSMS $sendSMS,
                             Utils $utils,
                             NotificationRepository $notification){

        $session = $request->getSession();
        if (!$session->has("user_session")){
            $this->addFlash('error',  $this->translator->trans('You must log in first to access SNVLT'));
            return $this->redirectToRoute('app_login');
        } else {
            //$utils = new Utils();
            $code_groupe = $groupeRepository->find(1);
            $user = $this->getUser();

            return $this->render('Administration/utilisateur/add-single-user.html.twig', [
                'liste_menus'=>$menus->findOnlyParent(),
                "all_menus"=>$menus->findAll(),
                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                'liste_parent'=>$permissions,
                'titres'=>$entityManager->getRepository(Titre::class)->findAll(),
                'type_op'=>$entityManager->getRepository(TypeOperateur::class)->findAll(),
                'groupes'=>$entityManager->getRepository(Groupe::class)->findBy(['parent_groupe'=>0],['nom_groupe'=>'ASC']),
            ]);
        }
    }

    #[Route('/snvlt/auth/search-single-user/admin/{id_user}', name: 'secure_asu_user_search')]
    public function searchSingleuser (
                                   ManagerRegistry $entityManager,
                                   Request $request,
                                   MenuRepository $menus,
                                   MenuPermissionRepository $permissions,

                                   int $id_user,
                                   NotificationRepository $notification){

        $session = $request->getSession();
        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN')) {
                $infos_user = array();
                $utilisateur = $entityManager->getRepository(User::class)->find($id_user);
                if ($utilisateur){
                    $entite = "-";
                    if ($utilisateur->getCodeindustriel()) { $code_industriel = $utilisateur->getCodeindustriel()->getId(); $entite = $utilisateur->getCodeindustriel()->getId();} else { $code_industriel = 0;}
                    if ($utilisateur->getCodeexploitant()) { $code_exploitant = $utilisateur->getCodeexploitant()->getId();$entite = $utilisateur->getCodeexploitant()->getId();} else { $code_exploitant = 0;}
                    if ($utilisateur->getCodeExportateur()) { $code_exportateur = $utilisateur->getCodeExportateur()->getId();$entite = $utilisateur->getCodeExportateur()->getId();} else { $code_exportateur= 0;}
                    if ($utilisateur->getCodeCantonnement()) { $code_cef = $utilisateur->getCodeCantonnement()->getId();$entite = $utilisateur->getCodeCantonnement()->getId();} else { $code_cef = 0;}
                    if ($utilisateur->getCodeDr()) { $code_dr = $utilisateur->getCodeDr()->getId();$entite = $utilisateur->getCodeDr()->getId();} else { $code_dr = 0;}
                    if ($utilisateur->getCodeDdef()) { $code_ddef = $utilisateur->getCodeDdef()->getId();$entite = $utilisateur->getCodeDdef()->getId();} else { $code_ddef = 0;}
                    if ($utilisateur->getCodePosteControle()) { $code_pf = $utilisateur->getCodePosteControle()->getId();$entite = $utilisateur->getCodePosteControle()->getId();} else { $code_pf = 0;}
                    if ($utilisateur->getCodeDirection()) { $code_direction = $utilisateur->getCodeDirection()->getId();$entite = $utilisateur->getCodeDirection()->getId();} else { $code_direction = 0;}
                    if ($utilisateur->getCodeService()) { $code_service = $utilisateur->getCodeService()->getId();$entite = $utilisateur->getCodeService()->getId();} else { $code_service = 0;}
                    if ($utilisateur->getCodeOi()) { $code_oi = $utilisateur->getCodeOi()->getId();$entite = $utilisateur->getCodeOi()->getId();} else { $code_oi = 0;}
                    if ($utilisateur->getCodeCommercant()) { $code_commercant = $utilisateur->getCodeCommercant()->getId();$entite = $utilisateur->getCodeCommercant()->getId();} else { $code_commercant = 0;}
                    if ($utilisateur->getTitre()) { $titre = $utilisateur->getTitre()->getId();} else { $titre = 0;}
                    if ($utilisateur->getCodeGroupe()) { $groupe_utilisateur = $utilisateur->getCodeGroupe()->getId();} else { $groupe_utilisateur = 0;}
                    if ($utilisateur->getCodeOperateur()) { $op = $utilisateur->getCodeOperateur()->getId();} else { $op = 0;}


                    $infos_user[] = array(
                        'json_code'=>true,
                        'nom'=>$utilisateur->getNomUtilisateur(),
                        'prenom'=>$utilisateur->getPrenomsUtilisateur(),
                        'email'=>$utilisateur->getEmail(),
                        'mobile'=>$utilisateur->getMobile(),
                        'groupe'=>$groupe_utilisateur,
                        'code_op'=>$op,
                        'code_industriel'=>$code_industriel,
                        'code_exploitant'=>$code_exploitant,
                        'code_cef'=>$code_cef,
                        'code_exportateur'=>$code_exportateur,
                        'code_direction'=>$code_direction,
                        'code_service'=>$code_service,
                        'code_ddef'=>$code_ddef,
                        'code_dr'=>$code_dr,
                        'code_oi'=>$code_oi,
                        'code_commercant'=>$code_commercant,
                        'code_pf'=>$code_pf,
                        'locale'=>$utilisateur->getLocale(),
                        'actif'=>$utilisateur->getActif(),
                        'verified'=>$utilisateur->isVerified(),
                        'responsable'=>$utilisateur->isIsResponsable(),
                        'fonction'=>$utilisateur->getFonction(),
                        'nouveau'=>$utilisateur->getNouveau(),
                        'sodefor'=>$utilisateur->getAgentSodef(),
                        'titre'=>$titre,
                        'statut'=>$utilisateur->getStatut(),
                        'roles'=>$utilisateur->getRoles(),
                        'entite'=>$entite
                    );

                } else {
                    $infos_user[] = array(
                        'json_code'=>false
                    );
                }
                return  new JsonResponse(json_encode($infos_user));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }


        }
    }

    #[Route('snvlt/users/edit-user/cgmdp/{id_user}', name: 'change_mdp')]
        public function change_mdp (UserPasswordHasherInterface $userPasswordHasher,
                                    UserAuthenticatorInterface $userAuthenticator,
                                    AppCustomAuthenticator $authenticator,
                                   ManagerRegistry $entityManager,
                                   Request $request,
                                   MenuRepository $menus,
                                   MenuPermissionRepository $permissions,

                                   int $id_user,
                                   NotificationRepository $notification,
                                   UserSnvlt1 $snvlt1){

        $session = $request->getSession();
        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN')) {
                $infos_user = array();
                $utilisateur = $entityManager->getRepository(User::class)->find($id_user);
                if ($utilisateur){
                    $mot_passe = $this->utils->uniqidReal(12);
                    $utilisateur->setPassword(
                        $userPasswordHasher->hashPassword(
                            $utilisateur,
                            strtoupper($mot_passe)
                            )
                        );
                    $entityManager->getManager()->persist($utilisateur);
                    $entityManager->getManager()->flush();

                    // MISE 0 JOUR DU MOT DE PASSE DANS SNVLT1
                    $snvlt1->Change_Pwd_User($utilisateur, strtoupper($mot_passe));
                    $infos_user[] = array(
                        'json_code'=>true,
                        'code'=>strtoupper($mot_passe)
                    );

                } else {
                    $infos_user[] = array(
                        'json_code'=>false,
                        'code'=>''
                    );
                }
                return  new JsonResponse(json_encode($infos_user));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }


        }
    }

    #[Route('/snvlt/users/create-single-user/{data}', name: 'add_user_on_demand')]
    public function addUserOnDemand (UserPasswordHasherInterface $userPasswordHasher,
                                   UserAuthenticatorInterface $userAuthenticator,
                                   AppCustomAuthenticator $authenticator,
                                   ManagerRegistry $entityManager,
                                   GroupeRepository $groupe,
                                   TranslatorInterface $translator,
                                   Request $request,
                                   ManagerRegistry $registry,
                                   string $data,
                                   MenuRepository $menus,
                                   MenuPermissionRepository $permissions,
                                   GroupeRepository $groupeRepository,
                                   SendSMS $sendSMS,
                                   Utils $utils,
                                   NotificationRepository $notification,
                                    UserSnvlt1 $snvlt1)
    {

        $session = $request->getSession();
        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF')) {
                if ($data) {

                    $my_user = array();
                    //Decoder le JSON BRH
                    $arraydata = json_decode($data);
                    $recherche_email = $registry->getRepository(User::class)->findOneBy(['email'=>$arraydata->email]);
                    if ($recherche_email){
                        $my_user[] = array(
                            'code' => 'EMAIL_EXIST',
                            'mot_passe' => ''
                        );
                    } else {


                        $user = new User();
                        //dd($arraydata->numero_lignepagebrh);


                        $date_jour = new \DateTimeImmutable();

                        //Mot de passe aleatoire
                        $mot_passe = $this->utils->uniqidReal(12);
                        $user->setPassword(
                            $userPasswordHasher->hashPassword(
                                $user,
                                strtoupper($mot_passe)
                            )
                        );
                        $user->setNomUtilisateur(strtoupper($arraydata->nom));
                        $user->setPrenomsUtilisateur(strtoupper($arraydata->prenoms));
                        $user->setEmail($arraydata->email);
                        $user->setMobile($arraydata->mobile);
                        $user->setTitre($registry->getRepository(Titre::class)->find((int)$arraydata->titre));
                        $user->setCodeGroupe($registry->getRepository(Groupe::class)->find((int)$arraydata->groupe));
                        $user->setLocale($arraydata->langue);
                        $user->setCodeOperateur($registry->getRepository(TypeOperateur::class)->find((int)$arraydata->op));

                        $code_op = (int)$arraydata->op;
						
                        if($code_op == 1) {
                            if ($arraydata->rattachement == "1") {
                                $user->setCodeDirection($registry->getRepository(Direction::class)->find((int)$arraydata->entite));
                                $user->setRoles(['ROLE_MINEF']);
                            } elseif ($arraydata->rattachement == "2") {
                                $user->setCodeService($registry->getRepository(ServiceMinef::class)->find((int)$arraydata->entite));
                                $user->setCodeDirection($registry->getRepository(Direction::class)->find($registry->getRepository(ServiceMinef::class)->find((int)$arraydata->entite)->getCodeDirection()));
                                $user->setRoles(['ROLE_MINEF']);
                            }
                        } elseif ($code_op == 2) {
                            $user->setCodeexploitant($registry->getRepository(Exploitant::class)->find((int)$arraydata->entite));
                            $user->setRoles(['ROLE_EXPLOITANT']);
                        } elseif ($code_op == 3) {
                            $user->setCodeindustriel($registry->getRepository(Usine::class)->find((int)$arraydata->entite));
                            $user->setRoles(['ROLE_INDUSTRIEL']);
                        } elseif ($code_op == 4) {
                            $user->setCodeExportateur($registry->getRepository(Exportateur::class)->find((int)$arraydata->entite));
                            $user->setRoles(['ROLE_EXPORTATEUR']);
                        } elseif ($code_op == 5) {
                            $user->setCodeDr($registry->getRepository(Dr::class)->find((int)$arraydata->entite));
                            $user->setRoles(['ROLE_MINEF', 'ROLE_DR']);
                        } elseif ($code_op == 6) {
                            $user->setCodeDdef($registry->getRepository(Ddef::class)->find((int)$arraydata->entite));
                            $user->setRoles(['ROLE_MINEF', 'ROLE_DDEF']);
                        } elseif ($code_op == 7) {
                            $user->setCodeCantonnement($registry->getRepository(Cantonnement::class)->find((int)$arraydata->entite));
                            $user->setRoles(['ROLE_MINEF', 'ROLE_CEF']);
                        } elseif ($code_op == 8) {
                            $user->setCodeCommercant($registry->getRepository(Commercant::class)->find((int)$arraydata->entite));
                            $user->setRoles(['ROLE_COMMERCANT']);
                        } elseif ($code_op == 9) {
                            $user->setCodeOi($registry->getRepository(Oi::class)->find((int)$arraydata->entite));
                            $user->setRoles(['ROLE_OI']);
                        } elseif ($code_op == 10) {
                            $user->setCodePosteControle($registry->getRepository(PosteForestier::class)->find((int)$arraydata->entite));
                            $user->setRoles(['ROLE_MINEF', 'ROLE_PF']);
                        } elseif ($code_op == 11) {
                            $user->setAgentSodef(true);
                            $user->setRoles(['ROLE_MINEF', 'ROLE_SODEFOR']);
                        }
						
						//dd($user->getRoles());
                        $user->setStatut(true);
                        $user->setCreatedAt($date_jour);
                        $user->setNouveau(false);
                        $user->setIsVerified(true);
                        $user->setIsResponsable(false);
                        $user->setActif(true);

                        $user->setFonction(strtoupper($arraydata->fonction));

                        $registry->getManager()->persist($user);
                        $registry->getManager()->flush();

                        //Enregistrement dans SNVLT1
                        $snvlt1->Ajout_User($user, strtoupper($mot_passe));
                        $my_user[] = array(
                            'code' => 'SUCCESS',
                            'mot_passe' => $mot_passe
                        );

                    }
                } else {
                    $my_user[] = array(
                        'code' => 'ERROR',
                        'mot_passe' => ''
                    );
                }
                return new JsonResponse(json_encode($my_user));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/users/edit-single-user/{id_user}/{data}', name: 'edit_user_on_demand')]
    public function editUserOnDemand (UserPasswordHasherInterface $userPasswordHasher,
                                     UserAuthenticatorInterface $userAuthenticator,
                                     AppCustomAuthenticator $authenticator,
                                     ManagerRegistry $entityManager,
                                     GroupeRepository $groupe,
                                     TranslatorInterface $translator,
                                     Request $request,
                                     ManagerRegistry $registry,
                                     string $data,
                                     int $id_user,
                                     UserSnvlt1 $snvlt1)
    {

        $session = $request->getSession();
        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF')) {
                $utilisateur = $registry->getRepository(User::class)->find($id_user);
                if ($utilisateur){
                    if ($data) {

                        $my_user = array();
                        //Decoder le JSON BRH
                        $arraydata = json_decode($data);
                        $recherche_email = $registry->getRepository(User::class)->findOneBy(['email'=>$arraydata->email]);

                            $date_jour = new \DateTimeImmutable();

                            //Mot de passe aleatoire

                        $utilisateur->setNomUtilisateur(strtoupper($this->utils->removeSpecialCharacters($arraydata->nom)));
                        $utilisateur->setPrenomsUtilisateur(strtoupper($this->utils->removeSpecialCharacters($arraydata->prenoms)));
                        $utilisateur->setEmail($arraydata->email);
                        $utilisateur->setMobile($arraydata->mobile);
                        $utilisateur->setTitre($registry->getRepository(Titre::class)->find((int)$arraydata->titre));
                        $utilisateur->setCodeGroupe($registry->getRepository(Groupe::class)->find((int)$arraydata->groupe));
                        $utilisateur->setLocale($arraydata->langue);
                        $utilisateur->setCodeOperateur($registry->getRepository(TypeOperateur::class)->find((int)$arraydata->op));

                            $code_op = (int)$arraydata->op;
                            $utilisateur->setCodeOi(null);
                            //$utilisateur->setCodeDirection(null);
                            //$utilisateur->setCodeService(null);
                            $utilisateur->setCodePosteControle(null);
                            $utilisateur->setCodeCommercant(null);
                            $utilisateur->setCodeCantonnement(null);
                            $utilisateur->setCodeDdef(null);
                            $utilisateur->setCodeDr(null);
                            $utilisateur->setCodeExportateur(null);
                            $utilisateur->setCodeindustriel(null);
                            $utilisateur->setCodeexploitant(null);
                            if($code_op == 1) {
                                if ($arraydata->rattachement == "1") {
                                    $utilisateur->setCodeDirection($registry->getRepository(Direction::class)->find((int)$arraydata->entite));
                                    //$utilisateur->setRoles(['ROLE_MINEF']);
                                } elseif ($arraydata->rattachement == "2") {
                                    $utilisateur->setCodeService($registry->getRepository(ServiceMinef::class)->find((int)$arraydata->entite));
                                    $utilisateur->setCodeDirection($registry->getRepository(Direction::class)->find($registry->getRepository(ServiceMinef::class)->find((int)$arraydata->entite)->getCodeDirection()));
                                    //$utilisateur->setRoles(['ROLE_MINEF']);
                                }
                            } elseif ($code_op == 2) {
                                $utilisateur->setCodeexploitant($registry->getRepository(Exploitant::class)->find((int)$arraydata->entite));

                            } elseif ($code_op == 3) {
                                $utilisateur->setCodeindustriel($registry->getRepository(Usine::class)->find((int)$arraydata->entite));

                            } elseif ($code_op == 4) {
                                $utilisateur->setCodeExportateur($registry->getRepository(Exportateur::class)->find((int)$arraydata->entite));

                            } elseif ($code_op == 5) {
                                $utilisateur->setCodeDr($registry->getRepository(Dr::class)->find((int)$arraydata->entite));

                            } elseif ($code_op == 6) {
                                $utilisateur->setCodeDdef($registry->getRepository(Ddef::class)->find((int)$arraydata->entite));

                            } elseif ($code_op == 7) {
                                $utilisateur->setCodeCantonnement($registry->getRepository(Cantonnement::class)->find((int)$arraydata->entite));

                            } elseif ($code_op == 8) {
                                $utilisateur->setCodeCommercant($registry->getRepository(Commercant::class)->find((int)$arraydata->entite));

                            } elseif ($code_op == 9) {
                                $utilisateur->setCodeOi($registry->getRepository(Oi::class)->find((int)$arraydata->entite));

                            } elseif ($code_op == 10) {
                                $utilisateur->setCodePosteControle($registry->getRepository(PosteForestier::class)->find((int)$arraydata->entite));

                            } elseif ($code_op == 11) {
                                $utilisateur->setAgentSodef(true);

                            }

                            //dd($user->getRoles());


                        $utilisateur->setFonction(strtoupper($this->utils->removeSpecialCharacters($arraydata->fonction)));
                        $utilisateur->setRoles($arraydata->roles);

                        if ((int) $arraydata->sodef == 1){$utilisateur->setAgentSodef(true);}else {$utilisateur->setAgentSodef(false);}
                        if ((int) $arraydata->nouveau == 1){$utilisateur->setNouveau(true);}else {$utilisateur->setNouveau(false);}
                        if ((int) $arraydata->actif == 1){$utilisateur->setActif(true);}else {$utilisateur->setActif(false);}
                        if ((int) $arraydata->verified == 1){$utilisateur->setIsVerified(true);}else {$utilisateur->setIsVerified(false);}

                        if ((int) $arraydata->statut == 1){$utilisateur->setStatut(true);}else {$utilisateur->setStatut(false);}

                        if ((int) $arraydata->responsable == 1){


                            //Mis à jour de la table associée
                                // Pour chaque structure il faut retirer le statut responsable
                            if($utilisateur->getCodeexploitant()){
                                        //Elimination du statut Responsable
                                        $usersStructure = $registry->getRepository(User::class)->findBy(
                                            [
                                                'codeexploitant'=>$utilisateur->getCodeexploitant()
                                            ]
                                        );


                                $this->utils->MajRespoExploitant(
                                    $entityManager,
                                    $utilisateur->getCodeexploitant(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur(),
                                    $utilisateur->getEmail(),
                                    $utilisateur->getMobile(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur()
                                );

                            } elseif ($utilisateur->getCodeindustriel()){
                                //Elimination du statut Responsable
                                $usersStructure = $registry->getRepository(User::class)->findBy(
                                    [
                                        'codeindustriel'=>$utilisateur->getCodeindustriel()
                                    ]
                                );


                                $this->utils->MajRespoIndustriel(
                                    $entityManager,
                                    $utilisateur->getCodeindustriel(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur(),
                                    $utilisateur->getEmail(),
                                    $utilisateur->getMobile(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur()
                                );

                            } elseif (!$utilisateur->getCodeService() && $utilisateur->getCodeDirection()){
                                //Elimination du statut Responsable
                                $usersStructure = $registry->getRepository(User::class)->findBy(
                                    [
                                        'code_direction'=>$utilisateur->getCodeDirection()
                                    ]
                                );

                                $this->utils->MajRespoDirectionMinef(
                                    $entityManager,
                                    $utilisateur->getCodeDirection(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur(),
                                    $utilisateur->getEmail(),
                                    $utilisateur->getMobile(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur()
                                );
                            } elseif ($utilisateur->getCodeExportateur()){
                                //Elimination du statut Responsable
                                $usersStructure = $registry->getRepository(User::class)->findBy(
                                    [
                                        'code_exportateur'=>$utilisateur->getCodeExportateur()
                                    ]
                                );


                                $this->utils->MajRespoExportateur(
                                    $entityManager,
                                    $utilisateur->getCodeExportateur(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur(),
                                    $utilisateur->getEmail(),
                                    $utilisateur->getMobile(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur()
                                );
                            } elseif ($utilisateur->getCodeService() && $utilisateur->getCodeDirection()){
                                //Elimination du statut Responsable
                                $usersStructure = $registry->getRepository(User::class)->findBy(
                                    [
                                        'code_service'=>$utilisateur->getCodeService()
                                    ]
                                );

                                $this->utils->MajRespoServiceMinef(
                                    $entityManager,
                                    $utilisateur->getCodeService(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur(),
                                    $utilisateur->getEmail(),
                                    $utilisateur->getMobile(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur()
                                );
                            } elseif ($utilisateur->getCodeCantonnement()){
                                //Elimination du statut Responsable
                                $usersStructure = $registry->getRepository(User::class)->findBy(
                                    [
                                        'code_cantonnement'=>$utilisateur->getCodeCantonnement()
                                    ]
                                );

                                $this->utils->MajRespoCef(
                                    $entityManager,
                                    $utilisateur->getCodeCantonnement(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur(),
                                    $utilisateur->getEmail(),
                                    $utilisateur->getMobile(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur()
                                );
                            }elseif ($utilisateur->getCodeDr()){
                                //Elimination du statut Responsable
                                $usersStructure = $registry->getRepository(User::class)->findBy(
                                    [
                                        'code_dr'=>$utilisateur->getCodeDr()
                                    ]
                                );

                                $this->utils->MajRespoDr(
                                    $entityManager,
                                    $utilisateur->getCodeDr(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur(),
                                    $utilisateur->getEmail(),
                                    $utilisateur->getMobile(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur()
                                );
                            }elseif ($utilisateur->getCodePosteControle()){
                                //Elimination du statut Responsable
                                $usersStructure = $registry->getRepository(User::class)->findBy(
                                    [
                                        'code_poste_controle'=>$utilisateur->getCodePosteControle()
                                    ]
                                );

                                $this->utils->MajRespoPf(
                                    $entityManager,
                                    $utilisateur->getCodePosteControle(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur(),
                                    $utilisateur->getEmail(),
                                    $utilisateur->getMobile(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur()
                                );
                            }elseif ($utilisateur->getCodeDdef()){
                                //Elimination du statut Responsable
                                $usersStructure = $registry->getRepository(User::class)->findBy(
                                    [
                                        'code_ddef'=>$utilisateur->getCodeDdef()
                                    ]
                                );

                                $this->utils->MajRespoDdef(
                                    $entityManager,
                                    $utilisateur->getCodeDdef(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur(),
                                    $utilisateur->getEmail(),
                                    $utilisateur->getMobile(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur()
                                );
                            }elseif ($utilisateur->getCodeOi()){
                                //Elimination du statut Responsable
                                $usersStructure = $registry->getRepository(User::class)->findBy(
                                    [
                                        'code_oi'=>$utilisateur->getCodeOi()
                                    ]
                                );

                                $this->utils->MajRespoOi(
                                    $entityManager,
                                    $utilisateur->getCodeOi(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur(),
                                    $utilisateur->getEmail(),
                                    $utilisateur->getMobile(),
                                    $utilisateur->getPrenomsUtilisateur(). " ". $utilisateur->getNomUtilisateur()
                                );
                            }
                            foreach ($usersStructure as $userStructure){
                                $userStructure->setIsResponsable(false);
                                $registry->getManager()->persist($userStructure);
                            }

                            $utilisateur->setIsResponsable(true);
                        }else {
                            $utilisateur->setIsResponsable(false);
                        }

                            $registry->getManager()->persist($utilisateur);
                            $registry->getManager()->flush();

                            // Enregistrement dans SNVLT1
                                $snvlt1->MAJ_User($utilisateur);
                            $my_user[] = array(
                                'code' => 'SUCCESS'
                            );


                    } else {
                        $my_user[] = array(
                            'code' => 'ERROR'
                        );
                    }
                }

                return new JsonResponse(json_encode($my_user));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/user/resetPwd/', name: 'app_forgot_password')]
    public function resetPwd(UserRepository $users,
                          MenuRepository $menus,
                          MenuPermissionRepository $permissions,
                          GroupeRepository $myGroups,
                          Request $request,
                          UserRepository $userRepository,
                          ManagerRegistry $registry,
                          NotificationRepository $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('administration/utilisateur/reset-pwd.html.twig', [
                    'liste_users' => $users->findBy(['isResponsable'=>true]),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'mygroups'=>$myGroups->findBy(['groupe_system'=>true],['nom_groupe'=>'ASC']),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'liste_parent'=>$permissions
                ]);
        }
    }

    #[Route('snvlt/user/resetPwd/ChangeWithNew/{data}', name: 'app_forgot_password_chg')]
    public function resetPwdChange(UserRepository $users,
                             MenuRepository $menus,
                             MenuPermissionRepository $permissions,
                             GroupeRepository $myGroups,
                             Request $request,
                             UserRepository $userRepository,
                             ManagerRegistry $registry,
                             int $id_user = null,
                             string $data = null,
                             NotificationRepository $notification,
                             UserPasswordHasherInterface $hasher,
                             UserSnvlt1 $snvlt1
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            //if ($this->isGranted('ROLE_USER'))
            //{


                $reponse = array();
                if($data){
                    //Decoder le JSON
                    $arraydata = json_decode($data);
                    //Recherche l'utilisateur
                    $user = $userRepository->find((int) $arraydata->id_user);
                    if ($user){
                        // Vérifie si l'ancien mot de passe est correct

                        $test = $hasher->isPasswordValid($user,  $arraydata->ancien);
                        if (!$test) {
                            $reponse[] = array(
                                'code'=>'LAST_PWD_NOT_VALID'
                            );
                        }  else if (strlen($arraydata->nouveau) < 8 ) {
                            $reponse[] = array(
                                'code'=>'NOT_ENOUGH_LENGTH'
                            );
                        } else if ($arraydata->nouveau == $arraydata->confirm){
                            // Vérifie que le nouveau mot de passe est confirmé
                            $user->setPassword(
                                $hasher->hashPassword($user, $arraydata->nouveau)
                            );
                            $registry->getManager()->persist($user);
                            $registry->getManager()->flush();

                            //Enregistrement dans SNVLT1
                            $snvlt1->Change_Pwd_User($user, $arraydata->nouveau);

                            $reponse[] = array(
                                'code'=>'PWD_CHANGED'
                            );
                    } else {
                                $reponse[] = array(
                                    'code'=>'PWD_NOT_CONFIRMED'
                                );
                            }

                    } else {
                        $reponse[] = array(
                            'code'=>'BAD_USER'
                        );
                    }

                } else {
                    $reponse[] = array(
                        'code'=>'BAD_REQUEST'
                    );
                }
                return new JsonResponse(json_encode($reponse));


            //} else {
            //    return  $this->redirectToRoute('app_no_permission_user_active');
           // }
        }
    }
}