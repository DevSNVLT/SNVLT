<?php

namespace App\Controller;

use App\Controller\Services\AdministrationService;
use App\Controller\Services\Utils;
use App\Entity\Admin\Exercice;
use App\Entity\Autorisation\Reprise;
use App\Entity\Blog\ArticleBlog;
use App\Entity\Blog\CategoryPublication;
use App\Entity\DocStats\Saisie\Lignepagebcbgfh;
use App\Entity\DocStats\Saisie\Lignepagebcbp;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\DocStats\Saisie\Lignepagecp;
use App\Entity\Observateur\PublicationRapport;
use App\Entity\References\Essence;
use App\Entity\References\Exploitant;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\References\Usine;
use App\Entity\Transformation\Billon;
use App\Repository\Blog\ArticleBlogRepository;
use App\Repository\Blog\AutresRubriquesRepository;
use App\Repository\Blog\CategorieRepository;
use App\Entity\Vues\EssenceVolumeTop10;
use App\Entity\Autorisation\Attribution;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PortailController extends AbstractController
{
    public function __construct(
        private AdministrationService $administrationService,
        private Utils $utils)
    {
    }

    #[Route('/', name: 'app_portail')]
    public function index(
        ManagerRegistry $registry,
        Request                                $request,
        ManagerRegistry                        $manager,
        ArticleBlogRepository $articleRepository,
        ArticleBlogRepository                      $uniqueArticle,
        CategorieRepository                    $categorieRepository,
        AutresRubriquesRepository $rubriquesRepository,
        int $idArticle = 0): Response
    {
        $lastArticle = new ArticleBlog();

        $idArticle = $uniqueArticle->findLastArticleBlogs();

        $articles =  $articleRepository->findAll();

        $lastArticle = $manager->getRepository(ArticleBlog::class)->find($idArticle[1]);

        //Top 10 Essences
       
        $exercice = $registry->getRepository(Exercice::class)->findOneBy([],['id'=>'DESC'])->getAnnee();
        

        $top10_essences = $registry->getRepository(EssenceVolumeTop10::class)
        ->findBy(['exo'=>$this->administrationService->getAnnee()->getId()],['cubage'=>'DESC'],10,0);

        // Données Transfo
        $donnees_production = array();
        $liste_mois = ['1', '2', '3', '4', '5', '6','7', '8', '9', '10', '11', '12'];


        foreach ($liste_mois as $mois){
            $billons = $registry->getRepository(Billon::class)->findAll();

            $vol_sciage = 0;
            $vol_deroulage = 0;
            $vol_tranchage = 0;

            foreach ($billons as $billon){
                $mois_decoupage =(int) $billon->getDateBillonnage()->format('m');
                //dd($mois_decoupage);
                if ((int)$mois == $mois_decoupage && $billon->getTypeTransformation()){
                    if ($billon->getTypeTransformation()->getId() == 1){
                        $vol_sciage = $vol_sciage + round($billon->getVolume(), 3);
                    } elseif ($billon->getTypeTransformation()->getId() == 2){
                        $vol_deroulage = $vol_deroulage + round($billon->getVolume(), 3);
                    } elseif ($billon->getTypeTransformation()->getId() == 3){
                        $vol_tranchage = $vol_tranchage + round($billon->getVolume(), 3);
                    }
                }
            }
            $donnees_production[] = array(
                'mois'=>$mois,
                'sciage'=>$vol_sciage,
                'deroulage'=>$vol_deroulage,
                'tranchage'=>$vol_tranchage
            );

        }
        sort($donnees_production);

        // Documents délivrés
        $doc_snvlt = array();
        $liste_docs = $registry->getRepository(TypeDocumentStatistique::class)->findBy(['statut'=>'ACTIF']);
        foreach ($liste_docs as $doc){
            $nb_doc = 0;
            if ($doc->getId() == 1){
                $nb_doc = $doc->getDocumentcps()->count();
            }elseif ($doc->getId() == 2){
                $nb_doc = $doc->getDocumentbrhs()->count();
            }elseif ($doc->getId() == 3){
                $nb_doc = $doc->getDocumentbcbps()->count();
            }elseif ($doc->getId() == 4){
                $nb_doc = $doc->getDocumentetatbs()->count();
            }elseif ($doc->getId() == 5){
                $nb_doc = $doc->getDocumentljes()->count();
            }elseif ($doc->getId() == 6){
                $nb_doc = $doc->getDocumentbtgus()->count();
            }elseif ($doc->getId() == 7){
                $nb_doc = $doc->getDocumentfps()->count();
            }elseif ($doc->getId() == 9){
                $nb_doc = $doc->getDocumentetate2s()->count();
            }elseif ($doc->getId() == 10){
                $nb_doc = $doc->getDocumentetatgs()->count();
            }elseif ($doc->getId() == 11){
                $nb_doc = $doc->getDocumentetaths()->count();
            }elseif ($doc->getId() == 12){
                $nb_doc = $doc->getDocumentdmps()->count();
            }elseif ($doc->getId() == 13){
                $nb_doc = $doc->getDocumentdmvs()->count();
            }elseif ($doc->getId() == 14){
                $nb_doc = $doc->getDocumentbths()->count();
            }elseif ($doc->getId() == 15){
                $nb_doc = $doc->getDocumentpdtdrvs()->count();
            }elseif ($doc->getId() == 18){
                $nb_doc = $doc->getDocumentbcburbs()->count();
            }elseif ($doc->getId() == 19){
                $nb_doc = $doc->getDocumentbrepfs()->count();
            }elseif ($doc->getId() == 20){
                $nb_doc = $doc->getDocumentrsdpfs()->count();
            }

            $doc_snvlt[] = array(
                'docname'=>$doc->getAbv(),
                'nb_doc'=>$nb_doc
            );
        }
        sort($doc_snvlt);

        // Chiffres clés
        $stats = [];

        $exercice = $registry->getRepository(Exercice::class)->findOneBy([], ['id' => 'DESC']);

        $totalExploitantExercice = $registry->getRepository(Exploitant::class)->count([]);
        $totalUsineExercice = $registry->getRepository(Usine::class)->count([]);
        $totalEssenceExercice = $registry->getRepository(Essence::class)->count([]);

        $totalArbresExercice = 0;

        $totalArbresExercice = $registry->getRepository(Lignepagebrh::class)
               ->count(['exercice'=>$this->administrationService->getAnnee(), 'lettre_lignepagebrh'=>'A']);
        $totalArbresExercice = $totalArbresExercice + $registry->getRepository(Lignepagebcbgfh::class)
               ->count(['exercice'=>$this->administrationService->getAnnee(), 'lettre_lignepagebcbgfh'=>'A']);
        $totalArbresExercice = $totalArbresExercice + $registry->getRepository(Lignepagebcbp::class)
               ->count(['exercice'=>$this->administrationService->getAnnee(), 'lettre'=>'A']);

        // Volume BRH
        $totalVolumeExercice = 0;
        foreach ($registry->getRepository(Lignepagebrh::class)->findBy(['exercice'=>$this->administrationService->getAnnee()]) as $ligne){
            $totalVolumeExercice = $totalVolumeExercice + $ligne->getCubageLignepagebrh();
        }
        foreach ($registry->getRepository(Lignepagebcbgfh::class)->findBy(['exercice'=>$this->administrationService->getAnnee()]) as $ligne){
            $totalVolumeExercice = $totalVolumeExercice + $ligne->getCubageLignepagebcbgfh();
        }
        foreach ($registry->getRepository(Lignepagebcbp::class)->findBy(['exercice'=>$this->administrationService->getAnnee()]) as $ligne){
            $totalVolumeExercice = $totalVolumeExercice + $ligne->getVolume();
        }

        $totalRepriseExercice = $registry->getRepository(Reprise::class)
               ->count(['exercice'=>$exercice]);
       
       
        //Stats en fonction de l'année d'exercice en cours...
        
        $stats[] = array(
            'nbExploitants'=>$totalExploitantExercice,
            'nbUsines'=>$totalUsineExercice,
            'nbEssence'=>$totalEssenceExercice,
            'nbArbres'=>number_format(intval($totalArbresExercice), 0, ',','.'),
            'volumeArbres'=>number_format(intval($totalVolumeExercice), 0, ',','.'),
            'reprises'=>$totalRepriseExercice
        );

        return $this->render('portail/index.html.twig',[
            'liste_essences'=>$top10_essences,
            'donnees_transformation'=>$donnees_production,
            'docs_delivres'=>$doc_snvlt,
            /*'exercices'=>$exploitation->findAll(),*/
            'articles_blog'=>$articles,
            'categories'=>$categorieRepository->findAll(),
            'last_articles'=>$lastArticle,
            'stats'=>$stats,
            'infos_ministre'=>$rubriquesRepository->find(1),
            'exercice_en_cours'=>$registry->getRepository(Exercice::class)->findOneBy(['cloture'=>false],['id'=>'DESC']),
            'infos_publiques'=>$registry->getRepository(CategoryPublication::class)->findAll(),
            'oi_rapports'=>$registry->getRepository(PublicationRapport::class)->findBy(['statut'=>'PUBLIE'],['created_at'=>'DESC'],5,0),
        ]);
    }
}
