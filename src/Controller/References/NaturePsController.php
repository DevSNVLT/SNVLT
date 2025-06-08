<?php

namespace App\Controller\References;

use App\Controller\Services\AdministrationService;
use App\Entity\References\NaturePs;
use App\Entity\User;
use App\Form\References\NaturePsType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\Administration\StockDocRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\NaturePsRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class NaturePsController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator, private AdministrationService $adminservice)
    {
    }
    
    #[Route('/nature/ps', name: 'app_nature_ps')]
    public function index(NaturePsRepository $naturePsRepository,
                          MenuRepository                    $menus,
                          MenuPermissionRepository          $permissions,
                          Request                           $request,
                          UserRepository                    $userRepository,
                          NotificationRepository            $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $titre = $this->translator->trans("Edit statistical document type");

                return $this->render('references/nature_ps/index.html.twig', [
                    'liste_nature_ps' => $naturePsRepository->findAll(),
                    'liste_menus' => $menus->findOnlyParent(),
                    "all_menus" => $menus->findAll(),
                    'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
                    'groupe' => $code_groupe,
                    'titre' => $titre,
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0)
                ]);
            }
        }

    }

    #[Route('snvlt/ref/edit/nps/{id_nature_ps?0}', name: 'nature_ps.edit')]
    public function editNaturePs(
        NaturePs  $naturePs = null,
        ManagerRegistry                   $doctrine,
        Request                           $request,
        NaturePsRepository $naturePsRepository,
        MenuPermissionRepository          $permissions,
        MenuRepository                    $menus,
        GroupeRepository                  $groupeRepository,
        int                               $id_nature_ps,
        UserRepository                    $userRepository,
        NotificationRepository            $notification): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $date_creation = new \DateTimeImmutable();
                $titre = $this->translator->trans("Edit Secondary Product Nature");
                $tnature_ps = $naturePsRepository->find($id_nature_ps);

                $new = false;
                $action = "MODIFICATION";

                if(!$tnature_ps){
                    $new = true;
                    $action = "AJOUT";
                    $tnature_ps = new NaturePs();
                    $titre = $this->translator->trans("Add Secondary Product Nature");

                    $tnature_ps->setCreatedAt($date_creation);
                    $tnature_ps->setCreatedBy($this->getUser());
                }

                    $form = $this->createForm(NaturePsType::class, $tnature_ps);

                    $form->handleRequest($request);

                    if ( $form->isSubmitted() && $form->isValid() ){

                        $tnature_ps->setLibelle(strtoupper($form->get('libelle')->getData()));

                        if (!$new ){
                        $tnature_ps->setUpdatedAt($date_creation);
                        $tnature_ps->setUpdatedBy($this->getUser());
                        }

                        $manager = $doctrine->getManager();
                        $manager->persist($tnature_ps);
                        $manager->flush();

                        //dd($tnature_ps->getLibelle());
                        if($new){
                            $decription = "Ajout du type de Produits Secondaire ". $tnature_ps->getLibelle() . " par ". $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur();
                            $this->addFlash('success', "L'élément Nature PS " . $tnature_ps->getLibelle() . " vient d'être créé.");
                        } else {
                            $decription = "Modification du type de Produits Secondaire". $tnature_ps->getLibelle() . " par ". $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur();
                            $this->addFlash('success', "L'élément Nature PS " . $tnature_ps->getLibelle() . " a été mis à jour");
                        }
                        $this->adminservice->save_action(
                            $user,
                            "NATURE PS",
                            $action,
                            $date_creation,
                            $decription
                        );




                        return $this->redirectToRoute("app_nature_ps");
                    } else {
                        return $this->render('references/nature_ps/add-nature_ps.html.twig',[
                            'form' =>$form->createView(),
                            'titre'=>$titre,
                            'liste_nature_ps' => $naturePsRepository->findAll(),
                            'liste_menus'=>$menus->findOnlyParent(),
                            "all_menus"=>$menus->findAll(),
                            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                            'groupe'=>$code_groupe,
                            'liste_parent'=>$permissions,
                            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0)
                        ]);
                    }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
    
}
