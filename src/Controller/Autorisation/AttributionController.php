<?php

namespace App\Controller\Autorisation;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\synchro\ForetSnvlt1;
use App\Controller\Services\synchro\Mouvements\AttributionSnvlt1;
use App\Controller\Services\Utils;
use App\Entity\Admin\Exercice;
use App\Entity\Admin\Option;
use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\Reprise;
use App\Entity\References\Ddef;
use App\Entity\References\DocumentOperateur;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Entity\References\TypeOperateur;
use App\Entity\User;
use App\Events\Autorisation\AddAttributionEvent;
use App\Events\Autorisation\AddRepriseEvent;
use App\Events\References\AddDocumentOperateurEvent;
use App\Form\Autorisation\AttributionType;
use App\Form\Autorisation\RepriseType;
use App\Form\References\DdefType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\Autorisations\AttributionRepository;
use App\Repository\Autorisations\RepriseRepository;
use App\Repository\DocumentOperateurRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\DdefRepository;
use App\Repository\References\GrilleLegaliteRepository;
use App\Repository\TypeAutorisationRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AttributionController extends AbstractController
{

    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private TranslatorInterface $translator,
        private AdministrationService $utils,
        private AttributionSnvlt1 $attributionSnvlt1,
        private ForetSnvlt1 $foretSnvlt1,
    )
    {}

    #[Route('/snvlt/admin/att', name: 'app_attribution')]
    public function index(AttributionRepository $attributionRepository,
                          MenuRepository $menus,
                          ManagerRegistry $registry,
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
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
            $user = $userRepository->find($this->getUser());
            $code_groupe = $user->getCodeGroupe()->getId();
            $titre = $this->translator->trans("Add an attribution");

                $exo = $request->getSession()->get("exercice");
                $exercice = $registry->getRepository(Exercice::class)->find((int) $exo);
                $attributions = $attributionRepository->findBy([
                    'exercice'=>$exercice
                ]);
            return $this->render('autorisation/attribution/index.html.twig', [
                'attributions' => $attributions,
                'liste_menus'=>$menus->findOnlyParent(),
                "all_menus"=>$menus->findAll(),
                'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                'groupe'=>$code_groupe,
                'titre'=>$titre,
                'liste_parent'=>$permissions
            ]);
        } else {
                return  $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
    #[Route('snvlt/att/addnew/{data}', name: 'json_add_att')]
        public function json_add_att(
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

                    $exo = $registry->getRepository(Exercice::class)->findOneBy(['cloture'=>false],['annee'=>'DESC']);

                    $date_creation = new \DateTimeImmutable();

                    $attribution = new Attribution();

                    $attribution->setExercice($exo);
                    $attribution->setStatut(true);
                    $attribution->setCreatedBy($user);
                    $attribution->setCreatedAt($date_creation);
                    $foret = $registry->getRepository(Foret::class)->find((int) $arraydata->foret);
                    $exploitant = $registry->getRepository(Exploitant::class)->find((int) $arraydata->exploitant);

                    $attribution->setCodeForet($foret);
                    $attribution->setCodeExploitant($exploitant);
                    $decision = $arraydata->numero_decision;
                    $attribution->setNumeroDecision(str_replace("-", "/", $decision) );
                    $attribution->setDateDecision(\DateTime::createFromFormat('Y-m-d', $arraydata->date_decision));

                    $attribution->setReprise(false);
                    $attribution->setRetire(false);

                    $registry->getManager()->persist($attribution);

                    // Mise à jour de la forêt
                    $attribution->getCodeForet()->setAttribue(true);

                    //Enregistrement des transactions
                    $registry->getManager()->flush();

                    // Enregistrement des valeurs dans SNVLT1 (Attribution et Foret)
                        $this->attributionSnvlt1->Ajout_Attribution($attribution);
                        $this->foretSnvlt1->MajAttributionForet($attribution->getCodeForet());

                    $response[] = array(
                        'code'=>'SUCCESS'
                    );
                    return new JsonResponse(json_encode($response));
                }
            } else {
                    return  $this->redirectToRoute('app_no_permission_user_active');
                }
            }
        }

    #[Route('snvlt/ref/edit/att/', name: 'attribution.edit')]
    public function edit_attribution(
        Reprise $reprise = null,
        ManagerRegistry $doctrine,
        Request $request,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('autorisation/attribution/add-attribution.html.twig',[

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


    #[Route('snvlt/docs_op/{id_exploitant}', name: 'docs_op.list')]
    public function affiche_docs_operateur(
        int $id_exploitant,
        Exploitant $exploitant = null,
        ManagerRegistry $registry,
        TypeAutorisationRepository $type_autorisations,
        DocumentOperateurRepository $doc_operateurRepository,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        Request $request,
        ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
            if($id_exploitant){
                $exploitant = $registry->getRepository(Exploitant::class)->find($id_exploitant);
                $typeOperateur = $registry->getRepository(TypeOperateur::class)->find(2);
                $doc_operateurRepository = $registry->getRepository(DocumentOperateur::class)->findBy(['type_operateur'=>$typeOperateur, 'codeOperateur'=>$exploitant->getId()]);


                $doc_attribution = $type_autorisations->find(1);
                $docs_attribution = $doc_attribution->getCodeDocGrille();

                $liste_docs = array(); 
                $recherche_doc = new DocumentOperateur();
                foreach ($docs_attribution as $doc) {
                    //foreach ($doc_operateurRepository as $doc_op) {
                    $recherche_doc = $registry->getRepository(DocumentOperateur::class)->searchDocOperateur(
                        $typeOperateur,
                        $exploitant->getId(),
                        $doc
                    );
                    //dd($recherche_doc);
                        if ($recherche_doc){
                            $liste_docs[] = array(
                                'id' => $recherche_doc[0]->getId(),
                                'libelle' => $recherche_doc[0]->getCodeDocumentGrille()->getLibelleDocument(),
                                'fichier' => $recherche_doc[0]->getImageName(),
                                'statut' => $recherche_doc[0]->getStatut() //ID de la grille de légalité
                            );
                        } else {
                            $liste_docs[] = array(
                                'id' => $doc->getId(),
                                'libelle' => $doc->getLibelleDocument(),
                                'fichier' => '-',
                                'statut' => '-' //ID de la grille de légalité
                            );
                        }

                    //}

                }
                return new JsonResponse(json_encode($liste_docs));

                } else {
                    $response = false;
                    return new JsonResponse(json_encode($response));
            }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/docs_exp', name: 'docs_attribution.list')]
    public function affiche_docs_attribution(
        ManagerRegistry $registry,
        TypeAutorisationRepository $type_autorisations,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        Request $request,
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                    $doc_attribution = $type_autorisations->find(1);
                    $docs_attribution = $doc_attribution->getCodeDocGrille();

                    $liste_docs_attribution = array();

                    foreach ($docs_attribution as $doc) {
                        $liste_docs_attribution[] = array(
                            'id' => $doc->getId(), //ID du document issu de la grille légalité
                            'libelle' => $doc->getLibelleDocument()
                        );
                    }
                    return new JsonResponse(json_encode($liste_docs_attribution));
                }
    }

    #[Route('snvlt/att/remove/{id_attribution}/{type_retrait}/{motif}', name: 'remove_att')]
    public function remove_att(
        ManagerRegistry $registry,
        UserRepository $userRepository,
        User $user = null,
        int $id_attribution,
        int $type_retrait,
        string $motif,
        Request $request,
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $remove_attribution = array();
                $attribution = $registry->getRepository(Attribution::class)->find($id_attribution);
                if ($attribution){
                    $attribution->setStatut(false);
                    if ((int) $type_retrait == 1){
                        $retrait = "RETRAIT ATTRIBUTION";
                        $description = "L'attribution N° ". $attribution->getNumeroDecision()." du " .
                            $attribution->getDateDecision()->format('d/m/Y').
                            " a été retirée avec succès par ". $user;
                        $attribution->setRetire(true);
                        $attribution->setDateRetrait(new  \DateTime());



                    } else {
                        $retrait = "ABANDON ATTRIBUTION";
                        $description = "L'attribution N° ". $attribution->getNumeroDecision()." du " .
                            $attribution->getDateDecision()->format('d/m/Y').
                            " a été abandonnée avec succès par ". $user;
                        $attribution->setAbandonne(true);
                        $attribution->setDateAbandon(new  \DateTime());
                    }
                    $attribution->setMotif($motif);

                    $registry->getManager()->persist($attribution);

                    // Mise à jour Forêt
                    $foret = $attribution->getCodeForet();
                    $foret->setAttribue(false);
                    $foret->setReprise(false);
                    $registry->getManager()->persist($foret);

                    $registry->getManager()->flush();


                    // Mise à jour dans SNVLT1
                    $this->attributionSnvlt1->RetraitAttribution($attribution);
                    $this->foretSnvlt1->RetraitAttributionForet($attribution->getCodeForet());

                    $remove_attribution[] = array(
                        'CODE' => 'SUCCESS'
                    );
                    $this->utils->save_action(
                        $user,
                        'ATTRIBUTION',
                        $retrait,
                        new \DateTimeImmutable(),
                        $description
                    );
                } else {
                    $remove_attribution[] = array(
                        'CODE' => 'ERROR'
                    );
                }
            return new JsonResponse(json_encode($remove_attribution));
        } else {
                return  $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
    #[Route('snvlt/att/restaure/{id_attribution}', name: 'restaure_att')]
    public function restaure_att(
        ManagerRegistry $registry,
        UserRepository $userRepository,
        User $user = null,
        int $id_attribution,
        Request $request,
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $remove_attribution = array();
                $attribution = $registry->getRepository(Attribution::class)->find($id_attribution);
                if ($attribution){
                    $attribution->setStatut(true);
                        $attribution->setRetire(false);
                        $attribution->setDateRetrait(null);
                        $attribution->setAbandonne(false);
                        $attribution->setDateAbandon(null);

                    $attribution->setMotif("");
                    $registry->getManager()->persist($attribution);

                    // Mise à jour Forêt
                    $foret = $attribution->getCodeForet();
                    $foret->setAttribue(true);
                    $registry->getManager()->persist($foret);

                    $registry->getManager()->flush();

                    // Mise à jour dans SNVLT1
                    $this->attributionSnvlt1->RestaurerAttribution($attribution);
                    $this->foretSnvlt1->MajAttributionForet($attribution->getCodeForet());

                    $remove_attribution[] = array(
                        'CODE' => 'SUCCESS'
                    );
                    $this->utils->save_action(
                        $user,
                        'ATTRIBUTION',
                        'RESTAURATION ATTRIBUTION',
                        new \DateTimeImmutable(),
                        "L'attribution N° ". $attribution->getNumeroDecision()." du " .
                        $attribution->getDateDecision()->format('d/m/Y').
                        " a été restaurée avec succès par ". $user
                    );
                } else {
                    $remove_attribution[] = array(
                        'CODE' => 'ERROR'
                    );
                }
            return new JsonResponse(json_encode($remove_attribution));
        } else {
                return  $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
