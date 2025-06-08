<?php

namespace App\Controller\References;


use App\Controller\Services\AdministrationService;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\User;
use App\Form\References\TypeDocumentStatistiqueType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\Administration\StockDocRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\TypeDocumentStatistiqueRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class TypeDocumentStatistiqueController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator, private AdministrationService $adminservice)
    {
    }

    #[Route('snvlt/ref/tds', name: 'ref_type_document_statistiques')]
    public function listing(TypeDocumentStatistiqueRepository $type_document_statistiques,
                            MenuRepository                    $menus,
                            MenuPermissionRepository          $permissions,
                            StockDocRepository                $stockDocRepository,
                            GroupeRepository                  $groupeRepository,
                            Request                           $request,
                            UserRepository                    $userRepository,
                            User                              $user = null,
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

                return $this->render('references/type_document_statistique/index.html.twig', [
                    'liste_type_document_statistiques' => $type_document_statistiques->findAll(),
                    'stock_solde' => $stockDocRepository,
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


    #[Route('snvlt/ref/edit/tds/{id_type_document_statistique?0}', name: 'type_document_statistique.edit')]
    public function editTypeDocumentStatistique(
        TypeDocumentStatistique           $type_document_statistique = null,
        ManagerRegistry                   $doctrine,
        Request                           $request,
        TypeDocumentStatistiqueRepository $type_document_statistiques,
        MenuPermissionRepository          $permissions,
        MenuRepository                    $menus,
        GroupeRepository                  $groupeRepository,
        int                               $id_type_document_statistique,
        UserRepository                    $userRepository,
        User                              $user = null,
        NotificationRepository            $notification): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $date_creation = new \DateTimeImmutable();
                $titre = $this->translator->trans("Edit statistical document type");
                $type_document_statistique = $type_document_statistiques->find($id_type_document_statistique);
                //dd($type_document_statistique);
                if(!$type_document_statistique){
                    $new = true;
                    $type_document_statistique = new TypeDocumentStatistique();
                    $titre = $this->translator->trans("Add statistical document type");

                    $type_document_statistique->setCreatedAt($date_creation);
                    $type_document_statistique->setCreatedBy($this->getUser());
        }

        $session = $request->getSession();
        if (!$session->has("user_session")){
            $this->addFlash('error', $this->translator->trans('You must log in first to access SNVLT'));
            return $this->redirectToRoute('app_login');
        } else {
            $action = "MODIFICATION";

            $new = false;
            if(!$type_document_statistique){
                $new = true;
                $action = "AJOUT";
            }
            $form = $this->createForm(TypeDocumentStatistiqueType::class, $type_document_statistique);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){


                $type_document_statistique->setUpdatedAt($date_creation);
                $type_document_statistique->setUpdatedBy($this->getUser());

                $manager = $doctrine->getManager();
                $manager->persist($type_document_statistique);
                $decription = "";
                if($new){
                    $decription = "Ajout du type Document statistique ". $type_document_statistique->getAbv() . " par ". $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur();
                } else {
                    $decription = "Modification du type Document statistique ". $type_document_statistique->getAbv() . " par ". $user->getPrenomsUtilisateur(). " ". $user->getNomUtilisateur();
                }
                $this->adminservice->save_action(
                    $user,
                    "TYPE_DOCUMENT_STATISTIQUE",
                    $action,
                    $date_creation,
                    $decription
                );

                $manager->flush();

                $this->addFlash('success',$this->translator->trans("Statistical document type has been updated successfully"));
                return $this->redirectToRoute("ref_type_document_statistiques");
            } else {
                return $this->render('references/type_document_statistique/add-type_document_statistique.html.twig',[
                    'form' =>$form->createView(),
                    'titre'=>$titre,
                    'liste_type_document_statistiques' => $type_document_statistiques->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0)
                ]);
            }
        }
    }
    }
    }


}
