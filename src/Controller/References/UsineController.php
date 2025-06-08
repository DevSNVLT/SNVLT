<?php

namespace App\Controller\References;

use App\Controller\Services\Utils;
use App\Entity\References\Cantonnement;
use App\Entity\References\Foret;
use App\Entity\References\Usine;
use App\Entity\Admin\Exercice;
use App\Entity\User;
use App\Entity\Autorisation\Attribution;
use App\Form\References\UsineType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\ExploitantRepository;
use App\Repository\References\UsineRepository;
use App\Repository\TypeAutorisationRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UsineController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator, private Utils $utils)
    {
    }

    #[Route('/liste-usines', name: 'liste_usines')]
    public function index(UsineRepository $usines,ManagerRegistry $registry): Response
    {   
        $totalUsineExercice = [];
        $exercice = $registry->getRepository(Exercice::class)->findOneBy([], ['id' => 'DESC']);
        $UsineList = $registry->getRepository(Usine::class)->findAll();

        foreach ($UsineList as $usine) {
            $codeExploitant = $usine->getCodeExploitant() ? $usine->getCodeExploitant()->getId(): null; //Vérifié:ok
            $raisonSocial = $usine->getRaisonSocialeUsine();
            $numero = $usine->getNumeroUsine();

           $attributions = $registry->getRepository(Attribution::class)
               ->findBy(['exercice'=>$exercice,'code_exploitant'=>$codeExploitant]);

           if($attributions>0){
                $totalUsineExercice[] = [
                    'codeExploitant' => $codeExploitant,
                    'raisonSocial' => $raisonSocial,
                    'numero' => $numero
                ] ;
           }

        }


        return $this->render('usine/liste.html.twig', [
            'liste_usines' => $totalUsineExercice,
            'titre'=>'TRANSFORMATEURS DE BOIS AGREES'
        ]);
    }


    #[Route('snvlt/ref/usines', name: 'ref_usines')]
    public function listing(UsineRepository $usines,
                            ManagerRegistry $doctrine,
                            Request $request,
                            TypeAutorisationRepository $autorisations,
                            MenuPermissionRepository $permissions,
                            MenuRepository $menus,
                            GroupeRepository $groupeRepository,
                            UserRepository $userRepository,
                            User $user = null,
                            NotificationRepository $notification
        ): Response
    {

            $code_groupe = $groupeRepository->find(1);
            $titre = $this->translator->trans("Edit Wood Factory");


                return $this->render('references/usine/index.html.twig', [
                    'liste_usines' => $usines->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                    'groupe'=>$code_groupe->getId(),
                    'titre'=>$titre,
                    'liste_parent'=>$permissions
                ]);


    }

    #[Route('snvlt/ref/usines/agreements', name: 'ref_usines_agreements')]
    public function ref_usines_agreements(UsineRepository $usines,
                            ManagerRegistry $doctrine,
                            Request $request,
                            TypeAutorisationRepository $autorisations,
                            MenuPermissionRepository $permissions,
                            MenuRepository $menus,
                            GroupeRepository $groupeRepository,
                            UserRepository $userRepository,
                            User $user = null,
                            NotificationRepository $notification
        ): Response
    {

            $code_groupe = $groupeRepository->find(1);
            $titre = $this->translator->trans("Edit Wood Factory");


                return $this->render('references/usine/agreements.html.twig', [
                    'liste_usines' => $usines->findBy([],['raison_sociale_usine'=>'ASC']),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                    'groupe'=>$code_groupe->getId(),
                    'titre'=>$titre,
                    'liste_parent'=>$permissions
                ]);


    }


    #[Route('/edit/usine/{id_usine?0}', name: 'usine.edit')]
    public function editUsine(
        Usine $usine = null,
        UsineRepository $usines,ManagerRegistry $doctrine,
        Request $request,
        TypeAutorisationRepository $autorisations,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_usine,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        $code_groupe = $groupeRepository->find(1);
        $titre = $this->translator->trans("Edit Wood Factory");
        $usine = $usines->find($id_usine);
        //dd($usine);
        if(!$usine){
            $new = true;
            $usine = new Usine();
            $titre = $this->translator->trans("Edit Wood Factory");
        }

            $new = false;
            if(!$usine){
                $new = true;
                $usine = new Usine();
            }
            $form = $this->createForm(UsineType::class, $usine);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){

                $usine->setRaisonSocialeUsine(strtoupper($this->utils->removeSpecialCharacters($form->get('raison_sociale_usine')->getData())));
                $usine->setSigle(strtoupper($this->utils->removeSpecialCharacters($form->get('sigle')->getData())));
                $usine->setPersonneRessource(strtoupper($this->utils->removeSpecialCharacters($form->get('personne_ressource')->getData())));
                $usine->setAdresseUsine(strtoupper($this->utils->removeSpecialCharacters($form->get('adresse_usine')->getData())));
                $usine->setLocalisationUsine(strtoupper($this->utils->removeSpecialCharacters($form->get('localisation_usine')->getData())));
                $usine->setLocalisationUsine(strtoupper($this->utils->removeSpecialCharacters($form->get('ville')->getData())));

                $manager = $doctrine->getManager();
                $manager->persist($usine);
                $manager->flush();

                $this->addFlash('success',$this->translator->trans("Wood Factory has been edited successfully"));
                return $this->redirectToRoute("ref_usines");
            } else {
                return $this->render('references/usine/add-usine.html.twig',[
                    'form' =>$form->createView(),
                    'titre'=>$titre,
                    'liste_usines' => $usines->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe->getId(),
                    'liste_parent'=>$permissions
                ]);
            }
        /*}*/
    }
    #[Route('snvlt/ref/usines/data_json', name: 'json_usines')]
    public function usines_json(

                                      UserRepository $userRepository,
                                      ManagerRegistry $doctrine,
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
                $liste_usines = $doctrine->getRepository(Usine::class)->findBy([], ['raison_sociale_usine'=>'ASC']);
                foreach ( $liste_usines as $usine){
                    $response[] =  array(
                        'id'=>$usine->getId(),
                        'sigle'=>$usine->getSigle(),
                        'rs'=>$usine->getRaisonSocialeUsine()
                    );


                }
                return  new  JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/ref/usines/search/{id_usine}/data_json', name: 'json_usine_by_id')]
    public function usine_by_id_json(UsineRepository $usines,
                                           MenuRepository $menus,
                                           MenuPermissionRepository $permissions,
                                           GroupeRepository $groupeRepository,
                                           UserRepository $userRepository,
                                           ManagerRegistry $doctrine,
                                           Request $request,
                                           Usine $single_usine = null,
                                           int $id_usine,
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

                $single_usine = $doctrine->getRepository(Usine::class)->find($id_usine);
                if($single_usine){
                    $response = array();

                    $response[] =  array(
                        'id'=>$single_usine->getId(),
                        'sigle'=>$single_usine->getSigle(),
                        'rs'=>$single_usine->getRaisonSocialeUsine(),
                        'personne_ressource'=>$single_usine->getPersonneRessource(),
                        'email_personne_ressource'=>$single_usine->getEmailPersonneRessource(),
                        'mobile_personne_ressource'=>$single_usine->getMobilePersonneRessource()
                    );



                    return  new  JsonResponse(json_encode($response));
                }else {
                    return  new  JsonResponse(json_encode("NO OPERATOR SELECTED"));
                }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/ref/usines/data_json', name: 'json_usines_all')]
    public function json_usines_all(UsineRepository $usines,
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

                //------------------------- Filtre les CP par type Opérateur ------------------------------------- //

                    $liste_usines= $usines->findBy([], ['raison_sociale_usine'=>'ASC']);
                    foreach ( $liste_usines as $usine){
                        if ($usine->getSigle()){
                            $rs = $usine->getSigle();
                        } else {
                            $rs = $usine->getRaisonSocialeUsine();
                        }
                        $response[] =  array(
                            'id'=>$usine->getId(),
                            'sigle'=>$usine->getSigle(),
                            'rs'=>$rs
                        );

                    }


                return  new  JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/ref/usines/agreement_usine/{id_usine}/{value}', name: 'agreement_usine')]
    public function agreement_usine(UsineRepository $usines,
                                    UserRepository $userRepository,
                                    ManagerRegistry $doctrine,
                                    Request $request,
                                    int $id_usine,
                                    int $value
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
                $valeur = false;
                //------------------------- Filtre les CP par type Opérateur ------------------------------------- //

                $usine= $usines->find($id_usine);
                if($usine){
                    if((int)$value == 1){
                        $valeur = true;
                    }
                    $usine->setAgree($valeur);

                    $doctrine->getManager()->persist($usine);
                    $doctrine->getManager()->flush();
                    $response[] = array(
                        'valeur'=>'SUCCESS'
                    );
                } else {
                    $response[] = array(
                        'valeur'=>'ERROR'
                    );
                }

                return  new  JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
