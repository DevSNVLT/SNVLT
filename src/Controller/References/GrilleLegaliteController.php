<?php

namespace App\Controller\References;


use App\Entity\References\CircuitCommunication;
use App\Entity\References\DocumentOperateur;
use App\Entity\References\GrilleLegalite;
use App\Entity\References\TypeOperateur;
use App\Entity\User;
use App\Form\References\GrilleLegaliteType;
use App\Repository\Administration\NotificationRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\References\GrilleLegaliteRepository;
use App\Repository\References\TypeOperateurRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class GrilleLegaliteController extends AbstractController
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    #[Route('snvlt/ref/gl', name: 'ref_grille_legalite')]
    public function listing(GrilleLegaliteRepository $grille_legalites,
    MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        Request $request,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification,
        TypeOperateurRepository $typeop
        ): Response
    {
        if (!$request->getSession()->has('user_session')) {

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $titre = $this->translator->trans("Edit Legality grid");

                $code_exploitant = $typeop->find(2);
                $code_industriel = $typeop->find(3);
                $code_exportateur = $typeop->find(4);

                return $this->render('references/grille_legalite/index.html.twig', [
                    'liste_gl_exp' => $grille_legalites->findBy(['code_operateur'=>$code_exploitant]),
                    'liste_gl_ind' => $grille_legalites->findBy(['code_operateur'=>$code_industriel]),
                    'liste_gl_expo' => $grille_legalites->findBy(['code_operateur'=>$code_exportateur]),
                    'liste_menus' => $menus->findOnlyParent(),
                    "all_menus" => $menus->findAll(),
                    'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
                    'groupe' => $code_groupe,
                    'titre' => $titre,
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                ]);

            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }

    }


    #[Route('snvlt/ref/edit/gl/{id_grille_legalite?0}', name: 'grille_legalite.edit')]
    public function editGrilleLegalite(
        GrilleLegalite $grille_legalite = null,
        ManagerRegistry $doctrine,
        Request $request,
        GrilleLegaliteRepository $grille_legalites,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        int $id_grille_legalite,
        UserRepository $userRepository,
        User $user = null,
        NotificationRepository $notification): Response
    {
        if(!$request->getSession()->has('user_session')){

            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')) {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();
                $date_creation = new \DateTimeImmutable();
                $code_groupe = $groupeRepository->find(1);
                $titre = $this->translator->trans("Edit Document template");
                $grille_legalite = $grille_legalites->find($id_grille_legalite);
                //dd($grille_legalite);
                if (!$grille_legalite) {
                    $new = true;
                    $grille_legalite = new GrilleLegalite();
                    $titre = $this->translator->trans("Add Document template");

                    $grille_legalite->setCreatedAt($date_creation);
                    $grille_legalite->setCreatedBy($this->getUser());
                }

                $new = false;
                if (!$grille_legalite) {
                    $new = true;
                    $grille_legalite = new GrilleLegalite();
                }
                $form = $this->createForm(GrilleLegaliteType::class, $grille_legalite);

                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {

                    $grille_legalite->setUpdatedAt($date_creation);
                    $grille_legalite->setUpdatedBy($this->getUser());

                    $manager = $doctrine->getManager();
                    $manager->persist($grille_legalite);
                    $manager->flush();

                    $this->addFlash('success', $this->translator->trans("Document template has been edited successfully"));
                    return $this->redirectToRoute("ref_grille_legalites");
                } else {
                    return $this->render('references/grille_legalite/add-grille_legalite.html.twig', [
                        'form' => $form->createView(),
                        'titre' => $titre,
                        'liste_grille_legalites' => $grille_legalites->findAll(),
                        'liste_menus' => $menus->findOnlyParent(),
                        "all_menus" => $menus->findAll(),
                        'menus' => $permissions->findBy(['code_groupe_id' => $code_groupe]),
                        'groupe' => $code_groupe,
                        'liste_parent'=>$permissions,
                        'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    ]);
                }
                /*}*/
            }else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }

    #[Route('infos-publiques/grilles-de-legalite/exploitants-forestiers', name: 'grille_legalite_infos_exp')]
    public function grille_legalite_infos(

        ManagerRegistry $doctrine,
        GrilleLegaliteRepository $grille_legalites): Response
    {
        $exploitant = $doctrine->getRepository(TypeOperateur::class)->find(2);
        return $this->render('exploitant/grille_legalite.html.twig', [
            'grille' => $grille_legalites->findBy(['code_operateur'=>$exploitant], ['libelle_document'=>'ASC'])
        ]);

    }
    #[Route('snvlt/grille/{type_grille?0}/{id_operateur?0}', name: 'display_doc_op')]
    public function display_doc_op(

        MenuRepository $menus,
        MenuPermissionRepository $permissions,
        User $user = null,
        int $type_grille,
        int $id_operateur,
        UserRepository $userRepository,
        ManagerRegistry $doctrine,
        Request $request,
        NotificationRepository $notification
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_MINEF') or $this->isGranted('ROLE_ADMIN')) {
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();


                $reponse = array();
                $has_doc = false;
                $docs = $doctrine->getRepository(GrilleLegalite::class)->findBy(['code_operateur'=>$type_grille]);
                foreach ($docs as $doc){

                    $doc_op = $doctrine->getRepository(DocumentOperateur::class)->findOneBy(
                        [
                            'codeOperateur'=>$id_operateur,
                            'code_document_grille'=>$doc
                        ]
                    );

                    $fichier = "-";
                    $date_etablissement = '-';
                    $date_expiration = '-';
                    $ecart_date = 0;
                    $id_doc = 0;
                    if ($doc_op){
                        $id_doc = $doc_op->getId();
                        $has_doc = true;
                        $fichier = $doc_op->getImageName();
                        $date_etablissement = $doc_op->getDateEtablissement()->format('d/m/Y');
                        $date_expiration = $doc_op->getDateExpiration()->format('d/m/Y');
                        $ecart_date = date_diff($doc_op->getDateExpiration(), $doc_op->getDateEtablissement())->days ;

                    }
                    $reponse[] = array(
                            'id'=>$id_doc,
                            'document'=>$doc->getLibelleDocument(),
                             'expire'=>$doc->getDuree(),
                                'fichier'=>$fichier,
                                'date_etablissement'=>$date_etablissement,
                                'date_expiration'=>$date_expiration,
                                'ecart'=>$ecart_date,
                                'has_doc'=>$has_doc
                    );
                }
                //dd($has_doc);
            }


                return  new JsonResponse(json_encode($reponse));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }


#[Route('snvlt/RecercheDoc/{id_grille?0}', name: 'rech_description')]
    public function rech_description(
        int $id_grille,
        UserRepository $userRepository,
        ManagerRegistry $doctrine,
        Request $request
    ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_EXPLOITANT') or $this->isGranted('ROLE_INDUSTRIEL') or $this->isGranted('ROLE_EXPORTATEUR') ) {

                $reponse = array();

                    $doc = $doctrine->getRepository(GrilleLegalite::class)->find($id_grille);
                    if ($doc){
                        $reponse[] = array(
                            'description'=>$doc->getDescriptionDocument()
                        );
                    }
                return  new JsonResponse(json_encode($reponse));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
