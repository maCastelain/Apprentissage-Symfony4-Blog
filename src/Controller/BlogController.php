<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo) //Injection de dépendance : index contient une instance de la classe ArticleRepository (on n'oublie pas le use).
    {
//        $repo = $this->getDoctrine()->getRepository(Article::class);
//  Symfony comprend qu'il passera à la fonction index un repository dans la variable $repo grâce à l'injection de dépendance.

        $articles = $repo->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/", name="home")
     */
    public function home() {
        return $this->render('blog/home.html.twig');
    }

    /**
     * @Route("/blog/{id}", name="blog_show")
     */
    public function show(Article $article) { // Injection de dépendance
        //Symfony utilise le paramConverter (converti un param de la requête en objet)
        //Symfony voit une route avec un identifiant et le besoin d'un article $article, il va donc chercher un article avec l'identifiant désiré.

        return $this->render('blog/show.html.twig', [
            'article' => $article
        ]);
    }
}
