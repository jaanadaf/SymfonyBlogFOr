<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Articles;

class HomeController extends AbstractController
{
    ##[Route('/home', name: 'app_home')]

    /**
     *Page d'acueil
      *
      *@return Response
     */
    
    public function index(): Response
    {
        // Entity Manager deSymfony (récupérer les données de la basede donnée)
        $em = $this->getDoctrine()->getManager();

        // Tous les articles en base de données
        $articles = $em->getRepository(Article::class)->findAll();

        return $this->render('home/index.html.twig', [
            'articles' => $articles,
        ]);
    }
}
