<?php

namespace App\Controller\Autorisation;

use App\Controller\Services\AdministrationService;
use App\Entity\Autorisation\ContratBcbgfh;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Entity\User;
use App\Form\Autorisation\ContratBcbgfhType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\Autorisation\ContratBcbgfhRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\TypeAutorisationRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContratBcbgfhController extends AbstractController
{
    public function __construct(private AdministrationService $administrationService)
    {
    }

    #[Route('/autorisation/contrat/bcbgfh', name: 'app_autorisation_contrat_bcbgfh')]
    public function index(ContratBcbgfhRepository $contratbcbgfhRepository,
                          ManagerRegistry $doctrine,
                          MenuRepository $menus,
                          MenuPermissionRepository $permissions,
                          GroupeRepository $groupeRepository,
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



                return $this->render('autorisation/contrat_bcbgfh/index.html.twig', [
                    'contrats' => $contratbcbgfhRepository->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'exploitants'=>$doctrine->getRepository(Exploitant::class)->findBy([], ['raison_sociale_exploitant'=>'ASC']),
                    'forets'=>$doctrine->getRepository(Foret::class)->findBy(['attribue'=>false], ['denomination'=>'ASC'])
                ]);
            } else {
                return  $this->redirectToRoute('app_no_permission_user_active');
            }
        }


    }



    #[Route('snvlt/ref/edit/contrat/bcbgfh', name: 'edit_contrat_bcbgfh')]
    public function edit_contrat_bcbgfh(

        ManagerRegistry $doctrine,
        Request $request,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        UserRepository $userRepository,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('autorisation/contrat_bcbgfh/add-contratbcbgfh.html.twig',[

                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user],[],5,0),
                    'liste_parent'=>$permissions,
                    'exploitants'=>$doctrine->getRepository(Exploitant::class)->findBy([], ['raison_sociale_exploitant'=>'ASC']),
                    'forets'=>$doctrine->getRepository(Foret::class)->findBy(['attribue'=>false], ['denomination'=>'ASC'])
                ]);

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    #[Route('snvlt/contrat/gcbgfh/addnew/{data}', name: 'json_add_contrat_bcbgfh')]
    public function json_add_contrat_bcbgfh(
        ManagerRegistry $registry,
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        string $data
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                if ($data){
                    $arraydata = json_decode($data);
                    $response = array();


                    $date_creation = new \DateTimeImmutable();

                    $contratBcbgfh = new ContratBcbgfh();

                    $contratBcbgfh->setStatut(true);
                    $contratBcbgfh->setCretaedBy($user);
                    $contratBcbgfh->setCreatedAt($date_creation);
                    $foret = $registry->getRepository(Foret::class)->find((int) $arraydata->foret);
                    $exploitant = $registry->getRepository(Exploitant::class)->find((int) $arraydata->exploitant);

                    $contratBcbgfh->setCodeForet($foret);
                    $contratBcbgfh->setCodeExploitant($exploitant);
                    $numero_contrat = $arraydata->numero_contrat;
                    $contratBcbgfh->setNumeroContrat(str_replace("-", "/", $numero_contrat) );
                    $contratBcbgfh->setDateContrat(\DateTime::createFromFormat('Y-m-d', $arraydata->date_signature));
                    $contratBcbgfh->setDateSignature(\DateTime::createFromFormat('Y-m-d', $arraydata->date_signature));

                    $contratBcbgfh->setDuree((int) $arraydata->duree);
                    $contratBcbgfh->setNbTiges((int) $arraydata->nb_tiges);

                    $registry->getManager()->persist($contratBcbgfh);


                    //Enregistrement des transactions
                    $registry->getManager()->flush();

                    //Log SNVLT1
                    $this->administrationService->save_action(
                        $user,
                        "CONTRAT_BCBGFH",
                        "Création Contrat BCBGFH",
                        new \DateTimeImmutable(),
                        "Le contrat BCBGFH N% ". $contratBcbgfh->getNumeroContrat() . " a été créé par " . $user
                    );
                    $response[] = array(
                        'code'=>'SUCCESS'
                    );

                } else {
                    $response[] = array(
                        'code'=>'ERROR'
                    );
                }
                return new JsonResponse(json_encode($response));
            } else {
                return  $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/contrat/gcbgfh/edit/{id_contrat?0}/{data}', name: 'json_edit_contrat_bcbgfh')]
    public function json_edit_contrat_bcbgfh(
        ManagerRegistry $registry,
        Request $request,
        UserRepository $userRepository,
        int $id_contrat,
        string $data
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $contratBcbgfh = $registry->getRepository(ContratBcbgfh::class)->find((int) $id_contrat);
                if ($contratBcbgfh and $data){
                    $arraydata = json_decode($data);
                    $response = array();


                    $date_creation = new \DateTimeImmutable();


                    $contratBcbgfh->setStatut(true);
                    $contratBcbgfh->setUpdatedBy($user);
                    $contratBcbgfh->setUpdatedAt($date_creation);
                    $foret = $registry->getRepository(Foret::class)->find((int) $arraydata->foret);
                    $exploitant = $registry->getRepository(Exploitant::class)->find((int) $arraydata->exploitant);

                    $contratBcbgfh->setCodeForet($foret);
                    $contratBcbgfh->setCodeExploitant($exploitant);
                    $numero_contrat = $arraydata->numero_contrat;
                    $contratBcbgfh->setNumeroContrat(str_replace("-", "/", $numero_contrat) );
                    $contratBcbgfh->setDateContrat(\DateTime::createFromFormat('Y-m-d', $arraydata->date_signature));
                    $contratBcbgfh->setDateSignature(\DateTime::createFromFormat('Y-m-d', $arraydata->date_signature));

                    $contratBcbgfh->setDuree((int) $arraydata->duree);
                    $contratBcbgfh->setNbTiges((int) $arraydata->nb_tiges);

                    $registry->getManager()->persist($contratBcbgfh);


                    //Enregistrement des transactions
                    $registry->getManager()->flush();

                    //Log SNVLT1
                    $this->administrationService->save_action(
                        $user,
                        "CONTRAT_BCBGFH",
                        "MISE A JOUR Contrat BCBGFH",
                        new \DateTimeImmutable(),
                        "Le contrat BCBGFH N% ". $contratBcbgfh->getNumeroContrat() . " a été mis à jour par " . $user
                    );
                    $response[] = array(
                        'code'=>'SUCCESS'
                    );

                } else {
                    $response[] = array(
                        'code'=>'ERROR'
                    );
                }
                return new JsonResponse(json_encode($response));
            } else {
                return  $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/contrat/gcbgfh/search/{id_contrat}', name: 'search_contrat_bcbgfh')]
    public function search_contrat_bcbgfh(
        ManagerRegistry $registry,
        Request $request,
        int $id_contrat
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $reponse = array();
                $contrat = $registry->getRepository(ContratBcbgfh::class)->find($id_contrat);
                if ($contrat){
                    $reponse[] = array(
                        'numero_contrat'=>$contrat->getNumeroContrat(),
                        'date_contrat'=>$contrat->getDateSignature()->format('Y-m-d'),
                        'duree'=>$contrat->getDuree(),
                        'nb_tiges'=>$contrat->getNbTiges(),
                        'foret'=>$contrat->getCodeForet()->getId(),
                        'exploitant'=>$contrat->getCodeExploitant()->getId(),
                    );
                }
                return new JsonResponse(json_encode($reponse));
            } else {
                return  $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
