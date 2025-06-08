<?php

namespace App\Controller\References;


use App\Entity\References\Dcg;
use App\Entity\User;
use App\Form\References\DcgType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\UgfRepository;
use App\Repository\References\DcgRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class DcgController extends AbstractController
{

    public function __construct(private TranslatorInterface $translator)
    {

    }

    #[Route('/dcg', name: 'ref_dcg')]
    public function index(DcgRepository $dcgs): Response
    {
        return $this->render('dcg/liste.html.twig', [
            'ref_dcgs' => $dcgs->findAll(),
        ]);
    }

    #[Route('snvlt/ref/dcg/{id_dcg?0}', name: 'ref_dcgs')]
    public function listing(DcgRepository $dcgs,
                            MenuRepository $menus,
                            MenuPermissionRepository $permissions,
                            GroupeRepository $groupeRepository,
                            Dcg $dcg = null,
                            ManagerRegistry $doctrine,
                            int $id_dcg,
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

                $titre = $this->translator->trans("Modifier le Centre de Gestion");
                $dcg = $dcgs->find($id_dcg);
                //dd($dcg);
                if(!$dcg){
                    $new = true;
                    $dcg = new Dcg();
                }
                $form = $this->createForm(DcgType::class, $dcg);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){


                    $manager = $doctrine->getManager();
                    $manager->persist($dcg);
                    $manager->flush();

                    $this->addFlash('success',$this->translator->trans("Le Centre de Gestion vient dêtre modifié avec succès"));
                    return $this->redirectToRoute("ref_dcg");
                } else {
                    return $this->render('references/dcg/index.html.twig', [
                        'ref_dcgs' => $dcgs->findAll(),
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'liste_dcgs' => $dcgs->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'groupe'=>$code_groupe,
                        'form' =>$form->createView(),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'titre'=>$titre,
                        'liste_parent'=>$permissions
                    ]);
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }


    #[Route('/edit/dcg/{id_dcg?0}', name: 'dcg.edit')]
    public function editDcg(
        Dcg $dcg = null,
        ManagerRegistry $doctrine,
        Request $request,
        DcgRepository $dcgs,
        UgfRepository $ugfRepository,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_dcg,
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

                $titre = $this->translator->trans("Modifier le Centre de Gestion");
                $dcg = $dcgs->find($id_dcg);
                //dd($dcg);
                if(!$dcg){
                    $new = true;
                    $dcg = new Dcg();
                    $titre = $this->translator->trans("Ajouter le Centre de Gestion");
                }


                $new = false;
                if(!$dcg){
                    $new = true;
                    $dcg = new Dcg();
                }
                $form = $this->createForm(DcgType::class, $dcg);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){


                    $manager = $doctrine->getManager();
                    $manager->persist($dcg);
                    $manager->flush();

                    $this->addFlash('success', $this->translator->trans("Le Centre de Gestion vient dêtre modifié avec succès"));
                    return $this->redirectToRoute("ref_dcgs");
                } else {
                    return $this->render('references/dcg/add-dcg.html.twig',[
                        'form' =>$form->createView(),
                        'titre'=>$titre,
                        'ref_dcgs' => $dcgs->findAll(),
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'groupe'=>$code_groupe,
                        'liste_parent'=>$permissions,
                        'liste_ugf'=>$ugfRepository->findBy(['code_dcg'=>$dcg])
                    ]);
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

}
