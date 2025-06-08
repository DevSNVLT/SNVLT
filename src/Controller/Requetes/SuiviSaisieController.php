<?php

namespace App\Controller\Requetes;

use App\Controller\Services\AdministrationService;
use App\Entity\DocStats\Saisie\Lignepagebcbgfh;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\Requetes\PerformanceBrhJour;
use App\Entity\Requetes\PerformanceBrh;
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
use App\Entity\References\Essence;
use App\Entity\References\Usine;
use App\Entity\References\ZoneHemispherique;
use App\Entity\References\SousPrefecture;

class SuiviSaisieController extends AbstractController
{
    public function __construct(private AdministrationService $administrationService)
    {
    }


    #[Route('/suivi/saisie', name: 'app_suivi_saisie')]
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



                return $this->render('requetes/suivi_saisie/index.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'exercice'=>$this->administrationService->getAnnee()->getAnnee(),
					'essences'=>$registry->getRepository(Essence::class)->findBy([], ['nom_vernaculaire'=>'ASC']),
                    'zones'=>$registry->getRepository(ZoneHemispherique::class)->findBy([], ['zone'=>'ASC']),
                    'usines'=>$registry->getRepository(Usine::class)->findBy([], ['raison_sociale_usine'=>'ASC']),
                    'villes'=>$registry->getRepository(SousPrefecture::class)->findBy([], ['nom_sousprefecture'=>'ASC']),
					'suivi_jour'=>$registry->getRepository(PerformanceBrhJour::class)->findBy([],['created_at'=>'DESC'])
                ]);

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

    #[Route('snvlt/rechercher_saisie/{date_debut}/{date_fin}', name: 'recherche_saisie')]
    public function recherche_concessions(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        User $user = null,
        string $date_debut,
        string $date_fin,
        NotificationRepository $notification
    ): Response
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


                $liste_sasies = array();


                $datedebut = \DateTime::createFromFormat('Y-m-d', $date_debut);
                $datefin = \DateTime::createFromFormat('Y-m-d', $date_fin);

                $saisies = $registry->getRepository(Lignepagebrh::class)->findBy([],['id'=>'DESC']);
                $saisies_bcbgfh = $registry->getRepository(Lignepagebcbgfh::class)->findBy([],['id'=>'DESC']);

                //Recherche des saisies BRH
                foreach ($saisies as $saisie){
					//dd($saisie->getCreatedAt()->format('Y-m-d'));
                    if ($saisie->getCreatedAt()->format('Y-m-d') >= $datedebut->format('Y-m-d') && $saisie->getCreatedAt()->format('Y-m-d') <= $datefin->format('Y-m-d')){
                        $zh = "-";
                        $essence = "-";
						if($saisie->getZhLignepagebrh()){
							$zh = $saisie->getZhLignepagebrh()->getZone();
						}
						if($saisie->getNomEssencebrh()){
							$essence = $saisie->getNomEssencebrh()->getNomVernaculaire();
						}

                        $exploitant_str = "-";
                        $foret = "-";
						if ($saisie->getCodePagebrh()){
							if ($saisie->getCodePagebrh()->getCodeDocbrh()){
								$exploitant = $saisie->getCodePagebrh()->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeExploitant();
								if ($exploitant->getSigle()){
									$exploitant_str = $exploitant->getSigle();
								} else {
									$exploitant_str = $exploitant->getRaisonSocialeExploitant();
								}
								$foret = $saisie->getCodePagebrh()->getCodeDocbrh()->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination();
							}
						
						$usine = "-";
						//$date_chargement = "-";


						if ($saisie->getCodePagebrh()->getParcUsineBrh()){
							$usine = $saisie->getCodePagebrh()->getParcUsineBrh()->getRaisonSocialeUsine();
						}

						if ($saisie->getCodePagebrh()->getDateChargementbrh()){
							$date_chargement = $saisie->getCodePagebrh()->getDateChargementbrh()->format('d/m/y');
						} else {
							$date_chargement = "-";
						}


                        $liste_sasies[] = array(
							'date_saisie'=>$saisie->getCreatedAt()->format('d/m/Y h:i:s'),
                            'id'=>$saisie->getId(),
                            'numero_ligne'=>$saisie->getNumeroLignepagebrh(),
                            'essence'=>$essence,
                            'zh'=>$zh,
                            'x'=>$saisie->getXLignepagebrh(),
                            'y'=>$saisie->getYLignepagebrh(),
                            'lettre'=>$saisie->getLettreLignepagebrh(),
                            'lng'=>$saisie->getLongeurLignepagebrh(),
                            'dm'=> $saisie->getDiametreLignepagebrh(),
                            'cubage'=>round($saisie->getCubageLignepagebrh(),3),
                            'agent'=>$saisie->getCreatedBy(),
                            'exploitant'=>$exploitant_str,
                            'foret'=>$foret,
                            'date_chargement'=>$date_chargement ,
                            'usine'=>$usine,
                            'type_doc'=>'BRH'
                        );
						}
                    }
                }

                //Recherche des saisies BCBGFH
                foreach ($saisies_bcbgfh as $saisie){
                    //dd($saisie->getCreatedAt()->format('Y-m-d'));
                    if ($saisie->getCreatedAt()->format('Y-m-d') >= $datedebut->format('Y-m-d') && $saisie->getCreatedAt()->format('Y-m-d') <= $datefin->format('Y-m-d')){
                        $zh = "-";
                        $essence= "-";
                        if($saisie->getZhLignepagebcbgfh()){
                            $zh = $saisie->getZhLignepagebcbgfh()->getZone();
                        }

                        if($saisie->getNomEssencebcbgfh()){
                            $essence = $saisie->getNomEssencebcbgfh()->getNomVernaculaire();
                        }

                        $exploitant = $saisie->getCodePagebcbgfh()->getCodeDocbcbgfh()->getCodeContrat()->getCodeExploitant();
                        if ($exploitant->getSigle()){
                            $exploitant_str = $exploitant->getSigle();
                        } else {
                            $exploitant_str = $exploitant->getRaisonSocialeExploitant();
                        }
                        $foret = $saisie->getCodePagebcbgfh()->getCodeDocbcbgfh()->getCodeContrat()->getCodeForet()->getDenomination();

                        $usine = "-";
                        //$date_chargement = "-";


                        if ($saisie->getCodePagebcbgfh()->getParcUsineBcbgfh()){
                            $usine = $saisie->getCodePagebcbgfh()->getParcUsineBcbgfh()->getRaisonSocialeUsine();
                        }

                        if ($saisie->getCodePagebcbgfh()->getDateChargementbcbgfh()){
                            $date_chargement = $saisie->getCodePagebcbgfh()->getDateChargementbcbgfh()->format('d/m/y');
                        } else {
                            $date_chargement = "-";
                        }


                        $liste_sasies[] = array(
                            'date_saisie'=>$saisie->getCreatedAt()->format('d/m/Y h:i:s'),
                            'id'=>$saisie->getId(),
                            'numero_ligne'=>$saisie->getNumeroLignepagebcbgfh(),
                            'essence'=>$essence,
                            'zh'=>$zh,
                            'x'=>$saisie->getXLignepagebcbgfh(),
                            'y'=>$saisie->getYLignepagebcbgfh(),
                            'lettre'=>$saisie->getLettreLignepagebcbgfh(),
                            'lng'=>$saisie->getLongeurLignepagebcbgfh(),
                            'dm'=> $saisie->getDiametreLignepagebcbgfh(),
                            'cubage'=>round($saisie->getCubageLignepagebcbgfh(),3),
                            'agent'=>$saisie->getCreatedBy(),
                            'exploitant'=>$exploitant_str,
                            'foret'=>$foret,
                            'date_chargement'=>$date_chargement ,
                            'usine'=>$usine,
                            'type_doc'=>'BCBGFH'

                        );
                    }
                }


				rsort($liste_sasies);
                return  new JsonResponse(json_encode($liste_sasies));


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

	#[Route('snvlt/performances/users/{date_performance}', name: 'performances_agents')]
    public function performances_agents(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry,
        string $date_performance,
    ): Response
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


                $liste_agents= array();


                $dateperformance =\DateTime::createFromFormat('Y-m-d', date('Y-m-d', strtotime($date_performance)));
				//dd(date('Y-m-d', $dateperformance));
                $agents = $registry->getRepository(PerformanceBrh::class)->findBy(['created_at'=>$dateperformance]);
				//dd($date_performance);
                foreach ($agents as $agent){

                    $liste_agents[] = array(
                            'created_by'=>$agent->getCreatedBy(),
                            'nb_ligne'=>$agent->getNbLigne(),
                            'nb_brh'=>$agent->getNbBrh(),
                            'volume'=>$agent->getVolume()
                        );

                }

                return  new JsonResponse(json_encode($liste_agents));


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }

	#[Route('snvlt/performances_jours', name: 'performances_jours')]
    public function performances_jours(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry
    ): Response
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


                $liste_performances= array();


                //dd(date('Y-m-d', $dateperformance));
                $performances = $registry->getRepository(PerformanceBrhJour::class)->findBy([], ['created_at'=>'DESC']);

                foreach ($performances as $performance){

                    $liste_performances[] = array(
						'id_performance'=>$performance->getIdPerformance(),
                        'created_at'=>$performance->getCreatedAt()->format('d-m-Y'),
                        'nb_ligne'=>$performance->getNbLigne(),
                        'nb_brh'=>$performance->getNbBrh(),
                        'volume'=>$performance->getVolume()
                    );

                }

                return  new JsonResponse(json_encode($liste_performances));


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
}
