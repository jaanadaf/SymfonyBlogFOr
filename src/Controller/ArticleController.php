<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Article;
use App\Form\ArticleType;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article/{id}", name="app_article", requirements={"id"="\d+"}, methods={"GET"})
     */
    public function index(int $id): Response
    {
        $em = $this->getDoctrine()->getManager();

        // Récupération de l'article par ID
        $article = $em->getRepository(Article::class)->find($id);

        return $this->render('home/index.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/article/edit/{id?}", name="article_edit", requirements={"id"="\d+"}, methods={"GET", "POST"})
     */
    public function edit(Request $request, int $id = null): Response
    {
        $em = $this->getDoctrine()->getManager();
        $article = $id ? $em->getRepository(Article::class)->find($id) : new Article();
        $mode = $id ? 'update' : 'new';

        // Formulaire
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->saveArticle($article, $mode);
            return $this->redirectToRoute('article_edit', ['id' => $article->getId()]);
        }

        return $this->render('article/edit.html.twig', [
            'form' => $form->createView(),
            'article' => $article,
            'mode' => $mode,
        ]);
    }

    /**
     * @Route("/article/remove/{id}", name="article_remove", requirements={"id"="\d+"}, methods={"POST"})
     */
    private function remove(int $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository(Article::class)->find($id);

        if ($article) {
            $em->remove($article);
            $em->flush();
        }

        return $this->redirectToRoute('homepage');
    }

    private function completeArticleBeforeSave(Article $article, string $mode): Article
    {
        if ($article->getIsPublished()) {
            $article->setPublishedAt(new \DateTime());
        }
        $article->setAuthor($this->getUser());

        return $article;
    }

    private function saveArticle(Article $article, string $mode): void
    {
        $article = $this->completeArticleBeforeSave($article, $mode);

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();
    }
}
