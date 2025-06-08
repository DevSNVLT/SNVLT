<?php

namespace App\Controller\Autorisation;

use App\Controller\Services\AdministrationService;
use App\Entity\Autorisation\AgreementPs;
use App\Entity\References\TypeDossierPs;
use App\Entity\User;
use App\Form\Autorisation\AgreementPsType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\Autorisations\AgreementPsRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\TypeAutorisationRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AgreementPsController extends AbstractController
{

    public function __construct(private TranslatorInterface $translator, private AdministrationService $administrationService)
    {}

    #[Route('snvlt/agreement/ps', name: 'app_agreement_ps')]
    public function index(AgreementPsRepository $agreementPsRepository,
                          MenuRepository $menus,
                          MenuPermissionRepository $permissions,
                          GroupeRepository $groupeRepository,
                          Request $request,
                          UserRepository $userRepository,
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
                $titre = $this->translator->trans("Add an attribution PV");


                return $this->render('autorisation/agreement_ps/index.html.twig', [
                    'agreementpss' => $agreementPsRepository->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'titre'=>$titre,
                    'liste_parent'=>$permissions
                ]);
            } else {
                return  $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/agreement/ps/edit/{id_agreement?0}', name: 'agreementps.edit')]
    public function editAgreementPs(
        AgreementPs $agreementps = null,
        ManagerRegistry $doctrine,
        Request $request,
        AgreementPsRepository $agreementpss,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_agreement,
        TypeAutorisationRepository $type_autorisations,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $date_creation = new \DateTimeImmutable();

                $titre = $this->translator->trans("Edit an PS Agreement");
                $agreementps = $agreementpss->find($id_agreement);
                //dd($ddef);
                if(!$agreementps){
                    $new = true;
                    $agreementps = new AgreementPs();
                    $titre = $this->translator->trans("Add a PS Agreement");

                    $agreementps->setCreatedAt($date_creation);
                    $agreementps->setCreatedBy($this->getUser());
                }



                $form = $this->createForm(AgreementPsType::class, $agreementps);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){


                    $agreementps->setCreatedAt($date_creation);
                    $agreementps->setCreatedBy($this->getUser());
                    $agreementps->setStatut(true);
                    $agreementps->setReprise(false);

                    //$option = $doctrine->getRepository(Option::class)->findBy(['name'=>'autorisations_validation'])[0];

                    /*if($option->getValue() == "1"){
                        $agreementps->setValidationDocument(true);
                    }else{
                        $agreementps->setValidationDocument(false);
                    }*/


                    $manager = $doctrine->getManager();
                    $manager->persist($agreementps);


                    $manager->flush();
                    $this->administrationService->save_action(
                        $user,
                        "AGREEMENT PS",
                        "AJOUT",
                        new \DateTimeImmutable(),
                        "Agreement N° " .$agreementps->getNumeroDossier() . " | Attributaire : " . $agreementps->getCodeAttributairePs()->getRaisonSociale() . " | Type Dossier : " . $agreementps->getCodeTypeDossier()->getLibelle()
                    );

                    //Crer l'evenement pour mettre à jour la Table Foret en modifiant la valeur ATTRIBUE à true
                    //$addAgreementPsEvent = new AddAgreementPsEvent($agreementps);

                    //Dispatcher l'evenement
                    //$this->dispatcher->dispatch($addAgreementPsEvent, AddAgreementPsEvent::ADD_ATTRIBUTION_EVENT);


                    $this->addFlash('success',$this->translator->trans("The agreementps has been updated successfully"));



                    return $this->redirectToRoute("app_agreement_ps");
                } else {
                    return $this->render('autorisation/agreement_ps/add-agreement-ps.html.twig',[
                        'form' =>$form->createView(),
                        'titre'=>$titre,
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'groupe'=>$code_groupe,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'liste_parent'=>$permissions,
                        'lastnumber'=>$agreementpss->findOneBy([],['id'=>'DESC'])

                       // 'type_autorisations'=>$type_autorisations->find(1)
                    ]);
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    #[Route('snvlt/ref/typeps/{id_tdps?0}', name: 'typedossierps.json')]
    public function liste_typedossierps(
        int $id_tdps,
        ManagerRegistry $registry,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        Request $request
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $type_dossier = $registry->getRepository(TypeDossierPs::class)->find($id_tdps);
                $dossier = array();
                if ($type_dossier){
                    $dossier[] = array(
                        'id_dossier'=>$type_dossier->getId(),
                        'libelle'=>$type_dossier->getLibelle(),
                        'montant_agreement'=>$type_dossier->getMontantAgreement()
                    );
                }
                return  new JsonResponse(json_encode($dossier));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
