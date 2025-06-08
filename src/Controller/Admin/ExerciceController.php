<?php

namespace App\Controller\Admin;

use App\Controller\Services\AdministrationService;
use App\Entity\Admin\Exercice;
use App\Entity\Autorisation\Attribution;
use App\Entity\User;
use App\Repository\Administration\NotificationRepository;
use App\Repository\DocStats\Entetes\DocumentcpRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use PhpParser\Node\Expr\New_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExerciceController extends AbstractController
{
    public function __construct(private AdministrationService $administrationService,
                                private ManagerRegistry $registry)
    {
    }

    #[Route('/admin/exercices', name: 'app_admin_exercice')]
    public function index(
        Request $request,
        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification
    ): Response
    {


        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN')  )
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $avant_dernier_exercice = $this->registry->getRepository(Exercice::class)->findOneBy([
                    'annee'=>$this->administrationService->getAnnee()->getAnnee()-1
                ]);
                if ($avant_dernier_exercice){
                    if (!$avant_dernier_exercice->getRallonge()){
                        $avant_dernier_exercice_value = $avant_dernier_exercice->getAnnee();
                    } else {
                        $avant_dernier_exercice_value = 0;
                    }
                } else {
                    $avant_dernier_exercice_value = 0;
                }
                return $this->render('admin/exercice/index.html.twig', [
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'groupe'=>$code_groupe,
                    'liste_parent'=>$permissions,
                    'av_der_exo'=>$avant_dernier_exercice_value
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }

            }
    }

    #[Route('snvlt/admin/exercices/load', name: 'app_load_exercices')]
    public function load_exercices(
        Request $request,
        ManagerRegistry $registry
    ): Response
    {


        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
           $liste_exos = array();
           $exercices = $registry->getRepository(Exercice::class)->findBy([],['annee'=>'DESC']);
           foreach ($exercices as $exo){
               $date_expiration = "-";
               $nb_jour_restants = 0;
               if ($exo->getDateExpirationRallonge()){
                   $date_expiration = $exo->getDateExpirationRallonge()->format('d/m/Y');
                   if ($exo->getDateExpirationRallonge() >= new \DateTime()){
                       $nb_jour_restants = date_diff($exo->getDateExpirationRallonge(),new \DateTime())->days + 1;
                   }
               }
               $liste_exos[] = array(
                   'exo'=>$exo->getAnnee(),
                   'actif'=>$exo->isCloture(),
                   'rallonge'=>$exo->getRallonge(),
                   'nb_mois'=>$exo->getNbMois(),
                   'date_expiration'=>$date_expiration,
                   'nb_jours_restants'=>$nb_jour_restants
               );
           }
           return new JsonResponse(json_encode($liste_exos));

        }
    }

    #[Route('snvlt/admin/exercices/create/new', name: 'app_create_new_exercice')]
    public function app_create_new_exercice(
        Request $request,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')  )
            {

           $liste_exos = array();
           $last_exercice = $registry->getRepository(Exercice::class)->findOneBy(['cloture'=>false],['annee'=>'DESC']);
           if ($last_exercice){
               $exercice = new Exercice();
               $exercice->setAnnee($last_exercice->getAnnee() + 1);
               $exercice->setCloture(false);
               $last_exercice->setCloture(true);
               $registry->getManager()->persist($last_exercice);
               $registry->getManager()->persist($exercice);


               // Copie Attribution
               $attributions = $registry->getRepository(Attribution::class)->findBy(['exercice'=>$last_exercice]);
               foreach($attributions as $attribution){
                   $new_att = new Attribution();
                   $new_att->setExercice($exercice);
                   $new_att->setStatut($attribution->isStatut());
                   $new_att->setCreatedAt(new \DateTimeImmutable());
                   $new_att->setCreatedBy($this->getUser());
                   $new_att->setReprise(false);
                   $new_att->setCodeExploitant($attribution->getCodeExploitant());
                   $new_att->setCodeForet($attribution->getCodeForet());
                   $new_att->setNumeroDecision($attribution->getNumeroDecision());
                   $new_att->setDateDecision($attribution->getDateDecision());
                   $registry->getManager()->persist($new_att);
               }

               $registry->getManager()->flush();


               $liste_exos[] = array(
                   'code'=>'SUCCESS'
               );
           } else {
               $liste_exos[] = array(
                   'code'=>'ERROR'
               );
           }

           return new JsonResponse(json_encode($liste_exos));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/admin/exercice/verifying', name: 'verifying_exo')]
    public function verifying_exo(
        Request $request,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {

           $liste_exos = array();
           $last_exercice = $registry->getRepository(Exercice::class)->findOneBy(['cloture'=>false],['annee'=>'DESC']);
           $cookie_exercice = 0;
           if ($request->getSession()->has("exercice")){
               $cookie_exercice =(int) $request->getSession()->get("exercice");
           }
               $liste_exos[] = array(
                   'exo'=> $last_exercice->getAnnee(),
                   'annee_cookie'=>$cookie_exercice
               );

           return new JsonResponse(json_encode($liste_exos));

        }
    }
    #[Route('snvlt/admin/exercice/rall/av_exo/{nb_mois}', name: 'app_rallonge_av_exo')]
    public function app_rallonge_av_exo(
        Request $request,
        int $nb_mois,
        ManagerRegistry $registry
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {

           $reponse = array();
            $dernier_exercice = $this->registry->getRepository(Exercice::class)->findOneBy(['cloture'=>false],['id'=>'DESC']);
            $avant_dernier_exercice = $this->registry->getRepository(Exercice::class)->findOneBy([
                'annee'=>$dernier_exercice->getAnnee() -1
            ]);
           if ($avant_dernier_exercice and $nb_mois){
               $aujourdhui = new \DateTime();
               $mois = '+' . $nb_mois . ' month';

               $date_expire = date('Y-m-d',strtotime($mois,strtotime($aujourdhui->format('Y-m-d'))));
               //dd($date_expire);
               $avant_dernier_exercice->setRallonge(true);
               $avant_dernier_exercice->setNbMois((int) $nb_mois);
               $avant_dernier_exercice->setDateExpirationRallonge( \DateTime::createFromFormat('Y-m-d', $date_expire) );
                $this->registry->getManager()->persist($avant_dernier_exercice);
                $this->registry->getManager()->flush();
               $reponse[] = array(
                   'code'=> "SUCCESS"
               );
           }
            $reponse[] = array(
                   'code'=> 'NOT_FOUND_OR_ERROR'
               );

           return new JsonResponse(json_encode($reponse));

        }
    }
}
