<?php

namespace App\Controller\Administration;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\Utils;
use App\Entity\Groupe;
use App\Entity\Menu;
use App\Entity\Permission;
use App\Entity\References\GrilleLegalite;
use App\Entity\User;
use App\Form\Administration\MyGroupsType;
use App\Form\References\GrilleLegaliteType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DemandeOperateurRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\PermissionRepository;
use App\Repository\References\GrilleLegaliteRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class MyGroupsController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator,
                                private LoggerInterface $logger,
                                private AdministrationService $administrationService,
                                private Utils $utils)
    {
    }

    #[Route('snvlt/my_groups', name: 'my_groups')]
    public function index(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        GroupeRepository $myGroups,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
                $code_operateur = null;

                $user = $userRepository->find($this->getUser());
                if($user){
                    $code_groupe = $user->getCodeGroupe()->getId();
                   if($user->getCodeOperateur()->getId() == 1){

                        if($user->getCodeService()){
                            $code_operateur = $user->getCodeService()->getId();
                        }else if($user->getCodeDirection()){
                            $code_operateur = $user->getCodeDirection()->getId();
                        }

                   }elseif($user->getCodeOperateur()->getId() == 2){
                       $code_operateur = $user->getCodeexploitant()->getId();
                   }elseif($user->getCodeOperateur()->getId() == 3){
                       $code_operateur = $user->getCodeindustriel()->getId();
                   }elseif($user->getCodeOperateur()->getId() == 4){
                       $code_operateur = $user->getCodeExportateur()->getId();
                   }elseif($user->getCodeOperateur()->getId() == 5){
                       $code_operateur = $user->getCodeDr()->getId();
                   }elseif($user->getCodeOperateur()->getId() == 6){
                       $code_operateur = $user->getCodeDdef()->getId();
                   }elseif($user->getCodeOperateur()->getId() == 7){
                       $code_operateur = $user->getCodeCantonnement()->getId();
                   }elseif($user->getCodeOperateur()->getId() == 10){
                       $code_operateur = $user->getCodePosteControle()->getId();
                   }
                }
                //$code_groupe = $user->getCodeGroupe()->getId();
                return $this->render('administration/my_groups/index.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'mygroups'=>$myGroups->findBy(['code_type_operateur'=>$user->getCodeOperateur(), 'code_operateur'=>$code_operateur])
                ]);



        }
    }

    #[Route('snvlt/my_groups/edit/{id_group?0}', name: 'my_groups.edit')]
    public function editMyGroups(
        Groupe $my_group = null,
        ManagerRegistry $doctrine,
        Request $request,
        GrilleLegaliteRepository $grille_legalites,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_group,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            /*if (!$this->isGranted('ROLE_ADMIN')) {*/
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $date_creation = new \DateTimeImmutable();
                $grp = $id_group;
                $titre = $this->translator->trans("Edit Users Group");
                $my_group = $groupeRepository->find($id_group);

                $groupResponsable = null;
                if ($my_group->getParentGroupe()){
                    $groupResponsable = $groupeRepository->find($my_group->getParentGroupe());
                }


                //dd($grille_legalite);


                $new = false;
                if (!$my_group) {
                    $new = true;
                    $my_group = new Groupe();
                    $grp = 0;
                }

                //dd($my_group);
                $form = $this->createForm(MyGroupsType::class, $my_group);


                $form->handleRequest($request);

                //dd($grp);
                if ($form->isSubmitted() && $form->isValid()) {

                    $my_group->setCodeTypeOperateur($user->getCodeOperateur()->getId());

                    $my_group->setGroupeSystem(false);
                    $my_group->setNomGroupe($this->utils->removeSpecialCharacters($form->get('nom_groupe')->getData()));

                    $manager = $doctrine->getManager();
                    $manager->persist($my_group);
                    $manager->flush();

                    if($new){
                        $action = "ENREGISTREMENT";
                        $description = $this->translator->trans("User "). $user. $this->translator->trans(" has created a new group for his organization. Details : Group name [") . $my_group->getNomGroupe() . ' ]' ;
                    } else {
                        $action = "MODIFICATION";
                        $description = $this->translator->trans("User "). $user. $this->translator->trans(" has edited an exiting group for his organization. Details : Group name [") . $my_group->getNomGroupe() . ' ]' ;
                    }
                    $this->administrationService->save_action(
                        $user,
                        "MY_GROUP",
                        $action,
                        new \DateTimeImmutable(),
                        $description
                    );

                    $this->addFlash('success', $this->translator->trans("Group has been edited successfully"));
                    return $this->redirectToRoute("my_groups");
                } else {
                    return $this->render('administration/my_groups/add.html.twig', [
                        'form' => $form->createView(),
                        'titre' => $titre,
                        'liste_grille_legalites' => $grille_legalites->findAll(),
                        'liste_menus' => $menus->findOnlyParent(),
                        "all_menus" => $menus->findAll(),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
                        'groupe' => $code_groupe,
                        'grp'=>$grp,
                        'liste_parent'=>$permissions,
                        'permission_par_groupe'=>$permissions->findBy(['code_groupe_id'=>$groupResponsable]),
                        'permission_my_groupe'=>$permissions->findBy(['code_groupe_id'=>$my_group])
                    ]);
                }
                /*}*/
            /*} else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }*/
        }
    }

    #[Route('snvlt/permission_par_groupe/all/{id_group}', name: 'permission_par_groupe')]
    public function permission_par_groupe(
        Request $request,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        int $id_group,
        UserRepository $userRepository): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {

            $my_group = $groupeRepository->find($id_group);
            $array_group = array();

            if ($my_group->getParentGroupe()){
                $groupResponsable = $groupeRepository->find($my_group->getParentGroupe());
                $perms = $permissions->findBy(['code_groupe_id'=>$groupResponsable]);
                foreach ($perms as $perm){
                     $trouve = false;
                    $check_perm = $permissions->findBy(['code_groupe_id'=>$my_group]);
                    foreach ($check_perm as $pm){
                        if($pm->getId() == $perm->getId()){
                            $trouve = true;
                        }
                    }
                    if(!$trouve){
                        $array_group[] = array(
                            'nom_menu'=>$perm->getNomMenu(),
                            'id'=>$perm->getId(),
                            'id_menu'=>$perm->getIdMenu()

                        );
                    }

                }
            }
            sort($array_group);
             return new JsonResponse(json_encode($array_group));

        }
    }
    #[Route('snvlt/permission_my_groupe/all/{id_group}', name: 'permission_my_groupe')]
        public function permission_my_groupe(
            Request $request,
            MenuPermissionRepository $permissions,
            GroupeRepository $groupeRepository,
            int $id_group,
            UserRepository $userRepository): Response
        {
            if(!$request->getSession()->has('user_session')){
                return $this->redirectToRoute('app_login');
            } else {

                $my_group = $groupeRepository->find($id_group);
                $array_group = array();

                if ($my_group){
                    $perms = $permissions->findBy(['code_groupe_id'=>$my_group]);
                    foreach ($perms as $perm){
                        $array_group[] = array(
                            'nom_menu'=>$perm->getNomMenu(),
                            'id'=>$perm->getId(),
                            'id_menu'=>$perm->getIdMenu()

                        );
                    }
                }

                sort($array_group);
                 return new JsonResponse(json_encode($array_group));
            }
        }

    // Recherche une permission et retourne une valeur booleen
    #[Route('snvlt/my_groups/prms/search/{id_groupe}/{id_menu}', name: 'my_groups-prms.search')]
    public function searchPermissionsByGroupOperateur(
        int $id_groupe,
        int $id_menu,
        ManagerRegistry $registry,
        GroupeRepository $groupeRepository,
        PermissionRepository $permissionRepository,
        Groupe $groupe = null,
        Menu $menu = null,
        User $user = null,
        UserRepository $userRepository,
        MenuRepository $menuRepository,
        Request $request
    ): Response

        {
            if(!$request->getSession()->has('user_session')){
                return $this->redirectToRoute('app_login');
            } else {
                if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR'))
                {
                    $user = $userRepository->find($this->getUser());
                        $majDate = new \DateTimeImmutable();
                        if ($id_groupe and $id_menu){
                        $groupe = $groupeRepository->find($id_groupe);
                        $menu = $menuRepository->find($id_menu);

                        $recherche_permission = $permissionRepository->findBy(['code_groupe'=>$groupe, 'code_menu'=>$menu]);
                        if ($recherche_permission){
                            $response = true;
                        }else{
                            $response = false;
                        }
                            return new JsonResponse($response);
                }
            } else {
                    return $this->redirectToRoute('app_no_permission_user_active');
                }

           }
    }

    #[Route('snvlt/my_groups/prms/add/{id_groupe}/{id_menu}', name: 'my_groups-prms.add')]
    public function addPermissionsByGroupOperateur(
        int $id_groupe,
        int $id_menu,
        ManagerRegistry $registry,
        GroupeRepository $groupeRepository,
        Groupe $groupe = null,
        Menu $menu = null,
        User $user = null,
        UserRepository $userRepository,
        MenuRepository $menuRepository,
        Request $request
    ): Response

    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {

                $user = $userRepository->find($this->getUser());
                $majDate = new \DateTimeImmutable();
                //dump($id_menu);
                //dump($id_groupe);

                if ($id_groupe and $id_menu){
                    $groupe = $groupeRepository->find($id_groupe);
                    $menu = $menuRepository->find($id_menu);

                    $permission = new Permission();
                    $permission->setCreatedBy($user);
                    $permission->setCreatedAt($majDate);
                    $permission->setCodeGroupe($groupe);
                    $permission->setCodeMenu($menu);

                    $registry->getManager()->persist($permission);
                    $registry->getManager()->flush();

                    $response = $this->translator->trans("Permissions added by"). $user;
                    $this->logger->log("INFO", $response);
                    return new JsonResponse(json_encode($response));
                }


        }
    }

    #[Route('snvlt/my_groups/prms/rem/{id_groupe}/{id_menu}', name: 'my_groups-prms.rem')]
    public function remPermissionsByGroupOperateur(
        int $id_groupe,
        int $id_menu,
        ManagerRegistry $registry,
        GroupeRepository $groupeRepository,
        MenuRepository $menuRepository,
        Groupe $groupe = null,
        User $user = null,
        UserRepository $userRepository,
        PermissionRepository $permissionRepository,
        Request $request
    ):Response

    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            
                $user = $userRepository->find($this->getUser());

                if ($id_groupe and $id_menu){
                    $groupe = $groupeRepository->find($id_groupe);
                    $menu = $menuRepository->find($id_menu);


                    $permission = $permissionRepository->findOneBy(['code_groupe'=>$groupe, 'code_menu'=>$menu]);
                    if ($permission){
                        $registry->getManager()->remove($permission);
                        $registry->getManager()->flush();
                    }
                }

                    $response = $this->translator->trans("Permissions removed by"). $user;
                    $this->logger->log("INFO", $response);

                    return new JsonResponse(json_encode($response));


        }
    }


    #[Route('snvlt/my_groups/add', name: 'my_groups.add')]
    public function addMyGroups(

        ManagerRegistry $doctrine,
        Request $request,
        GrilleLegaliteRepository $grille_legalites,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if (!$this->isGranted('ADMIN')) {
                $user = $userRepository->find($this->getUser());

                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $date_creation = new \DateTimeImmutable();
                $grp = 0;
                $titre = $this->translator->trans("Edit Users Group");
                $my_group = $groupeRepository->find($user->getCodeGroupe()->getId());


                $groupResponsable = null;

                if ($my_group->getParentGroupe()){
                    $groupResponsable = $groupeRepository->find($my_group->getParentGroupe());
                }
                $my_group = new Groupe();
                //dd($grille_legalite);



                $form = $this->createForm(MyGroupsType::class, $my_group);

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {

                    $my_group->setCodeTypeOperateur($user->getCodeOperateur()->getId());
                    $my_group->setNomGroupe($this->utils->removeSpecialCharacters($form->get('nom_groupe')->getData()));

                    if($user->getCodeOperateur()->getId() == 1){

                        if($user->getCodeService()){
                            $my_group->setCodeOperateur($user->getCodeService()->getId());
                        }else if($user->getCodeDirection()){
                            $my_group->setCodeOperateur($user->getCodeDirection()->getId());
                        }

                    }elseif($user->getCodeOperateur()->getId() == 2){
                        $my_group->setCodeOperateur($user->getCodeexploitant()->getId());
                    }elseif($user->getCodeOperateur()->getId() == 3){
                        $my_group->setCodeOperateur($user->getCodeindustriel()->getId());
                    }elseif($user->getCodeOperateur()->getId() == 4){
                        $my_group->setCodeOperateur($user->getCodeExportateur()->getId());
                    }elseif($user->getCodeOperateur()->getId() == 5){
                        $my_group->setCodeOperateur($user->getCodeDr()->getId());
                    }elseif($user->getCodeOperateur()->getId() == 6){
                        $my_group->setCodeOperateur($user->getCodeDdef()->getId());
                    }elseif($user->getCodeOperateur()->getId() == 7){
                        $my_group->setCodeOperateur($user->getCodeCantonnement()->getId());
                    }elseif($user->getCodeOperateur()->getId() == 10){
                        $my_group->setCodeOperateur($user->getCodePosteControle()->getId());
                    }
                    $my_group->setParentGroupe($user->getCodeGroupe()->getId());
                    $my_group->setGroupeSystem(false);

                    $manager = $doctrine->getManager();
                    $manager->persist($my_group);
                    $manager->flush();

                    $this->addFlash('success', $this->translator->trans("Group has been edited successfully"));
                    return $this->redirectToRoute("my_groups");
                } else {
                    return $this->render('administration/my_groups/add.html.twig', [
                        'form' => $form->createView(),
                        'titre' => $titre,
                        'liste_grille_legalites' => $grille_legalites->findAll(),
                        'liste_menus' => $menus->findOnlyParent(),
                        "all_menus" => $menus->findAll(),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
                        'groupe' => $code_groupe,
                        'grp' => $grp,
                        'liste_parent'=>$permissions,
                        'permission_par_groupe'=>$permissions->findBy(['code_groupe_id'=>$groupResponsable])
                    ]);
                }
                /*}*/
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
