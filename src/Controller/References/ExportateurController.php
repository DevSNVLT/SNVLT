<?php

namespace App\Controller\References;


use App\Entity\References\Exploitant;
use App\Entity\References\Exportateur;
use App\Entity\User;
use App\Form\References\ExportateurType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DemandeOperateurRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\ExploitantRepository;
use App\Repository\References\ExportateurRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExportateurController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    #[Route('snvlt/ref/exportmin1', name: 'ref_exportateurs')]
    public function listing(ExportateurRepository $exportateurs,
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
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                return $this->render('references/exportateur/index.html.twig',
                    [

                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'groupe'=>$code_groupe,
                        'exportateurs'=>$exportateurs->findAll(),
                        'liste_parent'=>$permissions
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('snvlt/ref/exportmin1/edit/exportateur/{id_exportateur?0}', name: 'exportateur.edit')]
    public function editExportateur(
        ManagerRegistry $doctrine,
        Request $request,
        ExportateurRepository $exportateurs,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        int $id_exportateur,
        UserRepository $userRepository,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $titre = $this->translator->trans("Edit Exporter");
                $exportateur = $exportateurs->find($id_exportateur);
                $new = false;

                if(!$exportateur){
                    $new = true;
                    $exportateur = new Exportateur();
                }

                $form = $this->createForm(ExportateurType::class, $exportateur);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){

                    $exportateur->setPersonneRessource(strtoupper($form->get('personne_ressource')->getData()));
                    $exportateur->setRaisonSocialeExportateur(strtoupper($form->get('raison_sociale_exportateur')->getData()));
                    $exportateur->setSigle(strtoupper($form->get('sigle')->getData()));
                    $exportateur->setCodeExportateur(strtoupper($form->get('code_exportateur')->getData()));
                    $exportateur->setAdresseExportateur(strtoupper($form->get('adresse_exportateur')->getData()));

                    $manager = $doctrine->getManager();
                    $manager->persist($exportateur);
                    $manager->flush();

                    if ($new){
                        $this->addFlash('success', "L'exportateur' " . $exportateur->getRaisonSocialeExportateur() . " vient d'être créé.");
                    } else {
                        $this->addFlash('success', "L'exportateur " . $exportateur->getRaisonSocialeExportateur() . " a été mis à jour");
                    }

                    return $this->redirectToRoute("ref_exportateurs");
                } else {
                    return $this->render('references/exportateur/add-exportateur.html.twig',[
                        'form' =>$form->createView(),
                        'titre'=>$titre,
                        'ref_exportateurs' => $exportateurs->findAll(),
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'groupe'=>$code_groupe,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                        'liste_parent'=>$permissions,
                        'new_item'=>$new
                    ]);
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
          }

    }
    #[Route('snvlt/ref/exportateurs/data_json', name: 'json_exportateurs')]
    public function exportateurs_json(ExportateurRepository $exportateurs,
                                     MenuRepository $menus,
                                     MenuPermissionRepository $permissions,
                                     GroupeRepository $groupeRepository,
                                     UserRepository $userRepository,
                                     ManagerRegistry $doctrine,
                                     Request $request,
                                     NotificationRepository $notificationRepository,
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
                $liste_exportateurs = $exportateurs->findBy([], ['raison_sociale_exportateur'=>'ASC']);
                foreach ( $liste_exportateurs as $exportateur){
                    $response[] =  array(
                        'id'=>$exportateur->getId(),
                        'sigle'=>$exportateur->getSigle(),
                        'rs'=>$exportateur->getRaisonSocialeExportateur()
                    );


                }
                return  new  JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/ref/exportateurs/search/{id_exportateur}/data_json', name: 'json_exportateur_by_id')]
    public function exportateur_by_id_json(ExportateurRepository $exportateurs,
                                          MenuRepository $menus,
                                          MenuPermissionRepository $permissions,
                                          GroupeRepository $groupeRepository,
                                          UserRepository $userRepository,
                                          ManagerRegistry $doctrine,
                                          Request $request,
                                          Exportateur $single_exportateur = null,
                                          int $id_exportateur,
                                          NotificationRepository $notificationRepository,
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $single_exportateur = $doctrine->getRepository(Exportateur::class)->find($id_exportateur);
                if($single_exportateur){
                    $response = array();

                    $response[] =  array(
                        'id'=>$single_exportateur->getId(),
                        'sigle'=>$single_exportateur->getSigle(),
                        'rs'=>$single_exportateur->getRaisonSocialeExportateur(),
                        'personne_ressource'=>$single_exportateur->getPersonneRessource(),
                        'email_personne_ressource'=>$single_exportateur->getEmailPersonneRessource(),
                        'mobile_personne_ressource'=>$single_exportateur->getMobilePersonneRessource(),
                        'mobile'=>$single_exportateur->getMobileExportateur(),
                        'adresse'=>$single_exportateur->getAdresseExportateur(),
                        'cc'=>$single_exportateur->getNccExportateur()
                    );



                    return  new  JsonResponse(json_encode($response));
                }else {
                    return  new  JsonResponse(json_encode("NO OPERATOR SELECTED"));
                }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
