<?php

namespace App\Controller\References;

use App\Entity\References\AttributairePs;
use App\Entity\References\Cantonnement;
use App\Entity\References\Nationalite;
use App\Entity\User;
use App\Form\References\AttributairePsType;
use App\Form\References\CantonnementType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\AttributairePsRepository;
use App\Repository\References\AttributionRepository;
use App\Repository\References\CantonnementRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AttributairePsController extends AbstractController
{
    #[Route('/references/attributaire/ps', name: 'app_references_attributaire_ps')]
    public function index( AttributairePsRepository $attributionRepository,
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

                return $this->render('references/attributaire_ps/index.html.twig', [
                    'liste_attributaires' => $attributionRepository->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'liste_parent'=>$permissions
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
    #[Route('/snvlt/edit/attributaire_ps/{id_attributaire?0}', name: 'attributaire_ps.edit')]
    public function attributaire_ps(
        AttributairePs $attributaire = null,
        ManagerRegistry $doctrine,
        Request $request,
        AttributairePsRepository $attributaires,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_attributaire,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_DPIF_SAISIE') )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $date_creation = new \DateTimeImmutable();

                $titre =  "Editer un attributaire";

                $attributaire = $attributaires->find($id_attributaire);

                $new = false;
                if(!$attributaire){
                    $new = true;
                    $attributaire = new AttributairePs();
                    $titre = "Ajouter un attributaire";
                }

                $form = $this->createForm(AttributairePsType::class, $attributaire);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){

                    $attributaire->setRaisonSociale(strtoupper($form->get('raison_sociale')->getData()));
                    $attributaire->setCc(strtoupper($form->get('cc')->getData()));
                    $attributaire->setAdresse(strtoupper($form->get('adresse')->getData()));
                    $attributaire->setCode(strtoupper($form->get('code')->getData()));
                    $attributaire->setPersonneRessource(strtoupper($form->get('personne_ressource')->getData()));

                    $manager = $doctrine->getManager();
                    $manager->persist($attributaire);
                    $manager->flush();

                    if ($new){
                        $this->addFlash('success', "L'attributaire' " . $attributaire->getRaisonSociale() . " vient d'être créé.");
                    } else {
                        $this->addFlash('success', "L'attributaire " . $attributaire->getRaisonSociale() . " a été mis à jour");
                    }
                    return $this->redirectToRoute("app_references_attributaire_ps");
                } else {
                    return $this->render('references/attributaire_ps/add-attributaire.html.twig',[
                        'form' =>$form->createView(),
                        'titre'=>$titre,
                        'liste_attributaires' => $attributaires->findAll(),
                        'liste_menus'=>$menus->findOnlyParent(),
                        "all_menus"=>$menus->findAll(),
                        'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                        'groupe'=>$code_groupe,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'liste_parent'=>$permissions,
                        'new_item'=>$new
                    ]);
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('/references/attributaire/ps/add/{data?0}/{id_attributaire?0}', name: 'attributaire_ps.add')]
    public function add_attributaire_ps(
                           AttributairePsRepository $attributionRepository,
                           MenuRepository $menus,
                           MenuPermissionRepository $permissions,
                           GroupeRepository $groupeRepository,
                           Request $request,
                           UserRepository $userRepository,
                           User $user = null,
                           Nationalite $nationalite = null,
                           AttributairePs $attributairePs = null,
                           string $data,
                           int $id_attributaire,
                           NotificationRepository $notification,
                           EntityManagerInterface $manager,
                           ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                    if ($data){
                        $attributairePs = $attributionRepository->find($id_attributaire);

                        if(!$attributairePs){
                            $attributairePs = new AttributairePs();
                        }

                        $arraydata[] = json_decode($data);

                        $attributairePs->setRaisonSociale($arraydata[0]->raison_sociale);
                        $attributairePs->setAdresse($arraydata[0]->adresse);
                        $attributairePs->setContact($arraydata[0]->contact);
                        $attributairePs->setCc($arraydata[0]->cc);
                        $attributairePs->setPersonneRessource($arraydata[0]->personne_ressource);
                        $attributairePs->setEmailPersonneRessource($arraydata[0]->email_personne_ressource);
                        $attributairePs->setMobilePersonneRessource($arraydata[0]->mobile_personne_ressource) ;
                        $attributairePs->setStatut(true);

                        if($arraydata[0]->nationalite){
                        $nationalite = new Nationalite();
                        $nationalite = $registry->getRepository(Nationalite::class)->find($arraydata[0]->nationalite);

                            if($arraydata[0]->nationalite){  $attributairePs->setNationalite($nationalite); }
                        }
                        if($arraydata[0]->type_attributaire){  $attributairePs->setTypeAtributaire($arraydata[0]->type_attributaire); }
                        if($arraydata[0]->sexe){  $attributairePs->setSexe($arraydata[0]->sexe); }

                        $manager->persist($attributairePs);
                        $manager->flush($attributairePs);

                    }
                    return  new JsonResponse(true);

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }


    #[Route('/references/attributaire/ps/show', name: 'attributaire_ps.liste')]
    public function show_attributaires_ps(
        AttributairePsRepository $attributionRepository,
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

               $liste_attributaires = $attributionRepository->findBy([],['id'=>'DESC']);
                $response = array();

               foreach ($liste_attributaires as $attributaire){
                   $nationalite = "";
                    if ($attributaire->getNationalite()){
                        $nationalite = $attributaire->getNationalite()->getNationalite();
                    } else {
                        $nationalite = "-";
                    }
                   $response[] =  array(
                       'id'=>$attributaire->getId(),
                       'nationalite'=>$nationalite,
                       'type_atributaire'=>$attributaire->getTypeAtributaire(),
                       'raison_sociale'=>$attributaire->getRaisonSociale(),
                       'adresse'=>$attributaire->getAdresse(),
                       'contact'=>$attributaire->getContact(),
                       'sexe'=>$attributaire->getSexe(),
                       'cc'=>$attributaire->getCc(),
                       'personne_ressource'=>$attributaire->getPersonneRessource(),
                       'email_personne_ressource'=>$attributaire->getEmailPersonneRessource(),
                       'mobile_personne_ressource'=>$attributaire->getMobilePersonneRessource(),
                       'statut'=>$attributaire->isStatut(),
                   );
               }
           return  new JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/references/attributaire/ps/show/{id_attributaire}', name: 'single_attributaire_ps')]
    public function single_attributaires_ps(
        AttributairePsRepository $attributionRepository,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        AttributairePs $attributaire = null,
        int $id_attributaire,
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

                $attributaire = new AttributairePs();
                $attributaire = $attributionRepository->find($id_attributaire);

                if($attributaire){
                    $response = array();
                    $nationalite = "";
                    if ($attributaire->getNationalite()){
                        $nationalite = $attributaire->getNationalite()->getNationalite();
                    } else {
                        $nationalite = "-";
                    }
                        $response[] =  array(
                            'id'=>$attributaire->getId(),
                            'nationalite'=>$nationalite,
                            'type_atributaire'=>$attributaire->getTypeAtributaire(),
                            'raison_sociale'=>$attributaire->getRaisonSociale(),
                            'adresse'=>$attributaire->getAdresse(),
                            'contact'=>$attributaire->getContact(),
                            'sexe'=>$attributaire->getSexe(),
                            'cc'=>$attributaire->getCc(),
                            'personne_ressource'=>$attributaire->getPersonneRessource(),
                            'email_personne_ressource'=>$attributaire->getEmailPersonneRessource(),
                            'mobile_personne_ressource'=>$attributaire->getMobilePersonneRessource(),
                            'statut'=>$attributaire->isStatut(),
                        );

                    return  new JsonResponse(json_encode($response));
                }else {
                    return $this->redirectToRoute(false);
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/references/attributaire/ps/stopactivity/{id_attributaire}/{value?0}', name: 'single_attributaire_ps_stop')]
    public function stop_attributaires_ps(
        AttributairePsRepository $attributionRepository,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        AttributairePs $attributaire = null,
        int $id_attributaire,
        int $value,
        NotificationRepository $notification,
        EntityManagerInterface $em
    ): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $attributaire = $attributionRepository->find($id_attributaire);

                if($attributaire){

                    $response = "SUCCESS";
                    if($value){
                        $attributaire->setStatut(true);
                    } else {
                        $attributaire->setStatut(false);
                    }


                    $em->persist($attributaire);
                    $em->flush();

                    return  new JsonResponse(json_encode($response));
                }else {
                    return $this->redirectToRoute(false);
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}