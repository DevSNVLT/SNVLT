<?php

namespace App\Controller\Administration;


use App\Entity\Administration\Gadget;
use App\Entity\User;
use App\Form\Administration\GadgetType;
use App\Repository\Administration\GadgetRepository;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class GadgetController extends AbstractController
{
public function __construct(private TranslatorInterface $translator)
{
}

    #[Route('snvlt/ref/gadget', name: 'ref_gadgets')]
    public function listing(GadgetRepository $gadgets,
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
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                return $this->render('administration/gadget/index.html.twig',
                    [

                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'groupe'=>$code_groupe,
                        'gadgets'=>$gadgets->findAll(),
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('/edit/gadget/{id_gadget?0}', name: 'gadget.edit')]
    public function editGadget(
        Gadget $gadget = null,
        ManagerRegistry $doctrine,
        Request $request,
        GadgetRepository $gadgets,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_gadget,
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




                $gadget = $gadgets->find($id_gadget);

                if(!$gadget){
                    $new = true;
                    $gadget = new Gadget();

                }

                $new = false;
                if(!$gadget){
                    $new = true;
                    $gadget = new Gadget();

                }
                $form = $this->createForm(GadgetType::class, $gadget);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){


                    $manager = $doctrine->getManager();
                    $manager->persist($gadget);
                    $manager->flush();

                    $this->addFlash('success',$this->translator->trans("Gadget has been edited and updated successfully"));
                    return $this->redirectToRoute("ref_gadgets");
                } else {
                    return $this->render('administration/gadget/add-gadget.html.twig',[
                        'form' =>$form->createView(),
                        'ref_gadgets' => $gadgets->findAll(),
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'groupe'=>$code_groupe,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'gadgets'=>$gadgets->findAll(),
                        'liste_parent'=>$permissions
                    ]);
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
    
}
