<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     * @Route("/", name="home")
     */
    public function index(ArticleRepository $repo): Response
    {
        $articles=$repo->findAll();
        return $this->render('blog/index.html.twig', [
            'articles'=>$articles
        ]);
    }

    /**
     * @Route("/blog/show/{id}", name="blog_show")
     */
    public function show(Article $article): Response
    {
        return $this->render('blog/show.html.twig', [
            'article'=>$article
        ]);
    }

    /**
     * @Route("/blog/create", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function create(Article $article = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        $create = false;
        if($article == null) {
            $article = new Article();
            $create = true;
        }

        $article = new Article();
        $form = $this->createFormBuilder($article)
            ->add('title')
            ->add('description')
            ->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($article);
            $entityManager->flush();
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }
        return $this->render('blog/create.html.twig', [
            'formArticle'=>$form->createView(),
            'create'=> $create
        ]);
    }

    /**
     * @Route("/blog/{id}/delete", name="blog_delete")
     */
    public function delete(Article $article, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($article);
        $entityManager->flush();
        return $this->redirectToRoute('blog');
    }


}
