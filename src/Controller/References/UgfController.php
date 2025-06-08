<?php

namespace App\Controller\References;


use App\Entity\References\Ugf;
use App\Entity\User;
use App\Form\References\UgfType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\UgfRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class UgfController extends AbstractController
{

    public function __construct(private TranslatorInterface $translator)
    {

    }

    #[Route('/snvlt/ref/ugf', name: 'ref_ugfs')]
    public function listing(
        UgfRepository $ugfs,
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
                $titre = $this->translator->trans("Edit Cantonment");

                return $this->render('references/ugf/index.html.twig', [
                    'liste_ugfs' => $ugfs->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'titre'=>$titre,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'liste_parent'=>$permissions
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }





    #[Route('/snvlt/edit/ugf/{id_ugf?0}', name: 'ugf.edit')]
    public function editUgf(
        Ugf $ugf = null,
        ManagerRegistry $doctrine,
        Request $request,
        UgfRepository $ugfs,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_ugf,
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

                $date_creation = new \DateTimeImmutable();
                $code_groupe = $groupeRepository->find(1);
                $titre =  'Ajouter UGF';
                $ugf = $ugfs->find($id_ugf);
                //dd($ugf);
                if(!$ugf){
                    $new = true;
                    $ugf = new Ugf();
                    $titre =  'Ajouter UGF';

                    $ugf->setCreatedAt($date_creation);
                    $ugf->setCreatedBy($this->getUser());
                }

                $session = $request->getSession();
                if (!$session->has("user_session")){
                    $this->addFlash('error', $this->translator->trans('You must log in first to access SNVLT'));
                    return $this->redirectToRoute('app_login');
                } else {

                    $new = false;
                    if(!$ugf){
                        $new = true;
                        $ugf = new Ugf();
                    }
                    $form = $this->createForm(UgfType::class, $ugf);

                    $form->handleRequest($request);

                    if ( $form->isSubmitted() && $form->isValid() ){


                        $ugf->setUpdatedAt($date_creation);
                        $ugf->setUpdatedBy($this->getUser());

                        $manager = $doctrine->getManager();
                        $manager->persist($ugf);
                        $manager->flush();

                        $this->addFlash('success',"L'UGF a été mis à  jour avec succès");
                        return $this->redirectToRoute("ref_ugfs");
                    } else {
                        return $this->render('references/ugf/add-ugf.html.twig',[
                            'form' =>$form->createView(),
                            'titre'=>$titre,
                            'liste_ugfs' => $ugfs->findAll(),
                            'liste_menus'=>$menus->findOnlyParent(),
                            "all_menus"=>$menus->findAll(),
                            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                            'groupe'=>$code_groupe,
                            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                            'liste_parent'=>$permissions
                        ]);
                    }
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
}
