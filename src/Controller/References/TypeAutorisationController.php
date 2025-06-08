<?php

namespace App\Controller\References;

use App\Entity\References\Direction;
use App\Entity\References\TypeAutorisation;
use App\Entity\User;
use App\Form\References\DirectionType;
use App\Form\References\TypeAutorisationType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DemandeOperateurRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\DirectionRepository;
use App\Repository\TypeAutorisationRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TypeAutorisationController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator, private LoggerInterface $logger)
    {
    }

    #[Route('snvlt/ref/typeauto', name: 'app_references_type_autorisation')]
    public function index(
        TypeAutorisationRepository $autorisations,
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or  $this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('references/type_autorisation/index.html.twig',
                    [
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'groupe'=>$code_groupe,
                        'types_autorisations'=>$autorisations->findAll(),
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/ref/typeauto/edit/{id_type_autorisation?0}', name: 'type_autorisation.edit')]
    public function editAuthorization(
        TypeAutorisation $autorisation = null,
        ManagerRegistry $doctrine,
        Request $request,
        TypeAutorisationRepository $autorisations,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_type_autorisation,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or  $this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();



                $titre = $this->translator->trans("Edit Authorization Type");
                $autorisation = $autorisations->find($id_type_autorisation);

                if(!$autorisation){
                    $new = true;
                    $autorisation = new TypeAutorisation();
                    $titre = $this->translator->trans("Add Authorization Type");
                }


                $form = $this->createForm(TypeAutorisationType::class, $autorisation);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){


                    $manager = $doctrine->getManager();
                    $manager->persist($autorisation);
                    $manager->flush();

                    $this->addFlash('success',$this->translator->trans("Authorization Type has been edited and updated successfully"));
                    $this->logger->info($this->translator->trans("Authorization Type has been edited and updated successfully"));
                    return $this->redirectToRoute("app_references_type_autorisation");
                } else {
                    return $this->render('references/type_autorisation/add-type-autorisation.html.twig',[
                        'form' =>$form->createView(),
                        'titre'=>$titre,
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'groupe'=>$code_groupe,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'type_autorisations'=>$autorisations->findAll(),
                        'liste_parent'=>$permissions,
                    ]);
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
}
