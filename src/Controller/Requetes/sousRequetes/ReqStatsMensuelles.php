<?php

namespace App\Controller\Requetes\sousRequetes;

use App\Entity\Admin\Exercice;
use App\Entity\References\Essence;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Entity\References\TypeForet;
use App\Entity\Requetes\StatsByExploitantAnneeEssence;
use App\Entity\Requetes\StatsByForetAnneeEssence;
use App\Entity\Requetes\StatsMensuelles;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReqStatsMensuelles extends AbstractController
{
    public function __construct(private ManagerRegistry $registry)
    {
    }

    #[Route('/snvlt/req/exploitation/stats', name: 'app_req_exp_stats')]
    public function index(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
        if (
            $this->isGranted('ROLE_ADMINISTRATIF') or
            $this->isGranted('ROLE_MINEF') or
            $this->isGranted('ROLE_ADMIN')
        ) {
            $user = $userRepository->find($this->getUser());
            $code_groupe = $user->getCodeGroupe()->getId();



            return $this->render('requetes/sous_requetes/stats/index.html.twig', [
                'liste_menus'=>$menus->findOnlyParent(),
                "all_menus"=>$menus->findAll(),
                'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                'groupe'=>$code_groupe,
                'liste_parent'=>$permissions,
                'forets'=>$registry->getRepository(Foret::class)->findBy(['code_type_foret'=>$registry->getRepository(TypeForet::class)->find(1)],['denomination'=>'ASC']),
                'exploitants'=>$registry->getRepository(Exploitant::class)->findBy([],['raison_sociale_exploitant'=>'ASC']),
                'essences'=>$registry->getRepository(Essence::class)->findBy([],['nom_vernaculaire'=>'ASC']),
                'exercices'=>$registry->getRepository(Exercice::class)->findBy([],['id'=>'DESC'])
            ]);

        } else {
            return $this->redirectToRoute('app_no_permission_user_active');
        }
       }
    }

    public function getMonthResults(int $annee) : array
    {
        $valeurs_stats = array();
        $donnees_stats = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
        foreach ($donnees_stats as $donnees_stat){
            $stats = $this->registry->getRepository(StatsMensuelles::class)->findBy(['annee'=>$annee], ['mois'=>'ASC']);
            $volume = 0;
            foreach ($stats as $stat){
                if ($stat->getMois() == $donnees_stat){
                    $volume = $volume + $stat->getCubage();
                }
            }
            $valeur_mois = $this->getMonthName($donnees_stat);
            //dd($valeur_mois);
            $valeurs_stats[] = array(
                'mois'=>$valeur_mois,
                'volume'=>round($volume, 3)
            );
        }
        return $valeurs_stats;
    }


    #[Route('/snvlt/req/exploitation/stats/tous-les-mois/{id_exercice}', name: 'rechercher_chr_stats_exercice')]
    public function rechercher_chr_stats_exercice(
        Request $request,
        int $id_exercice,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {

                $exercice = $registry->getRepository(Exercice::class)->find($id_exercice);
                $valeurs_stats = array();
                $donnees_stats = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
                if ($exercice){
                    foreach ($donnees_stats as $donnees_stat){
                        $stats = $this->registry->getRepository(StatsMensuelles::class)->findBy(['annee'=>$exercice->getAnnee()], ['mois'=>'ASC']);
                        $volume = 0;
                        $nb_billes = 0;
                        foreach ($stats as $stat){
                            if ($stat->getMois() == $donnees_stat){
                                $volume = $volume + $stat->getCubage();
                                $nb_billes = $nb_billes + $stat->getNbBilles();
                            }
                        }
                        $valeur_mois = $this->getMonthName($donnees_stat);
                        //dd($valeur_mois);
                        $valeurs_stats[] = array(
                            'mois'=>$valeur_mois,
                            'volume'=>round($volume, 3),
                            'nb_billes'=>$nb_billes
                        );
                    }
                }



                return  new JsonResponse(json_encode($valeurs_stats));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }


    public  function getMonthName(int $numero): string
    {
        $mois = "Aucun";

        switch ($numero){
            case 1:
                $mois = "Janvier";
                break;
            case 2:
                $mois = "Février";
                break;
             case 3:
                $mois = "Mars";
                 break;
             case 4:
                $mois = "Avril";
                 break;
             case 5:
                $mois = "Mai";
                 break;
             case 6:
                $mois = "Juin";
                 break;
             case 7:
                $mois = "Juillet";
                 break;
             case 8:
                $mois = "Août";
                 break;
             case 9:
                $mois = "Septembre";
                 break;
             case 10:
                $mois = "Octobre";
                 break;
             case 11:
                $mois = "Novembre";
                 break;
             case 12:
                $mois = "Décembre";
        }

        return $mois;

    }

    #[Route('/snvlt/req/exploitation/stats/tous-les-mois/exploitant/{id_exploitant}/{id_exercice}', name: 'rechercher_stats_exercice_exp')]
    public function rechercher_stats_exercice_exp(
        Request $request,
        int $id_exercice,
        int $id_exploitant,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {

                $exercice = $registry->getRepository(Exercice::class)->find($id_exercice);
                $exploitant = $registry->getRepository(Exploitant::class)->find($id_exploitant);
                $valeurs_stats = array();
                $donnees_stats = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
                if ($exercice && $exploitant){
                    foreach ($donnees_stats as $donnees_stat){
                        $stats = $this->registry->getRepository(StatsMensuelles::class)->findBy(['annee'=>$exercice->getAnnee(), 'exploitant_id'=>$exploitant->getId()], ['mois'=>'ASC']);
                        $volume = 0;
                        $nb_billes = 0;
                        foreach ($stats as $stat){
                            if ($stat->getMois() == $donnees_stat){
                                $volume = $volume + $stat->getCubage();
                                $nb_billes = $nb_billes + $stat->getNbBilles();
                            }
                        }
                        $valeur_mois = $this->getMonthName($donnees_stat);
                        //dd($valeur_mois);
                        $valeurs_stats[] = array(
                            'numero_mois'=>$donnees_stat,
                            'mois'=>$valeur_mois,
                            'volume'=>round($volume, 3),
                            'nb_billes'=>$nb_billes
                        );
                    }
                }
                sort($valeurs_stats);
                return  new JsonResponse(json_encode($valeurs_stats));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('/snvlt/req/exploitation/stats/tous-les-mois/essence/{id_essence}/{id_exercice}', name: 'rechercher_stats_exercice_essence')]
    public function rechercher_stats_exercice_essence(
        Request $request,
        int $id_exercice,
        int $id_essence,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {

                $exercice = $registry->getRepository(Exercice::class)->find($id_exercice);
                $essence = $registry->getRepository(Essence::class)->find($id_essence);
                $valeurs_stats = array();
                $donnees_stats = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
                if ($exercice && $essence){
                    foreach ($donnees_stats as $donnees_stat){
                        $stats = $this->registry->getRepository(StatsMensuelles::class)->findBy(['annee'=>$exercice->getAnnee(), 'essence_id'=>$essence->getId()], ['mois'=>'ASC']);
                        $volume = 0;
                        $nb_billes = 0;
                        foreach ($stats as $stat){
                            if ($stat->getMois() == $donnees_stat){
                                $volume = $volume + $stat->getCubage();
                                $nb_billes = $nb_billes + $stat->getNbBilles();
                            }
                        }
                        $valeur_mois = $this->getMonthName($donnees_stat);
                        //dd($valeur_mois);
                        $valeurs_stats[] = array(
                            'numero_mois'=>$donnees_stat,
                            'mois'=>$valeur_mois,
                            'volume'=>round($volume, 3),
                            'nb_billes'=>$nb_billes
                        );
                    }
                }
                sort($valeurs_stats);
                return  new JsonResponse(json_encode($valeurs_stats));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('/snvlt/req/exploitation/stats/tous-les-mois/foret/{id_foret}/{id_exercice}', name: 'rechercher_stats_exercice_foret')]
    public function rechercher_stats_exercice_foret(
        Request $request,
        int $id_exercice,
        int $id_foret,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {

                $exercice = $registry->getRepository(Exercice::class)->find($id_exercice);
                $foret = $registry->getRepository(Foret::class)->find($id_foret);
                $valeurs_stats = array();
                $donnees_stats = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
                if ($exercice && $foret){
                    foreach ($donnees_stats as $donnees_stat){
                        $stats = $this->registry->getRepository(StatsMensuelles::class)->findBy(['annee'=>$exercice->getAnnee(), 'foret_id'=>$foret->getId()], ['mois'=>'ASC']);
                        $volume = 0;
                        $nb_billes = 0;
                        foreach ($stats as $stat){
                            if ($stat->getMois() == $donnees_stat){
                                $volume = $volume + $stat->getCubage();
                                $nb_billes = $nb_billes + $stat->getNbBilles();
                            }
                        }
                        $valeur_mois = $this->getMonthName($donnees_stat);

                        $valeurs_stats[] = array(
                            'numero_mois'=>$donnees_stat,
                            'mois'=>$valeur_mois,
                            'volume'=>round($volume, 3),
                            'nb_billes'=>$nb_billes
                        );
                    }
                }
                sort($valeurs_stats);
                return  new JsonResponse(json_encode($valeurs_stats));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('/snvlt/req/exploitation/stats/tous-les-mois/exploitant/mois/{id_exploitant}/{mois}/{id_exercice}', name: 'rechercher_stats_exercice_exp_mois')]
    public function rechercher_stats_exercice_exp_mois(
        Request $request,
        int $id_exercice,
        int $id_exploitant,
        int $mois,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {

                $exercice = $registry->getRepository(Exercice::class)->find($id_exercice);
                $exploitant = $registry->getRepository(Exploitant::class)->find($id_exploitant);

                $valeurs_stats = array();
                $donnees_stats = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
                if ($exercice && $exploitant && $mois){

                        $stats = $this->registry->getRepository(StatsByExploitantAnneeEssence::class)->findBy(['annee'=>$exercice->getAnnee(), 'exploitant_id'=>$exploitant->getId(), 'mois'=>$mois]);

                        foreach ($stats as $stat){

                                $valeurs_stats[] = array(
                                    'essence'=>$stat->getNomVernaculaire(),
                                    'volume'=>round($stat->getCubage(), 3),
                                    'nb_billes'=>$stat->getNbBilles()
                                );
                        }

                   // }
                }
                sort($valeurs_stats);
                return  new JsonResponse(json_encode($valeurs_stats));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/req/exploitation/stats/tous-les-mois/foret/mois/{id_foret}/{mois}/{id_exercice}', name: 'rechercher_stats_exercice_foret_mois')]
    public function rechercher_stats_exercice_foret_mois(
        Request $request,
        int $id_exercice,
        int $id_foret,
        int $mois,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {

                $exercice = $registry->getRepository(Exercice::class)->find($id_exercice);
                $foret = $registry->getRepository(Foret::class)->find($id_foret);

                $valeurs_stats = array();

                if ($exercice && $foret && $mois){

                        $stats = $this->registry->getRepository(StatsByForetAnneeEssence::class)->findBy(['annee'=>$exercice->getAnnee(), 'foret_id'=>$foret->getId(), 'mois'=>$mois]);

                        foreach ($stats as $stat){

                                $valeurs_stats[] = array(
                                    'essence'=>$stat->getNomVernaculaire(),
                                    'volume'=>round($stat->getCubage(), 3),
                                    'nb_billes'=>$stat->getNbBilles()
                                );
                        }

                   // }
                }
                sort($valeurs_stats);
                return  new JsonResponse(json_encode($valeurs_stats));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/req/exploitation/stats/tous-les-mois/essence/mois/{id_essence}/{mois}/{id_exercice}', name: 'rechercher_stats_exercice_essence_mois')]
        public function rechercher_stats_exercice_essence_mois(
            Request $request,
            int $id_exercice,
            int $id_essence,
            int $mois,
            ManagerRegistry $registry
        ): Response
        {
            if(!$request->getSession()->has('user_session')){
                return $this->redirectToRoute('app_login');
            } else {
                if ($this->isGranted('ROLE_ADMIN'))
                {
    
                    $exercice = $registry->getRepository(Exercice::class)->find($id_exercice);
                    $essence = $registry->getRepository(Essence::class)->find($id_essence);
    
                    $valeurs_stats = array();
    
                    if ($exercice && $essence && $mois){
    
                            $stats = $this->registry->getRepository(StatsByForetAnneeEssence::class)->findBy(['annee'=>$exercice->getAnnee(), 'essence_id'=>$essence->getId(), 'mois'=>$mois]);
    
                            foreach ($stats as $stat){
    
                                    $valeurs_stats[] = array(
                                        'foret'=>$stat->getDenomination(),
                                        'volume'=>round($stat->getCubage(), 3),
                                        'nb_billes'=>$stat->getNbBilles()
                                    );
                            }
    
                       // }
                    }
                    sort($valeurs_stats);
                    return  new JsonResponse(json_encode($valeurs_stats));
    
                } else {
                    return $this->redirectToRoute('app_no_permission_user_active');
                }
    
            }
        }
    

}
