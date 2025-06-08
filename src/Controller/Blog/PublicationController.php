<?php

namespace App\Controller\Blog;

use App\Entity\Blog\CategoryPublication;
use App\Entity\Blog\FichierPublication;
use App\Entity\Blog\Publication;
use App\Entity\User;
use App\Form\Blog\PublicationType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\Blog\PublicationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\DirectionRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class PublicationController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator, private SluggerInterface $slugger)
    {
    }

    #[Route('snvlt/ref/il/', name: 'ref_infos_legales')]
    public function index(  PublicationRepository $publications,
                            MenuRepository $menus,
                            MenuPermissionRepository $permissions,
                            GroupeRepository $groupeRepository,
                            ManagerRegistry $doctrine,
                            Request $request,
                            UserRepository $userRepository,
                            User $user = null,
                            NotificationRepository $notification): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EDITEUR') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                return $this->render('blog/publication/index.html.twig',
                    [

                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'groupe'=>$code_groupe,
                        'publications'=>$publications->findAll(),
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('/edit/ref/il/{id_publication?0}', name: 'publication_blog.edit')]
    public function publication_blog_edit(
        Publication $publication = null,
        ManagerRegistry $doctrine,
        Request $request,
        PublicationRepository $pubs,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_publication,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EDITEUR') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();



                $titre = "Modifier la publication";
                $pub = $pubs->find($id_publication);

                if(!$pub){
                    $new = true;
                    $pub = new Publication();
                    $titre = "Ajouter la publication";
                }

                $new = false;

                $form = $this->createForm(PublicationType::class, $pub);

                $form->handleRequest($request);

                if ( $form->isSubmitted() ){

                    /*$titre = $form->get('libellePublication')->getData();
                    $pub->setLibellePublication(strtoupper($titre));*/

                    $manager = $doctrine->getManager();
                    $manager->persist($pub);

                    $fichiers = $form->get('fichiers')->getData();
                    //dd($fichiers);
                    foreach ($fichiers as $fichier){

                       /* $newFilename = $this->slugger->slug($fichier) . $fichier->guessExtension()/* .  md5(uniqid(). '.'.  $fichier->guessExtension()) */


                        $originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                        // this is needed to safely include the file name as part of the URL
                        $safeFilename = $this->slugger->slug($originalFilename);
                        $newFilename = $safeFilename.'-'.uniqid().'.'.$fichier->guessExtension();

                        // Move the file to the directory where brochures are stored

                             $fichier->move(
                                $this->getParameter('publication_directory'),
                                $newFilename
                            );

                            // updates the 'brochureFilename' property to store the PDF file name
                            // instead of its contents
                            $fichierPublication = new FichierPublication();
                            $fichierPublication->setLibelle($originalFilename);
                            $fichierPublication->setFichier($newFilename);
                            $fichierPublication->setCodePublication($pub);
                            $doctrine->getManager()->persist($fichierPublication);

                    }





                    $manager->flush();

                    $this->addFlash('success',$this->translator->trans("Publication has been edited and updated successfully"));
                    return $this->redirectToRoute("publication_blog.edit", ['id_publication'=>$id_publication]);
                } else {
                    return $this->render('blog/publication/add-publication.html.twig',[
                        'form' =>$form->createView(),
                        'titre'=>$titre,
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'groupe'=>$code_groupe,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'publication'=>$pub,
                        'liste_parent'=>$permissions,
                        'liste_fichiers'=>$doctrine->getRepository(FichierPublication::class)->findBy(['code_publication'=>$pub])
                    ]);
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    #[Route('snvlt/blog/publication', name: 'app_blog_publication')]
    public function liste(ManagerRegistry $registry): Response
    {
       $liste_publication = array();
       $pubs = $registry->getRepository(Publication::class)->findAll();
       foreach ($pubs as $pub){
           $liste_publication[] = array(
               'id_pub'=>$pub->getId(),
               'libelle'=>$pub->getLibellePublication(),
               'fichier'=>$pub->getFichier()
           );
           return new JsonResponse(json_encode($liste_publication));
       }
    }

    #[Route('snvlt/blog/publication/filter/{id_categorie?0}', name: 'pub_by_categorie')]
    public function pub_by_categorie(ManagerRegistry $registry, int $id_categorie): Response
    {
        $liste_publication = array();
        $categorie = $registry->getRepository(CategoryPublication::class)->find($id_categorie);

        if ($categorie){
            $pubs = $registry->getRepository(Publication::class)->findBy(['code_category'=>$categorie]);

            foreach ($pubs as $pub){

                $fichier = "";
                if ($pub->getFichierPublications()->count() === 1){
                    $fichier = $pub->getFichierPublications()->get(0)->getFichier();
                }

                $liste_publication[] = array(
                    'id_pub'=>$pub->getId(),
                    'libelle'=>$pub->getLibellePublication(),
                    'fichier'=>$fichier,
                    'actif'=>$pub->isActif(),
                    'nb_fichier'=>$pub->getFichierPublications()->count()
                );
        }

            return new JsonResponse(json_encode($liste_publication));
        }
    }
    #[Route('snvlt/pub/del/{id_fichier?0}', name: 'remove_fichier_pub')]
    public function remove_fichier_pub(ManagerRegistry $registry, int $id_fichier): Response
    {
        $reponse = array();
        $fichier_publication = $registry->getRepository(FichierPublication::class)->find($id_fichier);

        //dd($fichier_publication);
        if ($fichier_publication){
            $registry->getManager()->remove($fichier_publication);
            unlink($this->getParameter('publication_directory')."/".$fichier_publication->getFichier());
            $registry->getManager()->flush();

            $reponse[] = array(
                'CODE'=>'SUCCESS'
            );


        } else {
            $reponse[] = array(
                'CODE'=>'FAILED'
            );

        }
        return new JsonResponse(json_encode($reponse));
    }

    #[Route('snvlt/pub/del/all/{id_publication?0}', name: 'remove_fichiers_all')]
    public function remove_fichiers_all(ManagerRegistry $registry, int $id_publication): Response
    {
        $reponse = array();

        $publication = $registry->getRepository(Publication::class)->find($id_publication);

        if ($publication){
            $fichiers_publication = $registry->getRepository(FichierPublication::class)->findBy(['code_publication'=>$publication]);

            foreach ($fichiers_publication as $fichier){
                //dd($this->getParameter('publication_directory').$fichier->getFichier());
                $registry->getManager()->remove($fichier);
                unlink($this->getParameter('publication_directory')."/".$fichier->getFichier());
            }
            $registry->getManager()->flush();

            $reponse[] = array(
                'CODE'=>'SUCCESS'
            );

        }

        return new JsonResponse(json_encode($reponse));
    }
}
