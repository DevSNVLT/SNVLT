<?php

namespace App\Controller\Blog;

use App\Entity\Blog\ArticleBlog;
use App\Entity\Blog\Comment;
use App\Form\Blog\CommentType;
use App\Repository\Blog\ArticleBlogRepository;
use App\Repository\Blog\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleBlogController extends AbstractController
{
    #[Route('/actualite/{slug}', name: 'article_blog_show')]
    public function show(
        ?ArticleBlog $article,
        ArticleBlogRepository $articleBlogRepository,
        CategorieRepository $categorieRepository,
    ): Response
    {
        if (!$article){
            return $this->redirectToRoute('app_blog_home');
        }
        $comment = new Comment($article);

        $commentForm = $this->createForm(CommentType::class, $comment);

        return $this->render('blog/article/show.html.twig', [
            'liste_articles'=>$articleBlogRepository->findBy([],['createdAt'=>'DESC'],5,0),
            'categories_articles'=>$categorieRepository->findAll(),
            'article' => $article,
            'commentForm'=>$commentForm->createView()
        ]);
    }
    #[Route('/actualites-agridevaf', name: 'articles_blog_show')]
    public function showAllArticles(
        ArticleBlogRepository $articleBlogRepository,
        CategorieRepository $categorieRepository,
    ): Response
    {
        return $this->render('blog/article/tous_les_articles.html.twig', [
            'liste_articles'=>$articleBlogRepository->findBy([],['createdAt'=>'DESC'],10,0),
            'categories_articles'=>$categorieRepository->findAll()
        ]);
    }
}
