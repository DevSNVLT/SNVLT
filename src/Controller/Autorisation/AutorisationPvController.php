<?php

namespace App\Controller\Autorisation;

use App\Controller\Services\AdministrationService;
use App\Entity\Autorisation\AttributionPv;
use App\Entity\Autorisation\AutorisationPv;
use App\Entity\User;
use App\Form\Autorisation\AutorisationPvType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\Autorisation\AutorisationPvRepository;
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

class AutorisationPvController extends AbstractController
{

    public function __construct(private TranslatorInterface $translator, private AdministrationService $administrationService)
    {}

    #[Route('snvlt/autorisation/pv', name: 'app_autorisation_pv')]
    public function index(AutorisationPvRepository $autorisationPvRepository,
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
                $titre = $this->translator->trans("Add a PV authorization");


                return $this->render('autorisation/autorisation_pv/index.html.twig', [
                    'autorisationpvs' => $autorisationPvRepository->findAll(),
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

    #[Route('snvlt/autorisation/pv/edit/{id_autorisation?0}', name: 'autorisationpv.edit')]
    public function editAutorisationPv(
        AutorisationPv $autorisationpv = null,
        ManagerRegistry $doctrine,
        Request $request,
        AutorisationPvRepository $autorisationpvs,
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

                $titre = $this->translator->trans("Edit a PV authorization");
                $autorisationpv = $autorisationpvs->find($id_autorisation);
                //dd($ddef);
                if(!$autorisationpv){
                    $new = true;
                    $autorisationpv = new AutorisationPv();
                    $titre = $this->translator->trans("Add a PV Autorisation");

                    $autorisationpv->setCreatedAt($date_creation);
                    $autorisationpv->setCreatedBy($this->getUser());
                }



                $form = $this->createForm(AutorisationPvType::class, $autorisationpv);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){


                    $autorisationpv->setCreatedAt($date_creation);
                    $autorisationpv->setCreatedBy($this->getUser());

                    $manager = $doctrine->getManager();
                    $manager->persist($autorisationpv);

                    $agreementpv = $doctrine->getRepository(AttributionPv::class)->find($autorisationpv->getCodeAttributionPv());
                    $agreementpv->setReprise(true);
                    $manager->persist($agreementpv);

                    //Mise à jour du champs reprise dans la table Foret

                    $foret = $agreementpv->getCodeParcelle()->setReprise(true);
                    $manager->persist($foret);

                    if ($new){
                        $description_action = "Autorisation  PV N° ".$autorisationpv->getNumeroAutorisation(). " | Agreement : ". $agreementpv->getNumeroDecision() .  " | Attributaire : " . $autorisationpv->getCodeAttributionPv()->getRaisonSociale();
                    } else {
                        $description_action = "Autorisation  PV N° ".$autorisationpv->getNumeroAutorisation(). " | Agreement : ". $agreementpv->getNumeroDecision() .  " | Attributaire : " . $autorisationpv->getCodeAttributionPv()->getRaisonSociale();
                    }

                    $manager->flush();

                    $this->administrationService->save_action(
                        $user,
                        "AUTORISATION BOIS DE PLANTATION",
                        "AJOUT",
                        new \DateTimeImmutable(),
                        $description_action
                    );
                    //Crer l'evenement pour mettre à jour la Table Foret en modifiant la valeur ATTRIBUE à true
                    //$addAutorisationPvEvent = new AddAutorisationPvEvent($autorisationpv);

                    //Dispatcher l'evenement
                    //$this->dispatcher->dispatch($addAutorisationPvEvent, AddAutorisationPvEvent::ADD_ATTRIBUTION_EVENT);


                    $this->addFlash('success',$this->translator->trans("The authorization has been updated successfully"));



                    return $this->redirectToRoute("app_autorisation_pv");
                } else {
                    return $this->render('autorisation/autorisation_pv/add-autorisation-pv.html.twig',[
                        'form' =>$form->createView(),
                        'titre'=>$titre,
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'groupe'=>$code_groupe,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'liste_parent'=>$permissions,
                        'lastnumber'=>$autorisationpvs->findOneBy([],['id'=>'DESC'])

                        // 'type_autorisations'=>$type_autorisations->find(1)
                    ]);
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
}
