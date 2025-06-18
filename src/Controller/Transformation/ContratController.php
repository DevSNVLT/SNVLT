<?php

namespace App\Controller\Transformation;


use App\Controller\Services\AdministrationService;
use App\Controller\Services\Utils;
use App\Entity\Transformation\Contrat;
use App\Entity\User;
use App\Form\Transformation\ContratType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\EssenceRepository;
use App\Repository\Transformation\ContratRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ContratController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator,
                                private AdministrationService $administrationService,
                                private Utils $utils
    )
    {
    }

    #[Route('/transformation/contrat', name: 'app_transformation_contrat')]
    public function index(ManagerRegistry $registry,
                          ContratRepository $contratRepository,
                          Request $request,
                          MenuPermissionRepository $permissions,
                          MenuRepository $menus,
                          GroupeRepository $groupeRepository,
                          UserRepository $userRepository,
                          User $user = null,
                          NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $date_du_jour = new \DateTimeImmutable();

                return $this->render('transformation/contrat/index.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'contrats'=>$contratRepository->findBy(['code_usine'=>$user->getCodeindustriel()]),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/ref/edit/contrat/{id_contrat?0}', name: 'contrat.edit')]
    public function edit_Contrat(
        Contrat $contrat = null,
        ManagerRegistry $doctrine,
        Request $request,
        ContratRepository $contrats,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_contrat,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $date_creation = new \DateTimeImmutable();

                $titre = $this->translator->trans("Edit Contract");
                $contrat = $contrats->find($id_contrat);
                //dd($contrat);
                if(!$contrat){
                    $new = true;
                    $contrat = new Contrat();
                    $titre = $this->translator->trans("Add Contract");

                    $contrat->setCreatedAt($date_creation);
                    $contrat->setCreatedBy($this->getUser());
                } else {
                    $contrat->setUpdatedAt($date_creation);
                    $contrat->setUpdatedBy($this->getUser());
                }

                $session = $request->getSession();

                $form = $this->createForm(ContratType::class, $contrat);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){

                    $contrat->setCodeUsine($user->getCodeindustriel());
                    $contrat->setExercice($this->administrationService->getAnnee());
                    $contrat->setCloture(false);


                    $manager = $doctrine->getManager();
                    $manager->persist($contrat);
                    $manager->flush();

                    $this->addFlash('success',"Le Contrat a été mis à jour avec succès");
                    return $this->redirectToRoute("app_transformation_contrat");
                } else {
                    return $this->render('transformation/contrat/add-contrat.html.twig',[
                        'form' =>$form->createView(),
                        'titre'=>$titre,
                        'liste_contrats' => $contrats->findAll(),
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

    #[Route('/transformation/my_contracts', name: 'my_contracts')]
    public function my_contracts(ManagerRegistry $registry,
                          ContratRepository $contratRepository,
                          Request $request,
                          MenuPermissionRepository $permissions,
                          MenuRepository $menus,
                          GroupeRepository $groupeRepository,
                          UserRepository $userRepository,
                          User $user = null,
                          NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $my_cnt  = array();
                $contrats = $contratRepository->findBy(['code_usine'=>$user->getCodeindustriel(), 'cloture'=>false]);
                foreach($contrats as $contrat){
                        $my_cnt[] = array(
                            'id_contrat'=> $contrat->getId(),
                            'numero_contrat'=>$contrat->getNumeroContrat(),
                            'pays'=>$contrat->getPays()->getDenomination()
                    );
                }


                return  new JsonResponse(json_encode($my_cnt));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/contrat/search/{id_contrat}', name: 'single_contrat')]
    public function single_contrat(ManagerRegistry $registry,
                                 ContratRepository $contratRepository,
                                 Request $request,
                                 int $id_contrat = null,
                                 MenuPermissionRepository $permissions,
                                 MenuRepository $menus,
                                 GroupeRepository $groupeRepository,
                                 UserRepository $userRepository,
                                 User $user = null,
                                 NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_MINEF')  or $this->isGranted('ROLE_ADMIN') )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $my_cnt  = array();
                $contrats = $contratRepository->findBy(['code_usine'=>$user->getCodeindustriel(), 'cloture'=>false, 'id'=>$id_contrat]);
                foreach($contrats as $contrat){
                    $my_cnt[] = array(
                        'client'=> $contrat->getRaisonSocialeClt(),
                        'contact'=>$contrat->getContactPersonneRessource(),
                        'pays'=>$contrat->getPays()->getDenomination(),
                        'lead'=>$contrat->getPersonneResource()
                    );
                }


                return  new JsonResponse(json_encode($my_cnt));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/species/liste/contract/{id_contract}', name: 'essences_contract.json')]
    public function essences_contract_Json(
            ContratRepository $contratRepository,
            Request $request,
            int $id_contract,
            UserRepository $userRepository,
            User $user = null
        ): Response
        {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $user = $userRepository->find($this->getUser());

                $essences = array();
                $mon_contrat = $contratRepository->find($id_contract);
                if ($mon_contrat){
                    $mes_essences = $mon_contrat->getEssence();
					if ($mes_essences){
						foreach ($mes_essences as $essence){
							$essences[] = array(
								'essence_id'=>$essence->getId(),
								'nom_vernaculaire'=>$essence->getNomVernaculaire(),
								'code_essence'=>$essence->getNumeroEssence(),
								'dm'=>$essence->getDmMinima()
							);
						}
					}
                }
                return  new JsonResponse(json_encode($essences));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
   }
}
