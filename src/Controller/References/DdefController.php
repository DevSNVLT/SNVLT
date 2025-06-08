<?php

namespace App\Controller\References;


use App\Entity\References\Ddef;
use App\Entity\User;
use App\Form\References\DdefType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\DdefRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DdefController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator)
    {

    }

    #[Route('snvlt/ref/ddef/{id_ddef?0}', name: 'ref_ddefs')]
    public function listing(
            DdefRepository $ddefs,
            Request $request,
            MenuRepository $menus,
            MenuPermissionRepository $permissions,
            GroupeRepository $groupeRepository,
            UserRepository $userRepository,
            User $user = null,
            NotificationRepository $notification
        ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

            $titre = $this->translator->trans("Edit Departmental Direction");

                return $this->render('references/ddef/index.html.twig', [
                    'liste_ddefs' => $ddefs->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'titre'=>$titre,
                    'liste_parent'=>$permissions
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }


    #[Route('snvlt/ref/edit/ddef/{id_ddef?0}', name: 'ddef.edit')]
    public function edit_Ddef(
        Ddef $ddef = null,
        ManagerRegistry $doctrine,
        Request $request,
        DdefRepository $ddefs,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_ddef,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $date_creation = new \DateTimeImmutable();

                $titre = $this->translator->trans("Edit Departmental Direction");
                $ddef = $ddefs->find($id_ddef);
                //dd($ddef);
                if(!$ddef){
                    $new = true;
                    $ddef = new Ddef();
                    $titre = $this->translator->trans("Add Departmental Direction");

                    $ddef->setCreatedAt($date_creation);
                    $ddef->setCreatedBy($this->getUser());
                }

                $session = $request->getSession();

                    $form = $this->createForm(DdefType::class, $ddef);

                    $form->handleRequest($request);

                    if ( $form->isSubmitted() && $form->isValid() ){


                        $ddef->setCreatedAt($date_creation);
                        $ddef->setCreatedBy($this->getUser());

                        $manager = $doctrine->getManager();
                        $manager->persist($ddef);
                        $manager->flush();

                        $this->addFlash('success',$this->translator->trans("THe Departmental Direction has been updated successfully"));
                        return $this->redirectToRoute("ref_ddefs");
                    } else {
                        return $this->render('references/ddef/add-ddef.html.twig',[
                            'form' =>$form->createView(),
                            'titre'=>$titre,
                            'liste_ddefs' => $ddefs->findAll(),
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

    #[Route('snvlt/ddef/search-all/data_json', name: 'json_ddef_all')]
    public function json_ddef_all(DdefRepository $Ddefs,
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

                $liste_ddefs= $Ddefs->findBy([], ['nom_ddef'=>'ASC']);

                foreach ( $liste_ddefs as $ddef){

                    $response[] =  array(
                        'id'=>$ddef->getId(),
                        'sigle'=>$ddef->getNomDdef(),
                        'rs'=>$ddef->getNomDdef(),
                    );
                }

                //dd($response);
                return  new  JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
