<?php

namespace App\Controller\Autorisation;

use App\Entity\Autorisation\ContratBcbgfh;
use App\Entity\References\Foret;
use App\Entity\User;
use App\Form\Autorisation\ContratBcbgfhType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\Autorisation\ContratBcbgfhRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\TypeAutorisationRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContratBcbgfhController extends AbstractController
{
    #[Route('/autorisation/contrat/bcbgfh', name: 'app_autorisation_contrat_bcbgfh')]
    public function index(ContratBcbgfhRepository $contratbcbgfhRepository,
                          MenuRepository $menus,
                          MenuPermissionRepository $permissions,
                          GroupeRepository $groupeRepository,
                          Request $request,
                          UserRepository $userRepository,
                          User $user = null,
                          NotificationRepository $notification): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();



                return $this->render('autorisation/contrat_bcbgfh/index.html.twig', [
                    'contrats' => $contratbcbgfhRepository->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions
                ]);
            } else {
                return  $this->redirectToRoute('app_no_permission_user_active');
            }
        }


    }



    #[Route('/autorisation/contrat/bcbgfh/e/', name: 'contrat_bcbgfh.edit')]
    public function editContratBcbgfh(
        ContratBcbgfh $contratbcbgfh = null,
        ManagerRegistry $doctrine,
        Request $request,
        ContratBcbgfhRepository $contratbcbgfhs,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
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


                    return $this->render('autorisation/contrat_bcbgfh/add-contratbcbgfh.html.twig',[
                        'liste_ddefs' => $contratbcbgfhs->findAll(),
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'groupe'=>$code_groupe,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'liste_parent'=>$permissions
                    ]);


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    
}
