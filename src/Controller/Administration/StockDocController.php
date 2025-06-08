<?php

namespace App\Controller\Administration;


use App\Controller\Services\Utils;
use App\Entity\Administration\DocStatsGen;
use App\Entity\Administration\StockDoc;
use App\Entity\References\PageDocGen;
use App\Entity\References\TypeDocumentStatistique;
use App\Entity\User;
use App\Events\Administration\AddStockDocEvent;
use App\Form\Administration\StockDocType;
use App\Repository\Admin\ExerciceRepository;
use App\Repository\Administration\DocStatsGenRepository;
use App\Repository\Administration\NotificationRepository;
use App\Repository\Administration\StockDocRepository;
use App\Repository\GroupeRepository;
use App\Repository\MenuPermissionRepository;
use App\Repository\MenuRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class StockDocController extends AbstractController
{

    public function __construct(
        private EventDispatcherInterface $dispatcher,
        private TranslatorInterface $translator,
        private ManagerRegistry $rm,
        private Utils $utils)
    {

    }

    #[Route('snvlt/ref/std', name: 'ref_stockdocs')]

    public function listing(StockDocRepository $stockdocs,
    MenuRepository $menus,
        MenuPermissionRepository $permissions,
        GroupeRepository $groupeRepository,
        Request $request,
        NotificationRepository $notification,
        UserRepository $userRepository,
        User $user = null,
        ): Response
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or  $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

            $titre = $this->translator->trans("Edit Stock");


                return $this->render('administration/stockdoc/index.html.twig', [
                    'liste_stockdocs' => $stockdocs->findAll(),
                    'stock_doc'=>$stockdocs,
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'titre'=>$titre,
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                ]);
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }





    #[Route('snvlt/ref/std/edit', name: 'stockdoc.edit')]
    public function editStockDoc(
        StockDoc $stockdoc = null,
        ManagerRegistry $doctrine,
        Request $request,
        StockDocRepository $stockdocs,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        UserRepository $userRepository,
        GroupeRepository $groupeRepository,
        NotificationRepository $notification,
        ExerciceRepository $exerciceRepository,
        User $user = null)
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or  $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

            $date_creation = new \DateTimeImmutable();
            $docstatgen = $doctrine->getRepository(TypeDocumentStatistique::class)->find(2);
            $nb = $doctrine->getRepository(PageDocGen::class)->findLastPage(1);

            $stockdoc = new StockDoc();
            $titre = $this->translator->trans("Add Stock");

            $stockdoc->setCreatedAt($date_creation);
            $stockdoc->setCreatedBy($this->getUser());

            $form = $this->createForm(StockDocType::class, $stockdoc);

            $form->handleRequest($request);

            if ( $form->isSubmitted() && $form->isValid() ){


                $stockdoc->setUpdatedAt($date_creation);
                $stockdoc->setUpdatedBy($this->getUser());

                $stockdoc->setTypeMouvement('APPROVISIONNEMENT');

                $manager = $doctrine->getManager();
                $manager->persist($stockdoc);
                $manager->flush();

                //Crer l'evenement
                $addDocstockEvent = new AddStockDocEvent($stockdoc);

                //Dispatcher l'evenement
                $this->dispatcher->dispatch($addDocstockEvent, AddStockDocEvent::ADD_DOCSTOCK_EVENT);

                $this->addFlash('success',$this->translator->trans("The document stock has just been successfully updated"));
                return $this->redirectToRoute("ref_stockdocs");
            } else {
                return $this->render('administration/stockdoc/add-stockdoc.html.twig',[
                    'form' =>$form->createView(),
                    'titre'=>$titre,
                    'liste_stockdocs' => $stockdocs->findAll(),
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                   // 'exo'=>$exercice,
                    'nb'=>$nb,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                    'liste_parent'=>$permissions
                ]);
            }
        } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
    }
    }


    #[Route('snvlt/ref/std/show/{id_stock}', name: 'stockdoc.show')]
    public function showMouvement(
        StockDoc $stockdoc = null,
        ManagerRegistry $doctrine,
        Request $request,
        DocStatsGenRepository $stockdocs,
        StockDocRepository $stock,
        UserRepository $userRepository,
        MenuPermissionRepository $permissions,
        MenuRepository $menus,
        GroupeRepository $groupeRepository,
        ExerciceRepository $exerciceRepository,
        NotificationRepository $notification,
        User $user = null,
        int $id_stock)
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or  $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                return $this->render('administration/stockdoc/show-stockdoc.html.twig',[
                    'liste_menus'=>$menus->findOnlyParent(),
                    "all_menus"=>$menus->findAll(),
                    'menus'=>$permissions->findBy(['code_groupe_id'=>$code_groupe]),
                    'groupe'=>$code_groupe,
                    'details_doc'=>$stockdocs->shwoDocumentsGenDetails($id_stock),
                    'detail_stock'=>$stock->find($id_stock),
                    'liste_parent'=>$permissions,
                    'mes_notifs'=>$notification->findBy(['to_user'=>$user, 'lu'=>false],[],5,0),
                ]);
        } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
         }
    }

    #[Route('snvlt/ref/std/alldocssituation/', name: 'stockdoc_json')]
    public function situation_docs(
        ManagerRegistry $doctrine,
        Request $request,
        UserRepository $userRepository,
        User $user = null)
    {
        if(!$request->getSession()->has('user_session')){
            return $this->redirectToRoute('app_login');
        } else {
            if ($this->isGranted('ROLE_ADMIN') or  $this->isGranted('ROLE_MINEF'))
            {
                $user = $userRepository->find($this->getUser());
                $code_groupe = $user->getCodeGroupe()->getId();

                $liste_docs_type = $doctrine->getRepository(TypeDocumentStatistique::class)->findAll();
                $liste = array();

                foreach ($liste_docs_type as $doc_type){
                    $stockrestant = $this->utils->getDocsRestants($doc_type);
                    $liste[] = array(
                        'id_doc'=>$doc_type->getId(),
                        'libelle'=>$doc_type->getAbv(),
                        'stock_restant'=>$stockrestant,
                        'stock_alert'=>$this->utils->getStateStock($doc_type),
                    );
                }
                return new JsonResponse(json_encode($liste));
            } else {
                return $this->redirectToRoute('app_no_permission_user_active');
            }
        }
    }
}
