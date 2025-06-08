<?php

namespace App\Controller\References;


use App\Entity\Autorisation\Attribution;
use App\Entity\References\Cantonnement;
use App\Entity\References\Commercant;
use App\Entity\References\Foret;
use App\Form\References\CommercantType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\CommercantRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CommercantController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    #[Route('snvlt/ref/commercants', name: 'ref_commercants')]
    public function listing(CommercantRepository $commercants,
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


                return $this->render('references/commercant/index.html.twig', [
                    'liste_commercants' => $commercants->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    'mes_notifs'=>$notificationRepository->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
       }
        }
    #[Route('snvlt/ref/commercants/data_json', name: 'json_commercants')]
    public function commercants_json(CommercantRepository $commercants,
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
            if (
                $this->isGranted('ROLE_MINEF') or
                $this->isGranted('ROLE_ADMIN') or
                $this->isGranted('ROLE_EXPLOITANT') or
                $this->isGranted('ROLE_INDUSTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $response = array();

                //------------------------- Filtre les CP par type Opérateur ------------------------------------- //
                if ($user->getCodeOperateur()->getId() == 1){
                    $liste_commercants = $commercants->findBy([], ['raison_sociale_commercant'=>'ASC']);
                    foreach ( $liste_commercants as $commercant){
                        if ($commercant->getSigle()){
                            $rs = $commercant->getSigle();
                        } else {
                            $rs = $commercant->getRaisonSocialeCommercant();
                        }
                        $response[] =  array(
                            'id'=>$commercant->getId(),
                            'sigle'=>$commercant->getSigle(),
                            'rs'=>$rs
                        );


                    }
                } elseif ($user->getCodeOperateur()->getId() == 2){
                        if ($user->getCodecommercant()->getSigle()){
                            $rs = $user->getCodecommercant()->getSigle();
                        } else {
                            $rs = $user->getCodecommercant()->getRaisonSocialeCommercant();
                        }
                        $response[] =  array(
                            'id'=>$user->getCodecommercant()->getId(),
                            'sigle'=>$user->getCodecommercant()->getSigle(),
                            'rs'=>$rs
                        );

                } elseif ($user->getCodeOperateur()->getId() == 3){
                    $exp = $user->getCodeindustriel()->getCodeCommercant();
                    if ($exp){
                        if ($exp->getSigle()){
                            $rs = $exp->getSigle();
                        } else {
                            $rs = $exp->getRaisonSocialeCommercant();
                        }
                        $response[] =  array(
                            'id'=>$exp->getId(),
                            'sigle'=>$exp->getSigle(),
                            'rs'=>$rs
                        );
                    }


                } elseif ($user->getCodeOperateur()->getId() == 5){
                    //Recherche les forêts de la DR
                    $cantons = $doctrine->getRepository(Cantonnement::class)->findBy(['code_dr'=>$user->getCodeDr()]);
                    foreach($cantons as $canton){
                        $forets = $doctrine->getRepository(Foret::class)->findBy(['code_cantonnement'=>$canton]);
                        foreach ($forets as $fore){
                            $attributions = $fore->getAttributions();
                            foreach($attributions as $att){
                                if ($att->getCodecommercant()->getSigle()){
                                    $rs = $att->getCodecommercant()->getSigle();
                                } else {
                                    $rs = $att->getCodecommercant()->getRaisonSocialeCommercant();
                                }
                                $response[] =  array(
                                    'id'=>$att->getCodeCommercant()->getId(),
                                    'sigle'=>$att->getCodeCommercant()->getSigle(),
                                    'rs'=>$rs
                                );
                            }

                        }
                    }

                }  elseif ($user->getCodeOperateur()->getId() == 6){
                    //Recherche les forêts de la DR
                    $cantons = $doctrine->getRepository(Cantonnement::class)->findBy(['code_ddef'=>$user->getCodeDdef()]);
                    foreach($cantons as $canton){
                        $forets = $doctrine->getRepository(Foret::class)->findBy(['code_cantonnement'=>$canton]);
                        foreach ($forets as $fore){
                            $attributions = $fore->getAttributions();
                            foreach($attributions as $att){
                                if ($att->getCodecommercant()->getSigle()){
                                    $rs = $att->getCodecommercant()->getSigle();
                                } else {
                                    $rs = $att->getCodecommercant()->getRaisonSocialeCommercant();
                                }
                                $response[] =  array(
                                    'id'=>$att->getCodeCommercant()->getId(),
                                    'sigle'=>$att->getCodeCommercant()->getSigle(),
                                    'rs'=>$rs
                                );
                            }

                        }
                    }

                } elseif ($user->getCodeOperateur()->getId() == 7){
                    $forets = $doctrine->getRepository(Foret::class)->findBy(['code_cantonnement'=>$user->getCodeCantonnement()]);
                    foreach ($forets as $fore){
                        $attributions = $fore->getAttributions();
                        foreach($attributions as $att){
                            if ($att->getCodecommercant()->getSigle()){
                                $rs = $att->getCodecommercant()->getSigle();
                            } else {
                                $rs = $att->getCodecommercant()->getRaisonSocialeCommercant();
                            }
                            $response[] =  array(
                                'id'=>$att->getCodeCommercant()->getId(),
                                'sigle'=>$att->getCodeCommercant()->getSigle(),
                                'rs'=>$rs
                            );
                        }

                    }
                }

                return  new  JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/ref/commercants/search/{id_commercant}/data_json', name: 'json_commercant_by_id')]
    public function commercant_by_id_json(CommercantRepository $commercants,
                                          MenuRepository $menus,
                                          MenuPermissionRepository $permissions,
                                          GroupeRepository $groupeRepository,
                                          UserRepository $userRepository,
                                          ManagerRegistry $doctrine,
                                          Request $request,
                                          Commercant $single_commercant = null,
                                          int $id_commercant,
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

                $single_commercant = $doctrine->getRepository(Commercant::class)->find($id_commercant);
                if($single_commercant){
                    $response = array();

                    $response[] =  array(
                        'id'=>$single_commercant->getId(),
                        'sigle'=>$single_commercant->getSigle(),
                        'rs'=>$single_commercant->getRaisonSocialeCommercant(),
                        'personne_ressource'=>$single_commercant->getPersonneRessource(),
                        'email_personne_ressource'=>$single_commercant->getEmailPersonneRessource(),
                        'mobile_personne_ressource'=>$single_commercant->getMobilePersonneRessource(),
                        'mobile'=>$single_commercant->getMobileCommercant(),
                        'adresse'=>$single_commercant->getAdresseCommercant(),
                        'cc'=>$single_commercant->getNccCommercant()
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


    #[Route('/edit/commercants/{id_commercant?0}', name: 'commercant.edit')]
    public function editCommercant(
        Commercant $commercant = null,
        ManagerRegistry $doctrine,
        Request $request,
        CommercantRepository $commercants,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        int $id_commercant,
        NotificationRepository $notificationRepository,): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
        $titre = $this->translator->trans("Edit Forester");
        $commercant = $commercants->find($id_commercant);
        //dd($commercant);
        if(!$commercant){
            $new = true;
            $commercant = new Commercant();
            $titre = $this->translator->trans("Add Forester");
        }

            $new = false;
            if(!$commercant){
                $new = true;
                $commercant = new Commercant();
            }
            $form = $this->createForm(CommercantType::class, $commercant);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){



                $manager = $doctrine->getManager();
                $manager->persist($commercant);
                $manager->flush();

                $this->addFlash('success', $this->translator->trans("The forester has been updated successfuylly"));
                return $this->redirectToRoute("ref_commercants");
            } else {
                return $this->render('references/commercant/add-commercant.html.twig',[
                    'form' =>$form->createView(),
                    'titre'=>$titre,
                    'liste_commercants' => $commercants->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    'mes_notifs'=>$notificationRepository->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions
                ]);
            }
        } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
}
