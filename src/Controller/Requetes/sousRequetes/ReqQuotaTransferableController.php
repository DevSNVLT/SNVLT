<?php

namespace App\Controller\Requetes\sousRequetes;

use App\Entity\Admin\Exercice;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\References\Essence;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Entity\References\QualiteExploitant;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\Requetes\Qt;
use App\Entity\Requetes\QuotaTransferable;
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

class ReqQuotaTransferableController extends AbstractController
{
    #[Route('/requetes/sous/requetes/req/qtr', name: 'app_req_qtr')]
    public function app_req_qtr(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        User $user = null,
        NotificationRepository $notification): Response
    { if(!$request->getSession()->has('user_session')){
        return $this->redirectToRoute('app_login');
    } else {
        if (
            $this->isGranted('ROLE_ADMINISTRATIF') or
            $this->isGranted('ROLE_MINEF') or
            $this->isGranted('ROLE_ADMIN')
        ) {
            $user = $userRepository->find($this->getUser());
            $code_groupe = $user->getCodeGroupe()->getId();

            $liste_exploitants = array();
            $code_industriel = $registry->getRepository(QualiteExploitant::class)->find(3);
            $exploitants = $registry->getRepository(Exploitant::class)->findBy([],['raison_sociale_exploitant'=>'ASC']);
            foreach ($exploitants as $exploitant){
                $qualites = $exploitant->getCodeQualite();
                foreach ($qualites as $qualite){
                    if ($qualite == $code_industriel){
                        $liste_exploitants[] = array(
                            'sigle'=>$exploitant->getSigle(),
                            'raisonSocialeExploitant'=>$exploitant->getRaisonSocialeExploitant(),
                            'id'=>$exploitant->getId()
                        );
                    }
                }
            }
            sort($liste_exploitants);
        return $this->render('requetes/sous_requetes/qtr/index.html.twig', [
            'liste_menus'=>$menus->findOnlyParent(),
            "all_menus"=>$menus->findAll(),
            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
            'groupe'=>$code_groupe,
            'liste_parent'=>$permissions,
            'donnees'=>$registry->getRepository(Qt::class)->findAll(),
            'exploitants'=>$liste_exploitants,
            'exercices'=>$registry->getRepository(Exercice::class)->findBy([],['id'=>'DESC']),
            'forets'=>$registry->getRepository(Foret::class)->findBy([],['denomination'=>'ASC'])
        ]);

        } else {
            return $this->redirectToRoute('app_no_permission_user_active');
        }
      }
    }
    #[Route('/snvlt/req/exploitation/qtr/all/{id_exercice}', name: 'rechercher_tous_les_qtrs')]
    public function rechercher_tous_les_qtrs(
        Request $request,
        ManagerRegistry $registry,
        int $id_exercice
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF') )
            {

                $exploitants_quota = array();

                $exercice = $registry->getRepository(Exercice::class)->find($id_exercice);
                if ($exercice){
                    $exp_quotas = $registry->getRepository(QuotaTransferable::class)->findBy(['id_annee'=>$exercice->getId()]);

                    foreach ($exp_quotas as $quota){
                        if ($quota->getCubage() > 0){
                            $exploitants_quota[] =array(
                                'id'=>$quota->getId(),
                                'foret'=>$quota->getNumeroForet(),
                                'exploitant'=>$quota->getRsExp(),
                                'quota'=>round($quota->getQuota(),0),
                                'qt'=>round($quota->getTiersQuota(),0),
                                'cubage'=>round($quota->getCubage(),0),
                                'ecart'=>round($quota->getTiersQuota() - $quota->getCubage(),0)
                            );
                        }

                    }

                }


                return  new JsonResponse(json_encode($exploitants_quota));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('/snvlt/req/exploitation/qtr_exp/{id_exploitant}/{id_exercice}', name: 'rechercher_tous_les_qtrs_exp')]
    public function rechercher_tous_les_qtrs_exp(
        Request $request,
        ManagerRegistry $registry,
        int $id_exercice,
        int $id_exploitant
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF') )
            {

                $exploitants_quota = array();

                $exercice = $registry->getRepository(Exercice::class)->find($id_exercice);
                $exploitant = $registry->getRepository(Exploitant::class)->find($id_exploitant);
                if ($exercice && $exploitant){
                    $exp_quotas = $registry->getRepository(QuotaTransferable::class)->findBy(['id_annee'=>$exercice->getId(), 'id_exp'=>$exploitant->getId()]);

                    foreach ($exp_quotas as $quota){
                        if ($quota->getCubage() > 0){
                            $exploitants_quota[] =array(
                                'foret'=>$quota->getNumeroForet(),
                                'id'=>$quota->getId(),
                                'quota'=>round($quota->getQuota(),0),
                                'qt'=>round($quota->getTiersQuota(),0),
                                'cubage'=>round($quota->getCubage(),0),
                                'ecart'=>round($quota->getTiersQuota() - $quota->getCubage(),0)
                            );
                        }

                    }

                }

                sort($exploitants_quota);
                return  new JsonResponse(json_encode($exploitants_quota));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('/snvlt/req/exploitation/qtr_exp/{id_foret}/{id_exercice}', name: 'rechercher_tous_les_qtrs_foret')]
    public function rechercher_tous_les_qtrs_foret(
        Request $request,
        ManagerRegistry $registry,
        int $id_exercice,
        int $id_foret,
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or $this->isGranted('ROLE_MINEF') )
            {

                $exploitants_quota = array();

                $exercice = $registry->getRepository(Exercice::class)->find($id_exercice);
                $foret= $registry->getRepository(Foret::class)->find($id_foret);
                if ($exercice && $foret){
                    $exp_quotas = $registry->getRepository(QuotaTransferable::class)->findBy(['id_annee'=>$exercice->getId(), 'numero_foret'=>$foret->getNumeroForet()]);

                    foreach ($exp_quotas as $quota){
                        if ($quota->getCubage() > 0){
                            $exploitants_quota[] =array(
                                'id'=>$quota->getId(),
                                'foret'=>$quota->getNumeroForet(),
                                'exploitant'=>$quota->getRsExp(),
                                'quota'=>round($quota->getQuota(),0),
                                'qt'=>round($quota->getTiersQuota(),0),
                                'cubage'=>round($quota->getCubage(),0),
                                'ecart'=>round($quota->getTiersQuota() - $quota->getCubage(),0)
                            );
                        }

                    }

                }


                return  new JsonResponse(json_encode($exploitants_quota));

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
    #[Route('snvlt/qtr/details/{numero_foret}/{id_exercice}', name: 'qtr_numero_foret')]
    public function qtr_numero_foret(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry,
        string $numero_foret,
        int $id_exercice
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


                $details= array();


                $exercice = $registry->getRepository(Exercice::class)->find($id_exercice);
                if ($exercice){
                    $qts = $registry->getRepository(Qt::class)->findBy(['numero_foret'=>$numero_foret, 'id_annee'=>$id_exercice]);
                    foreach ($qts as $qt){

                        $details[] = array(
                            'foret'=>$numero_foret,
                            'exploitant'=>$qt->getRsExp(),
                            'usine'=>$qt->getRsUsine(),
                            'date_chargement'=>$qt->getDateChargementbrh()->format('d/m/Y'),
                            'cubage'=>round($qt->getCubage(),3)
                        );

                    }
                }


                return  new JsonResponse(json_encode($details));


            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }
}