<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
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
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article = null, Request $request, Objectmanager $manager) {
        // HttpFoundation\Request : la classe qui permet d'analyser-manipuler la requête HTTP
            // Parameterbag (dans le navigateur via inspection requête) : un objet qui renferme les données passées par le POST/GET
        // Doctrine : l'ObjectManager : permet de de gérer une ligne d'une table (insert/update/delete)
        // function create devient form pour créer et éditer

        if(!$article) {
            $article = new Article();
        }

/*        $article->setTitle("Titre de l'exemple du tuto2") // Le formulaire est pré-rempli avec les données de l'article
                ->setContent("Le contenu de l'article");*/

/*        $form =$this->createFormBuilder($article)
                    ->add('title')
                    ->add('content')
                    ->add('image')
                    ->getForm();*/
        // Utilisation de la console pour créer un formulaire dossier src Form, ArticleType
        // On utilise la méthode suivante, sans oublier le use ArticleType :
        $form = $this->createForm(ArticleType::class, $article);

        $form->handleRequest($request); // Le formulaire analyse la recherche et l'associe aux éléments title, content, image de l'article.

        if($form->isSubmitted() && $form->isValid()) {
            if(!$article->getId()) { // Si mon article ne dispose  pas d'un id, il n'existe pas et je place une nouvelle date de création.
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($article);
            $manager->flush();

            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
            // Ajout d'une variable editMode qui renvoie à true si l'article existe et false dans le cas inverse dans l'optique de changer le bouton Ajout ou Editer.
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
