<?php

namespace App\Controller;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\synchro\UserSnvlt1;
use App\Controller\Services\Utils;
use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\Groupe;
use App\Entity\References\Cantonnement;
use App\Entity\References\Commercant;
use App\Entity\References\Ddef;
use App\Entity\References\Direction;
use App\Entity\References\Dr;
use App\Entity\References\Essence;
use App\Entity\References\Exploitant;
use App\Entity\References\Exportateur;
use App\Entity\References\Foret;
use App\Entity\References\PosteForestier;
use App\Entity\References\ServiceMinef;
use App\Entity\References\TypeOperateur;
use App\Entity\References\Usine;
use App\Entity\References\ZoneHemispherique;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DocStats\Pages\PagebrhRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use App\Security\AppCustomAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use http\Url;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Throwable;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private $translator;
    private $util;

    public function __construct(EmailVerifier $emailVerifier,
                                private AdministrationService $administrationService,
                                TranslatorInterface $translator,
                                Utils $utils)
    {
        $this->emailVerifier = $emailVerifier;
        $this->translator = $translator;
        $this->util = $utils;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request,
                             UserPasswordHasherInterface $userPasswordHasher,
                             UserAuthenticatorInterface $userAuthenticator,
                             AppCustomAuthenticator $authenticator,
                             ManagerRegistry $registry,
                             EntityManagerInterface $entityManager,
                             GroupeRepository $groupe,
                             TranslatorInterface $translator,
                            ): Response
    {
        $user = new User();
        $Responsable = new User();

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emailResponsable = "";

            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $groupe_aucun = $groupe->find(0);
            $user->setCodeGroupe($groupe_aucun);
            $entityManager->persist($user);
            $entityManager->flush();

            //Enregistrement dans le log
            $this->administrationService->save_action(
                $user,
                "USER",
                "DEMANDE_ACCESS",
                new \DateTimeImmutable(),
                "Nouvelle demande d'accès par " . $user
            );

            //Envoi d'une notification App et email au responsable de la structure

                //Recherche du responsable

                if($form->get('code_operateur')->getViewData() == "2"){
                    $emailResponsable =$entityManager->getRepository(Exploitant::class)->find($form->get('codeexploitant')->getViewData())->getEmailPersonneRessource();
                }elseif($form->get('code_operateur')->getViewData() == "3"){
                    $emailResponsable =$entityManager->getRepository(Usine::class)->find($form->get('codeindustriel')->getViewData())->getEmailPersonneRessource();
                }elseif($form->get('code_operateur')->getViewData() == "4"){
                    $emailResponsable =$entityManager->getRepository(Exportateur::class)->find($form->get('code_exportateur')->getViewData())->getEmailPersonneRessource();
                }elseif($form->get('code_operateur')->getViewData() == "5"){
                    $emailResponsable =$entityManager->getRepository(Dr::class)->find($form->get('code_exportateur')->getViewData())->getEmailPersonneRessource();
                }elseif($form->get('code_operateur')->getViewData() == "6"){
                    $emailResponsable =$entityManager->getRepository(Ddef::class)->find($form->get('code_exportateur')->getViewData())->getEmailPersonneRessource();
                }elseif($form->get('code_operateur')->getViewData() == "7"){
                    $emailResponsable =$entityManager->getRepository(Cantonnement::class)->find($form->get('code_exportateur')->getViewData())->getEmailPersonneRessource();
                }elseif($form->get('code_operateur')->getViewData() == "10"){
                    $emailResponsable =$entityManager->getRepository(PosteForestier::class)->find($form->get('code_exportateur')->getViewData())->getEmailPersonneRessource();
                }

                //Retrouver l'email du responsable
                    $Responsable = $registry->getRepository(User::class)->findOneBy(['email'=>$emailResponsable]);


            //envoi une notification (email et App) au responsable
            $sujet = $this->translator->trans("SNVLT membership application");
            $salutation =$this->translator->trans("Hi")." ". $Responsable->getPrenomsUtilisateur(). " ".$Responsable->getNomUtilisateur()." \n\n";
            $description = $salutation." ".$this->translator->trans("You have a SNVLT access request from ". $user." Please log in at " . $this->generateUrl("app_login"). " \n\n");

            //Notification SNVLT
            $user = $this->getUser();
            $this->util->envoiNotification(
                $registry,
                $sujet,
                $description,
                $Responsable[0],
                $user->getId(),
                "app_administration_validation_adhesion",
                "PROFILE",
                $user->getId()
            );

            //Envoi email au Responsable
            $this->util->sendEmail($emailResponsable, $sujet, $description);


                    // generate a signed url and email it to the user
                    $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                        (new TemplatedEmail())
                            ->from(new Address('snvlt@system2is.com', 'SNVLT INFOS'))
                            ->to($user->getEmail())
                            ->subject($translator->trans('Please Confirm your Email'))
                            ->htmlTemplate('registration/confirmation_email.html.twig')
                    );


            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            $this->addFlash('error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_tdb_admin');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success','Your email address has been verified.');

        return $this->redirectToRoute('app_tdb_admin');
    }


    #[Route('/snvlt/check_email/{value}', name: 'checkEmail')]
    public function checkEmail(Request $request,
                               TranslatorInterface $translator,
                                string $value,
                                ManagerRegistry $registry): Response
    {
        $reponse = array();
        if ($value){
            $emailsearch = $registry->getRepository(User::class)->findOneBy(['email'=>$value]);

            if ($emailsearch){
                $reponse[] =array(
                    'valeur'=>true
                );

            } else {
                $reponse[] =array(
                    'valeur'=>false
                );
            }

        } else {
            $reponse[] =array(
                'valeur'=>"BAD_REQUEST"
            );
        }
        return new JsonResponse(json_encode($reponse));
    }

    #[Route('/snvlt/check_phone/{value}', name: 'checkPhone')]
    public function checkPhone(Request $request,
                               TranslatorInterface $translator,
                               string $value,
                               ManagerRegistry $registry): Response
    {
        $reponse = array();
        if ($value){
            $emailsearch = $registry->getRepository(User::class)->findOneBy(['mobile'=>$value]);

            if ($emailsearch){
                $reponse[] =array(
                    'valeur'=>true
                );

            } else {
                $reponse[] =array(
                    'valeur'=>false
                );
            }

        } else {
            $reponse[] =array(
                'valeur'=>"BAD_REQUEST"
            );
        }
        return new JsonResponse(json_encode($reponse));
    }

    #[Route('snvlt/user/op/save_member/data/{data}', name: 'add_member_request')]
    public function add_member_request(
        UserPasswordHasherInterface $userPasswordHasher,
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        string $data,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry,
        Utils $utils,
        AdministrationService $administrationService,
        UserSnvlt1 $snvlt1
    ): Response
    {
                if($data){
                    $user = new User();
                    $responsable =  new User();
                    //Decoder le JSON BRH
                    $arraydata = json_decode($data);
                    $id_operateur = (int) $arraydata->operateur;
                    if ((int) $arraydata->operateur){
                        $type_operateur = $registry->getRepository(TypeOperateur::class)->find($id_operateur);
                        if ($type_operateur){


                            $user->setCodeOperateur($type_operateur);


                            if ($type_operateur->getId() == 1){
                                if ((int) $arraydata->select_minef){
                                    $direction = $registry->getRepository(Direction::class)->find((int) $arraydata->direction);
                                    $user->setCodeDirection($direction);
                                    $roles = ['ROLE_ADMINISTRATIF', 'ROLE_MINEF'];

                                    $responsable = $registry->getRepository(User::class)->findOneBy(['email'=>$direction->getEmailPersonneRessource()]);

                                } else {
                                    $service_minef = $registry->getRepository(ServiceMinef::class)->find((int) $arraydata->service_minef);
                                    $user->setCodeService($service_minef);
                                    $roles = ['ROLE_ADMINISTRATIF', 'ROLE_MINEF'];

                                    $responsable = $registry->getRepository(User::class)->findOneBy(['email'=>$service_minef->getEmailPersonneRessource()]);
                                }

                            } else if ($type_operateur->getId() == 2){

                                $exploitant = $registry->getRepository(Exploitant::class)->find((int) $arraydata->exploitant);
                                $user->setCodeexploitant($exploitant);
                                $roles = ['ROLE_EXPLOITANT'];
                                //dd($registry->getRepository(User::class)->findOneBy(['email'=>$exploitant->getEmailPersonneRessource()]));
                                $responsable = $registry->getRepository(User::class)->findOneBy(['email'=>$exploitant->getEmailPersonneRessource()]);

                            } else if ($type_operateur->getId() == 3){
                                $usine = $registry->getRepository(Usine::class)->find((int) $arraydata->usine);
                                $user->setCodeindustriel($usine);
                                $roles = ['ROLE_INDUSTRIEL'];
                                $responsable = $registry->getRepository(User::class)->findOneBy(['email'=>$usine->getEmailPersonneRessource()]);

                            } else if ($type_operateur->getId() == 4){
                                $exportateur = $registry->getRepository(Exportateur::class)->find((int) $arraydata->exportateur);
                                $user->setCodeExportateur($exportateur);
                                $roles = ['ROLE_EXPORTATEUR'];
                                $responsable = $registry->getRepository(User::class)->findOneBy(['email'=>$exportateur->getEmailPersonneRessource()]);

                            } else if ($type_operateur->getId() == 5){
                                $dr = $registry->getRepository(Dr::class)->find((int) $arraydata->dr);
                                $user->setCodeDr($dr);
                                $roles = ['ROLE_DR','ROLE_MINEF','ROLE_ADMINISTRATIF'];
                                $responsable = $registry->getRepository(User::class)->findOneBy(['email'=>$dr->getEmailPersonneRessource()]);

                            } else if ($type_operateur->getId() == 6){
                                $ddef = $registry->getRepository(Ddef::class)->find((int) $arraydata->dd);
                                $user->setCodeDdef($ddef);
                                $roles = ['ROLE_DDEF','ROLE_MINEF','ROLE_ADMINISTRATIF'];
                                $responsable = $registry->getRepository(User::class)->findOneBy(['email'=>$ddef->getEmailPersonneRessource()]);

                            } else if ($type_operateur->getId() == 7){
                                $cef = $registry->getRepository(Cantonnement::class)->find((int) $arraydata->cef);
                                $user->setCodeCantonnement($cef);
                                $roles = ['ROLE_CEF','ROLE_MINEF','ROLE_ADMINISTRATIF'];
                                $responsable = $registry->getRepository(User::class)->findOneBy(['email'=>$cef->getEmailPersonneRessource()]);

                            } else if ($type_operateur->getId() == 10){
                                $pf = $registry->getRepository(PosteForestier::class)->find((int) $arraydata->pf);
                                $user->setCodePosteControle($pf);
                                $roles = ['ROLE_PF','ROLE_MINEF','ROLE_ADMINISTRATIF'];
                                $responsable = $registry->getRepository(User::class)->findOneBy(['email'=>$pf->getEmailPersonneRessource()]);

                            }
                        }

                    }

                    $user->setNomUtilisateur(strtoupper($this->util->removeSpecialCharacters($arraydata->nom)));
                    $user->setPrenomsUtilisateur(strtoupper($this->util->removeSpecialCharacters($arraydata->prenoms)));
                    $user->setFonction(strtoupper($this->util->removeSpecialCharacters($arraydata->fonction)));

                    $user->setPassword(
                        $userPasswordHasher->hashPassword(
                            $user,
                            $arraydata->mpd
                        )
                    );


                    $user->setMobile(strtoupper($this->util->removeSpecialCharacters($arraydata->mobile)));
                    $user->setEmail(strtolower($arraydata->email));

                    $user->setCodeGroupe($registry->getRepository(Groupe::class)->find(0));

                    $user->setLocale($arraydata->langue);
                    $user->setIsVerified(false);
                    $user->setIsResponsable(false);
                    $user->setActif(false);
                    $user->setCreatedBy($user);
                    $user->setCreatedAt(new \DateTimeImmutable());
                    $code = strtoupper($utils->uniqidReal(4));
                    $user->setCodeSms($code);
                    $user->setRoles($roles);

                    $registry->getManager()->persist($user);
                    $registry->getManager()->flush();

                    // Enregistrement dans le log
                    $this->administrationService->save_action(
                        $user,
                        "USER",
                        "DEMANDE_ACCESS",
                        new \DateTimeImmutable(),
                        "Nouvelle demande d'accès par " . $user
                    );
                    $this->addFlash("success", "Votre demande a été prise en compte.");
                    //dd($responsable);
                    if ($responsable){

                        if($responsable->getEmail()){
                            dd($responsable->getEmail());
                            //App Notification
                            $this->util->envoiNotification(
                                $registry,
                                $this->translator->trans("Vous avez une nouvelle demande à valider"),
                                "Bonjour ". $responsable .". ". $user ." vous a envoyé une demande d'accès. Connectez-vous SVP à SNVLT en suivant ce lien https://boislegal.ci/snvlt",
                                $responsable,
                                $user->getId(),
                                "app_administration_validation_adhesion",
                                "PROFILE",
                                $user->getId()
                            );
                            //Envoi un email au responsable
                                $utils->sendEmail(
                                    $responsable->getEmail(),
                                    "Vous avez une nouvelle demande d'accès",
                                    $this->translator->trans("Bonjour "). $responsable . ". Vous avez reçu une demande d'accès de ". $user ." Merci de vous connecter à l'adresse https://boislegal.ci/snvlt afin de valider ou invalider son adhésion à votre organisation."
                                );

                        } else {
                            return  new JsonResponse(json_encode("PAS_DE_RESPONSABLE"));
                        }
                    } else {
                        return  new JsonResponse(json_encode("PAS_DE_RESPONSABLE"));
                    }

                    //Maj User dans la base de données SNVLT1
                    $snvlt1->Ajout_User($user, $arraydata->mpd);



                    // Envoi d'un email à l'utilisateur pour validation email
                    //try {

//                        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
//                            (new TemplatedEmail())
//                                ->from(new Address('snvlt@system2is.com', 'SNVLT INFOS'))
//                                ->to($user->getEmail())
//                                ->subject($this->translator->trans('Please Confirm your Email'))
//                                ->htmlTemplate('registration/confirmation_email_register.html.twig')
//                                ->context([
//                                    'nom_prenoms'=>$user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur(),
//                                    'mot_passe'=>"Celui renseigné par vous",
//                                    'email_utilisateur'=>$user->getEmail()
//                                ])
//                        );
//                    } catch (Throwable $e) {
//
//                    }

                    return  new JsonResponse(json_encode("SUCCESS"));
                    //return $this->render('exceptions/registration_ok.html.twig');
                } else {
                return  new JsonResponse(json_encode("BAD_DATA"));
            }

  }

    #[Route('/snvlt/registration/request_submitted', name: 'request_submitted')]
    public function request_submitted(Request $request): Response
    {
        return $this->render('exceptions/registration_ok.html.twig');
    }
}
