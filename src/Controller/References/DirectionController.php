<?php

namespace App\Controller\References;


use App\Entity\References\Direction;
use App\Entity\User;
use App\Form\References\DirectionType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DemandeOperateurRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\DirectionRepository;
use App\Repository\References\ServiceMinefRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DirectionController extends AbstractController
{

    public function __construct(private TranslatorInterface $translator)
    {

    }

    #[Route('snvlt/ref/dirminef', name: 'ref_directions')]
    public function listing(DirectionRepository $directions,
    MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        ManagerRegistry $doctrine,
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        //dd($request);
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                return $this->render('references/direction/index.html.twig',
                    [

                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'groupe'=>$code_groupe,
                        'directions'=>$directions->findAll(),
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('/edit/direction/{id_direction?0}', name: 'direction.edit')]
    public function editDirection(
        Direction $direction = null,
        ManagerRegistry $doctrine,
        Request $request,
        DirectionRepository $directions,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        ServiceMinefRepository $serviceMinefRepository,
        GroupeRepository $groupeRepository,
        int $id_direction,
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



        $titre = $this->translator->trans("Edit Ministry Direction");
        $direction = $directions->find($id_direction);

        if(!$direction){
            $new = true;
            $direction = new Direction();
            $titre = $this->translator->trans("Add Ministry Direction");
        }

            $new = false;
            if(!$direction){
                $new = true;
                $direction = new Direction();

            }
            $form = $this->createForm(DirectionType::class, $direction);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){


                $manager = $doctrine->getManager();
                $manager->persist($direction);
                $manager->flush();

                $this->addFlash('success',$this->translator->trans("Ministry Direction has been edited and updated successfully"));
                return $this->redirectToRoute("ref_directions");
            } else {
                return $this->render('references/direction/add-direction.html.twig',[
                    'form' =>$form->createView(),
                    'titre'=>$titre,
                    'ref_directions' => $directions->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                    'directions'=>$directions->findAll(),
                    'liste_parent'=>$permissions,
                    'liste_services'=>$serviceMinefRepository->findBy(['code_direction'=>$direction])
                ]);
            }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
            }

    }
    #[Route('snvlt/directions/list', name: 'liste_directions')]
    public function direction_json(Request $request, DirectionRepository $directionRepository):Response{
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
                {

                    $liste_directions = $directionRepository->findWithResponsable();

                    $response = array();
                    foreach ($liste_directions as $direction) {
                        $response[] = array(
                            'id' => $direction->getId(),
                            'denomination' => $direction->getSigle()
                        );
                    }

                    return new JsonResponse(json_encode($response));

                } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/ref/directions/data_json', name: 'json_directions_minef')]
    public function json_directions_minef(DirectionRepository $Drs,
                            UserRepository $userRepository,
                            Request $request
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $response = array();

                //------------------------- Filtre les CP par type Opérateur ------------------------------------- //

                $liste_Drs= $Drs->findBy([], ['denomination'=>'ASC']);
                foreach ( $liste_Drs as $Dr){
                    if ($Dr->getSigle()){
                        $sigle = $Dr->getSigle();
                    } else {
                        $sigle = $Dr->getDenomination();
                    }
                    $response[] =  array(
                        'id'=>$Dr->getId(),
                        'sigle'=>$sigle,
                        'rs'=>$sigle,
                    );

                }


                return  new  JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
    #[Route('snvlt/ref/sodefor/data_json', name: 'json_sodefor')]
    public function json_sodefor(DirectionRepository $Drs,
                            UserRepository $userRepository,
                            Request $request
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $response = array();

                //------------------------- Filtre les CP par type Opérateur ------------------------------------- //



                    $response[] =  array(
                        'id'=>1,
                        'sigle'=>"SODEFOR",
                        'rs'=>"SODEFOR",
                    );




                return  new  JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
