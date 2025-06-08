<?php

namespace App\Controller;

use App\Entity\Blog\CategoryPublication;
use App\Entity\Blog\FichierPublication;
use App\Entity\Blog\Publication;
use App\Repository\Blog\CategoryPublicationRepository;
use App\Repository\Blog\PublicationRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InformationsPubliquesController extends AbstractController
{
    #[Route('/informations/publiques', name: 'app_informations_publiques')]
    public function index(CategoryPublicationRepository $categoryPublicationRepository ,PublicationRepository $publicationRepository): Response
    {

        return $this->render('informations_publiques/index.html.twig', [
            "categories_publication"=>$categoryPublicationRepository->findAll(),
            "publications"=>$publicationRepository
        ]);
    }
    #[Route('/informations/publiques/{slug?_}', name: 'app_infos_publiques')]
    public function infos_publiques(
        ManagerRegistry $registry,
        string    $slug): Response
    {
        $categorie = $registry->getRepository(CategoryPublication::class)->findOneBy(['slug'=>$slug]);


        return $this->render('informations_publiques/index.html.twig', [
            "categorie"=>$categorie
        ]);
    }
    #[Route('infos/public/telecharger-textes/{id_pub?0}', name: 'fichiers_publiques_infos')]
    public function fichiers_publiques_infos(
        ManagerRegistry $registry,
        int             $id_pub): Response
    {

        $publication = $registry->getRepository(Publication::class)->find($id_pub);

            $fichiers = $registry->getRepository(FichierPublication::class)->findBy(['code_publication'=>$publication]);


        return $this->render('informations_publiques/files.html.twig', [
            "fichiers"=>$fichiers,
            'publication'=>$publication
        ]);
    }
}
