<?php

namespace App\Controller;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\DocStatService;
use App\Entity\Autorisation\Attribution;
use App\Entity\Autorisation\Reprise;
use App\Entity\DocStats\DocsStats;
use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Entetes\Documentcp;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\References\Cantonnement;
use App\Entity\References\Ddef;
use App\Entity\References\DocumentOperateur;
use App\Entity\References\Dr;
use App\Entity\References\Exploitant;
use App\Entity\References\Exportateur;
use App\Entity\References\Foret;
use App\Entity\References\Oi;
use App\Entity\References\PosteForestier;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\References\TypeForet;
use App\Entity\References\Usine;
use App\Entity\Requetes\PerformanceBrh;
use App\Entity\Transformation\Billon;
use App\Repository\Autorisations\RepriseRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;

class SituationSnvltController extends AbstractController
{

    public function __construct(
        private DocStatService $docStatService,
        private AdministrationService $administrationService,
    )
    {
    }

    #[Route('snvlt/st752c938b59b99200fa3bb26e80efc9d1/pt/6LfpPfooAAAAAIiyD9NpidUyC9x-yAy8Y17L5hl4', name: 'app_situation_snvlt')]
    public function index(ManagerRegistry $registry): Response
    {
        $liste_docs= array();



        return $this->render('situation_snvlt/index.html.twig',[
            'exercice'=>$this->administrationService->getAnnee()->getAnnee(),
            'documents'=>$registry->getRepository(TypeDocumentStatistique::class)->findAll(),
            'docstats'=>$registry->getRepository(DocsStats::class)->findBy([],['nb_delivres'=>'DESC'], 5, 0),
        ] );
    }

//    ------------------------------------------------------------------------------------------------------
//    ------------------------------------- Point de Situation ---------------------------------------------
//    ------------------------------------------------------------------------------------------------------

    /****************** Reprises ************************/
    #[Route('snvlt/p/st/pt_reprises', name: 'pt_reprises')]
    public function pt_reprises(ManagerRegistry $registry,
                                Request $request,
                                RepriseRepository $repriseRepository): Response
    {
        $exercice = $this->administrationService->getAnnee();
        $reprises = array();
        $point_reprise_value = $repriseRepository->count(['exercice'=>$exercice]);
        $forets = $registry->getRepository(Foret::class)->count(['code_type_foret'=>$registry->getRepository(TypeForet::class)->find(1)]);
        $reprises[] = array(
            'value'=>$point_reprise_value . "/" . $forets
        );
        return new JsonResponse(json_encode($reprises)) ;
    }

    /****************** Opérateurs ************************/
    #[Route('snvlt/p/st/pt_operateurs', name: 'pt_operateurs')]
    public function pt_operateurs(ManagerRegistry $registry,
                                  Request $request): Response
    {
        $exercice = $this->administrationService->getAnnee();
        $operateurs = array();
        //liste Exploitants agréés
        $exps = $registry->getRepository(Exploitant::class)->findAll();
        $nb_exploitants = 0;
        foreach ($exps as $exp){
            $isAgree = false;

            $atts = $registry->getRepository(Attribution::class)->findBy(['code_exploitant'=>$exp, 'statut'=>true, 'exercice'=>$exercice]);
            foreach ($atts as $att){
                $reprise = $registry->getRepository(Reprise::class)->findOneBy(['code_attribution'=>$att]);
                if($reprise){
                    $isAgree = true;
                }
            }
            if ($isAgree){
                $nb_exploitants = $nb_exploitants + 1;
            }
        }

        $operateurs[] = array(
            'exploitants'=>$nb_exploitants. "/" .$registry->getRepository(Exploitant::class)->count([]) ,
            'usines' =>$registry->getRepository(Usine::class)->count([]),
            'exportateurs' =>$registry->getRepository(Exportateur::class)->count([]),
            'drs' =>$registry->getRepository(Dr::class)->count([]),
            'cefs' =>$registry->getRepository(Cantonnement::class)->count([]),
            'ddefs' =>$registry->getRepository(Ddef::class)->count([]),
            'pfs' =>$registry->getRepository(PosteForestier::class)->count([]),
            'ois' =>$registry->getRepository(Oi::class)->count([])
        );
        return new JsonResponse(json_encode($operateurs)) ;
    }

