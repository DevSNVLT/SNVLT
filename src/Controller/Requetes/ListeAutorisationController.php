<?php

namespace App\Controller\Requetes;

use App\Controller\Services\AdministrationService;
use App\Entity\Autorisation\Reprise;
use App\Entity\References\Exploitant;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\TypeAutorisationRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Proxies\__CG__\App\Entity\Autorisation\Attribution;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListeAutorisationController extends AbstractController
{

    public function __construct(private AdministrationService $administrationService)
    {
    }

    #[Route('snvlt/requetes/liste/auto/agr', name: 'app_requetes_liste_reprises')]
    public function index(
        Request $request,
        UserRepository $userRepository,
        ManagerRegistry $registry,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        User $user = null,
        NotificationRepository $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') ) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


            }
        }
        return $this->render('requetes/liste_reprises/index.html.twig', [
            'liste_menus'=>$menus->findOnlyParent(),
            "all_menus"=>$menus->findAll(),
            'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
            'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
            'groupe'=>$code_groupe,
            'liste_parent'=>$permissions,
            'exercice'=>$this->administrationService->getAnnee()->getAnnee()
        ]);
    }


    #[Route('snvlt/req_liste/reprises', name: 'req_liste.list')]
    public function affiche_docs_reprises(
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
            $reprises = $registry->getRepository(Reprise::class)->findBy([],['date_autorisation'=>'DESC']);

            $liste_docs_attribution = array();

            foreach ($reprises as $reprise) {

                if($reprise->getCodeAttribution()->getCodeForet()->getCodeCantonnement()){
                    $cef = $reprise->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getNomCantonnement();
                    $dr = $reprise->getCodeAttribution()->getCodeForet()->getCodeCantonnement()->getCodeDr()->getDenomination();
                } else {
                    $cef = "-";
                    $dr = "-";
                }

                $liste_docs_attribution[] = array(
                    'id_reprise' => $reprise->getId(), //ID du document issu de la grille légalité
                    'numero_autorisation' => $reprise->getNumeroAutorisation(). " du ". $reprise->getDateAutorisation()->format('d/m/Y'),
                    'exploitant'=>$reprise->getCodeAttribution()->getCodeExploitant()->getMarteauExploitant(). " - ". $reprise->getCodeAttribution()->getCodeExploitant()->getRaisonSocialeExploitant(),
                    'foret'=>$reprise->getCodeAttribution()->getCodeForet()->getDenomination(),
                    'cef'=>$cef,
                    'dr'=>$dr
                );
            }
            return new JsonResponse(json_encode($liste_docs_attribution));
        }

    }

    #[Route('snvlt/req_liste/exp', name: 'req_liste_exp.list')]
    public function affiche_docs_exp(
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
            $exploitants = $registry->getRepository(Exploitant::class)->findBy([],['raison_sociale_exploitant'=>'ASC']);

            $liste_docs_attribution = array();

            foreach ($exploitants as $exploitant) {

                if($exploitant->getCodeCantonnement()){
                    $cef = $exploitant->getCodeCantonnement()->getNomCantonnement();
                    $dr = $exploitant->getCodeCantonnement()->getCodeDr()->getDenomination();
                } else {
                    $cef = "-";
                    $dr = "-";
                }

                $liste_docs_attribution[] = array(
                    'id_exploitant' => $exploitant->getId(), //ID du document issu de la grille légalité
                    'code' => $exploitant->getNumeroExploitant(),
                    'exploitant'=>$exploitant->getRaisonSocialeExploitant(),
                    'sigle'=>$exploitant->getSigle(),
                    'marteau'=>$exploitant->getMarteauExploitant(),
                    'cef'=>$cef,
                    'dr'=>$dr
                );
            }
            return new JsonResponse(json_encode($liste_docs_attribution));
        }

    }

}
