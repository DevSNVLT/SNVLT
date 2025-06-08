<?php

namespace App\Controller\References;

use App\Entity\References\DetailsModele;
use App\Entity\References\Direction;
use App\Entity\References\GrilleLegalite;
use App\Entity\References\ModeleCommunication;
use App\Entity\References\ServiceMinef;
use App\Entity\User;
use App\Form\References\GrilleLegaliteType;
use App\Form\References\ModeleCommunicationType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DemandeOperateurRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\DetailsModeleRepository;
use App\Repository\References\DirectionRepository;
use App\Repository\References\GrilleLegaliteRepository;
use App\Repository\References\ModeleCommunicationRepository;
use App\Repository\References\ServiceMinefRepository;
use App\Repository\References\TypeModeleCommunicationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ModeleCommunicationController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    #[Route('snvlt/ref/mc', name: 'app_modele_com')]
    public function index(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        ModeleCommunicationRepository $communicationRepository): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or  $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_EXPORTATEUR'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('references/modele_communication/index.html.twig', [

                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'mes_modeles'=>$communicationRepository->findAll(),
                    'liste_parent'=>$permissions
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }


    }
    }

    #[Route('snvlt/ref/mc/edit/{id_modele?0}', name: 'app_modele_com.edit')]
    public function editGrilleLegalite(
        ModeleCommunication $modeleCommunication = null,
        ManagerRegistry $doctrine,
        Request $request,
        ModeleCommunicationRepository $modeleCommunicationRepository,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_modele,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        DetailsModeleRepository $detailsModeleRepository): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $date_creation = new \DateTimeImmutable();

                $titre = $this->translator->trans("Edit Model");
                $modeleCommunication = $modeleCommunicationRepository->find($id_modele);
                //dd($grille_legalite);
                if (!$modeleCommunication) {
                    $new = true;
                    $modeleCommunication = new ModeleCommunication();
                    $titre = $this->translator->trans("Add Model");

                    $modeleCommunication->setCreatedAt($date_creation);
                    $modeleCommunication->setCreatedBy($this->getUser());
                }

                $new = false;
                if (!$modeleCommunication) {
                    $new = true;
                    $modeleCommunication = new ModeleCommunication();
                }
                $form = $this->createForm(ModeleCommunicationType::class, $modeleCommunication);

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {

                    $modeleCommunication->setUpdatedAt($date_creation);
                    $modeleCommunication->setUpdatedBy($this->getUser());

                    $manager = $doctrine->getManager();
                    $manager->persist($modeleCommunication);
                    $manager->flush();

                    $this->addFlash('success', $this->translator->trans("Model has been updated successfully"));
                    return $this->redirectToRoute("app_modele_com");
                } else {
                    return $this->render('references/modele_communication/add-modele.html.twig', [
                        'form' => $form->createView(),
                        'titre' => $titre,
                        'liste_modeles' => $modeleCommunicationRepository->findAll(),
                        'liste_menus' => $menus->findOnlyParent(),
                        "all_menus" => $menus->findAll(),
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                        'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
                        'groupe' => $code_groupe,
                        'liste_parent'=>$permissions,
                        'detail_model'=>$detailsModeleRepository->findBy(['code_modele'=>$modeleCommunication],['numseq'=>'ASC']),
                        'id_modele'=>$modeleCommunication->getId()
                    ]);
                }
                /*}*/
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

        #[Route('snvlt/ref/mc/change_statut/{id_modele}/{id_type_modele}', name: 'app_modele_com.change_status')]
        public  function  modeleActif(
            int $id_modele,
            int $id_type_modele,
            ModeleCommunication $modeleCommunication = null,
            ModeleCommunicationRepository $modeleCommunicationRepository,
            TypeModeleCommunicationRepository $typeModeleCommunicationRepository,
            UserRepository $userRepository,
            EntityManagerInterface $entityManager,
            User $user = null,
            Request $request
        ):Response
        {
            $date_creation = new \DateTimeImmutable();
            if(!$request->getSession()->has('user_session')){

                return $this->redirectToRoute('app_login');
            } else {
                if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')) {
                    if ($id_type_modele){
                        $modeleType = $typeModeleCommunicationRepository->find($id_type_modele);

                        $liste_modeles = $modeleCommunicationRepository->findBy(['code_type_modele_communication'=>$modeleType]);
                        // Réinitialisation  et enregistrement de tous les modeles avec Statut = ""
                        foreach ($liste_modeles as $modele)
                        {
                            $modele->setStatut("");
                            $entityManager->persist($modele);
                        }


                        //Enregistrement du statut sélectionné
                        $modeleCommunication = $modeleCommunicationRepository->find($id_modele);
                        if ($modeleCommunication){
                            $modeleCommunication->setStatut("ACTIF");
                            $modeleCommunication->setUpdatedAt($date_creation);
                            $modeleCommunication->setUpdatedBy($this->getUser());
                            $entityManager->persist($modeleCommunication);
                        }
                        //Sauvegarde de toutes les transactions
                        $entityManager->flush();
                        return  $this->redirectToRoute('app_modele_com');
                    }

                } else {
                    return $this->redirectToRoute('app_no_permission_user_active');
                }
        }
    }

    #[Route('snvlt/ref/mc/add-detail/{code_modele}/{type_service}/{code_direction?0}/{code_service?0}', name: 'app_modele_com.add_detail')]
    public  function  addDetail(
                                int $code_modele,
                                string $type_service,
                                int $code_direction,
                                int $code_service,
                                  EntityManagerInterface $manager,
                                  ModeleCommunication $modeleCommunication = null,
                                  ModeleCommunicationRepository $modeleCommunicationRepository,

                                  DetailsModele $detailsModele = null,
                                  DetailsModeleRepository $detailsModeleRepository,

                                  ServiceMinef $serviceMinef = null,
                                  ServiceMinefRepository $serviceMinefRepository,

                                 Direction $direction = null,
                                  DirectionRepository $directionRepository,

                                  UserRepository $userRepository,
                                  EntityManagerInterface $entityManager,

                                  User $user = null,
                                  Request $request
    ):Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $detailsModele = new DetailsModele();
                $majDate = new \DateTimeImmutable();

                $modeleCommunication = $modeleCommunicationRepository->find($code_modele);

                if($type_service == "DIRECTION"){
                    $direction = $directionRepository->find($code_direction);
                    $detailsModele->setCodeDirection($direction);
                } else {
                    $serviceMinef = $serviceMinefRepository->find($code_service);
                    $detailsModele->setCodeService($serviceMinef);
                }

                $detailsModele->setTypeService($type_service);
                $detailsModele->setCodeModele($modeleCommunication);


                $nombreServices = $detailsModeleRepository->findBy(['code_modele'=>$code_modele]);
                $i = 0;
                foreach ($nombreServices as $element){
                    $i++;
                }

                $detailsModele->setNumseq($i + 1);

                $manager->persist($detailsModele);
                $manager->flush();

                $response = true;
                return new JsonResponse(json_encode($response));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

            }
    }


    #[Route('snvlt/mc/list/details/{id_modele}', name: 'liste_details_modcel')]
    public function direction_json(int $id_modele, Request $request, DetailsModeleRepository $detailsModeleRepository, ModeleCommunication $modeleCommunication = null, ModeleCommunicationRepository $modeleCommunicationRepository):Response{
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $modeleCommunication = $modeleCommunicationRepository->find($id_modele);
                $liste_details= $detailsModeleRepository->findBy(['code_modele'=>$modeleCommunication],['numseq'=>'ASC']);


                $response = array();

                $serviceMinef = "";
                $directionMinef = "";
                foreach ($liste_details as $details) {

                    if($details->getCodeService()){
                        $serviceMinef = $details->getCodeService()->getLibelleService();
                    }
                    if($details->getCodeDirection()){
                        $directionMinef = $details->getCodeDirection()->getDenomination();
                    }

                    $response[] = array(
                        'id_minef' => $details->getId(),
                        'type_service' => $details->getTypeService(),
                        'service_minef' => $serviceMinef,
                        'direction_minef' => $directionMinef,
                        'code_modele' => $details->getCodeModele()->getId(),
                        'numseq'=>$details->getNumseq()
                    );
                }

                return new JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/mc/list/details/rem/{id_detail}', name: 'app_details_modcel_rem')]
    public function removeDetail(int $id_detail,
                                 Request $request,
                                 DetailsModeleRepository $detailsModeleRepository,
                                 DetailsModele $detailsModele = null,
                                 ModeleCommunication $modeleCommunication = null,
                                 ManagerRegistry $manager
    ):Response{

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {

               if($id_detail){

                   $detailsModele = $detailsModeleRepository->find($id_detail);
                   $modeleCommunication = $detailsModele->getCodeModele();

                   $manager->getManager()->remove($detailsModele);
                   $manager->getManager()->flush();
               }

               $liste_details = $detailsModeleRepository->findBy(['code_modele'=>$modeleCommunication], ['numseq'=>'ASC']);
                    $i = 0;
                foreach ($liste_details as $liste_detail) {
                    $i++;
                    $liste_detail->setNumseq($i);
                    $manager->getManager()->persist($liste_detail);
               }

                $manager->getManager()->flush();

                $response = true;
                return new JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
