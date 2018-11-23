<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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
     * @Route("/blog/new", name="blog_create")
     */
    public function create(Request $request, Objectmanager $manager) {
        // HttpFoundation\Request : la classe qui permet d'analyser-manipuler la requête HTTP
            // Parameterbag (dans le navigateur via inspection requête) : un objet qui renferme les données passées par le POST/GET
        // Doctrine : l'ObjectManager : permet de de gérer une ligne d'une table (insert/update/delete)
        $article = new Article();

        $form =$this->createFormBuilder($article)
                    ->add('title')
                    ->add('content')
                    ->add('image')
                    ->getForm();

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView()
        ]);
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
