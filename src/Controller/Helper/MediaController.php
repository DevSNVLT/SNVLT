<?php

namespace App\Controller\Helper;

use App\Entity\Helper\Media;
use App\Entity\References\Direction;
use App\Entity\User;
use App\Form\Helper\MadiatechType;
use App\Form\References\DirectionType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\Helper\MediaRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\DirectionRepository;
use App\Repository\References\ServiceMinefRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MediaController extends AbstractController
{
    public function __construct(
        private SluggerInterface $slugger
    )
    {
    }

    #[Route('/snvlt/mediatech', name: 'app_helper_media')]
    public function index(
          MediaRepository $medias = null,
          Request $request,
          MenuPermissionRepository $permissions,
          MenuRepository $menus,
          UserRepository $userRepository,
          User $user = null,
          NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN') or  $this->isGranted('ROLE_EDITEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                return $this->render('helper/media/index.html.twig', [
                    'liste_medias' => $medias->findAll(),
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
    #[Route('/snvlt/mediatech/show/{id_media}', name: 'show_media')]
    public function show_media(
        MediaRepository $medias = null,
        Request $request,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        UserRepository $userRepository,
        int     $id_media,
        User $user = null,
        Media $media = null,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF')
                or  $this->isGranted('ROLE_ADMIN')
                or  $this->isGranted('ROLE_EDITEUR')
                or  $this->isGranted('ROLE_ADMINISTRATIF')
                or  $this->isGranted('ROLE_OI')
                or  $this->isGranted('ROLE_EXPLOITANT')
                or  $this->isGranted('ROLE_INDUSTRIEL')
                or  $this->isGranted('ROLE_COMMERCANT')
                or  $this->isGranted('ROLE_EXPORTATEUR')
            )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $media = $medias->find($id_media);

                return $this->render('helper/media/media.html.twig', [
                    'media' => $media,
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

    #[Route('/snvlt/mediatech/op/', name: 'operator_media')]
    public function operator_media(
        MediaRepository $medias = null,
        Request $request,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        UserRepository $userRepository,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            $user = $userRepository->find($this->getUser());
            $code_groupe = $user->getCodeGroupe()->getId();
            $code_operateur = $user->getCodeOperateur();



                    $medias = $medias->findAll();


                return $this->render('helper/media/operator_media.html.twig', [
                    'mes_media' => $medias,
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                    'liste_parent'=>$permissions
                ]);

        }
    }

    #[Route('/snvlt/mediatech/edit/{id_media?0}', name: 'mediatech.edit')]
    public function editDirection(
        MediaRepository $medias = null,
        ManagerRegistry $doctrine,
        Request $request,
        DirectionRepository $directions,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        ServiceMinefRepository $serviceMinefRepository,
        GroupeRepository $groupeRepository,
        int $id_media,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN') or  $this->isGranted('ROLE_EDITEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();



                $titre = "Modifier le média";
                $media = $medias->find($id_media);

                $new = false;

                if(!$media){
                    $new = true;
                    $media = new Media();
                    $titre = "Ajouter un média";
                    $media->setCreatedAt(new \DateTime());
                    $media->setCreatedBy($user);
                }

                $form = $this->createForm(MadiatechType::class, $media);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){

                    $fichier = $form->get('fichier')->getData();

                    if ($fichier) {$originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                        // this is needed to safely include the file name as part of the URL
                        $safeFilename = $this->slugger->slug($originalFilename);
                        $newFilename = uniqid().'.'.$fichier->guessExtension();

                        // Move the file to the directory where brochures are stored
                        try {
                            $fichier->move(
                                $this->getParameter('mediatech_directory'),
                                $newFilename
                            );
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }

                        // updates the 'brochureFilename' property to store the PDF file name
                        // instead of its contents
                        $media->setFichier($newFilename);
                        $media->setLibelle($originalFilename);

                        if (!$new){
                            $media->setUpdatedAt(new \DateTime());
                            $media->setUpdatedBy($user);
                        }


                    }

                    $manager = $doctrine->getManager();
                    $manager->persist($media);
                    $manager->flush();

                    $this->addFlash('success',"Le média a été mis à jour avec succès");
                    return $this->redirectToRoute("app_helper_media");
                } else {
                    return $this->render('helper/media/add-media.html.twig',[
                        'form' =>$form->createView(),
                        'titre'=>$titre,
                        'ref_directions' => $directions->findAll(),
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'groupe'=>$code_groupe,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'liste_parent'=>$permissions
                    ]);
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
}
