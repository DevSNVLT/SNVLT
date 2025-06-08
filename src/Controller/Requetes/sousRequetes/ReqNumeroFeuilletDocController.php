<?php

namespace App\Controller\Requetes\sousRequetes;

use App\Entity\DocStats\Entetes\Documentbrh;
use App\Entity\DocStats\Pages\Pagebrh;
use App\Entity\DocStats\Saisie\Lignepagebrh;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\Requetes\MenuRequetes;
use App\Entity\Requetes\TypeRequetes;
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

class ReqNumeroFeuilletDocController extends AbstractController
{
    #[Route('/snvlt/req/numero/feuillet/doc', name: 'app_req_feuillet')]
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
            return $this->render('requetes/sous_requetes/req_numero_feuillet_doc/index.html.twig', [
                'liste_menus'=>$menus->findOnlyParent(),
                "all_menus"=>$menus->findAll(),
                'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                'groupe'=>$code_groupe,
                'liste_parent'=>$permissions,
                'listes_type_docs'=>$registry->getRepository(TypeDocumentStatistique::class)->findAll()

            ]);

        } else {
            return $this->redirectToRoute('app_no_permission_user_active');
        }
       }
    }

    #[Route('/snvlt/rechercher_feuillet/{numero_page}/{type_doc}', name: 'rechercher_feuillet')]
    public function rechercher_feuillet(
        Request $request,
        UserRepository $userRepository,
        string $numero_page = null,
        int $type_doc = null,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {

                $response = array();

                $typedoc = $registry->getRepository(TypeDocumentStatistique::class)->find($type_doc);

                if ($typedoc){
                    switch ($typedoc->getId()){
                        case 2:
                            $docs_brh = $registry->getRepository(Documentbrh::class)->findBy(['type_document'=>$typedoc]);
                            foreach ($docs_brh as $doc){
                                $pagebrhs= $registry->getRepository(Pagebrh::class)->findBy(['code_docbrh'=>$doc]);
                                foreach ($pagebrhs as $pagebrh){
                                    if ($pagebrh->getNumeroPagebrh() == $numero_page){
                                        $response[] = array(
                                            'id_brh'=> $doc->getId(),
                                            'numero_brh'=>$doc->getNumeroDocbrh()
                                        );
                                    }

                                }
                            }
                    }
                }


                return  new JsonResponse(json_encode($response));



            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }

    #[Route('/snvlt/feuillet/infos/{numero_feuillet}/{id_doc}/{type_doc}', name: 'infos_feuillet')]
    public function infos_feuillet(
        Request $request,
        UserRepository $userRepository,
        string $numero_feuillet = null,
        int $id_doc = null,
        int $type_doc = null,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN'))
            {

                $response = array();

                $typedoc = $registry->getRepository(TypeDocumentStatistique::class)->find($type_doc);

                if ($typedoc){
                    switch ($typedoc->getId()){
                        case 2:
                            $doc_brh = $registry->getRepository(Documentbrh::class)->find($id_doc);
                            if($doc_brh){
                                $pagebrh= $registry->getRepository(Pagebrh::class)->findOneBy(['code_docbrh'=>$doc_brh, 'numero_pagebrh'=>$numero_feuillet]);

                                if ($pagebrh){
                                    $cubage = 0;
                                    $nb_lignes = 0;
                                    $usine = "-";
                                    $exploitant = "-";
									$date_chr;
                                    $essences = array();
                                    $lignes_brh = $registry->getRepository(Lignepagebrh::class)->findBy(['code_pagebrh'=>$pagebrh]);

                                        foreach ($lignes_brh as $ligne) {
                                            $cubage = $cubage + $ligne->getCubageLignepagebrh();
                                            $nb_lignes = $nb_lignes + 1;
                                            $essences[] = array(
                                                'essence'=>$ligne->getNomEssencebrh()->getNomVernaculaire()
                                            );
                                        }
                                            if ($doc_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle()){
                                                $exploitant = $doc_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getSigle(). " [" . $doc_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant() . "]";
                                            } else {
                                                $exploitant = $doc_brh->getCodeReprise()->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant();
                                            }

                                            if ($pagebrh->getParcUsineBrh()){
                                                $usine = $pagebrh->getParcUsineBrh()->getRaisonSocialeUsine(). " [" .  $pagebrh->getDestinationPagebrh() . "]";
                                            }
											if ($pagebrh->getDateChargementbrh()){
                                                $date_chr = $pagebrh->getDateChargementbrh()->format('d/m/Y');
                                            }


                                        $response[] = array(
                                            'foret'=> $doc_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getDenomination(). " [" . $doc_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeTypeForet()->getLibelle() . "]",
                                            'cantonnement'=> $doc_brh->getCodeReprise()->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement(),
                                            'exploitant'=>$exploitant,
                                            'usine'=>$usine,
                                            'date_chargement'=>$date_chr,
                                            'nb_lignes'=>$nb_lignes,
                                            'cubage'=>round($cubage,3)
                                            //'essences'=>implode(',', array_unique($essences))
                                        );


                                }
                            }
                    }
                }


                return  new JsonResponse(json_encode($response));



            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

        }
    }
}
