<?php

namespace App\Controller\Transformation;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\Utils;
use App\Entity\Administration\DocStatsGen;
use App\Entity\DocStats\Entetes\Documentlje;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Pages\Pagelje;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\DocStats\Saisie\Lignepagelje;
use App\Entity\References\Essence;
use App\Entity\References\TypeTransformation;
use App\Entity\References\Usine;
use App\Entity\Transformation\Billon;
use App\Entity\Transformation\Elements;
use App\Entity\Transformation\FicheLot;
use App\Entity\Transformation\FicheLotProd;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DocStats\Pages\PagebrhRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\PageDocGenRepository;
use App\Repository\Transformation\BillonRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Util\Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Contracts\Translation\TranslatorInterface;

class BillonController extends AbstractController
{

    public function __construct(private TranslatorInterface $translator, private Utils $utils, private AdministrationService $administrationService)
    {
    }


    #[Route('/snvlt/trans/billon/gen', name: 'app_trans_ind_billon')]
    public function billon_interface_industriel(ManagerRegistry $registry,
                                                BillonRepository $billonRepository,
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
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATTIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                // $billons = $registry->getRepository(Billon::class)->find($user->);

                //$response = new QrCodeResponse($doctrine->getUniqueDoc());
                return $this->render('transformation/billon/index.html.twig', [
                    //'qr_code_doc' => $response,
                    'billes_non_decoupes'=>$registry->getRepository(Lignepagelje::class)->findAll(),
                    'type_transfo'=>$registry->getRepository(TypeTransformation::class)->findAll(),
                    'billons'=>$registry->getRepository(Billon::class)->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'essences'=>$registry->getRepository(Essence::class)->findAll()
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/trans/billon/stock', name: 'stock_billon')]
    public function stock_billon(ManagerRegistry $registry,
                                 BillonRepository $billonRepository,
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
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATTIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('transformation/billon/stock.html.twig', [
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

    #[Route('/snvlt/trans/allbillon/stock/{date_debut}/{date_fin}', name: 'stock_billon_json')]
    public function stock_billon_json(ManagerRegistry $registry,
                                      BillonRepository $billonRepository,
                                      Request $request,
                                      string $date_debut,
                                      string $date_fin,
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
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATTIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $billons = array();

                if ($user->getCodeindustriel()){
                    $mes_lje = $registry->getRepository(Documentlje::class)->findBy(['code_usine'=>$user->getCodeindustriel()]);
                    foreach($mes_lje as $lje){
                        $mes_pages = $registry->getRepository(Pagelje::class)->findBy(['code_doclje'=>$lje]);
                        foreach($mes_pages as $page){
                            $mes_billes = $registry->getRepository(Lignepagelje::class)->findBy(['code_pagelje'=>$page]);
                            foreach($mes_billes as $bille){
                                $mes_billons = $registry->getRepository(Billon::class)->findBy(['code_lignepagelje'=>$bille]);


                                foreach($mes_billons as $billon){

                                    $datedebut = \DateTime::createFromFormat('Y-m-d', $date_debut);
                                    $datefin = \DateTime::createFromFormat('Y-m-d', $date_fin);
                                    //dd($datedebut);
                                    if($billon->getDateBillonnage() >= $datedebut && $billon->getDateBillonnage() <= $datefin){
                                        $elts = $registry->getRepository(Elements::class)->findBy(['code_billon'=>$billon]);
                                        $nb_elts = 0;
                                        $vol_elts = 0;
                                        $type_transfo = "-";

                                        if ($billon->getTypeTransformation()){
                                            $type_transfo = $billon->getTypeTransformation()->getLibelle();
                                        }

                                        foreach($elts as $elt){
                                            $nb_elts = $nb_elts + $elt->getNombre();
                                            $vol_elts = $vol_elts + $elt->getVolume();
                                        }
                                        $rendement = 0;
                                        if ($billon->getVolume()){
                                            $rendement = round(($vol_elts / $billon->getVolume()) * 100, 3);
                                        }

                                        $usine_prod = "";
                                        if ($billon->getCodeLignepagelje()->getCodePagelje()->getCodeDoclje()->getCodeUsine()->getSigle()){
                                            $usine_prod = $billon->getCodeLignepagelje()->getCodePagelje()->getCodeDoclje()->getCodeUsine()->getSigle();
                                        } else {
                                            $usine_prod = $billon->getCodeLignepagelje()->getCodePagelje()->getCodeDoclje()->getCodeUsine()->getRaisonSocialeUsine();
                                        }

                                        $billons[] = array(

                                            'id_billon'=>$billon->getId(),
                                            'numero_billon'=>$billon->getNumeroBillon(),
                                            'date_billonage'=>$billon->getDateBillonnage()->format('Y-m-d'),
                                            'exploitant'=>$registry->getRepository(Pagebrh::class)->find($billon->getCodeLignepagelje()->getCodeFeuillet())->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle(),
                                            'lng'=>$billon->getLng(),
                                            'dm'=>$billon->getDm(),
                                            'volume'=>round($billon->getVolume(), 3),
                                            'nb_elt'=> $nb_elts,
                                            'vol_elt'=>round($vol_elts,3),
                                            'rendement'=>$rendement,
                                            'type_transfo'=>$type_transfo,
                                            'cloture'=>$billon->isCloture(),
                                            'essence'=>$billon->getCodeLignepagelje()->getEssence()->getNomVernaculaire(),
                                            'usine'=>$usine_prod
                                        );
                                    }


                                }
                            }
                        }
                    }
                } elseif ($user->getCodeOperateur()->getId() == 1 or $user->getCodeGroupe()->getId() == 1){
                    $mes_billons = $registry->getRepository(Billon::class)->findAll();


                    foreach($mes_billons as $billon){

                        $datedebut = \DateTime::createFromFormat('Y-m-d', $date_debut);
                        $datefin = \DateTime::createFromFormat('Y-m-d', $date_fin);
                        //dd($datedebut);
                        if($billon->getDateBillonnage() >= $datedebut && $billon->getDateBillonnage() <= $datefin){
                            $elts = $registry->getRepository(Elements::class)->findBy(['code_billon'=>$billon]);
                            $nb_elts = 0;
                            $vol_elts = 0;
                            $type_transfo = "-";

                            if ($billon->getTypeTransformation()){
                                $type_transfo = $billon->getTypeTransformation()->getLibelle();
                            }

                            foreach($elts as $elt){
                                $nb_elts = $nb_elts + $elt->getNombre();
                                $vol_elts = $vol_elts + $elt->getVolume();
                            }
                            $rendement = 0;
                            if ($billon->getVolume()){
                                $rendement = round(($vol_elts / $billon->getVolume()) * 100, 3);
                            }

                            $usine_prod = "";
                            if ($billon->getCodeLignepagelje()->getCodePagelje()->getCodeDoclje()->getCodeUsine()->getSigle()){
                                $usine_prod = $billon->getCodeLignepagelje()->getCodePagelje()->getCodeDoclje()->getCodeUsine()->getSigle();
                            } else {
                                $usine_prod = $billon->getCodeLignepagelje()->getCodePagelje()->getCodeDoclje()->getCodeUsine()->getRaisonSocialeUsine();
                            }

                            $billons[] = array(

                                'id_billon'=>$billon->getId(),
                                'numero_billon'=>$billon->getNumeroBillon(),
                                'date_billonage'=>$billon->getDateBillonnage()->format('Y-m-d'),
                                'exploitant'=>$registry->getRepository(Pagebrh::class)->find($billon->getCodeLignepagelje()->getCodeFeuillet())->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle(),
                                'lng'=>$billon->getLng(),
                                'dm'=>$billon->getDm(),
                                'volume'=>round($billon->getVolume(), 3),
                                'nb_elt'=> $nb_elts,
                                'vol_elt'=>round($vol_elts,3),
                                'rendement'=>$rendement,
                                'type_transfo'=>$type_transfo,
                                'cloture'=>$billon->isCloture(),
                                'essence'=>$billon->getCodeLignepagelje()->getEssence()->getNomVernaculaire(),
                                'usine'=>$usine_prod
                            );
                        }


                    }
                }


                return new JsonResponse(json_encode($billons));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/search_bille/clipping/{numero_bille}', name: 'search_bille')]
    public function search_bille(
        Request $request,
        string $numero_bille,
        UserRepository $userRepository,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATTIF'))
            {
                $user = $userRepository->find($this->getUser());
                $sources_forets = array();


                if(strval(intval($numero_bille))){

                    $doc_ljes = $registry->getRepository(Documentlje::class)->findBy(['code_usine'=>$user->getCodeindustriel()]);
                    foreach ($doc_ljes as $doc_lje){
                        $pages_lje = $registry->getRepository(Pagelje::class)->findBy(['code_doclje'=>$doc_lje]);
                        foreach ($pages_lje as $page_lje){
                            $numero = (int) substr(strtoupper($numero_bille), 0, -1) ;
                            $lettre = substr(strtoupper($numero_bille), -1);
                            //dd($numero. " - ". $lettre);
                            $lignes= $registry->getRepository(Lignepagelje::class)->findBy(['numero_arbre'=> $numero  , 'lettre'=>$lettre, 'code_pagelje'=>$page_lje]);
                            foreach ($lignes as $ligne){
                                $sources_forets[] = array(
                                    'foret'=>$registry->getRepository(Pagebrh::class)->find($ligne->getCodeFeuillet())->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(),
                                    'lng'=>$ligne->getLng(),
                                    'essence'=>$ligne->getEssence()->getNomVernaculaire(),
                                    'dm_lje'=>$ligne->getDm(),
                                    'code_bille_lje'=>$ligne->getId(),
                                    'tronconnee'=>$ligne->isTronconnee()
                                );
                            }
                        }
                    }

                    return  new JsonResponse(json_encode($sources_forets));

                } else {
                    return  new JsonResponse(json_encode($sources_forets));
                }

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/liste_billes_dispo/clipping', name: 'liste_billes_dispo_lje')]
    public function liste_billes_lje_dispo(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATTIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $sources_forets = array();

                $mes_lje = $registry->getRepository(Documentlje::class)->findBy(['code_usine'=>$user->getCodeindustriel()]);
                foreach($mes_lje as $lje) {
                    $mes_pages = $registry->getRepository(Pagelje::class)->findBy(['code_doclje' => $lje]);
                    foreach ($mes_pages as $page) {
                        $lignes = $registry->getRepository(Lignepagelje::class)->findBy(['code_pagelje'=>$page, 'tronconnee' => false]);
                        foreach ($lignes as $ligne){
                            if ($ligne->getEssence()){
                                $ess = $ligne->getEssence()->getNomVernaculaire();
                            } else {
                                $ess = "-";
                            }

                            $sources_forets[] = array(
                                'numero'=>$ligne->getNumeroArbre() . $ligne->getLettre(),
                                'foret'=>$ligne->getForet() ,
                                'lng_lje'=>$ligne->getLng(),
                                'dm_lje'=>$ligne->getDm(),
                                'vol_lje'=> round($ligne->getVolume()/1000, 3),
                                'essence'=>$ess,
                                'code_bille_lje'=>$ligne->getId()
                            );

                        }
                    }
                }
                sort($sources_forets);
                return  new JsonResponse(json_encode($sources_forets));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/billon/add_data/{longueur}/{nombre}/{code_bille_lje}/{date_billonage}', name: 'add_billon')]
    public function add_billon(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        int $longueur,
        int $nombre,
        int $code_bille_lje,
        string $date_billonage,
        User $user = null,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATTIF'))
            {

                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                if(strval(intval($longueur)) && strval(intval($nombre)) && strval(intval($code_bille_lje)) && $date_billonage){
                    $ligne_lje= $registry->getRepository(Lignepagelje::class)->find($code_bille_lje);

                    //Recherche de la foret
                    $foret = $registry->getRepository(Pagebrh::class)->find($ligne_lje->getCodeFeuillet())->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination();

                    //Recherche la longueur totale utilisée
                    $lng_billons = 0;
                    $nombre_billons = 0;
                    if ($ligne_lje->getBillons()){
                        foreach($ligne_lje->getBillons() as $billon){
                            $lng_billons = $lng_billons + $billon->getLng();
                            $nombre_billons = $nombre_billons + 1;
                        }
                    }
                    $lng_restant = $ligne_lje->getLng() - $lng_billons;
                    $lng_demandee = $longueur * $nombre;

                    if ($lng_demandee < $lng_restant){
                        for($i =0 ; $i < $nombre; $i++){
                            $billon = new Billon();
                            $billon->setLng($longueur);
                            $billon->setDm($ligne_lje->getDm());

                            //$volume = (pow(($billon->getDm() / 2), 2) * $billon->getLng() * 3.14159 / 10000);
                            // $volume = ($billon->getLng() * $billon->getDm() * $billon->getDm() * 7854 / 10000000000);
                            $billon->setVolume($this->utils->calcul_volume(
                                $longueur,
                                $ligne_lje->getDm()
                            ));
                            $billon->setCloture(false);
                            $billon->setCodeLignepagelje($ligne_lje);
                            $billon->setDateBillonnage(\DateTime::createFromFormat('Y-m-d', $date_billonage));

                            //numero séquentiel du billon (N° arbre + Lettre + Foret + Nombre billons + 1)
                            $nombre_billons =  $nombre_billons + 1;
                            $num_billon = $ligne_lje->getNumeroArbre().$ligne_lje->getLettre()."-".$foret."-".$nombre_billons;
                            $billon->setNumeroBillon($num_billon);



                            $registry->getManager()->persist($billon);
                            $registry->getManager()->flush();
                        }

                        $ligne_lje->setTransforme(true);
                        $registry->getManager()->persist($ligne_lje);
                        $registry->getManager()->flush();

                    }
                }
                $lignes= $registry->getRepository(Lignepagelje::class)->findBy(['tronconnee'=>false]);

                $sources_forets = array();

                foreach ($lignes as $ligne){
                    $foret = "-";
                    $pagebrh = $registry->getRepository(Pagebrh::class)->find($ligne->getCodeFeuillet());
                    if ($pagebrh){
                        $foret = $registry->getRepository(Pagebrh::class)->find($ligne->getCodeFeuillet())->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination();
                    }
                    $sources_forets[] = array(

                        'foret'=>$foret,
                        'numero'=>$ligne->getNumeroArbre() . $ligne->getLettre(),
                        'lng_lje'=>$ligne->getLng(),
                        'dm_lje'=>$ligne->getDm(),
                        'essence'=>$ligne->getEssence()->getNomVernaculaire()
                    );
                }

                return  new JsonResponse(json_encode($sources_forets));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/liste_billons/{id_bille_lje}', name: 'liste_billons_dispo_lje')]
    public function liste_billons(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        int $id_bille_lje,
        User $user = null,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {

            $user = $userRepository->find($this->getUser());
            $code_groupe = $user->getCodeGroupe()->getId();

            $bille = $registry->getRepository(Lignepagelje::class)->find($id_bille_lje);
            if ($bille){

                $lng_billons = 0;
                $nombre_billons = 0;
                if ($bille->getBillons()){
                    foreach($bille->getBillons() as $billon){
                        $lng_billons = $lng_billons + $billon->getLng();
                        $nombre_billons = $nombre_billons + 1;
                    }
                }
                $lng_restant = $bille->getLng() - $lng_billons;

                $billons= $registry->getRepository(Billon::class)->findBy(['code_lignepagelje'=>$bille, 'cloture'=>false]);
                $sources_forets = array();
                foreach ($billons as $elt){
                    $sources_forets[] = array(

                        'id_billon'=>$elt->getId(),
                        'numero_billon'=>$elt->getNumeroBillon(),
                        'lng_billon'=>$elt->getLng(),
                        'dm_billon'=>$elt->getDm(),
                        'volume_billon'=>round($elt->getVolume(), 3),
                        'date_billon'=>$elt->getDateBillonnage()->format('d/m/Y'),
                        'essence'=>$bille->getEssence()->getNomVernaculaire(),
                        'lng_restant'=>$lng_restant,
                        'nb_billons'=>$nombre_billons,
                        'rebus'=>$elt->isRebus()
                    );
                }
            }


            return  new JsonResponse(json_encode($sources_forets));


        }
    }

    #[Route('/snvlt/lje_bille/lng_rest/{id_bille_lje}', name: 'lng_restant')]
    public function lng_restant(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        int $id_bille_lje,
        User $user = null,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {

            $user = $userRepository->find($this->getUser());
            $code_groupe = $user->getCodeGroupe()->getId();

            $bille = $registry->getRepository(Lignepagelje::class)->find($id_bille_lje);
            if ($bille){

                $lng_billons = 0;
                $nombre_billons = 0;
                if ($bille->getBillons()){
                    foreach($bille->getBillons() as $billon){
                        $lng_billons = $lng_billons + $billon->getLng();
                        $nombre_billons = $nombre_billons + 1;
                    }
                }
                $lng_restant = $bille->getLng() - $lng_billons;

                $sources_forets[] = array(
                    'lng_restant'=>$lng_restant,
                    'nb_billons'=>$nombre_billons . $this->translator->trans(" cm remaining")
                );

            }


            return  new JsonResponse(json_encode($sources_forets));


        }
    }

    #[Route('/snvlt/mes_billons/{id_transfo}/{id_fiche_lot}', name: 'mes_billons')]
    public function mes_billons(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        int $id_transfo,
        int $id_fiche_lot,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATTIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $my_billons = array();
                $liste_lje = $registry->getRepository(Documentlje::class)->findBy(['code_usine'=>$user->getCodeindustriel()]);
                $fiche_lot = $registry->getRepository(FicheLot::class)->find($id_fiche_lot);
                foreach ($liste_lje as $doc_lje){
                    $pages_lje = $registry->getRepository(Pagelje::class)->findBy(['code_doclje'=>$doc_lje]);
                    foreach ($pages_lje as $page_lje){
                        $lignepageslje = $registry->getRepository(Lignepagelje::class)->findBy(['code_pagelje'=>$page_lje]);
                        $transfo = $registry->getRepository(TypeTransformation::class)->find($id_transfo);
                        foreach ($lignepageslje as $ligne){
                            $billons = $registry->getRepository(Billon::class)->findBy(['code_lignepagelje'=>$ligne, 'cloture'=>false, 'type_transformation'=>$transfo, 'code_lot'=>$fiche_lot]);

                            foreach ($billons as $billon){
                                $my_billons[] = array(
                                    'id_billon'=>$billon->getId(),
                                    'numero_billon'=>$billon->getNumeroBillon()
                                );
                            }
                        }
                    }
                }
                return  new JsonResponse(json_encode($my_billons));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/billon/info/{id_billon}', name: 'infos_billon')]
    public function infos_billon(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        int $id_billon,
        User $user = null,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATTIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $my_billon = array();
                $volume_elt = 0;
                $mon_billon = $registry->getRepository(Billon::class)->find($id_billon);
                $mes_elts = $registry->getRepository(Elements::class)->findBy(['code_billon'=>$mon_billon]);

                if ($mes_elts){
                    foreach($mes_elts as $elt){
                        $volume_elt = $volume_elt + $elt->getVolume();
                    }
                    $volume_elt = round($volume_elt, 3);
                }

                if($mon_billon){
                    $my_billon[] = array(
                        'id_billon'=>$mon_billon->getId(),
                        'essence'=>$mon_billon->getCodeLignepagelje()->getEssence()->getNomVernaculaire(),
                        'numero_billon'=>$mon_billon->getNumeroBillon(),
                        'lng_billon'=>$mon_billon->getLng(),
                        'dm_billon'=>$mon_billon->getDm(),
                        'vol_billon'=>round($mon_billon->getVolume(), 3),
                        'vol_elts'=>$volume_elt,
                        'vol_restant'=>round($mon_billon->getVolume() - $volume_elt, 3),
                        'rendement'=>round(($volume_elt / $mon_billon->getVolume() )* 100,2)
                    );
                }

                return  new JsonResponse(json_encode($my_billon));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/billon/clb/{id_bille}', name: 'cloturer_billon')]
    public function cloturer_billon(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        int $id_bille,
        User $user = null,
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
                $my_billon = array();
                $volume_elt = 0;
                $ma_bille = $registry->getRepository(Lignepagelje::class)->find($id_bille);

                if ($ma_bille){
                    if($ma_bille->getCodePagelje()->getCodeDoclje()->getCodeUsine()->getId() == $user->getCodeindustriel()->getId()){
                        $ma_bille->setTronconnee(true);

                        $registry->getManager()->persist($ma_bille);

                        //Création du dernier billon
                        $billons = $registry->getRepository(Billon::class)->findBy(['code_lignepagelje'=>$ma_bille]);
                        $foret = $registry->getRepository(Pagebrh::class)->find($ma_bille->getCodeFeuillet())->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination();
                        $lng_billons = 0;
                        $nombre_billons = 0;
                        $date_billonnage = $ma_bille->getBillons()->get(0)->getDateBillonnage();
                        foreach ($billons as $billon){
                            $lng_billons = $lng_billons + $billon->getLng();
                            $nombre_billons = $nombre_billons + 1;
                        }
                        if ($lng_billons == 0){
                            $my_billon[] = array(
                                'code'=>'AUCUN_BILLON'
                            );
                        } else {
                            // Création du dernier billon (boût)
                            $new_billon = new Billon();
                            $new_billon->setLng($ma_bille->getLng() - $lng_billons);
                            $new_billon->setDm($ma_bille->getDm());

                            $new_billon->setVolume($this->utils->calcul_volume(
                                $ma_bille->getLng() - $lng_billons,
                                $ma_bille->getDm()
                            ));

                            //numero séquentiel du billon (N° arbre + Lettre + Foret + Nombre billons + 1)
                            $nombre_billons =  $nombre_billons + 1;
                            $num_billon = $ma_bille->getNumeroArbre().$ma_bille->getLettre()."-".$foret."-".$nombre_billons;
                            $new_billon->setNumeroBillon($num_billon);

                            $new_billon->setCloture(false);
                            $new_billon->setRebus(true);

                            $new_billon->setDateBillonnage($date_billonnage);

                            $new_billon->setCodeLignepagelje($ma_bille);
                            //Il faut noter que le bout de la bille n'a pas au préalable de type transfo

                            /* $type_transfo = $registry->getRepository(TypeTransformation::class)->find();
                             if ($type_transfo){
                                 $billon->setTypeTransformation($type_transfo);
                             }*/
                            $registry->getManager()->persist($new_billon);

                        }

                        $registry->getManager()->flush();


                        // Création de la fiche LOT
                            $recherche_lot = $registry->getRepository(FicheLot::class)->findOneBy(['code_usine'=>$user->getCodeindustriel(), 'date_lot'=>$date_billonnage]);
                            $volume_fiche_lot = 0;
                            if(!$recherche_lot){
                                $recherche_lot = new FicheLot();
                                $recherche_lot->setCodeUsine($user->getCodeindustriel());
                                $recherche_lot->setDateLot($date_billonnage);
                                $registry->getManager()->persist($recherche_lot);
                                $recherche_lot->setNumero($user->getCodeindustriel()->getId()."-".$recherche_lot->getId());
                                $recherche_lot->setCloture(false);
                                $recherche_lot->setClotureProd(false);
                                foreach ($billons as $billon){
                                    $billon->setCodeLot($recherche_lot);
                                    $volume_fiche_lot = $volume_fiche_lot + $billon->getVolume();
                                }

                                $recherche_lot->setVolume($volume_fiche_lot);
                            } else {

                                foreach ($billons as $billon){
                                    $billon->setCodeLot($recherche_lot);
                                    $volume_fiche_lot = $volume_fiche_lot + $billon->getVolume();
                                }

                                $recherche_lot->setVolume($recherche_lot->getVolume() + $volume_fiche_lot);
                            }

                            $registry->getManager()->persist($recherche_lot);
                            $registry->getManager()->flush();

                        $my_billon[] = array(
                            'code'=>'SUCCESS'
                        );



                    } else {
                        $my_billon[] = array(
                            'code'=>'INVALID_DATA'
                        );
                    }

                }

                return  new JsonResponse(json_encode($my_billon));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/billon/clb/billon/{id_billon}', name: 'cloturer_billon_clb')]
    public function cloturer_billon_clb(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        int $id_billon,
        User $user = null,
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

                //Recherche le billon pour cloture
                $mon_billon = $registry->getRepository(Billon::class)->find($id_billon);

                if ($mon_billon){
                    //vérifie si le billon est issu du sciage et a des éléments
                        if ($mon_billon->getTypeTransformation()->getId() == 1 or $mon_billon->getTypeTransformation()->getId() == 3){
                            $elts = $registry->getRepository(Elements::class)->findBy(['code_billon'=>$mon_billon]);
                            $volume_elts = 0;
                            foreach ($elts as $elt) {

                                $volume_elts = $volume_elts + $elt->getVolume();
                            }
                            if ( $volume_elts > 0){
                                $mon_billon->setCloture(true);
                                $registry->getManager()->persist($mon_billon);
                                $registry->getManager()->flush();

                                // Création de la fiche LOT Production
                                $date_elements = $mon_billon->getElements()->get(0)->getDateEnr();
                                $recherche_lot = $registry->getRepository(FicheLotProd::class)->findOneBy(['code_usine'=>$user->getCodeindustriel(), 'date_fiche'=>$date_elements]);

                                $volume_fiche_lot = 0;


                                if(!$recherche_lot){
                                    $recherche_lot = new FicheLotProd();
                                    $recherche_lot->setCodeUsine($user->getCodeindustriel());
                                    $recherche_lot->setDateFiche($date_elements);
                                    $registry->getManager()->persist($recherche_lot);
                                    $recherche_lot->setNumero($user->getCodeindustriel()->getId()."-".$recherche_lot->getId());

                                    foreach ($elts as $elt) {
                                        $elt->setCodeFicheProd($recherche_lot);
                                        $volume_fiche_lot = $volume_fiche_lot + $elt->getVolume();
                                    }

                                    $recherche_lot->setVolume($volume_fiche_lot);
                                } else {

                                    foreach ($elts as $elt) {
                                        $elt->setCodeFicheProd($recherche_lot);
                                        $volume_fiche_lot = $volume_fiche_lot + $elt->getVolume();
                                    }

                                    $recherche_lot->setVolume($recherche_lot->getVolume() + $volume_fiche_lot);
                                }

                                $registry->getManager()->persist($recherche_lot);
                                $registry->getManager()->flush();

                                $my_billon[] = array(
                                    'code'=>'SUCCESS'
                                );
                            }
                        }
                }


                        // Création de la fiche LOT


                return  new JsonResponse(json_encode($my_billon));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/type_transfo', name: 'type_transfo')]
    public function type_transfo(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATTIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $usine = $registry->getRepository(Usine::class)->find($user->getCodeindustriel()->getId());
                $transfos = array();
                if($usine){
                    foreach ($usine->getTypeTransformation() as $typetransfo){
                        $transfos[] = array(
                            'id_transfo'=>$typetransfo->getId(),
                            'libelle'=>$typetransfo->getLibelle()
                        );
                    }
                }


                return  new JsonResponse(json_encode($transfos));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/fiches_lot/{id_essence}/{id_transfo}', name: 'liste_lots_essences')]
    public function liste_lots_essences(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_essence,
        int $id_transfo,
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

                $liste_fiches = array();
                $trouve = false;
                $essence = $registry->getRepository(Essence::class)->find($id_essence);
                $transfo = $registry->getRepository(TypeTransformation::class)->find($id_transfo);
                $fiches_lot = $registry->getRepository(FicheLot::class)->findBy(['code_usine'=>$user->getCodeindustriel(), 'cloture_prod'=>false]);
                foreach ($fiches_lot as $fiche){
                    $billons = $registry->getRepository(Billon::class)->findBy(['code_lot'=>$fiche, "type_transformation"=>$transfo]);
                    $trouve = false;
                    foreach ($billons as $billon){
                        if ($billon->getCodeLignepagelje()->getEssence()->getId() == $essence->getId()){
                            $trouve = true;
                        }
                    }
                    if ($trouve){
                        $liste_fiches[] = array(
                            'id_fiche'=>$fiche->getId(),
                            'numero_fiche'=>$fiche->getDateLot()->format("d/m/Y")
                        );
                    }

                }
                rsort($liste_fiches);

                return  new JsonResponse(json_encode($liste_fiches));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/fiches_lot/infos/{id_fiche}/{id_essence}/{id_transfo}', name: 'lot_essence_infos')]
    public function lot_essence_infos(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_fiche,
        int $id_essence,
        int $id_transfo,
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

                $infos_fiche = array();

                $essence = $registry->getRepository(Essence::class)->find($id_essence);
                $fiche= $registry->getRepository(FicheLot::class)->find($id_fiche);
                $transfo = $registry->getRepository(TypeTransformation::class)->find($id_transfo);

                if ($fiche && $essence){
                   $volume_essence = 0;

                    $billons = $registry->getRepository(Billon::class)->findBy(['code_lot'=>$fiche, 'type_transformation'=>$transfo]);

                    foreach ($billons as $billon){
                        if ($billon->getCodeLignepagelje()->getEssence()->getId() == $essence->getId()){
                            $volume_essence = $volume_essence + $billon->getVolume();
                        }
                    }

                    $infos_fiche[] = array(
                            'volume_fiche'=>round($fiche->getVolume(),3),
                            'essence'=>$essence->getNomVernaculaire(),
                            'volume_essence'=>round($volume_essence,3)
                        );

                }



                return  new JsonResponse(json_encode($infos_fiche));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }


    #[Route('/snvlt/fiches_lot/affiche_billons/{id_fiche}', name: 'affiche_billons_fiche')]
    public function affiche_billons_fiche(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_fiche,
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

                $infos_fiche = array();

                $fiche= $registry->getRepository(FicheLot::class)->find($id_fiche);

                if ($fiche){

                    $billons = $registry->getRepository(Billon::class)->findBy(['code_lot'=>$fiche]);

                    foreach ($billons as $billon){
                        $type_transfo = 0;
                        if($billon->getTypeTransformation()){
                            $type_transfo = $billon->getTypeTransformation()->getId();
                        }
                        $infos_fiche[] = array(
                            'numero_billon'=>$billon->getNumeroBillon(),
                            'essence'=>$billon->getCodeLignepagelje()->getEssence()->getNomVernaculaire(),
                            'lng'=>$billon->getLng(),
                            'dm'=>$billon->getDm(),
                            'vol'=>$billon->getVolume(),
                            'type_transfo'=>$type_transfo
                        );
                    }



                }



                return  new JsonResponse(json_encode($infos_fiche));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('/snvlt/liste_billons/fiche_lot/{id_fiche}', name: 'affiche_billons_fiche_date')]
    public function affiche_billons_fiche_date(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_fiche,
        NotificationRepository $notification,
        PagebrhRepository $pages_brh,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or  $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATTIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $infos_fiche = array();


                $fiche= $registry->getRepository(FicheLot::class)->find($id_fiche);

                if ($fiche){

                    $billons = $registry->getRepository(Billon::class)->findBy(['code_lot'=>$fiche]);

                    foreach ($billons as $billon){
                        $type_transfo = "";
                        if($billon->getTypeTransformation()){
                            $type_transfo = $billon->getTypeTransformation()->getLibelle();
                        }
                        $infos_fiche[] = array(
                            'id_billon'=>$billon->getId(),
                            'numero_billon'=>$billon->getNumeroBillon(),
                            'essence'=>$billon->getCodeLignepagelje()->getEssence()->getNomVernaculaire(),
                            'lng'=>$billon->getLng(),
                            'dm'=>$billon->getDm(),
                            'vol'=>$billon->getVolume(),
                            'type_transfo'=>$type_transfo,
                            'cloture'=>$fiche->isCloture()
                        );
                    }



                }



                return  new JsonResponse(json_encode($infos_fiche));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/fiche_lot/billon/cloture/{id_fiche}', name: 'cloture_fiche_tr')]
    public function cloture_fiche_tr(
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        int $id_fiche,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or  $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATTIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $infos_fiche = array();


                $fiche= $registry->getRepository(FicheLot::class)->find($id_fiche);

                if ($fiche){
                    $fiche->setCloture(true);
                    $registry->getManager()->persist($fiche);
                    $registry->getManager()->flush();
                    $infos_fiche[] = array(
                        'CODE'=>"SUCCESS"
                    );
                }
                return  new JsonResponse(json_encode($infos_fiche));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/fiche_lot/billon/clotureProd/{id_fiche}', name: 'cloture_fiche_tr_prod')]
    public function cloture_fiche_tr_prod(
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        int $id_fiche,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_INDUSTRIEL') or  $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATTIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $infos_fiche = array();


                $fiche= $registry->getRepository(FicheLot::class)->find($id_fiche);

                if ($fiche){
                    $fiche->setClotureProd(true);
                    $registry->getManager()->persist($fiche);
                    $registry->getManager()->flush();
                    $infos_fiche[] = array(
                        'CODE'=>"SUCCESS"
                    );
                }
                return  new JsonResponse(json_encode($infos_fiche));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/fiche_lot/chk/{date_fiche}', name: 'infos_cloture_fiche_tr')]
        public function infos_cloture_fiche_tr(
            Request $request,
            UserRepository $userRepository,
            User $user = null,
            string $date_fiche,
            ManagerRegistry $registry
        ): Response
        {
            if(!$request->getSession()->has('user_session')){
                return $this->redirectToRoute('app_login');
            } else {
                if ($this->isGranted('ROLE_INDUSTRIEL') or  $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATTIF'))
                {
                    $user = $userRepository->find($this->getUser());
                    $code_groupe = $user->getCodeGroupe()->getId();

                    $infos_fiche = array();

                    $dateFiche =  \DateTime::createFromFormat('Y-m-d', $date_fiche);

                    $fiche= $registry->getRepository(FicheLot::class)->findOneBy(['date_lot'=>$dateFiche]);

                    if ($fiche){
                        if ($fiche->isCloture()){
                            $infos_fiche[] = array(
                                'CODE'=>true
                            );
                        } else {
                            $infos_fiche[] = array(
                                'CODE'=>false
                            );
                        }

                    } else {
                        $infos_fiche[] = array(
                            'CODE'=>"ERROR"
                        );
                    }
                    return  new JsonResponse(json_encode($infos_fiche));

                } else {
                    return $this->redirectToRoute('app_no_permission_user_active');
                }
            }
        }


    #[Route('/snvlt/trans/fiche_lot/all', name: 'fiche_lot_all')]
    public function fiche_lot_all(ManagerRegistry $registry,
                                 BillonRepository $billonRepository,
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
            if ($this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_ADMINISTRATTIF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                if ($this->isGranted('ROLE_INDUSTRIEL')){
                $fiches_lots = $registry->getRepository(FicheLot::class)->findBy(['code_usine'=>$user->getCodeindustriel()], ['date_lot'=>'DESC']);
                } else {
                    $fiches_lots = $registry->getRepository(FicheLot::class)->findBy([], ['date_lot'=>'DESC']);
                }

                return $this->render('transformation/billon/fiche_lot.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'fiches'=>$fiches_lots
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
}

    #[Route('/snvlt/liste_billons/fiche_lot/billon/maj_transfo/{id_billon}/{id_type_transfo}', name: 'maj_transfo_billon')]
    public function maj_transfo_billon(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        UserRepository $userRepository,
        User $user = null,
        int $id_billon,
        int $id_type_transfo,
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

                $info_maj = array();


                $billon= $registry->getRepository(Billon::class)->find($id_billon);
                $type_transfo= $registry->getRepository(TypeTransformation::class)->find($id_type_transfo);

                if ($billon && $type_transfo){

                    //$fiche_lot_prod = $registry->getRepository(Elements::class)->findOneBy(['code_billon'=>$billon]);

                    $billon->setTypeTransformation($type_transfo);

                   $registry->getManager()->persist($billon);
                   $registry->getManager()->flush();
                    $info_maj[] = array(
                        'code'=>'SUCCESS'
                    );
                }
                return  new JsonResponse(json_encode($info_maj));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}