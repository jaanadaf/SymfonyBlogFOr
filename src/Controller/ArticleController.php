<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Article;
use App\Entity\Category;
use App\Form\ArticleType;

class ArticleController extends AbstractController
{
    /**
     * @Route("/article/{id}", name="article_index", methods={"GET"})
     */
    public function index(int $id): Response
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        return $this->render('article/index.html.twig', [
            'article' => $article,
        ]);
    }

    /**
     * @Route("/article/edit/{id}", name="article_edit", methods={"GET", "POST"})
     * @Route("/article/create", name="article_create", methods={"GET", "POST"})
     */
    public function edit(Request $request, int $id = null): Response
    {
        $em = $this->getDoctrine()->getManager();

        if ($id) {
            $mode = 'update';
            $article = $em->getRepository(Article::class)->find($id);
        } else {
            $mode = 'new';
            $article = new Article();
        }

        $categories = $em->getRepository(Category::class)->findAll();
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
     * @Route("/article/delete/{id}", name="article_remove", methods={"POST"})
     */
    public function remove(int $id): Response
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository(Article::class)->find($id);

        if ($article) {
            $em->remove($article);
            $em->flush();
            $this->addFlash('success', 'Article deleted successfully.');
        }

        return $this->redirectToRoute('homepage');
    }

    private function completeArticleBeforeSave(Article $article, string $mode)
    {
        if ($article->getIsPublished()) {
            $article->setPublishedAt(new \DateTime());
        }
        $article->setAuthor($this->getUser());

        return $article;
    }

    private function saveArticle(Article $article, string $mode)
    {
        $article = $this->completeArticleBeforeSave($article, $mode);
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();
        $this->addFlash('success', 'Article saved successfully.');
    }
}
