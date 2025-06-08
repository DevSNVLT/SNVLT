<?php

namespace App\Controller\References;


use App\Controller\Services\synchro\ForetSnvlt1;
use App\Entity\References\Foret;
use App\Entity\User;
use App\Form\References\ForetType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\ForetRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ForetController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator, private ForetSnvlt1 $snvlt1)
    {
    }

    #[Route('snvlt/ref/forets/{id_foret?0}', name: 'ref_forets')]
    public function listing(ForetRepository $forets,
    MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        Foret $foret = null,
        ManagerRegistry $doctrine,
        int $id_foret,
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification
        ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

            $titre = $this->translator->trans("Edit Forest");
            $foret = $forets->find($id_foret);
            //dd($foret);
            if(!$foret){
                $new = true;
                $foret = new Foret();
            }
            $form = $this->createForm(ForetType::class, $foret);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){

                $foret->setAttribue(false);
                $manager = $doctrine->getManager();
                $manager->persist($foret);
                $manager->flush();


                $this->addFlash('success',$this->translator->trans("Forest has been edited successfully"));
                return $this->redirectToRoute("ref_forets");
            } else {
                return $this->render('references/foret/index.html.twig', [
                    'liste_forets' => $forets->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'form' =>$form->createView(),
                    'titre'=>$titre,
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                ]);
            }
        }else {
            return $this->redirectToRoute('app_no_permission_user_active');
        }
      }


    }


    #[Route('snvlt/ref/edit/forets/{id_foret?0}', name: 'foret.edit')]
    public function editForet(
        Foret $foret = null,
        ManagerRegistry $doctrine,
        Request $request,
        ForetRepository $forets,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_foret,
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
        $titre = $this->translator->trans("Edit Forest");
        $foret = $forets->find($id_foret);
        $new = false;
        if(!$foret){
            $new = true;
            $foret = new Foret();
            $titre =$this->translator->trans("Add Forest");
        }


            $form = $this->createForm(ForetType::class, $foret);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){

                $foret->setAttribue(false);
                $foret->setCreatedAt(new \DateTimeImmutable());
                $foret->setCreatedBy($user);

                $manager = $doctrine->getManager();
                $manager->persist($foret);
                $manager->flush();

                $this->addFlash('success',$this->translator->trans("Forest has been edited successfully"));
                if ($new){
                    $this->addFlash('success', "La forêt " . $foret->getDenomination() . " vient d'être créé.");
                } else {
                    $this->addFlash('success', "La forêt " . $foret->getDenomination() . " a été mise à jour");
                }
                $this->snvlt1->Maj_Foret($foret, $new);
                return $this->redirectToRoute("ref_forets");
            } else {
                return $this->render('references/foret/add-foret.html.twig',[
                    'form' =>$form->createView(),
                    'titre'=>$titre,
                    'liste_forets' => $forets->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'new_item'=>$new
                ]);
            }
            }else {
            return $this->redirectToRoute('app_no_permission_user_active');
        }
      }

    }

}
