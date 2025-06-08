<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NoPermissionUserActiveController extends AbstractController
{
    #[Route('/no/permission/user/active', name: 'app_no_permission_user_active')]
    public function index(Request $request,
                          MenuRepository $menus,
                          MenuPermissionRepository $permissions,
                          GroupeRepository $groupeRepository,
                          GroupeRepository $groupe,
                          UserRepository $userRepository,
                          User $user = null,
                          NotificationRepository $notification): Response
    {


                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                return $this->render('exceptions/user-active-pas-de-permissions.html.twig', [
                    'liste_menus' => $menus->findOnlyParent(),
                    "all_menus" => $menus->findAll(),
                    'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
                    'liste_parent' => $permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                ]);


    }

    #[Route('/no/permission/user/pnf', name: 'app_no_page_found')]
    public function app_no_page_found(Request $request,
                          MenuRepository $menus,
                          MenuPermissionRepository $permissions,
                          GroupeRepository $groupeRepository,
                          GroupeRepository $groupe,
                          UserRepository $userRepository,
                          User $user = null,
                          NotificationRepository $notification): Response
    {


        $user = $userRepository->find($this->getUser());
        $code_groupe = $user->getCodeGroupe()->getId();
        return $this->render('exceptions/no_page_found.html.twig', [
            'liste_menus' => $menus->findOnlyParent(),
            "all_menus" => $menus->findAll(),
            'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
            'liste_parent' => $permissions,
            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
        ]);


    }

    #[Route('/no/permission/user/activity', name: 'user_not_active')]
    public function user_not_active(Request $request,
                                      MenuRepository $menus,
                                      MenuPermissionRepository $permissions,
                                      GroupeRepository $groupeRepository,
                                      GroupeRepository $groupe,
                                      UserRepository $userRepository,
                                      User $user = null,
                                      NotificationRepository $notification): Response
    {


        $user = $userRepository->find($this->getUser());
        $code_groupe = $user->getCodeGroupe()->getId();
        return $this->render('exceptions/user_not_active.html.twig', [
            'liste_menus' => $menus->findOnlyParent(),
            "all_menus" => $menus->findAll(),
            'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
            'liste_parent' => $permissions,
            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
        ]);


    }
}
