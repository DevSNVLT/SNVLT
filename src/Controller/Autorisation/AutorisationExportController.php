<?php

namespace App\Controller\Autorisation;

use App\Controller\Services\AdministrationService;
use App\Entity\Autorisation\AgreementExportateur;
use App\Entity\Autorisation\AutorisationExportateur;
use App\Entity\References\Exportateur;
use App\Entity\References\TypeDossierPs;
use App\Entity\User;
use App\Form\Autorisation\AutorisationExportateurType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\Autorisation\AutorisationExportateurRepository;
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

class AutorisationExportController extends AbstractController
{

    public function __construct(private TranslatorInterface $translator, private AdministrationService $administrationService)
    {}

    #[Route('snvlt/autorisation/expimp', name: 'app_autorisation_expimp')]
    public function index(AutorisationExportateurRepository $autorisationExportRepository,
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
                $titre = $this->translator->trans("Add an Export AUthorization");


                return $this->render('autorisation/autorisation_exportateur/index.html.twig', [
                    'autorisationexports' => $autorisationExportRepository->findAll(),
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

    #[Route('snvlt/autorisation/expimp/edit/{id_autorisation?0}', name: 'autorisationexpimp.edit')]
    public function editAutorisationExport(
        AutorisationExportateur $autorisationexport = null,
        ManagerRegistry $doctrine,
        Request $request,
        AutorisationExportateurRepository $autorisationexports,
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
                $new = false;
                $titre = $this->translator->trans("Edit an Export Autorisation");
                $autorisationexport = $autorisationexports->find($id_autorisation);
                //dd($ddef);
                if(!$autorisationexport){
                    $new = true;
                    $autorisationexport = new AutorisationExportateur();
                    $titre = $this->translator->trans("Add an Export Autorisation");

                    $autorisationexport->setCreatedAt($date_creation);
                    $autorisationexport->setCreatedBy($this->getUser());
                }



                $form = $this->createForm(AutorisationExportateurType::class, $autorisationexport);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){


                    $autorisationexport->setCreatedAt($date_creation);
                    $autorisationexport->setCreatedBy($this->getUser());
                    $autorisationexport->setReprise(true);

                    $manager = $doctrine->getManager();
                    $manager->persist($autorisationexport);

                    if ($new){
                        $action = "MODIFICATION";
                        $description_action = "Autorisation Export/Import N° " .$autorisationexport->getNumeroAutorisation() . " | Exportateur : " . $autorisationexport->getCodeAgreement()->getCodeExportateur()->getRaisonSocialeExportateur() . " | Code : " . $autorisationexport->getCodeAgreement()->getCodeExportateur()->getCodeExportateur() . " - [modifié par " . $user->getPrenomsUtilisateur() . " " . $user->getNomUtilisateur() . "]";
                    } else {
                        $action = "AJOUT";
                        $description_action = "Autorisation Export/Import N° " .$autorisationexport->getNumeroAutorisation() . " | Exportateur : " . $autorisationexport->getCodeAgreement()->getCodeExportateur()->getRaisonSocialeExportateur() . " | Code : " . $autorisationexport->getCodeAgreement()->getCodeExportateur()->getCodeExportateur() . " - [Créé par " . $user->getPrenomsUtilisateur() . " " . $user->getNomUtilisateur() . "]";
                    }
                    $agreement = $doctrine->getRepository(AgreementExportateur::class)->find($autorisationexport->getCodeAgreement());
                    $agreement->setReprise(true);
                    $manager->persist($agreement);
                    $manager->flush();

                    $this->administrationService->save_action(
                        $user,
                        "AGREEMENT EXPORT/IMPORT",
                        $action,
                        new \DateTimeImmutable(),
                        $description_action
                    );

                    $this->addFlash('success',$this->translator->trans("The autorisationps has been updated successfully"));



                    return $this->redirectToRoute("app_autorisation_expimp");
                } else {
                    return $this->render('autorisation/autorisation_exportateur/add-autorisation-exportateur.html.twig',[
                        'form' =>$form->createView(),
                        'titre'=>$titre,
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'groupe'=>$code_groupe,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'liste_parent'=>$permissions,
                        'lastnumber'=>$autorisationexports->findOneBy([],['id'=>'DESC'])

                       // 'type_autorisations'=>$type_autorisations->find(1)
                    ]);
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
}
