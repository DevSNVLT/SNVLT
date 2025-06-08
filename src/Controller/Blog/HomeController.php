<?php

namespace App\Controller\Blog;

use App\Entity\Blog\ArticleBlog;
use App\Repository\Blog\ArticleBlogRepository;
use App\Repository\Blog\CategorieRepository;
use App\Services\OptionsService;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_blog_home')]
    public function index(
        PaginatorInterface $paginator,
        Request $request,
        ManagerRegistry $manager,
        ArticleBlogRepository $articleRepository,
        ArticleBlogRepository $uniqueArticleBlog,
        CategorieRepository $categorieRepository,
        OptionsService $optionsService,
        int $idArticleBlog = 0
    ): Response
    {
        $lastArticleBlog = new ArticleBlog;
        $articles  = $paginator->paginate(
            $articleRepository->findAllArticleBlogs(),
            $request->query->getInt('page',1),
            $optionsService->findValue('blog_articles_limit')
        );
        $idArticleBlog = $uniqueArticleBlog->findLastArticleBlogs();
//dd($idArticleBlog);
         //dd($manager->getRepository(ArticleBlog::class)->find($idArticleBlog[1]));
        $lastArticleBlog = $manager->getRepository(ArticleBlog::class)->find($idArticleBlog[1]);
        //dd($lastArticleBlog);
        return $this->render('portail/website.html.twig', [
            'articles'=>$articles,
            'categories'=>$categorieRepository->findAll(),
            'last_articles'=>$lastArticleBlog
        ]);
    }
}
