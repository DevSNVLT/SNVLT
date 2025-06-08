<?php

namespace App\Controller\Transformation;


use App\Controller\Services\AdministrationService;
use App\Controller\Services\Utils;
use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Entetes\Documentlje;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Pages\Pagelje;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\DocStats\Saisie\Lignepagelje;
use App\Entity\References\Essence;
use App\Entity\References\Foret;
use App\Entity\References\ZoneHemispherique;
use App\Entity\Transformation\Billon;
use App\Entity\Transformation\Colis;
use App\Entity\Transformation\Contrat;
use App\Entity\Transformation\Elements;
use App\Entity\Transformation\ElementsColis;
use App\Entity\Transformation\ElementsDisponibleColis;
use App\Entity\Transformation\FicheLotProd;
use App\Entity\User;
use App\Form\Transformation\ColisType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DocStats\Pages\PagebrhRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\Transformation\ColisRepository;
use App\Repository\Transformation\ContratRepository;
use App\Repository\Transformation\ElementsRepository;
use App\Repository\Transformation\FicheLotProdRepository;
use App\Repository\UserRepository;
use App\Services\OptionsService;
use Doctrine\Persistence\ManagerRegistry;
use phpDocumentor\Reflection\Element;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ColisController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator,
                                private AdministrationService $administrationService,
                                private Utils $utils
    )
    {
    }



    #[Route('/transformation/colis', name: 'app_transformation_colis')]
    public function index(ManagerRegistry $registry,
                          ColisRepository $colisRepository,
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

                return $this->render('transformation/colis/index.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
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

    #[Route('/transformation/colis/details/{id_colis}', name: 'colis_details')]
    public function colis_details(ManagerRegistry $registry,
                                  ColisRepository $colisRepository,
                                  Request $request,
                                  int $id_colis,
                                  MenuPermissionRepository $permissions,
                                  MenuRepository $menus,
                                  GroupeRepository $groupeRepository,
                                  UserRepository $userRepository,
                                  User $user = null,
                                  OptionsService $optionsService,
                                  NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $colis = $registry->getRepository(Colis::class)->find($id_colis);
                $date_du_jour = new \DateTimeImmutable();

                return $this->render('transformation/colis/details.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    'mon_colis'=>$colis,
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'options_service'=>$optionsService
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/ref/edit/colis/{id_colis?0}', name: 'colis.edit')]
    public function edit_Colis(
        Colis $colis = null,
        ManagerRegistry $doctrine,
        Request $request,
        ColisRepository $coliss,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_colis,
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
                $colis = $coliss->find($id_colis);
                //dd($colis);
                if(!$colis){
                    $new = true;
                    $colis = new Colis();
                    $titre = $this->translator->trans("Add Contract");

                    $colis->setCreatedAt($date_creation);
                    $colis->setCreatedBy($this->getUser());
                } else {
                    $colis->setUpdatedAt($date_creation);
                    $colis->setUpdatedBy($this->getUser());
                }

                $session = $request->getSession();

                $form = $this->createForm(ColisType::class, $colis);

                $form->handleRequest($request);

                if ( $form->isSubmitted() && $form->isValid() ){

                    $colis->setCodeUsine($user->getCodeindustriel());
                    $colis->setExercice($this->administrationService->getAnnee());
                    $colis->setCloture(false);


                    $manager = $doctrine->getManager();
                    $manager->persist($colis);
                    $manager->flush();

                    $this->addFlash('success',$this->translator->trans("THe Departmental Direction has been updated successfully"));
                    return $this->redirectToRoute("app_transformation_colis");
                } else {
                    return $this->render('transformation/colis/add-colis.html.twig',[
                        'form' =>$form->createView(),
                        'titre'=>$titre,
                        'liste_coliss' => $coliss->findAll(),
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

    // Liste des colis en JSON
    #[Route('/snvlt/contrats/colis/list/{id_contract}', name: 'colis_contrats.json')]
    public function colis_contrats(
        ContratRepository $contratRepository,
        Request $request,
        int $id_contract,
        UserRepository $userRepository,
        User $user = null,
        ManagerRegistry $registry
    ): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $user = $userRepository->find($this->getUser());

                $liste_colis = array();
                $mon_contrat = $contratRepository->find($id_contract);
                if ($mon_contrat){
                    $mes_colis = $mon_contrat->getColis();

                    foreach ($mes_colis as $colis){
                        $elts_colis = $registry->getRepository(ElementsColis::class)->findBy(['code_colis'=>$colis]);
                        $nb_elt = 0;
                        $vol_elt = 0;
                        foreach($elts_colis as $elt){
                            $nb_elt = $nb_elt + $elt->getNombre();
                            $vol_elt = round($vol_elt + $elt->getVolume(), 3);
                        }

                        $liste_colis[] = array(
                            'id_colis'=>$colis->getId(),
                            'contrat'=>$colis->getCodeContrat()->getNumeroContrat(),
                            'transformation'=>$colis->getCodeContrat()->getTypeTransfo()->getLibelle(),
                            'ref_colis'=>$colis->getReference(),
                            'code_essence'=>$colis->getCodeEssence()->getNomVernaculaire(),
                            'date_confection'=>$colis->getDateConfection()->format('d/m/Y'),
                            'etat'=>$colis->getEtatHygro(),
                            'nb_elts'=>$nb_elt,
                            'vol_elt'=>$vol_elt,
                            'cloture'=>$colis->isCloture()
                        );
                    }

                }
                return  new JsonResponse(json_encode($liste_colis));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/colis/op/add_colis/{data}', name: 'add_colis_json')]
    public function add_colis_json(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        string $data,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                //$page_brh = $pages_brh->find($id_page);
                if($data){
                    $colis = new Colis();


                    //Decoder le JSON BRH
                    $arraydata = json_decode($data);

                    //Rechercher le colis dans le contrat
                    $colis_search = $registry->getRepository(Colis::class)->findBy(['reference'=>$arraydata->reference, 'code_contrat'=>(int) $arraydata->code_contrat]);


                    $contrat = $registry->getRepository(Contrat::class)->find($arraydata->code_contrat);
                    $essence = $registry->getRepository(Essence::class)->find($arraydata->code_essence);

                    if(!$colis_search){
                        if ($contrat && $essence){
                            $date_jour = new \DateTime();
                            $colis->setReference(strtoupper($arraydata->reference));
                            $colis->setExercice($this->administrationService->getAnnee());
                            $colis->setEtatHygro(strtoupper($arraydata->etat_hygro));
                            $colis->setDateConfection((\DateTime::createFromFormat('Y-m-d', $arraydata->date_confection)));

                            $colis->setCodeContrat($contrat);
                            $colis->setCodeEssence($essence);

                            $colis->setCreatedAt($date_jour);
                            $colis->setCreatedBy($user);

                            $registry->getManager()->persist($colis);
                            $registry->getManager()->flush();

                            return  new JsonResponse(json_encode("PACKAGE_SAVED"));
                        } else {
                            return  new JsonResponse(json_encode("NO_CONTRACT_NO_SPECIES"));
                        }
                    } else {
                        return  new JsonResponse(json_encode("COLIS_EXISTING"));
                    }

                } else {
                    return  new JsonResponse(json_encode("INVALID_DATA"));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    // Infos colis en JSON
    #[Route('snvlt/colis/infos/id/{id_colis}', name: 'colis_infos.json')]
    public function colis_infos(
        ColisRepository $colisRepository,
        Request $request,
        int $id_colis,
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

                $infos_colis = array();
                $mon_colis = $colisRepository->find((int) $id_colis);
                if ($mon_colis){

                    $infos_colis[] = array(
                        'id_colis'=>$mon_colis->getId(),
                        'contrat'=>$mon_colis->getCodeContrat()->getNumeroContrat(),
                        'transformation'=>$mon_colis->getCodeContrat()->getTypeTransfo()->getLibelle(),
                        'ref_colis'=>$mon_colis->getReference(),
                        'code_essence'=>$mon_colis->getCodeEssence()->getNomVernaculaire(),
                        'date_confection'=>$mon_colis->getDateConfection()->format('d/m/Y'),
                        'etat'=>$mon_colis->getEtatHygro(),
                        'nb_elts'=>0,
                        'vol_elt'=>0
                    );


                }
                return  new JsonResponse(json_encode($infos_colis));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    // Infos colis en JSON
    #[Route('snvlt/colis/billons/search/{id_colis}', name: 'elements_disponible_colis.json')]
    public function elements_disponible_colis(
        ColisRepository $colisRepository,
        Request $request,
        int $id_colis,
        UserRepository $userRepository,
        User $user = null,
        ManagerRegistry $registry
    ): Response
    {

        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF'))
            {
                $user = $userRepository->find($this->getUser());

                $infos_elts = array();


                $mon_colis = $colisRepository->find($id_colis);
                if ($mon_colis){

                    $mes_elts= $registry->getRepository(ElementsDisponibleColis::class)->findBy(['id_essence'=>$mon_colis->getCodeEssence()->getId(), 'type_transformation_id'=>$mon_colis->getCodeContrat()->getTypeTransfo()->getId(), 'id_usine'=>$user->getCodeindustriel()->getId()], ['numero_bille'=>'ASC']);

                    foreach($mes_elts as $elt){
                        $infos_elts[] = array(
                            'id'=>$elt->getId(),
                            'lng'=>$elt->getLng(),
                            'lrg'=>$elt->getLrg(),
                            'ep'=>$elt->getEp(),
                            'nb'=>$elt->getNombre(),
                            'bille'=>$elt->getNumeroBille()
                        );
                    }
                }
                return  new JsonResponse(json_encode($infos_elts));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    // Infos colis en JSON
    #[Route('snvlt/colis/billons/disponible/{id_colis}', name: 'billons_disponible_colis.json')]
    public function billons_disponible_colis(
        ColisRepository $colisRepository,
        Request $request,
        int $id_colis,
        UserRepository $userRepository,
        User $user = null,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $billons = array();
                /*['id_essence'=>$mon_colis->getCodeEssence()->getId(), 'type_transformation_id'=>$mon_colis->getCodeContrat()->getTypeTransfo()->getId(), 'id_usine'=>$user->getCodeindustriel()->getId()], ['numero_bille'=>'ASC']*/


                $mon_colis = $colisRepository->find($id_colis);
                if ($mon_colis){

                    $mes_lje = $registry->getRepository(Documentlje::class)->findBy(['code_usine'=>$user->getCodeindustriel()]);
                    foreach($mes_lje as $lje){
                        $mes_pages = $registry->getRepository(Pagelje::class)->findBy(['code_doclje'=>$lje]);
                        foreach($mes_pages as $page){
                            $mes_billes = $registry->getRepository(Lignepagelje::class)->findBy(['code_pagelje'=>$page, 'essence'=>$mon_colis->getCodeEssence()], ['numero_arbre'=>'ASC']);
                            foreach($mes_billes as $bille){
                                $mes_billons = $registry->getRepository(Billon::class)->findBy(['code_lignepagelje'=>$bille, 'type_transformation'=>$mon_colis->getCodeContrat()->getTypeTransfo()]);

                                foreach($mes_billons as $billon){

                                    $elts = $registry->getRepository(Elements::class)->findBy(['code_billon'=>$billon]);

                                    foreach($elts as $elt){

                                        $elts_colis = $registry->getRepository(ElementsColis::class)->findOneBy(['code_elements'=>$elt]);
                                        if(!$elts_colis){
                                            $billons[] = array(
                                                'id'=>$elt->getId(),
                                                'lng'=>$elt->getLng(),
                                                'lrg'=>$elt->getLrg(),
                                                'ep'=>$elt->getEp(),
                                                'nb'=>$elt->getNombre(),
                                                'billon'=>$elt->getCodeBillon()->getNumeroBillon()
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }

                }
                return new JsonResponse(json_encode($billons));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }


    // Infos colis en JSON snvlt/colis/infos_elt/id/
    #[Route('snvlt/colis/infos_elt/id/{id_element}', name: 'infos_element.json')]
    public function infos_element(
        ElementsRepository $eltRepository,
        Request $request,
        int $id_element,
        UserRepository $userRepository,
        User $user = null,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $elements = array();

                $mon_element = $eltRepository->find($id_element);
                if ($mon_element){

                    $elts_colis = $registry->getRepository(ElementsColis::class)->findBy(['code_elements'=>$mon_element]);
                    $nb_elt = 0;
                    $vol_elt = 0;
                    foreach($elts_colis as $elt){
                        $nb_elt = $nb_elt + $elt->getNombre();
                        $vol_elt = round($vol_elt + $elt->getVolume(), 3);
                    }

                    $elements[] = array(
                        'id'=>$mon_element->getId(),
                        'lng'=>$mon_element->getLng(),
                        'lrg'=>$mon_element->getLrg(),
                        'ep'=>$mon_element->getEp(),
                        'nb'=>$mon_element->getNombre() - $nb_elt,
                        'billon'=>$mon_element->getCodeBillon()->getNumeroBillon()
                    );
                }
                return new JsonResponse(json_encode($elements));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

// Infos colis en JSON snvlt/colis/infos_elt/id/
    #[Route('snvlt/colis/infos_elt/id/{id_element}', name: 'infos_fiche.json')]
    public function infos_fiche(
        FicheLotProdRepository $ficheLotProdRepository,
        Request $request,
        int $id_fiche,
        UserRepository $userRepository,
        User $user = null,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $elements = array();

                $fiche = $ficheLotProdRepository->find($id_fiche);
                if ($fiche){

                    $elts_colis = $registry->getRepository(ElementsColis::class)->findBy(['code_elements'=>$fiche]);
                    $nb_elt = 0;
                    $vol_elt = 0;
                    foreach($elts_colis as $elt){
                        $nb_elt = $nb_elt + $elt->getNombre();
                        $vol_elt = round($vol_elt + $elt->getVolume(), 3);
                    }

                    $elements[] = array(
                        'id'=>$mon_element->getId(),
                        'lng'=>$mon_element->getLng(),
                        'lrg'=>$mon_element->getLrg(),
                        'ep'=>$mon_element->getEp(),
                        'nb'=>$mon_element->getNombre() - $nb_elt,
                        'billon'=>$mon_element->getCodeBillon()->getNumeroBillon()
                    );
                }
                return new JsonResponse(json_encode($elements));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
    //Ajouter un élément du colis
    #[Route('/snvlt/colis/elent/add/{data}', name: 'add_elements_colis')]
    public function add_elements_colis(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        string $data,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                //$page_brh = $pages_brh->find($id_page);
                if($data){
                    $elt_colis = new ElementsColis();

                    //Decoder le JSON BRH
                    /*obj.lng =  $("#txt_lng").val();
                    obj.lrg =  $("#txt_lrg").val();
                    obj.ep =  $("#txt_ep").val();
                    obj.nb =  $("#txt_nb").val();
                    obj.code_colis =  id_colis;
                    obj.code_element =  id_element;*/
                    $arraydata = json_decode($data);

                    $colis = $registry->getRepository(Colis::class)->find($arraydata->code_colis);
                    $elt= $registry->getRepository(Elements::class)->find($arraydata->code_element);

                    //Rechercher le colis dans le contrat
                    $elt_search = $registry->getRepository(ElementsColis::class)->findBy(['code_elements'=>$elt, 'code_colis'=>$colis]);




                    if(!$elt_search){
                        if ($colis && $elt){
                            $date_jour = new \DateTime();
                            $elt_colis->setLng((int) $arraydata->lng);
                            $elt_colis->setLrg((int) $arraydata->lrg);
                            $elt_colis->setEp((int) $arraydata->ep);
                            $elt_colis->setNombre((int) $arraydata->nb);

                            if($elt->getCodeBillon()->getTypeTransformation()->getId() == 1){ /*SCIAGE : cm*/
                                $volume = (($arraydata->lng/100) * ($arraydata->lrg/100) * ($arraydata->ep/100) * $arraydata->nb);
                            } elseif($elt->getCodeBillon()->getTypeTransformation()->getId() == 2) { /*DEROULAGE : mm*/
                                $volume = (($arraydata->lng/100) * ($arraydata->lrg/100) * ($arraydata->ep/1000) * $arraydata->nb);
                            } elseif ($elt->getCodeBillon()->getTypeTransformation()->getId() == 3){ /*TRANCHAGE : mm*/
                                $volume = (($arraydata->lng/100) * ($arraydata->lrg/100) * ($arraydata->ep/1000) * $arraydata->nb);
                            }

                            $elt_colis->setVolume($volume);
                            $elt_colis->setCodeColis($colis);
                            $elt_colis->setCodeElements($elt);

                            $elt_colis->setCreatedAt($date_jour);
                            $elt_colis->setCreatedBy($user);

                            $registry->getManager()->persist($elt_colis);
                            $registry->getManager()->flush();

                            return  new JsonResponse(json_encode("ELEMENT_SAVED"));
                        } else {
                            return  new JsonResponse(json_encode("NO_PACKAGE_NO_ELEMENT"));
                        }
                    } else {
                        return  new JsonResponse(json_encode("ELEMENT_EXISTING"));
                    }

                } else {
                    return  new JsonResponse(json_encode("INVALID_DATA"));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    //Ajouter un élément du colis
    #[Route('/snvlt/colis/elent/add/deroulage/{data}', name: 'add_elements_colis_deroulage')]
    public function add_elements_colis_deroulage(
        Request $request,
        UserRepository $userRepository,
        string $data,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                //$page_brh = $pages_brh->find($id_page);
                if($data){
                    $elt_colis = new ElementsColis();

                    $arraydata = json_decode($data);

                    $colis = $registry->getRepository(Colis::class)->find($arraydata->code_colis);
                    $elt= $registry->getRepository(Elements::class)->find($arraydata->code_element);
                    $fiche_lot_prod = $registry->getRepository(FicheLotProd::class)->find($arraydata->fiche_lot_prod);
                    //Rechercher le colis dans le contrat
                    $elt_search = $registry->getRepository(ElementsColis::class)->findBy(['code_elements'=>$elt, 'code_colis'=>$colis]);




                            $date_jour = new \DateTime();
                            $elt_colis->setLng((int) $arraydata->lng);
                            $elt_colis->setLrg((int) $arraydata->lrg);
                            $elt_colis->setEp((int) $arraydata->ep);

                            //$nb_el= ($arraydata->nb) / (($arraydata->lng/100) * ($arraydata->lrg/100) * ($arraydata->ep/1000));


                            $elt_colis->setNombre($arraydata->nb);
                            $volume = ($arraydata->lng/100) * ($arraydata->lrg/100) * ($arraydata->ep/1000) * ($arraydata->nb);

                            $elt_colis->setVolume($volume);

                            $elt_colis->setCodeColis($colis);
                            $elt_colis->setCodeElements($elt);

                            if($fiche_lot_prod){
                                $elt_colis->setCodeFicheLotProd($fiche_lot_prod);
                            }


                            $elt_colis->setCreatedAt($date_jour);
                            $elt_colis->setCreatedBy($user);

                            $registry->getManager()->persist($elt_colis);
                            $registry->getManager()->flush();

                            return  new JsonResponse(json_encode("ELEMENT_SAVED"));



                } else {
                    return  new JsonResponse(json_encode("INVALID_DATA"));
                }
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    //Ajouter un élément du colius
    #[Route('/snvlt/colis/clp/{id_colis}', name: 'cloturer_colis')]
    public function cloture_colis(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_colis,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                //$page_brh = $pages_brh->find($id_page);
                $mon_colis = $registry->getRepository(Colis::class)->find($id_colis);
                if($mon_colis){
                    $mon_colis->setUpdatedBy($user);
                    $mon_colis->setUpdatedAt(new \DateTime());
                    $mon_colis->setCloture(true);

                    $registry->getManager()->persist($mon_colis);
                    $registry->getManager()->flush();

                    return  new JsonResponse(json_encode("PACKAGE_COMPLETE"));
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/recherche_dimensions/{lng}/{lrg}/{ep}/{id_colis}', name: 'rechercher_dimensions')]
    public function rechercher_dimensions(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $lng,
        int $lrg,
        int $ep,
        int $id_colis,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $liste_fiche_lot_prod = array();
                $ficheLotProds = $registry->getRepository(FicheLotProd::class)->findBy(['code_usine' => $user->getCodeindustriel()]);
                $colis = $registry->getRepository(Colis::class)->find($id_colis);

                if ($colis){
                    foreach ($ficheLotProds as $ficheLotProd) {
                        //Recherche les dimensions
                        $essence = $colis->getCodeEssence();
                        $transfo = $colis->getCodeContrat()->getTypeTransfo();
                        $dimensions = $registry->getRepository(Elements::class)->findBy(['lng' => (int)$lng, 'lrg' => (int)$lrg, 'ep' => (int)$ep, 'code_fiche_prod' => $ficheLotProd, 'code_essence'=>$essence, 'code_type_transfo'=>$transfo]);
                        if ($dimensions){
                            $liste_fiche_lot_prod[] = array(
                                'id_fiche'=>$ficheLotProd->getId(),
                                'numero'=>$ficheLotProd->getDateFiche()->format('d/m/Y')
                            );
                        }
                    }
                    return  new JsonResponse(json_encode($liste_fiche_lot_prod));
                }


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/fiche_lot/op/infos/{id_fiche}/{id_colis}/{lng}/{lrg}/{ep}', name: 'recherche_fiche_lot_prod_infos')]
    public function recherche_fiche_lot_prod_infos(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_fiche,
        int $id_colis,
        int $lng,
        int $lrg,
        int $ep,
        ManagerRegistry $registry
    ): Response
    {
        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $liste_fiche_lot_prod = array();
                $ficheLotProds = $registry->getRepository(FicheLotProd::class)->find($id_fiche);
                $colis = $registry->getRepository(Colis::class)->find($id_colis);
                $volume_elts = 0;
                $nb_elts = 0;
                $liste_elts = $registry->getRepository(Elements::class)->findBy([
                    'code_fiche_prod'=>$ficheLotProds,
                    'code_type_transfo'=>$colis->getCodeContrat()->getTypeTransfo(),
                    'code_essence'=>$colis->getCodeEssence(),
                    'lng'=>$lng,
                    'lrg'=>$lrg,
                    'ep'=>$ep
                ]);
                foreach($liste_elts as $elt){
                    $volume_elts = $volume_elts + $elt->getVolume();
                    $nb_elts = $nb_elts + $elt->getNombre();
                }

                $liste_fiche_lot_prod[] = array(
                    'nb'=>$nb_elts,
                    'volume'=>$volume_elts
                );

                return  new JsonResponse(json_encode($liste_fiche_lot_prod));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

}
