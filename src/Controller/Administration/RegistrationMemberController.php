<?php

namespace App\Controller\Administration;

use App\Controller\Services\SendSMS;
use App\Controller\Services\Utils;
use App\Entity\Groupe;
use App\Entity\References\Exploitant;
use App\Entity\References\Exportateur;
use App\Entity\References\Usine;
use App\Entity\User;
use App\Form\Administration\RegisterMemberFormType;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\Notifier;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationMemberController extends AbstractController
{
    private $util;
    private $sendsms;
    private $translator;
    public function __construct(Utils $utils, SendSMS $sendSMS, TranslatorInterface $translator)
    {
        $this->util = $utils;
        $this->sendsms = $sendSMS;
        $this->translator = $translator;
    }

    #[Route('/snvlt/registration/new-member', name: 'app_snvlt_register')]
    public function index(UserPasswordHasherInterface $userPasswordHasher, ManagerRegistry  $entityManager,UserRepository $userRepository, Request $request, User $user = null): Response
    {
        $Member = new User();
        $Responsable = new User();

        $date_creation = new \DateTimeImmutable();

        $form = $this->createForm(RegisterMemberFormType::class, $Member);
        $emailResponsable = "";

        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid() ){


            $Member->setCreatedAt($date_creation);
           // dd($form->get('code_operateur')->getViewData());
            if($form->get('code_operateur')->getViewData() == "2"){
                $emailResponsable =$entityManager->getRepository(Exploitant::class)->find($form->get('codeexploitant')->getViewData())->getEmailPersonneRessource();
            }elseif($form->get('code_operateur')->getViewData() == "3"){
                 $emailResponsable =$entityManager->getRepository(Usine::class)->find($form->get('codeindustriel')->getViewData())->getEmailPersonneRessource();
            }elseif($form->get('code_operateur')->getViewData() == "4"){
               $emailResponsable =$entityManager->getRepository(Exportateur::class)->find($form->get('code_exportateur')->getViewData())->getEmailPersonneRessource();
            }
            //Retrouver l'email du responsable
            $Responsable = $userRepository->findBy(['email'=>$emailResponsable]);

            //dd($form->get('email')->getData());
           // dd($Responsable);
            //Affecter les valeurs de base à l'entité User
            $Member->setCreatedAt($date_creation);
            $Member->setCodeGroupe($entityManager->getRepository(Groupe::class)->find(0));
            $Member->setCreatedAt($date_creation);
            $Member->setActif(false);
            $Member->setIsVerified(false);
            $Member->setIsResponsable(false);

            $Member->setPassword(
                $userPasswordHasher->hashPassword(
                    $Member,
                    $form->get('mobile')->getData()
                )
            );


            $sujet = $this->translator->trans("SNVLT membership application");
            $salutation =$this->translator->trans("Hi"). " ". $form->get('nom_utilisateur')->getViewData();

            $description = $salutation." ".$this->translator->trans("Your registration has just been taken into account. You will receive a validation email from your administrator. Cheers!");





                //envoi une notification (email et App) au responsable
                        $salutation =$this->translator->trans("Hi")." ". $Responsable[0]->getPrenomsUtilisateur(). " ".$Responsable[0]->getNomUtilisateur()." \n\n";
                        $description = $salutation." ".$this->translator->trans("You have a new membership application for SNVLT. Please log in at https:127.0.0.1:8000 \n\n").$this->translator->trans("Cheers!");

            //Notification SNVLT
                        $user = $this->getUser();
                        $this->util->envoiNotification(
                            $entityManager,
                            $sujet,
                            $description,
                            $Responsable[0],
                            $user->getId(),
                            "app_administration_validation_adhesion",
                            "PROFILE",
                            $user->getId()
                        );
            //Notification Email
            $this->util->sendEmail($emailResponsable, $sujet, $description);


            //envoi une notification (email, SMS) au demandeur
            /*$this->sendsms->messagerie($form->get('mobile')->getData(),$description);*/
            $this->util->sendEmail($form->get('email')->getData(), $sujet, $description);


            //enregistrement
            $entityManager->getManager()->persist($Member);
            $entityManager->getManager()->flush();
            return $this->redirectToRoute("app_login");
        } else {
            return $this->render('registration/registerMember.html.twig',[
                'form' =>$form->createView()
            ]);
        }
    }
}
