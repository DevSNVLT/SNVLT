<?php

namespace App\Controller\References;

use App\Controller\Services\AdministrationService;
use App\Entity\References\SousPrefecture;
use App\Entity\User;
use App\Form\References\SousPrefectureType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\SousPrefectureRepository;
use App\Repository\References\ServiceMinefRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SousPrefectureController extends AbstractController
{
    public function __construct(private AdministrationService $administrationService)
    {
    }

    #[Route('/references/sous-prefecture', name: 'app_references_sous_prefecture')]
    public function index(
        SousPrefectureRepository $sousprefectures,
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
                return $this->render('references/sous_prefecture/index.html.twig',
                    [
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'groupe'=>$code_groupe,
                        'sousprefectures'=>$sousprefectures->findAll(),
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('/edit/sousprefecture/{id_sousprefecture?0}', name: 'sousprefecture.edit')]
    public function editSousPrefecture(
        ManagerRegistry $doctrine,
        Request $request,
        SousPrefectureRepository $sousprefectures,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        int $id_sousprefecture,
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

                $titre = "Modifier la sous-préfecture";
                $sousprefecture = $sousprefectures->find($id_sousprefecture);
                $new = false;
                $action = "MODIFICATION";

                if(!$sousprefecture){
                    $new = true;
                    $action = "AJOUT";
                    $sousprefecture = new SousPrefecture();
                    $titre = "Ajouter une Sous-préfecture";
                }

                $form = $this->createForm(SousPrefectureType::class, $sousprefecture);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){


                    $sousprefecture->setNomSousprefecture(strtoupper($form->get('nom_sousprefecture')->getData()));
                    $manager = $doctrine->getManager();
                    $manager->persist($sousprefecture);
                    $manager->flush();

                    if ($new){
                        $this->addFlash('success', "La sous-préfecture " . $sousprefecture->getNomSousprefecture() . " vient d'être créé.");
                        $decription = "La sous-préfecture " . $sousprefecture->getNomSousprefecture()  . " a été ajoutée par ". $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur();
                    } else {
                        $this->addFlash('success', "La sous-préfecture " . $sousprefecture->getNomSousprefecture()   . " a été mise à jour");
                        $decription = "La sous-préfecture " . $sousprefecture->getNomSousprefecture() . " a été modifiée par ". $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur();
                    }

                    // Ajout dans le log SNVLT
                    $this->administrationService->save_action(
                        $user,
                        "SERVICE MINEF",
                        $action,
                        new \DateTimeImmutable(),
                        $decription
                    );

                    return $this->redirectToRoute("app_references_sous_prefecture");
                } else {
                    return $this->render('references/sous_prefecture/add-sousprefecture.html.twig',[
                        'form' =>$form->createView(),
                        'titre'=>$titre,
                        'sousprefectures' => $sousprefectures->findAll(),
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'groupe'=>$code_groupe,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'liste_parent'=>$permissions,
                        'item_new'=>$new
                    ]);
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
    #[Route('snvlt/sous-prefectures/list', name: 'liste_sousprefectures')]
    public function sousprefecture_json(Request $request, SousPrefectureRepository $sousprefectureRepository):Response{
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {

                $liste_sousprefectures = $sousprefectureRepository->findWithResponsable();

                $response = array();
                foreach ($liste_sousprefectures as $sousprefecture) {
                    $response[] = array(
                        'id' => $sousprefecture->getId(),
                        'nom_sousprefecture' => $sousprefecture->getNomSousPrefecture()
                    );
                }

                return new JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
