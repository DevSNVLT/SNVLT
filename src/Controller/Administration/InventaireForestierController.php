<?php

namespace App\Controller\Administration;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\Utils;
use App\Entity\Admin\LogSnvlt;
use App\Entity\Administration\FicheProspection;
use App\Entity\Administration\InventaireForestier;
use App\Entity\Administration\Notification;
use App\Entity\Administration\ProspectionTemp;
use App\Entity\Autorisation\Attribution;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Saisie\Lignepagecp;
use App\Entity\Groupe;
use App\Entity\Menu;
use App\Entity\MenuPermission;
use App\Entity\References\Cantonnement;
use App\Entity\References\Essence;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\References\TypeOperateur;
use App\Entity\References\Usine;
use App\Entity\References\ZoneHemispherique;
use App\Entity\User;
use App\Events\Administration\AddFicheProspectionEvent;
use App\Events\Autorisation\AddAttributionEvent;
use App\Form\Administration\FicheInvType;
use App\Form\Administration\FicheProspectionType;
use App\Form\References\ExploitantType;
use App\Repository\Administration\InventaireForestierRepository;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\ForetRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use League\Csv\Reader;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class InventaireForestierController extends AbstractController
{


    public function __construct(private SluggerInterface $slugger,
                                private EventDispatcherInterface $dispatcher,
                                private TranslatorInterface $translator,
                                private AdministrationService $administrationService,
                                private Utils $utils,
                                private ManagerRegistry $registry)
    {
    }

    #[Route('snvlt/admin/prospect/{id_attribution?0}', name: 'app_inventaire')]
    public function index(
        Request                       $request,
        MenuRepository                $menus,
        MenuPermissionRepository      $permissions,
        UserRepository                $userRepository,
        int                           $id_attribution,
        ManagerRegistry               $registry,
        NotificationRepository        $notification
    ): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')) {
                    $user = $userRepository->find($this->getUser());
                    $code_groupe = $user->getCodeGroupe()->getId();

                    $attribution = $registry->getRepository(Attribution::class)->find($id_attribution);
                    if ($this->isGranted('ROLE_EXPLOITANT')){
                        $mes_attributions = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$user->getCodeexploitant()]);
                    } else {
                        $mes_attributions = $registry->getRepository(Attribution::class)->findAll();
                    }

                /*if ($attribution) {*/
                        $fiche_prospection =new FicheProspection();

                        $form = $this->createForm(FicheProspectionType::class, $fiche_prospection);

                        $form->handleRequest($request);

                        if ($form->isSubmitted() && $form->isValid()) {

                            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')){
                                if ($this->isGranted('ROLE_EXPLOITANT')){
                                    $donnees_attibution = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$user->getCodeexploitant()]);
                                } else {
                                    $attri =(int) $form->get('code_attribution')->getViewData();
                                    //dd($attri);
                                    if($attri > 0){
                                        $exp=$registry->getRepository(Attribution::class)->find($attri)->getCodeExploitant();
                                        $donnees_attibution = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$exp]);
                                    }

                                }
                                if($donnees_attibution){
                                    foreach ($donnees_attibution as $att){
                                        $donnees_fiches = $registry->getRepository(FicheProspection::class)->findBy(['code_attribution'=>$att]);
                                        //dd($donnees_fiches);
                                        if($donnees_fiches){
                                            foreach ($donnees_fiches as $fiche){
                                                $donnees_prospection = $registry->getRepository(ProspectionTemp::class)->findBy(['code_fichep'=>$fiche]);
                                                if($donnees_prospection){
                                                    foreach ($donnees_prospection as $prospection){
                                                        $registry->getManager()->remove($prospection);

                                                    }

                                                }
                                                //dd($fiche->getInventaireForestiers()->count());
                                                if($fiche->getInventaireForestiers()->count() == 0){
                                                    $registry->getManager()->remove($fiche);
                                                }

                                            }
                                        }
                                    }
                                }
                            }

                            $registry->getManager()->flush();

                            $createdDate = new \DateTimeImmutable();

                            $fichier = $form->get('fichier')->getData();
                            //dd($form->get('code_attribution')->getData());
                            $attribution = $registry->getRepository(Attribution::class)->find($form->get('code_attribution')->getData());
                            if ($fichier) {$originalFilename = pathinfo($fichier->getClientOriginalName(), PATHINFO_FILENAME);
                                // this is needed to safely include the file name as part of the URL
                                $safeFilename = $this->slugger->slug($originalFilename);
                                $newFilename =$this->utils->uniqidReal(30).'.'.$fichier->guessExtension();

                                // Move the file to the directory where brochures are stored
                                try {
                                    $fichier->move(
                                        $this->getParameter('prospections_csv_directory'),
                                        $newFilename
                                    );
                                    //sleep(4);
                                } catch (FileException $e) {
                                    // ... handle exception if something happens during file upload
                                }

                                // updates the 'brochureFilename' property to store the PDF file name
                                // instead of its contents
                                $fiche_prospection->setFichier($newFilename);
                                $fiche_prospection->setLienComplet($this->getParameter('prospections_csv_directory'). "/".$newFilename);
                            }

                            //dd($this->getParameter('prospections_csv_directory'),);

                            $fiche_prospection->setCreatedAt($createdDate);
                            $fiche_prospection->setCreatedBy($user);

                            $fiche_prospection->setCodeAttribution($attribution);
                            if ($this->isGranted('ROLE_EXPLOITANT')){
                                $fiche_prospection->setCodeExploitant($user->getCodeexploitant());
                            } else {
                                //dd($exp);
                                $fiche_prospection->setCodeExploitant($exp);
                            }


                            $manager = $registry->getManager();
                            $manager->persist($fiche_prospection);
                            $manager->flush();

                            // Créé l'évènement d'enregistrement du contenu du fichier dans la table ProspectionTem
                            $addProspectionEvent = new AddFicheProspectionEvent($fiche_prospection);

                            //Dispatcher l'evenement
                            $this->dispatcher->dispatch($addProspectionEvent, AddFicheProspectionEvent::ADD_FICHE_PROSPECTION_EVENT);


                            return $this->redirectToRoute("app_inventaire");
                        } else {

                            return $this->render('administration/inventaire_forestier/inventaire.html.twig',
                                [
                                    'liste_menus' => $menus->findOnlyParent(),
                                    "all_menus" => $menus->findAll(),
                                    'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
                                    'mes_notifs' => $notification->findBy(['to_user' => $user, 'lu' => false], [], 5, 0),
                                    'groupe' => $code_groupe,
                                    'mes_attributions' => $mes_attributions,
                                    'liste_parent' => $permissions,
                                    'form'=>$form->createView(),
                                    'operateurs'=>$this->registry->getRepository(Exploitant::class)->findAll()

                                ]);
                        }
                    } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }#[Route('snvlt/admin/inventaire', name: 'inventaire_admin')]
    public function inventaire_admin(
        Request                       $request,
        MenuRepository                $menus,
        MenuPermissionRepository      $permissions,
        UserRepository                $userRepository,
        ManagerRegistry               $registry,
        NotificationRepository        $notification
    ): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')) {
                    $user = $userRepository->find($this->getUser());
                    $code_groupe = $user->getCodeGroupe()->getId();

                $fiche_prospection = new FicheProspection();
                $form = $this->createForm(FicheInvType::class, $fiche_prospection);

                $form->handleRequest($request);

