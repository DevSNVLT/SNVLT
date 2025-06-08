<?php

namespace App\Controller\Autorisation;

use App\Entity\Admin\Option;
use App\Entity\Autorisation\AttributionPv;
use App\Entity\References\DocumentOperateur;
use App\Entity\References\Exploitant;
use App\Entity\References\Foret;
use App\Entity\References\TypeOperateur;
use App\Entity\User;
use App\Form\Autorisation\AttributionPvType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\Autorisations\AttributionPvRepository;
use App\Repository\DocumentOperateurRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\TypeAutorisationRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class AttributionPvController extends AbstractController
{

    public function __construct(private TranslatorInterface $translator)
    {}

    #[Route('/snvlt/admin/att/pv', name: 'app_attributionpv')]
    public function index(AttributionPvRepository $attributionpvRepository,
                          MenuRepository $menus,
                          MenuPermissionRepository $permissions,
                          GroupeRepository $groupeRepository,
                          Request $request,
                          UserRepository $userRepository,
                          User $user = null,
                          NotificationRepository $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $titre = $this->translator->trans("Add an attribution PV");


                return $this->render('autorisation/attributionpv/index.html.twig', [
                    'attributionpvs' => $attributionpvRepository->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'titre'=>$titre,
                    'liste_parent'=>$permissions
                ]);
            } else {
                return  $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/ref/edit/attr/pv', name: 'edit_attr_pv')]
    public function edit_attr_pv(
        Request $request,
        ManagerRegistry $registry,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        UserRepository $userRepository,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $titre = $this->translator->trans("Add an attribution PV");


                return $this->render('autorisation/attributionpv/add-attributionpv.html.twig', [

                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'titre'=>$titre,
                    'liste_parent'=>$permissions,
                    'parcelles'=>$registry->getRepository(Foret::class)->findBy(['code_type_foret'=>3, 'attribue'=>false], ['numero_foret'=>'ASC'])
                ]);
            } else {
                return  $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('snvlt/add-new/attpv/{data}', name: 'ajouter_attr_pv')]
    public function editAttrPv(
        Request $request,
        ManagerRegistry $registry,
        string $data,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        UserRepository $userRepository,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or  $this->isGranted('ROLE_ADMINISTRATIF') or $this->isGranted('ROLE_ADMIN'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $reponse = array();
                if ($data){
                    $arraydata = json_decode($data);
                    $parcelle = $registry->getRepository(Foret::class)->find($arraydata->parcelle);
                    if ($parcelle and  $parcelle->isAttribue()){
                        $reponse[] = array(
                            'code'=>'PARCELLE_ATTRIBUEE'
                        );
                    } else {
                        $attributionPv = new  AttributionPv();

                        $date_attribution = new \DateTime();
                        $date_attribution = $arraydata->date_decision;

                        //$date_attribution = str_replace("-", "/", $arraydata->date_decision,);
                        //dd($date_attribution);
                        $attributionPv->setStatut(true);
                        $attributionPv->setRaisonSociale(strtoupper($arraydata->attributaire));
                        $attributionPv->setNumeroDecision($arraydata->numero_decision);
                        $attributionPv->setDateDecision(\DateTime::createFromFormat('Y-m-d', $date_attribution));

                            $attributionPv->setCodeParcelle($parcelle);

                        $attributionPv->setMobilePersonneRessource($arraydata->numero_mobile);
                        $attributionPv->setCreatedAt(new \DateTimeImmutable());
                        $attributionPv->setCreatedBy($user);

                        $registry->getManager()->persist($attributionPv);

                        $attributionPv->getCodeParcelle()->setAttribue(true);
                        $registry->getManager()->persist($attributionPv->getCodeParcelle());

                        $registry->getManager()->flush();
                        $reponse[] = array(
                            'code'=>'SUCCESS'
                        );
                    }

                } else {
                    $reponse[] = array(
                        'code'=>'ERROR'
                    );
                }
            return new JsonResponse(json_encode($reponse));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }


}
