<?php

namespace App\Controller\Blog;

use App\Entity\Blog\Categorie;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
    #[Route('/categorie-articles/{slug}', name: 'categorie_show')]
    public function show(Categorie $categorie): Response
    {
        if (!$categorie){
            return $this->redirectToRoute('app_portail');
        }

        return $this->render('blog/categorie/show.html.twig', [
            'categorie' => $categorie
        ]);
    }
}
