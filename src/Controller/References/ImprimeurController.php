<?php

namespace App\Controller\References;

use App\Entity\References\Imprimeur;
use App\Entity\User;
use App\Form\References\ImprimeurType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\ImprimeurRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImprimeurController extends AbstractController
{

    public function __construct(private TranslatorInterface $translator, private  SluggerInterface $slugger)
    {

    }

    #[Route('snvlt/ref/imp', name: 'ref_imprimeurs')]
    public function listing(ImprimeurRepository $imprimeurs,
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
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF') )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                return $this->render('references/imprimeur/index.html.twig',
                    [

                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'groupe'=>$code_groupe,
                        'imprimeurs'=>$imprimeurs->findAll(),
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('/edit/imprimeur/{id_imprimeur?0}', name: 'imprimeur.edit')]
    public function editImprimeur(
        Imprimeur $imprimeur = null,
        ManagerRegistry $doctrine,
        Request $request,
        ImprimeurRepository $imprimeurs,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_imprimeur,
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



                $titre = $this->translator->trans("Edit PRINTER OFFICE");
                $imprimeur = $imprimeurs->find($id_imprimeur);

                if(!$imprimeur){
                    $new = true;
                    $imprimeur = new Imprimeur();
                    $titre = $this->translator->trans("Add PRINTER OFFICE");
                }

                $new = false;
                if(!$imprimeur){
                    $new = true;
                    $imprimeur = new Imprimeur();

                }
                $form = $this->createForm(ImprimeurType::class, $imprimeur);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){

                    $photo = $form->get('logo')->getData();

                    if ($photo) {$originalFilename = pathinfo($photo->getClientOriginalName(), PATHINFO_FILENAME);
                        // this is needed to safely include the file name as part of the URL
                        $safeFilename = $this->slugger->slug($originalFilename);
                        $newFilename = $safeFilename.'-'.uniqid().'.'.$photo->guessExtension();

                        // Move the file to the directory where brochures are stored
                        try {
                            $photo->move(
                                $this->getParameter('printers_directory'),
                                $newFilename
                            );
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }

                        // updates the 'brochureFilename' property to store the PDF file name
                        // instead of its contents
                        $imprimeur->setLogo($newFilename);
                    }

                    $manager = $doctrine->getManager();
                    $manager->persist($imprimeur);
                    $manager->flush();

                    $this->addFlash('success',$this->translator->trans("PRINTER OFFICE has been edited and updated successfully"));
                    return $this->redirectToRoute("ref_imprimeurs");
                } else {
                    return $this->render('references/imprimeur/add-imprimeur.html.twig',[
                        'form' =>$form->createView(),
                        'infos_imprimeur'=>$imprimeur,
                        'titre'=>$titre,
                        'ref_imprimeurs' => $imprimeurs->findAll(),
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'groupe'=>$code_groupe,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'imprimeurs'=>$imprimeurs->findAll(),
                        'liste_parent'=>$permissions
                    ]);
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
    #[Route('snvlt/imprimeurs/list', name: 'liste_imprimeurs')]
    public function imprimeur_json(Request $request, ImprimeurRepository $imprimeurRepository):Response{
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {

                $liste_imprimeurs = $imprimeurRepository->findAll();

                $response = array();
                foreach ($liste_imprimeurs as $imprimeur) {
                    $response[] = array(
                        'id' => $imprimeur->getId(),
                        'denomination' => $imprimeur->getRaisonSocialeImprimeur()
                    );
                }

                return new JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
