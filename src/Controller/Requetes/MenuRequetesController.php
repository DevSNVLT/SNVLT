<?php

namespace App\Controller\Requetes;

use App\Entity\References\Essence;
use App\Entity\References\SousPrefecture;
use App\Entity\References\Usine;
use App\Entity\References\ZoneHemispherique;
use App\Entity\Requetes\MenuRequetes;
use App\Entity\Requetes\PerformanceBrhJour;
use App\Entity\Requetes\TypeRequetes;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuRequetesController extends AbstractController
{
    #[Route('snvlt/requests/0/admin', name: 'app_requests')]
    public function index(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if (
                $this->isGranted('ROLE_ADMINISTRATIF') or
                $this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_ADMIN')
            ) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();



                return $this->render('requetes/menu_requetes/index.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'listes_type_req'=>$registry->getRepository(TypeRequetes::class)->findAll(),
                    'listes_req'=>$registry->getRepository(MenuRequetes::class)->findBy([],['libelle'=>'ASC'])
                ]);

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
}


