<?php

namespace App\Controller\Autorisation;

use App\Controller\Services\AdministrationService;
use App\Entity\Autorisation\AgreementExport;
use App\Entity\Autorisation\AgreementExportateur;
use App\Entity\References\Exportateur;
use App\Entity\References\TypeDossierPs;
use App\Entity\User;
use App\Form\Autorisation\AgreementExportateurType;
use App\Form\Autorisation\AgreementExportType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\Autorisation\AgreementExportateurRepository;
use App\Repository\Autorisations\AgreementExportRepository;
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

class AgreementExportController extends AbstractController
{

    public function __construct(private TranslatorInterface $translator, private AdministrationService $administrationService)
    {}

    #[Route('snvlt/agreement/expimp', name: 'app_agreement_expimp')]
    public function index(AgreementExportateurRepository $agreementExportRepository,
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


                return $this->render('autorisation/agreement_exportateur/index.html.twig', [
                    'agreementexports' => $agreementExportRepository->findAll(),
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

    #[Route('snvlt/agreement/expimp/edit/{id_exportateur?0}', name: 'agreementexpimp.edit')]
    public function editAgreementExport(
        AgreementExportateur $agreementexport = null,
        ManagerRegistry $doctrine,
        Request $request,
        AgreementExportateurRepository $agreementexports,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_exportateur,
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
                $titre = $this->translator->trans("Edit an Export Agreement");
                $agreementexport = $agreementexports->find($id_exportateur);
                //dd($ddef);
                if(!$agreementexport){
                    $new = true;
                    $agreementexport = new AgreementExportateur();
                    $titre = $this->translator->trans("Add an Export Agreement");

                    $agreementexport->setCreatedAt($date_creation);
                    $agreementexport->setCreatedBy($this->getUser());
                }



                $form = $this->createForm(AgreementExportateurType::class, $agreementexport);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){


                    $agreementexport->setCreatedAt($date_creation);
                    $agreementexport->setCreatedBy($this->getUser());
                    $agreementexport->setStatut(true);
                    $agreementexport->setReprise(false);

                    $manager = $doctrine->getManager();
                    $manager->persist($agreementexport);

                    if ($new){
                        $action = "MODIFICATION";
                        $description_action = "Agreement N° " .$agreementexport->getNumeroDecision() . " | Exportateur : " . $agreementexport->getCodeExportateur()->getRaisonSocialeExportateur() . " | Code : " . $agreementexport->getCodeExportateur()->getCodeExportateur() . " - [modifié par " . $user->getPrenomsUtilisateur() . " " . $user->getNomUtilisateur() . "]";
                    } else {
                        $action = "AJOUT";
                        $description_action = "Agreement N° " .$agreementexport->getNumeroDecision() . " | Exportateur : " . $agreementexport->getCodeExportateur()->getRaisonSocialeExportateur() . " | Code : " . $agreementexport->getCodeExportateur()->getCodeExportateur() . " - [Créé par " . $user->getPrenomsUtilisateur() . " " . $user->getNomUtilisateur() . "]";
                    }
                    $exportateur = $doctrine->getRepository(Exportateur::class)->find($agreementexport->getCodeExportateur());
                    $exportateur->setStatut(true);
                    $manager->persist($exportateur);
                    $manager->flush();

                    $this->administrationService->save_action(
                        $user,
                        "AGREEMENT EXPORT/IMPORT",
                        $action,
                        new \DateTimeImmutable(),
                        $description_action
                    );

                    $this->addFlash('success',$this->translator->trans("The agreementps has been updated successfully"));



                    return $this->redirectToRoute("app_agreement_expimp");
                } else {
                    return $this->render('autorisation/agreement_exportateur/add-agreement-exportateur.html.twig',[
                        'form' =>$form->createView(),
                        'titre'=>$titre,
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'groupe'=>$code_groupe,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'liste_parent'=>$permissions,
                        'lastnumber'=>$agreementexports->findOneBy([],['id'=>'DESC'])

                       // 'type_autorisations'=>$type_autorisations->find(1)
                    ]);
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
}
