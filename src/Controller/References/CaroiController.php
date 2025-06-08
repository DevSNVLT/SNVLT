<?php

namespace App\Controller\References;

use App\Entity\References\Caroi;
use App\Entity\References\Direction;
use App\Entity\References\ServiceMinef;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CaroiController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator)
    {

    }

    #[Route('/references/caroi', name: 'app_references_caroi')]
    public function index(
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
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('references/caroi/index.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'liste_parent'=>$permissions
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/users/caroi/{lettre}/{id_service}', name: 'caroi_users')]
    public function caroi_users(
        ManagerRegistry $registry,
        Request $request,
        string $lettre,
        int $id_service,
        UserRepository $userRepository
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {

                $user = $userRepository->find($this->getUser());
                $liste_users = array();

                if($id_service){
                    if ($lettre == "D"){
                        $dir_minef = $registry->getRepository(Direction::class)->find((int) $id_service);
                        $users = $registry->getRepository(User::class)->findBy(['actif'=>true, 'code_direction'=>$dir_minef, 'code_service'=>null]);

                        foreach($users as $user_minef){

                            $liste_users[] = array(
                                'id_user'=>$user_minef->getId(),
                                'nom_prenoms'=>$user_minef->getNomUtilisateur()." " . $user_minef->getPrenomsUtilisateur()
                            );
                        }

                    } elseif ($lettre == "S") {
                        $serv_minef = $registry->getRepository(ServiceMinef::class)->find((int) $id_service);
                        $users = $registry->getRepository(User::class)->findBy(['actif' => true, 'code_service' => $serv_minef]);

                        foreach ($users as $user_minef) {

                            $liste_users[] = array(
                                'id_user' => $user_minef->getId(),
                                'nom_prenoms' => $user_minef->getNomUtilisateur() . " " . $user_minef->getPrenomsUtilisateur()
                            );

                        }
                    }
                }

                return new JsonResponse(json_encode($liste_users));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/liste_services/caroi', name: 'caroi_liste_services')]
    public function caroi_liste_services(
        ManagerRegistry $registry,
        Request $request,
        UserRepository $userRepository
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {

                $user = $userRepository->find($this->getUser());
                $liste_services = array();

                $directions = $registry->getRepository(Direction::class)->findAll();
                foreach($directions as $direction){
                    $services_minef = $registry->getRepository(ServiceMinef::class)->findBy(['code_direction'=>$direction]);
                    $liste_services[] = array(
                        'libelle'=>$direction->getSigle(),
                        'id_service'=>"D".$direction->getId()
                    );
                    foreach($services_minef as $service_minef){
                        $liste_services[] = array(
                            'libelle'=>$service_minef->getSigle(),
                            'id_service'=>"S".$service_minef->getId()
                        );
                    }
                }
                sort($liste_services);
                return new JsonResponse(json_encode($liste_services));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/add_user/caroi/{id_user}', name: 'caroi_add_user')]
    public function caroi_add_user(
        ManagerRegistry $registry,
        Request $request,
        int $id_user,
        UserRepository $userRepository
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {

                $user = $userRepository->find($this->getUser());
                $reponse = array();

                $user_caroi = $registry->getRepository(User::class)->find($id_user);


                if ($user_caroi){
                    $check_user = $registry->getRepository(Caroi::class)->findOneBy(['code_user'=>$user_caroi]);
                    if ($check_user){
                        $reponse[] = array(
                            'code'=>'INSIDE'
                        );
                    } else {
                        $caroi = new Caroi();

                        $caroi->setNomPrenoms($user_caroi);
                        $caroi->setCraetedAt(new \DateTime());
                        $caroi->setCreatedBy($user);

                        if ($user_caroi->getCodeService()){
                            $caroi->setServiceMinef($registry->getRepository(ServiceMinef::class)->find($user_caroi->getCodeService()->getId())->getSigle());
                        } else {
                            $caroi->setServiceMinef($registry->getRepository(Direction::class)->find($user_caroi->getCodeDirection()->getId())->getSigle());
                        }

                        $caroi->setCodeUser($user_caroi);
                        $caroi->setFonction($user_caroi->getFonction());

                        $registry->getManager()->persist($caroi);
                        $registry->getManager()->flush();

                        $reponse[] = array(
                            'code'=>'SUCCESS'
                        );
                    }

                } else {
                    $reponse[] = array(
                        'code'=>'FAILED'
                    );
                }

                return new JsonResponse(json_encode($reponse));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/users/caroi/del/{id_user}', name: 'caroi_del_user')]
    public function caroi_del_user(
        ManagerRegistry $registry,
        Request $request,
        int $id_user,
        UserRepository $userRepository
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {

                $user = $userRepository->find($this->getUser());
                $reponse = array();

                $user_caroi = $registry->getRepository(Caroi::class)->find($id_user);
                //dd($user_caroi);
                if ($user_caroi){
                    $registry->getManager()->remove($user_caroi);
                    $registry->getManager()->flush();


                    $reponse[] = array(
                        'code'=>'SUCCESS'
                    );
                } else {
                    $reponse[] = array(
                        'code'=>'FAILED'
                    );
                }

                return new JsonResponse(json_encode($reponse));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }


    #[Route('/snvlt/liste_users/caroi/all', name: 'caroi_liste_users_all')]
    public function caroi_liste_users_all(
        ManagerRegistry $registry,
        Request $request,
        UserRepository $userRepository
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {

                $user = $userRepository->find($this->getUser());
                $liste_users = array();

                $users = $registry->getRepository(Caroi::class)->findAll();
                foreach($users as $user_caroi){

                    $liste_users[] = array(
                         'nom_prenoms'=>$user_caroi->getNomPrenoms(),
                         'fonction'=>$user_caroi->getFonction(),
                         'service'=>$user_caroi->getServiceMinef(),
                         'id_user'=>$user_caroi->getCodeUser()->getId(),
                         'id_caroi'=>$user_caroi->getId(),
                         'email'=>$registry->getRepository(User::class)->find($user_caroi->getCodeUser())->getEmail(),
                         'Mobile'=>$registry->getRepository(User::class)->find($user_caroi->getCodeUser())->getMobile(),
                    );
                }
                sort($liste_users);
                return new JsonResponse(json_encode($liste_users));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/membres/caroi/sup_c/{id_caroi}', name: 'supprime_caroi')]
    public function supprime_caroi(
        ManagerRegistry $registry,
        Request $request,
        int $id_caroi,
        UserRepository $userRepository
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {

                $user = $userRepository->find($this->getUser());
                $reponse = array();

                $user_caroi = $registry->getRepository(Caroi::class)->find($id_caroi);
                //dd($user_caroi);
                if ($user_caroi){
                    $registry->getManager()->remove($user_caroi);
                    $registry->getManager()->flush();


                    $reponse[] = array(
                        'code'=>'SUCCESS'
                    );
                } else {
                    $reponse[] = array(
                        'code'=>'FAILED'
                    );
                }

                return new JsonResponse(json_encode($reponse));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