//                if ($form->isSubmitted() && $form->isValid()) {
//                }

                    return $this->render('administration/inventaire_forestier/inventaire_admin.html.twig',
                                [
                                    'liste_menus' => $menus->findOnlyParent(),
                                    "all_menus" => $menus->findAll(),
                                    'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
                                    'mes_notifs' => $notification->findBy(['to_user' => $user, 'lu' => false], [], 5, 0),
                                    'groupe' => $code_groupe,
                                    'exploitants' => $registry->getRepository(Exploitant::class)->findAll(),
                                    'liste_parent' => $permissions,
                                    'form'=>$form

                                ]);
                        }

                return $this->redirectToRoute('app_no_permission_user_active');
            }
    }

    #[Route('snvlt/admin/fiche/print/{id_fiche?0}', name: 'imprime_fiche_lac')]
    public function imprime_fiche_lac(

        int                           $id_fiche,
        ManagerRegistry               $registry): Response
    {

                    $fiche = $registry->getRepository(FicheProspection::class)->find($id_fiche);
                    return $this->render('administration/inventaire_forestier/fiche_print.twig',
                        [
                            'fiche'=>$fiche,

                        ]);
    }

    #[Route('snvlt/admin/lac_temp', name: 'lac_temp')]
    public function lac_temp(
        Request                       $request,
        UserRepository                $userRepository,
        ManagerRegistry               $registry): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $inventaire_temp = array();
                $fiche = $registry->getRepository(FicheProspection::class)->findOneBy(['code_exploitant'=>$user->getCodeexploitant()], ['id'=>'DESC']);

                if($fiche){
                    $donnees_temp =  $registry->getRepository(ProspectionTemp::class)->findBy(['code_fichep'=>$fiche], ['id'=>'ASC']);
                    foreach ($donnees_temp as $donnee){

                        $nom_essence = $registry->getRepository(Essence::class)->findOneBy(['numero_essence'=>$donnee->getCodeEssence()]);
                        $essence = $donnee->getCodeEssence();
                        if ($nom_essence){
                            $essence = $nom_essence->getNomVernaculaire();
                        }
                        $inventaire_temp[] = array(
                            'id_inv'=>$donnee->getId(),
                            'essence'=>$essence,
                            'zh'=>$donnee->getZoneH(),
                            'x'=>$donnee->getX(),
                            'y'=>$donnee->getY(),
                            'lng'=>$donnee->getLng(),
                            'dm'=>$donnee->getDm(),
                            'volume'=>$donnee->getVolume(),
                            'code_att'=>$fiche->getCodeAttribution()->getId(),
                            'lac'=>$donnee->isLac(),
                            'erreur'=>$donnee->isHasError(),
                            'motif'=>$donnee->getMotifError()
                        );
                    }
                }


                return  new JsonResponse(json_encode($inventaire_temp));

                } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/admin/arbres/no_lac/{id_attribution}', name: 'arbres_no_lac')]
    public function arbres_no_lac(
        Request                       $request,
        UserRepository                $userRepository,
        ManagerRegistry               $registry,
        int                           $id_attribution): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT')) {
                $user = $userRepository->find($this->getUser());

                $inventaire_temp = array();

                $foret = $registry->getRepository(Attribution::class)->find($id_attribution)->getCodeForet();

                if ($foret){
                    $inv_no_lac = $registry->getRepository(InventaireForestier::class)->findBy(['code_foret'=>$foret, 'lac'=>false]);
                    foreach ($inv_no_lac as $donnee){
                        $inventaire_temp[] = array(
                            'id_inv'=>$donnee->getId(),
                            'numero'=>$donnee->getNumeroArbre(),
                            'essence'=>$donnee->getCodeEssence()->getNomVernaculaire(),
                            'zh'=>$donnee->getZoneH()->getZone(),
                            'x'=>$donnee->getX(),
                            'y'=>$donnee->getY(),
                            'lng'=>$donnee->getLng(),
                            'dm'=>$donnee->getDm(),
                            'volume'=>$donnee->getVolume()
                        );
                    }
                }


                return  new JsonResponse(json_encode($inventaire_temp));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/admin/prospect/add/', name: 'app_inventaire_add')]
    public function app_inventaire_add(
        Request                       $request,
        UserRepository                $userRepository,
        ManagerRegistry               $registry,
        MailerInterface               $mailer): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $inventaire_temp = array();
                $fiche = $registry->getRepository(FicheProspection::class)->findOneBy(['code_exploitant'=>$user->getCodeexploitant()], ['id'=>'DESC']);

                if($fiche){
                    $donnees_temp =  $registry->getRepository(ProspectionTemp::class)->findBy(['code_fichep'=>$fiche]);
                    $hesErreurs = false;
                    foreach ($donnees_temp as $donnee){
                        if ($donnee->isHasError()){
                            $hesErreurs = true;
                        }
                    }

                    if (!$hesErreurs){
                        foreach ($donnees_temp as $donnee){

                            $essence = $registry->getRepository(Essence::class)->findOneBy(['numero_essence'=>$donnee->getCodeEssence()]);
                            $foret = $registry->getRepository(Foret::class)->findOneBy(['numero_foret'=>$donnee->getForet()]);
                            $zone = $registry->getRepository(ZoneHemispherique::class)->findOneBy(['zone'=>$donnee->getZoneH()]);

                            // crée une ligne inventaire
                            $tbl_inventaire = new InventaireForestier();

                            $tbl_inventaire->setCodeEssence($essence);
                            $tbl_inventaire->setCodeForet($foret);

                            $tbl_inventaire->setCreatedAt(new \DateTimeImmutable());
                            $tbl_inventaire->setCreatedBy($user);
                            $tbl_inventaire->setLng($donnee->getLng());
                            $tbl_inventaire->setDm($donnee->getDm());
                            $tbl_inventaire->setVolume($donnee->getVolume());

                            $tbl_inventaire->setX($donnee->getX());
                            $tbl_inventaire->setY($donnee->getY());
                            $tbl_inventaire->setZoneh($zone);
                            $tbl_inventaire->setNumeroArbre($donnee->getNumero());

                            $tbl_inventaire->setValide(true);

                            $tbl_inventaire->setLac($donnee->isLac());
                            $tbl_inventaire->setCodeFicheProspection($fiche);


                            $registry->getManager()->persist($tbl_inventaire);

                        }

                        foreach ($donnees_temp as $donnee){
                            $registry->getManager()->remove($donnee);
                        }

                        $fiche->setValide(false);
                        $registry->getManager()->persist($fiche);

                        $registry->getManager()->flush();



                        //Envoi d'une notification aux utilisateurs utilisant le menu <<VALIDATION LAAC>>

                        /*$menu_lac = $registry->getRepository(Menu::class)->findOneBy(['classname_menu'=>'admin_validation_lac']);
                        if ($menu_lac){*/
                            // Recherche tous les groupes qui utilisent ce menu
                            $critere = 'admin_validation_lac';
                            $liste_groupe = $registry->getRepository(MenuPermission::class)->findBy(['classname_menu'=>$critere]);
                            //dd($liste_groupe);
                            foreach ($liste_groupe as $groupe_permission){
                                $groupe = $registry->getRepository(Groupe::class)->find($groupe_permission->getCodeGroupe());
                                // Cherche les utilisateurs du groupe
                                $liste_users = $registry->getRepository(User::class)->findBy(['code_groupe'=>$groupe]);
                                //dd($liste_users);
                                foreach($liste_users as $utilisateur){

                                    $sujet = "LAAC ". $fiche->getCodeAttribution()->getCodeForet()->getDenomination() ." A VALIDER";

                                    $message = " la structure " . $fiche->getCodeExploitant()->getSigle() . " vous a envoyé son inventaire forestier pour validation en relation avec la forêt dénommée ". $fiche->getCodeAttribution()->getCodeForet()->getDenomination();

                                    // envoi d'une notification App à l'administration
                                        $this->utils->envoiNotification(
                                            $registry,
                                            $sujet,
                                            $message,
                                            $utilisateur,
                                            $user->getId(),
                                            'infos_admin_fiche_lac',
                                            'INVENTAIRE_FORESTIER',
                                            $fiche->getId()
                                        );

                                    // envoi d'un email
                                   /* $this->utils->sendEmail(
                                        $utilisateur->getEmail(),
                                        $sujet,
                                        $message
                                    );*/

                                    $email = (new TemplatedEmail())
                                        ->from(new Address('snvlt@system2is.com', 'SNVLT INFOS'))
                                        ->to($utilisateur->getEmail())
                                        ->subject($sujet)
                                        ->htmlTemplate('emails/lac.html.twig')
                                        ->context([
                                            'message' => $message,
                                            'utilisateur'=>$utilisateur
                                        ])
                                    ;

                                    $mailer->send($email);

                                }
                            }
                        /*}*/

                        $inventaire_temp[] = array(
                            'code'=>'SUCCESS'
                        );

                    } else {
                        $inventaire_temp[] = array(
                            'code'=>'FILE_ERROR'
                        );
                    }

                }


                return  new JsonResponse(json_encode($inventaire_temp));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('snvlt/admin/prospect/check/', name: 'app_inventaire_check')]
    public function app_inventaire_check(
        Request                       $request,
        UserRepository                $userRepository,
        ManagerRegistry               $registry): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $inventaire_temp = array();
                $fiche = $registry->getRepository(FicheProspection::class)->findOneBy(['code_exploitant'=>$user->getCodeexploitant()], ['id'=>'DESC']);

                if($fiche){
                    $donnees_temp =  $registry->getRepository(ProspectionTemp::class)->findBy(['code_fichep'=>$fiche]);
                    $hesErreurs = false;
                    foreach ($donnees_temp as $donnee){
                        if ($donnee->isHasError()){
                            $hesErreurs = true;
                        }
                    }

                    if (!$hesErreurs){
                        $inventaire_temp[] = array(
                            'code'=>'FILE_SUCCESS'
                        );
                    }

                }


                return  new JsonResponse(json_encode($inventaire_temp));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('snvlt/admin/inventaire_list/{code_attibution}', name: 'inventaire_list')]
    public function inventaire_list(
        Request                       $request,
        UserRepository                $userRepository,
        int                           $code_attibution,
        ManagerRegistry               $registry): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $inventaire_temp = array();
                $exp = "-";
				//dd($registry->getRepository(Attribution::class)->find($code_attibution));
                if($registry->getRepository(Attribution::class)->find($code_attibution)->getCodeExploitant()->getSigle()){
                    $exp = $registry->getRepository(Attribution::class)->find($code_attibution)->getCodeExploitant()->getSigle();
                }
                 //dd($code_attibution);
                //dd($registry->getRepository(Attribution::class)->find($code_attibution));
                    $donnees_inventaire =  $registry->getRepository(InventaireForestier::class)->findBy(['code_foret'=>$registry->getRepository(Attribution::class)->find($code_attibution)->getCodeForet()], ['id'=>'DESC']);
					//dd($registry->getRepository(Attribution::class)->find($code_attibution)->getCodeForet());
                    foreach ($donnees_inventaire as $donnee){
                        if($donnee->getCodeEssence()){
                            $essence = $donnee->getCodeEssence()->getNomVernaculaire();
                        } else {
                            $essence = "-";
                        }

                        if($donnee->getZoneh()){
                            $zone = $donnee->getZoneh()->getZone();
                        } else {
                            $zone = "-";
                        }

                        $numero_cp_arbre = "";
                        $cp_arbre = $registry->getRepository(Lignepagecp::class)->findOneBy(['code_inv'=>$donnee->getId()]);

                        if ($cp_arbre){
                            $numero_cp_arbre = $cp_arbre->getNumeroArbrecp();
                        }
						$dateinve = "-";
						if ($donnee->getCodeFicheProspection()->getDateInventaire()){
                            $dateinve = $donnee->getCodeFicheProspection()->getDateInventaire()->format('d/m/Y');
                        }
						
                        $inventaire_temp[] = array(
                            'id_inv'=>$donnee->getId(),
                            'numero'=>$donnee->getNumeroArbre(),
                            'essence'=>$essence,
                            'zh'=>$zone,
                            'x'=>$donnee->getX(),
                            'y'=>$donnee->getY(),
                            'lng'=>$donnee->getLng(),
                            'dm'=>$donnee->getDm(),
                            'volume'=>$donnee->getVolume(),
                            'lac'=>$donnee->isLac(),
                            'cp'=>$donnee->isCp(),
                            'exp'=>$exp,
                            'cp_arbre'=>$numero_cp_arbre,
                            'statut'=>$donnee->isValide(),
                            'autorise'=>$donnee->getCodeEssence()->isAutorisation(),
                            'date_inv'=>$dateinve
                        );
                    }



                return  new JsonResponse(json_encode($inventaire_temp));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('snvlt/admin/inv/archives', name: 'archives_list')]
        public function archives_list(
            Request                       $request,
            UserRepository                $userRepository,
            ManagerRegistry               $registry): Response
        {

            if (!$request->getSession()->has('user_session')) {
                return $this->redirectToRoute('app_login');
            } else {
                if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF')) {
                    $user = $userRepository->find($this->getUser());
                    $code_groupe = $user->getCodeGroupe()->getId();

                    $inventaire_temp = array();


                    $atts = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$user->getCodeexploitant()]);
                    foreach ($atts as $att){
                        $donnees_inventaire =  $registry->getRepository(InventaireForestier::class)->findBy(['code_foret'=>$att->getCodeForet(), 'lac'=>false]);
                        foreach ($donnees_inventaire as $inv){
                            $jour = new \DateTime();
                            $nb_jours = date_diff($jour, $inv->getCodeFicheProspection()->getDateInventaire())->days;
                            //dd($nb_jours);
                            if ($nb_jours >= 730){

                                if($inv->getCodeEssence()){
                                    $essence = $inv->getCodeEssence()->getNomVernaculaire();
                                } else {
                                    $essence = "-";
                                }

                                if($inv->getZoneh()){
                                    $zone = $inv->getZoneh()->getZone();
                                } else {
                                    $zone = "-";
                                }


                                $inventaire_temp[] = array(
                                    'id_inv'=>$inv->getId(),
                                    'numero'=>$inv->getNumeroArbre(),
                                    'essence'=>$essence,
                                    'zh'=>$zone,
                                    'x'=>$inv->getX(),
                                    'y'=>$inv->getY(),
                                    'lng'=>$inv->getLng(),
                                    'dm'=>$inv->getDm(),
                                    'volume'=>$inv->getVolume(),
                                    'date_inv'=>$inv->getCodeFicheProspection()->getDateInventaire()->format('d/m/Y'),
                                    'foret'=>$att->getCodeForet()->getDenomination()
                                );

                            }
                        }
                    }






                    return  new JsonResponse(json_encode($inventaire_temp));

                } else {
                    return $this->redirectToRoute('app_no_permission_user_active');
                }

            }
        }

    #[Route('snvlt/admin/inventaire_list/lac/{code_attibution}', name: 'inventaire_lac_list')]
    public function inventaire_lac_list(
        Request                       $request,
        UserRepository                $userRepository,
        int $code_attibution,
        ManagerRegistry               $registry): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $inventaire_temp = array();
                //dd($code_attibution);
                //dd($registry->getRepository(Attribution::class)->find($code_attibution));
                $donnees_inventaire =  $registry->getRepository(InventaireForestier::class)->findBy([
                    'code_foret'=>$registry->getRepository(Attribution::class)->find($code_attibution)->getCodeForet(),
                    'lac'=>true,
                    'cp'=>null,
                    'valide'=>true
                ],
                    [
                        'numero_arbre'=>'asc'
                    ]);

                foreach ($donnees_inventaire as $donnee){
                    if($donnee->getCodeEssence()){
                        $essence = $donnee->getCodeEssence()->getNomVernaculaire();
                    } else {
                        $essence = "-";
                    }

                    if($donnee->getZoneh()){
                        $zone = $donnee->getZoneh()->getZone();
                    } else {
                        $zone = "-";
                    }
                    if($donnee->getCodeEssence()->isAutorisation()){
                        $inventaire_temp[] = array(
                            'id_inv'=>$donnee->getId(),
                            'numero'=>$donnee->getNumeroArbre(),
                            'essence'=>$essence,
                            'zh'=>$zone,
                            'x'=>$donnee->getX(),
                            'y'=>$donnee->getY(),
                            'lng'=>$donnee->getLng(),
                            'dm'=>$donnee->getDm(),
                            'volume'=>$donnee->getVolume(),
                            'lac'=>$donnee->isLac()
                        );
                    }
                }



                return  new JsonResponse(json_encode($inventaire_temp));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('snvlt/admin/mon_inventaire/{code_exploitant}', name: 'mon_inv_exp')]
    public function mon_inv_exp(
        Request                       $request,
        UserRepository                $userRepository,
        int                           $code_exploitant,
        ManagerRegistry               $registry): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT')) {

                $inventaire_temp = array();
                $exp = $registry->getRepository(Exploitant::class)->find($code_exploitant);
                if ($exp){
                    $mes_att = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$exp]);
                    foreach ($mes_att as $att){
                        $donnees_inventaire =  $registry->getRepository(InventaireForestier::class)->findBy(['code_foret'=>$att->getCodeForet()]);
                        foreach ($donnees_inventaire as $donnee){
                            if($donnee->getCodeEssence()){
                                $essence = $donnee->getCodeEssence()->getNomVernaculaire();
                            } else {
                                $essence = "-";
                            }

                            if($donnee->getZoneh()){
                                $zone = $donnee->getZoneh()->getZone();
                            } else {
                                $zone = "-";
                            }

                            $numero_cp_arbre = "";
                            $cp_arbre = $registry->getRepository(Lignepagecp::class)->findOneBy(['code_inv'=>$donnee->getId()]);

                            if ($cp_arbre){
                                $numero_cp_arbre = $cp_arbre->getNumeroArbrecp();
                            }
                            $inventaire_temp[] = array(
                                'id_inv'=>$donnee->getId(),
                                'numero'=>$donnee->getNumeroArbre(),
                                'essence'=>$essence,
                                'zh'=>$zone,
                                'x'=>$donnee->getX(),
                                'y'=>$donnee->getY(),
                                'lng'=>$donnee->getLng(),
                                'dm'=>$donnee->getDm(),
                                'volume'=>$donnee->getVolume(),
                                'lac'=>$donnee->isLac(),
                                'cp'=>$donnee->isCp(),
                                'exp'=>$exp,
                                'cp_arbre'=>$numero_cp_arbre,
                                'statut'=>$donnee->isValide(),
                                'autorise'=>$donnee->getCodeEssence()->isAutorisation(),
                                'foret'=>$att->getCodeForet()->getDenomination(),
                                'date_inv'=>$donnee->getCodeFicheProspection()->getDateInventaire()->format('d/m/Y')
                            );
                        }
                    }
                }






                return  new JsonResponse(json_encode($inventaire_temp));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('/snvlt/inventaires/l/atributions_inventaire', name:'atributions_inventaire')]
    public function atributions_inventaire(
        ManagerRegistry $registry,
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT')){
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $liste_attributions = array();

                $mes_attributions = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$user->getCodeexploitant()]);

                foreach ($mes_attributions as $attribution){
                    $liste_attributions[] = array(
                        'id_attribution'=>$attribution->getId(),
                        'denomination'=>$attribution->getCodeForet()->getDenomination()
                    );
                }
                return new JsonResponse(json_encode($liste_attributions));


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/inventaire/admin/s_atributions_inventaire/{id_exploitant}', name:'s_atributions_inventaire')]
    public function s_atributions_inventaire(
        ManagerRegistry $registry,
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        int $id_exploitant
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')){
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $liste_attributions = array();
                $exp = $registry->getRepository(Exploitant::class)->find($id_exploitant);
                $mes_attributions = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$exp, 'statut'=>true]);

                foreach ($mes_attributions as $attribution){
                    $liste_attributions[] = array(
                        'id_attribution'=>$attribution->getId(),
                        'denomination'=>$attribution->getCodeForet()->getDenomination()
                    );
                }
                return new JsonResponse(json_encode($liste_attributions));


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/inventaires/l/atributions_inventaire/admin', name:'atributions_inventaire_admin')]
    public function atributions_inventaire_admin(
        ManagerRegistry $registry,
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF')){
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $liste_attributions = array();

                $mes_attributions = $registry->getRepository(Attribution::class)->findBy(['statut'=>true]);

                foreach ($mes_attributions as $attribution){
                    $liste_attributions[] = array(
                        'id_attribution'=>$attribution->getId(),
                        'denomination'=>$attribution->getCodeForet()->getDenomination()
                    );
                }

                return new JsonResponse(json_encode($liste_attributions));


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
    #[Route('/snvlt/inventaire/searchligne/{id_ligne}', name:'search_inventaire_ligne')]
    public function search_inventaire_ligne(
        ManagerRegistry $registry,
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_ligne,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT')  or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF')){
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                $ligne_json = array();

                if($id_ligne){
                    $ligne = $registry->getRepository(InventaireForestier::class)->find($id_ligne);
                    if ($ligne){
                        $ligne_json[] = array(
                            'id_ligne'=>$ligne->getId(),
                            'essence'=>$ligne->getCodeEssence()->getId(),
                            'zh'=>$ligne->getZoneh()->getId(),
                            'x'=>$ligne->getX(),
                            'y'=>$ligne->getY(),
                            'lng'=>$ligne->getLng(),
                            'dm'=>$ligne->getDm(),
                            'vol'=>$ligne->getVolume()

                        );
                    }
                }
                return new JsonResponse(json_encode($ligne_json));


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    public function telechargerFichier(): Response
    {
        // Chemin complet vers le fichier à télécharger
        $cheminFichier = '/chemin/vers/mon_fichier.txt';

        // Créez la réponse pour le téléchargement
        $response = new Response();
        $response->headers->set('Content-Type', 'text/plain');
        $response->headers->set('Content-Disposition', 'attachment; filename="mon_fichier.txt"');
        $response->setContent(file_get_contents($cheminFichier));

        return $response;
    }

    #[Route('snvlt/inventaires/admin/m', name: 'app_inventaires_admin')]
    public function visualise_inventaire(ManagerRegistry $registry,
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
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMINISTRATIF')){

                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('administration/inventaire_forestier/inventaire_admin.html.twig',
                    [
                        'liste_menus' => $menus->findOnlyParent(),
                        "all_menus" => $menus->findAll(),
                        'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
                        'mes_notifs' => $notification->findBy(['to_user' => $user, 'lu' => false], [], 5, 0),
                        'groupe' => $code_groupe,
                        'liste_parent' => $permissions,
                        'essences'=>$registry->getRepository(Essence::class)->findAll(),
                        'zones'=>$registry->getRepository(ZoneHemispherique::class)->findAll(),

                    ]);
          }
        }

    }

    #[Route('/snvlt/inventaire/admin/edit_ligne/{data}/{id_ligne}', name: 'edit_ligne_json')]
    public function edit_ligne_json(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        string $data,
        int $id_ligne,
        NotificationRepository $notification,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                //Créer le Json des données à mettre à jour
                $arraydata = json_decode($data);
                $response = array();

                $ligne = $registry->getRepository(InventaireForestier::class)->find($id_ligne);
                if ($ligne){
                    $ligne->setCodeEssence($registry->getRepository(Essence::class)->find((int) $arraydata->essence));
                    $ligne->setZoneh($registry->getRepository(ZoneHemispherique::class)->find((int) $arraydata->zh));
                    $ligne->setX($arraydata->x);
                    $ligne->setY($arraydata->y);
                    $ligne->setLng($arraydata->lng);
                    $ligne->setDm($arraydata->dm);
                    $ligne->setVolume($arraydata->vol);
                    $ligne->setUpdatedBy($user);
                    $ligne->setUpdatedAt(new \DateTime());

                   $registry->getManager()->persist($ligne);
                   $registry->getManager()->flush();


                    $response[] = array(
                        'code_brh'=>'SUCCESS',
                        'html'=>''
                    );

                }
                return  new JsonResponse(json_encode($response));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }


    }


    #[Route('/snvlt/inventaire/maj/inventaire/{id_ligne}', name:'maj_inventaire_ligne')]
    public function maj_inventaire_ligne(
        ManagerRegistry $registry,
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_ligne,
        NotificationRepository $notifications
    ){
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT')){
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                $ligne_json = array();

                if($id_ligne){
                    $ligne = $registry->getRepository(InventaireForestier::class)->find($id_ligne);
                    if ($ligne){

                        $ligne->setCp(true);

                        $registry->getManager()->persist($ligne);
                        $registry->getManager()->flush();

                        $ligne_json[] = array(
                            'code'=>"SUCCESS"
                        );
                    }
                }
                return new JsonResponse(json_encode($ligne_json));


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/admin/inventaire/validation', name: 'admin_validation_lac')]
    public function validation_lac(
        Request                       $request,
        MenuRepository                $menus,
        MenuPermissionRepository      $permissions,
        GroupeRepository              $groupeRepository,
        UserRepository                $userRepository,
        ManagerRegistry               $registry,
        NotificationRepository        $notification,
        InventaireForestierRepository $inventaires): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                    return $this->render('administration/inventaire_forestier/validation_lac.html.twig',
                        [
                            'liste_menus' => $menus->findOnlyParent(),
                            "all_menus" => $menus->findAll(),
                            'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
                            'mes_notifs' => $notification->findBy(['to_user' => $user, 'lu' => false], [], 5, 0),
                            'groupe' => $code_groupe,
                            'liste_parent' => $permissions,
                            'liste_fiches'=>$registry->getRepository(FicheProspection::class)->findBy([], ['created_at'=>'DESC'])
                        ]);
                } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/admin/inv/valid_lac', name: 'app_valid_fiche_lac')]
    public function valider_lac(
        Request                       $request,
        MenuRepository                $menus,
        MenuPermissionRepository      $permissions,
        GroupeRepository              $groupeRepository,
        UserRepository                $userRepository,
        ManagerRegistry               $registry,
        NotificationRepository        $notification,
        InventaireForestierRepository $inventaires): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                    return $this->render('administration/inventaire_forestier/validation_lac.html.twig',
                        [
                            'liste_menus' => $menus->findOnlyParent(),
                            "all_menus" => $menus->findAll(),
                            'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
                            'mes_notifs' => $notification->findBy(['to_user' => $user, 'lu' => false], [], 5, 0),
                            'groupe' => $code_groupe,
                            'liste_parent' => $permissions
                        ]);
                } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/admin/inv/lac/{id_fiche}', name: 'app_valid_fiche_lac_idfiche')]
    public function valider_fiche_lac(
        Request                       $request,
        MenuRepository                $menus,
        MenuPermissionRepository      $permissions,
        GroupeRepository              $groupeRepository,
        UserRepository                $userRepository,
        ManagerRegistry               $registry,
        int                           $id_fiche,
        NotificationRepository        $notification,
        InventaireForestierRepository $inventaires): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $fiche = $registry->getRepository(FicheProspection::class)->find($id_fiche);

                return $this->render('administration/inventaire_forestier/validation_details_lac.html.twig',
                    [
                        'liste_menus' => $menus->findOnlyParent(),
                        "all_menus" => $menus->findAll(),
                        'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
                        'mes_notifs' => $notification->findBy(['to_user' => $user, 'lu' => false], [], 5, 0),
                        'groupe' => $code_groupe,
                        'liste_parent' => $permissions,
                        'fiche'=>$fiche
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    #[Route('snvlt/admin/inv/lac/infos/{id_notification}', name: 'infos_fiche_lac')]
    public function infos_fiche_lac(
        Request                       $request,
        MenuRepository                $menus,
        MenuPermissionRepository      $permissions,
        GroupeRepository              $groupeRepository,
        UserRepository                $userRepository,
        ManagerRegistry               $registry,
        int                           $id_notification,
        NotificationRepository        $notification,
        InventaireForestierRepository $inventaires): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();



                $notif = $registry->getRepository(Notification::class)->find($id_notification);
                if ($notif){
                    $fiche = $registry->getRepository(FicheProspection::class)->find($notif->getRelatedToId());
                } else {
                    $fiche = new FicheProspection();
                }


                return $this->render('administration/inventaire_forestier/notification_details_lac.html.twig',
                    [
                        'liste_menus' => $menus->findOnlyParent(),
                        "all_menus" => $menus->findAll(),
                        'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
                        'mes_notifs' => $notification->findBy(['to_user' => $user, 'lu' => false], [], 5, 0),
                        'groupe' => $code_groupe,
                        'liste_parent' => $permissions,
                        'fiche'=>$fiche
                    ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/admin/inv/lac/infos/admin/{id_notification}', name: 'infos_admin_fiche_lac')]
    public function infos_admin_fiche_lac(
        Request                       $request,
        MenuRepository                $menus,
        MenuPermissionRepository      $permissions,
        GroupeRepository              $groupeRepository,
        UserRepository                $userRepository,
        ManagerRegistry               $registry,
        int                           $id_notification,
        NotificationRepository        $notification,
        InventaireForestierRepository $inventaires): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();



                $notif = $registry->getRepository(Notification::class)->find($id_notification);
                if ($notif){
                    $fiche = $registry->getRepository(FicheProspection::class)->find($notif->getRelatedToId());
                } else {
                    $fiche = new FicheProspection();
                }


                return $this->render('administration/inventaire_forestier/validation_details_lac.html.twig',
                    [
                        'liste_menus' => $menus->findOnlyParent(),
                        "all_menus" => $menus->findAll(),
                        'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
                        'mes_notifs' => $notification->findBy(['to_user' => $user, 'lu' => false], [], 5, 0),
                        'groupe' => $code_groupe,
                        'liste_parent' => $permissions,
                        'fiche'=>$fiche
                    ]);


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/inv/validate/id_inv/refus/{id_inv}/{motif}', name: 'app_change_status_refus_fiche_lac_idinv')]
    public function change_status_refus_fiche_lac(
        Request                       $request,
        MenuRepository                $menus,
        MenuPermissionRepository      $permissions,
        GroupeRepository              $groupeRepository,
        UserRepository                $userRepository,
        ManagerRegistry               $registry,
        int                           $id_inv,
        string                        $motif,
        NotificationRepository        $notification,
        InventaireForestierRepository $inventaires): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $arbre_inv = $registry->getRepository(InventaireForestier::class)->find($id_inv);
                $resultat = array();
                if ($arbre_inv && $motif){
                    $arbre_inv->setValide(false);
                    $arbre_inv->setMotif($motif);
                    $arbre_inv->setUpdatedBy($user);
                    $arbre_inv->setUpdatedAt(new \DateTime());
                    $registry->getManager()->persist($arbre_inv);
                    $registry->getManager()->flush();

                    $resultat[] = array(
                        'CODE'=>'SUCCESS'
                    );

                } else {
                    $resultat[] = array(
                        'CODE'=>'NO_FOUND'
                    );
                }

                return new JsonResponse(json_encode($resultat));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('snvlt/inv/validate/id_inv/accepter/{id_inv}', name: 'app_change_status_accepter_fiche_lac_idinv')]
    public function change_status_accepter_fiche_lac(
        Request                       $request,
        MenuRepository                $menus,
        MenuPermissionRepository      $permissions,
        GroupeRepository              $groupeRepository,
        UserRepository                $userRepository,
        ManagerRegistry               $registry,
        int                           $id_inv,
        NotificationRepository        $notification,
        InventaireForestierRepository $inventaires): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $arbre_inv = $registry->getRepository(InventaireForestier::class)->find($id_inv);
                $resultat = array();
                if ($arbre_inv){
                    $arbre_inv->setValide(true);
                    $arbre_inv->setMotif("");
                    $arbre_inv->setUpdatedBy($user);
                    $arbre_inv->setUpdatedAt(new \DateTime());
                    $registry->getManager()->persist($arbre_inv);
                    $registry->getManager()->flush();

                    $resultat[] = array(
                        'CODE'=>'SUCCESS'
                    );

                } else {
                    $resultat[] = array(
                        'CODE'=>'NO_FOUND'
                    );
                }

                return new JsonResponse(json_encode($resultat));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/inv/validate/inv/fiche/{id_fiche}', name: 'validation_fiche_lac')]
    public function validation_fiche_lac(
        Request                       $request,
        MenuRepository                $menus,
        MenuPermissionRepository      $permissions,
        GroupeRepository              $groupeRepository,
        UserRepository                $userRepository,
        ManagerRegistry               $registry,
        int                           $id_fiche,
        NotificationRepository        $notification,
        InventaireForestierRepository $inventaires,
        MailerInterface               $mailer): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $fiche = $registry->getRepository(FicheProspection::class)->find($id_fiche);
                $resultat = array();
                if ($fiche){
                    $fiche->setValide(true);
                    $fiche->setUpdatedBy($user);
                    $fiche->setUpdatedAt(new \DateTime());
                    $registry->getManager()->persist($fiche);
                    $registry->getManager()->flush();

                    $sujet = "INFOS SUR LA VALIDATION DE LA LAAC N° ".$fiche->getId(). " [FORET : ". $fiche->getCodeAttribution()->getCodeForet()->getDenomination() ."]";
                    $message =  "Votre LAAC N° ".$fiche->getId(). " en relation avec la forêt ". $fiche->getCodeAttribution()->getCodeForet()->getDenomination() . " a été révisée et validée par un des administrateur du MINEF. Merci de vous connectez à votre interfece SNVLT pour la visualisation ";
                    $autorisation = true;
                    foreach($fiche->getInventaireForestiers() as $arbre){
                        if(!$arbre->getCodeEssence()->isAutorisation()){
                            $autorisation = false;
                        }
                    }

                    if (!$autorisation){
                        $message =  "Votre LAAC N° ".$fiche->getId(). " en relation avec la forêt ". $fiche->getCodeAttribution()->getCodeForet()->getDenomination() . " a été révisée et validée par un des administrateur du MINEF. Nous rappelons que des arbres non autorisés à la coupe ont été identifiés. Merci de vous connectez à votre interfece SNVLT pour la visualisation ";
                    }
                    // envoi d'une notification App au responsable de la société forestière
                    if ($fiche->getCodeExploitant()->getEmailPersonneRessource()) {
                        $utilisateur = $registry->getRepository(User::class)->findOneBy(['email'=>$fiche->getCodeExploitant()->getEmailPersonneRessource()]);
                        //dd($utilisateur);
                        $this->utils->envoiNotification(
                            $registry,
                            $sujet,
                            $message,
                            $utilisateur,
                            $user->getId(),
                            'infos_fiche_lac',
                            'INVENTAIRE_FORESTIER',
                            $fiche->getId()
                        );
                    }


                    
                    if ($fiche->getCodeExploitant()->getEmailPersonneRessource()){
                       /* $this->utils->sendEmail(
                            $fiche->getCodeExploitant()->getEmailPersonneRessource(),
                            $sujet,
                            $message
                        );*/
                        try {
                            $email = (new TemplatedEmail())
                                ->from(new Address('snvlt@system2is.com', 'SNVLT INFOS'))
                                ->to($fiche->getCodeExploitant()->getEmailPersonneRessource())
                                ->subject($sujet)
                                ->htmlTemplate('emails/lac_retour_exp.html.twig')
                                ->context([
                                    'message' => $message,
                                    'utilisateur'=>$utilisateur,
                                    'fiche'=>$fiche
                                ])
                            ;
                            $mailer->send($email);
                        } catch (\Throwable $exception){

                        }

                    }

                    $resultat[] = array(
                        'CODE'=>'SUCCESS'
                    );

                } else {
                    $resultat[] = array(
                        'CODE'=>'NO_FOUND'
                    );
                }

                return new JsonResponse(json_encode($resultat));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/inv/add/fiche_lac/{code_attribution}', name: 'add_fiche_lac')]
    public function add_fiche_lac(
        Request                       $request,
        MenuRepository                $menus,
        MenuPermissionRepository      $permissions,
        int                           $code_attribution,
        GroupeRepository              $groupeRepository,
        UserRepository                $userRepository,
        ManagerRegistry               $registry): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $attribution = $registry->getRepository(Attribution::class)->find($code_attribution);

                if ($attribution){
                    $fiche = new FicheProspection();
                    $resultat = array();


                    $fiche->setValide(false);
                    $fiche->setCodeAttribution($attribution);
                    $fiche->setCodeExploitant($attribution->getCodeExploitant());
                    $fiche->setCreatedBy($user);
                    $fiche->setCreatedAt(new \DateTime());

                    $registry->getManager()->persist($fiche);
                    $registry->getManager()->flush();



                    $resultat[] = array(
                        'CODE'=>'SUCCESS',
                        'id_fiche'=>$fiche->getId()
                    );
                }




                return new JsonResponse(json_encode($resultat));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/inv/edit/tonewlac/{id_arbre}/{id_fiche}', name: 'edit_arbre_to_new_lac')]
    public function edit_arbre_to_new_lac(
        Request                       $request,
        UserRepository                $userRepository,
        int                           $id_arbre,
        int                           $id_fiche,
        ManagerRegistry               $registry): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $resultat = array();

                $arbre = $registry->getRepository(InventaireForestier::class)->find($id_arbre);
                $fiche = $registry->getRepository(FicheProspection::class)->find($id_fiche);

                    if ($arbre && $fiche){
                        $arbre->setCodeFicheProspection($fiche);
                        $arbre->setLac(true);
                        $arbre->setValide(true);

                        $registry->getManager()->persist($arbre);
                        $registry->getManager()->flush();
                        $resultat[] = array(
                            'CODE'=>'SUCCESS'
                        );
                    } else {
                        $resultat[] = array(
                            'CODE'=>'FAILED'
                        );
                    }

                return new JsonResponse(json_encode($resultat));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/inv/edit/inv/tonewlac/sendnotif/{id_fiche}', name: 'new_fiche_send_notif')]
    public function new_fiche_send_notif(
        Request                       $request,
        UserRepository                $userRepository,
        int                           $id_fiche,
        ManagerRegistry               $registry,
        MailerInterface               $mailer): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $resultat = array();

                $fiche = $registry->getRepository(FicheProspection::class)->find($id_fiche);

                    if ($fiche){

                        //Envoi d'une notification aux utilisateurs utilisant le menu <<VALIDATION LAAC>>

                        /*$menu_lac = $registry->getRepository(Menu::class)->findOneBy(['classname_menu'=>'admin_validation_lac']);
                        if ($menu_lac){*/
                        // Recherche tous les groupes qui utilisent ce menu
                        $critere = "admin_validation_lac";
                        $liste_groupe = $registry->getRepository(MenuPermission::class)->findBy(['classname_menu'=>$critere]);
                        //dd($liste_groupe);
                        foreach ($liste_groupe as $groupe_permission){
                            $groupe = $registry->getRepository(Groupe::class)->find($groupe_permission->getCodeGroupe());
                            // Cherche les utilisateurs du groupe
                            $liste_users = $registry->getRepository(User::class)->findBy(['code_groupe'=>$groupe]);
                            //dd($liste_users);
                            foreach($liste_users as $utilisateur){

                                $sujet = "LAAC ". $fiche->getCodeAttribution()->getCodeForet()->getDenomination() ." A VALIDER";

                                $message = " la structure " . $fiche->getCodeExploitant()->getSigle() . " vous a envoyé son inventaire forestier pour validation en relation avec la forêt dénommée ". $fiche->getCodeAttribution()->getCodeForet()->getDenomination();

                                // envoi d'une notification App à l'administration
                                $this->utils->envoiNotification(
                                    $registry,
                                    $sujet,
                                    $message,
                                    $utilisateur,
                                    $user->getId(),
                                    'infos_admin_fiche_lac',
                                    'INVENTAIRE_FORESTIER',
                                    $fiche->getId()
                                );

                                $email = (new TemplatedEmail())
                                    ->from(new Address('snvlt@system2is.com', 'SNVLT INFOS'))
                                    ->to($utilisateur->getEmail())
                                    ->subject($sujet)
                                    ->htmlTemplate('emails/lac.html.twig')
                                    ->context([
                                        'message' => $message,
                                        'utilisateur'=>$utilisateur
                                    ])
                                ;

                                $mailer->send($email);

                            }
                        }

                        $resultat[] = array(
                            'CODE'=>'SUCCESS'
                        );
                    } else {
                        $resultat[] = array(
                            'CODE'=>'FAILED'
                        );
                    }

                return new JsonResponse(json_encode($resultat));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('snvlt/search_att/exp/{id_exploitant}', name: 'search_att_exp')]
    public function search_att_exp(
        Request                       $request,
        UserRepository                $userRepository,
        int                           $id_exploitant,
        ManagerRegistry               $registry): Response
    {

        if (!$request->getSession()->has('user_session')) {
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $resultat = array();
                $exercice = $this->administrationService->getAnnee();
                $exploitant = $registry->getRepository(Exploitant::class)->find((int) $id_exploitant);

                if ($exploitant){
                   $attributions = $registry->getRepository(Attribution::class)->findBy(['exercice'=>$exercice, 'code_exploitant'=>$exploitant]);
                    foreach ($attributions as $attribution){
                        $resultat[] = array(
                            'foret'=>$attribution->getCodeForet()->getDenomination(),
                            'id'=>$attribution->getId()
                        );
                    }
                    asort($resultat);
                }

                return new JsonResponse(json_encode($resultat));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

}