    /****************** Opérateurs ************************/
    #[Route('snvlt/p/st/pt_volume', name: 'pt_volume')]
    public function pt_volume(ManagerRegistry $registry,
                              Request $request,
                              AdministrationService $administrationService): Response
    {
        $exercice = $administrationService->getAnnee();
        $cubage = 0;
        $cubage_billon = 0;
        $volumes = array();
        $cubage_brh = $registry->getRepository(Lignepagebrh::class)->findBy(['exercice'=>$exercice]);
        $billons = $registry->getRepository(Billon::class)->findAll();
        foreach ($cubage_brh as $brh){
            $cubage = $cubage + $brh->getCubageLignepagebrh();
        }
        foreach ($billons as $billon){
            $cubage_billon = $cubage_billon + $billon->getVolume();
        }

        $volumes[] = array(
            'exploitation'=>round($cubage, 3),
            'transformation' =>round($cubage_billon, 3)
        );
        return new JsonResponse(json_encode($volumes)) ;
    }

    #[Route('snvlt/p/st/pt_users/{date_performance}', name: 'pt_agents')]
    public function pt_agents(
        ManagerRegistry $registry,
        string $date_performance,
    ): Response
    {
        $exercice = $this->administrationService->getAnnee();
        $liste_agents= array();


        $dateperformance =\DateTime::createFromFormat('Y-m-d', date('Y-m-d', strtotime($date_performance)));
        //dd(date('Y-m-d', $dateperformance));
        $agents = $registry->getRepository(PerformanceBrh::class)->findBy(['created_at'=>$dateperformance]);
        //dd($date_performance);
        foreach ($agents as $agent){
            $derniere_maj = $registry->getRepository(Lignepagebrh::class)->findOneBy(['exercice'=>$exercice, 'created_by'=>$agent->getCreatedBy()],['created_at'=>'DESC'])->getCreatedAt();
            $date_jour = new \DateTime();
            //$difference = date_diff($date_jour,$derniere_maj,false);
            //$nb_sec = $difference->s + ($difference->i * 60) + ($difference->i * 3600);

            $now = new DateTime(); // Heure de maintenant
            $heure = DateTime::createFromFormat('H:i:s', $derniere_maj->format('H:i:s')); // Heure avec laquelle j'aimerais comparer $now
            //echo $now->format('U') - $heure->format('U'); // Affichage en secondes

            //$difference = date_diff($date_jour,$derniere_maj,false);
            $nb_sec = $date_jour->format('U') - $heure->format('U');


            $liste_agents[] = array(
                'nb_ligne'=>$agent->getNbLigne(),
                'created_by'=>$agent->getCreatedBy(),
                'nb_brh'=>$agent->getNbBrh(),
                'volume'=>$agent->getVolume(),
				'derniere_maj'=>$derniere_maj->format('Y-m-d H:i:s'),
                'nb_sec'=>$nb_sec
            );
            rsort($liste_agents);
        }
        //dd($liste_agents);

        return  new JsonResponse(json_encode($liste_agents));

    }

    #[Route('snvlt/p/st/pt_docs_generated', name: 'pt_docs_generated')]
    public function pt_docs_generated(
        ManagerRegistry $registry,
    ): Response
    {

        $liste_docs= array();
        $exercice = $this->administrationService->getAnnee();

        $type_docs =$registry->getRepository(DocsStats::class)->findBy(['exo'=>$exercice->getId()],['nb_delivres'=>'DESC']);
        //dd($type_docs);
		
			foreach ($type_docs as $type_doc){
				if ($type_doc->getNbDelivres()>0){
					$liste_docs[] = array(
					'generes'=>$type_doc->getNbDelivres(),
					'doc'=>$type_doc->getAbv(),
					'utilises'=>$type_doc->getNbSaisi()
					);
					rsort($liste_docs);
				}
			}

        return  new JsonResponse(json_encode($liste_docs));

    }
}
