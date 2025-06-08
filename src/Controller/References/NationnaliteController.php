<?php

namespace App\Controller\References;

use App\Entity\References\Nationalite;
use App\Form\References\NationaliteType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\ExploitantRepository;
use App\Repository\References\NationaliteRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NationnaliteController extends AbstractController
{
    #[Route('/nationnalite', name: 'app_nationnalite')]
    public function index(): Response
    {
        return $this->render('nationnalite/index.html.twig', [
            'controller_name' => 'NationnaliteController',
        ]);
    }

    #[Route('/snvlt/ref/nationalites', name: 'ref_nationalites')]
    public function listing(
        NationaliteRepository $nationalites,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        Request $request,
        UserRepository $userRepository,
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

                return $this->render('references/nationalite/index.html.twig', [
                    'liste_nationalites' => $nationalites->findAll(),
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
    #[Route('snvlt/ref/nationnalite/{id_nationalite?0}', name: 'nationalite.edit')]
    public function nationalite_edit(NationaliteRepository $nationalites,
                            MenuRepository $menus,
                            MenuPermissionRepository $permissions,
                            GroupeRepository $groupeRepository,
                            ManagerRegistry $doctrine,
                            int $id_nationalite,
                            Request $request,
                            UserRepository $userRepository,
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

                $titre = "Modifier la nationalité";
                $nationalite = $nationalites->find($id_nationalite);
                $new = false;

                if(!$nationalite){
                    $new = true;
                    $nationalite = new Nationalite();
                }

                $form = $this->createForm(NationaliteType::class, $nationalite);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){

                    $nat = strtoupper($form->get('nationalite')->getData());
                    $nationalite->setNationalite($nat);;
                    $manager = $doctrine->getManager();

                    $manager->persist($nationalite);
                    $manager->flush();
                    $manager->flush();

                    if ($new){
                        $this->addFlash('success', "La nationalité " . $nationalite->getNationalite() . " vient d'être créé.");
                    } else {
                        $this->addFlash('success', "La nationalité " . $nationalite->getNationalite() . " a été mise à jour");
                    }
                    return $this->redirectToRoute("ref_nationalites");
                } else {
                    return $this->render('references/nationalite/add.html.twig', [
                        'liste_nationalites' => $nationalites->findAll(),
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'liste_drs' => $nationalites->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'groupe'=>$code_groupe,
                        'form' =>$form->createView(),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'titre'=>$titre,
                        'liste_parent'=>$permissions,
                        'new_value'=>$new
                    ]);
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
    #[Route('snvlt/ref/nationalites/data_json', name: 'json_nationalites')]
    public function exploitants_json(NationaliteRepository $nationaliteRepository,
                                     MenuRepository $menus,
                                     MenuPermissionRepository $permissions,
                                     GroupeRepository $groupeRepository,
                                     UserRepository $userRepository,
                                     ManagerRegistry $doctrine,
                                     Request $request,
                                     NotificationRepository $notificationRepository,
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
                $liste_nationalite = $nationaliteRepository->findBy([], ['nationalite'=>'ASC']);
                foreach ( $liste_nationalite as $nationalite){
                    $response[] =  array(
                        'id'=>$nationalite->getId(),
                        'nationalite'=>$nationalite->getNationalite()
                    );


                }
                return  new  JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
