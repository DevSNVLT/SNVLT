<?php

namespace App\Controller\Blog;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PageController extends AbstractController
{
    #[Route('/blog/page/{slug}', name: 'page_show')]
    public function show(): Response
    {
        return $this->render('blog/page/show.html.twig', [
            'controller_name' => 'PageController',
        ]);
    }
}
