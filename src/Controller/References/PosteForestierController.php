<?php

namespace App\Controller\References;


use App\Controller\Services\AdministrationService;
use App\Entity\References\PosteForestier;
use App\Form\References\PosteForestierType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\CantonnementRepository;
use App\Repository\References\PosteForestierRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PosteForestierController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator, private AdministrationService $administrationService)
    {
    }
    #[Route('snvlt/ref/poste_f', name: 'ref_poste_forestiers')]
    public function listing(PosteForestierRepository $poste_forestiers,
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

                return $this->render('references/poste_forestier/index.html.twig', [
                    'liste_poste_forestiers' => $poste_forestiers->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'mes_notifs'=>$notificationRepository->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }


    #[Route('/edit/ref/poste_f/{id_poste_forestier?0}', name: 'poste_forestier.edit')]
    public function editPosteForestier(
        ManagerRegistry $doctrine,
        Request $request,
        PosteForestierRepository $poste_forestiers,
        MenuPermissionRepository $permissions,
        UserRepository $userRepository,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_poste_forestier,
        NotificationRepository $notificationRepository): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $titre = $this->translator->trans("Edit forester checkpoint");
                $poste_forestier = $poste_forestiers->find($id_poste_forestier);

                $new = false;
                $action = "MODIFICATION";

                if(!$poste_forestier){
                    $new = true;
                    $action = "AJOUT";

                    $poste_forestier = new PosteForestier();
                }




            $form = $this->createForm(PosteForestierType::class, $poste_forestier);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){

                if(!$new){
                    $poste_forestier->setUpdatedAt(new \DateTimeImmutable());
                    $poste_forestier->setUpdatedBy($user);
                }
                $poste_forestier->setPersonneRessource(strtoupper($form->get('personneRessource')->getData()));
                $poste_forestier->setDenomination(strtoupper($form->get('denomination')->getData()));
                $poste_forestier->setSituationGeographique(strtoupper($form->get('situation_geographique')->getData()));

                $manager = $doctrine->getManager();
                $manager->persist($poste_forestier);
                $manager->flush();

                if ($new){
                    $this->addFlash('success', "Le poste forestier " . $poste_forestier->getDenomination() . " vient d'être créé.");
                    $decription = "Le poste forestier " . $poste_forestier->getDenomination()  . " a été ajouté par ". $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur();
                } else {
                    $this->addFlash('success', "L'OI' " . $poste_forestier->getDenomination() . " a été mis à jour");
                    $decription = "Le poste forestier " . $poste_forestier->getDenomination()  . " a été modifié par ". $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur();
                }

                // Ajout dans le log SNVLT
                $this->administrationService->save_action(
                    $user,
                    "Poste Forestier",
                    $action,
                    new \DateTimeImmutable(),
                    $decription
                );

                return $this->redirectToRoute("ref_poste_forestiers");
            } else {
                return $this->render('references/poste_forestier/add-poste_forestier.html.twig',[
                    'form' =>$form->createView(),
                    'titre'=>$titre,
                    'liste_poste_forestiers' => $poste_forestiers->findAll(),
                    'mes_notifs'=>$notificationRepository->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'new_value'=>$new
                ]);
            }

                } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    #[Route('snvlt/ref/pf/data_json', name: 'json_pf')]
    public function json_pf(PosteForestierRepository $pfs,
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

                $liste_pfs= $pfs->findBy([], ['denomination'=>'ASC']);
                foreach ( $liste_pfs as $pf){

                    $response[] =  array(
                        'id'=>$pf->getId(),
                        'sigle'=>$pf->getDenomination(),
                        'rs'=>$pf->getDenomination(),
                    );

                }


                return  new  JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
