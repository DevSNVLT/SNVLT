<?php

namespace App\Controller\Autorisation;

use App\Controller\Services\AdministrationService;
use App\Entity\Autorisation\AgreementPs;
use App\Entity\Autorisation\AutorisationPs;
use App\Entity\References\NaturePs;
use App\Entity\References\TypeDossierPs;
use App\Entity\User;
use App\Form\Autorisation\AutorisationPsType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\Autorisation\AutorisationPsRepository;
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

class AutorisationPsController extends AbstractController
{

    public function __construct(private TranslatorInterface $translator, private AdministrationService $administrationService)
    {}

    #[Route('snvlt/autorisation/ps', name: 'app_autorisation_ps')]
    public function index(AutorisationPsRepository $autorisationPsRepository,
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
                $titre = $this->translator->trans("Add a PS authorization");


                return $this->render('autorisation/autorisation_ps/index.html.twig', [
                    'autorisationpss' => $autorisationPsRepository->findAll(),
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

    #[Route('snvlt/autorisation/ps/edit/{id_autorisation?0}', name: 'autorisationps.edit')]
    public function editAutorisationPs(
        AutorisationPs $autorisationps = null,
        ManagerRegistry $doctrine,
        Request $request,
        AutorisationPsRepository $autorisationpss,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_autorisation,
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

                $titre = $this->translator->trans("Edit a PS authorization");
                $autorisationps = $autorisationpss->find($id_autorisation);
                //dd($ddef);
                if(!$autorisationps){
                    $new = true;
                    $autorisationps = new AutorisationPs();
                    $titre = $this->translator->trans("Add a PS authorization");

                    $autorisationps->setCreatedAt($date_creation);
                    $autorisationps->setCreatedBy($this->getUser());
                }



                $form = $this->createForm(AutorisationPsType::class, $autorisationps);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){


                    $autorisationps->setCreatedAt($date_creation);
                    $autorisationps->setCreatedBy($this->getUser());


                    //$option = $doctrine->getRepository(Option::class)->findBy(['name'=>'autorisations_validation'])[0];

                    /*if($option->getValue() == "1"){
                        $autorisationps->setValidationDocument(true);
                    }else{
                        $autorisationps->setValidationDocument(false);
                    }*/


                    $manager = $doctrine->getManager();
                    $manager->persist($autorisationps);

                    $agreementps = $doctrine->getRepository(AgreementPs::class)->find($autorisationps->getCodeDossier());
                    $agreementps->setReprise(true);
                    $manager->persist($agreementps);

                    $manager->flush();

                    $this->administrationService->save_action(
                        $user,
                        "AUTORISATION PS",
                        "AJOUT",
                        new \DateTimeImmutable(),
                        "Autorisation  PS N° ".$autorisationps->getNumeroAutoPs(). " | Dossier : ". $agreementps->getNumeroDossier() .  " | Attributaire : " . $agreementps->getCodeAttributairePs()->getRaisonSociale() . " | Type autorisation : " . $agreementps->getCodeTypeDossier()->getLibelle()
                    );
                    //Crer l'evenement pour mettre à jour la Table Foret en modifiant la valeur ATTRIBUE à true
                    //$addAutorisationPsEvent = new AddAutorisationPsEvent($autorisationps);

                    //Dispatcher l'evenement
                    //$this->dispatcher->dispatch($addAutorisationPsEvent, AddAutorisationPsEvent::ADD_ATTRIBUTION_EVENT);


                    $this->addFlash('success',$this->translator->trans("The authorization has been updated successfully"));



                    return $this->redirectToRoute("app_autorisation_ps");
                } else {
                    return $this->render('autorisation/autorisation_ps/add-autorisation-ps.html.twig',[
                        'form' =>$form->createView(),
                        'titre'=>$titre,
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'groupe'=>$code_groupe,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'liste_parent'=>$permissions,
                        'lastnumber'=>$autorisationpss->findOneBy([],['id'=>'DESC'])

                       // 'type_autorisations'=>$type_autorisations->find(1)
                    ]);
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    #[Route('snvlt/ref/natureps/{id_natureps?0}', name: 'natureps.json')]
    public function liste_typedossierps(
        int $id_natureps,
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

                $nature_ps = $registry->getRepository(NaturePs::class)->find($id_natureps);
                $dossier = array();
                if ($nature_ps){
                    $dossier[] = array(
                        'id_dossier'=>$nature_ps->getId(),
                        'libelle'=>$nature_ps->getLibelle(),
                        'montant_autorisation'=>$nature_ps->getMontantAutorisation(),
                        'duree_autorisation'=>$nature_ps->getDureeAutorisation()
                    );
                }
                return  new JsonResponse(json_encode($dossier));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
