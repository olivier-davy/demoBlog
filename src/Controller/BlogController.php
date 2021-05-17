<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{

    /**
     * Méthode permettant de définir la page d'accueil du blog, point d'entrée du site
     * @Route("/", name="home")
     */
    
     public function home(): Response
     {
         return $this->render('blog/home.html.twig', [
            'title' => 'Bienvenue sur le blog Symfony',
            'age' => 20
         
         ]);
     }
    
    
    /**
     * Méthode permettant d'afficher tous les articles du blog
     * 
     * @Route("/blog", name="blog")
     */
    public function blog(ArticleRepository $repoArticle): Response
    {
        // Pour selectionner dans une table SQL, nous devons importer une classe Repository
        // une class repository permet uniquement de selectionner des données dans une table SQL
        // $repoArticle est un objet issu de la class ArticleRepository
        // $repoArticle = $this->getDoctrine()->getRepository(Article::class);
        dump($repoArticle);

        $articles = $repoArticle->findAll();
        dump($articles);

        return $this->render('blog/blog.html.twig',[
            'articles' => $articles // on transmets au template les articles selectionnés en BDD afin de pouvoir les traiter et les afficher en Twig
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create")
     * @Route("/blog/{id}/edit", name="blog_edit")
    */
    public function create(Request $request, EntityManagerInterface $manager, Article $article = null) : Response
    
    {
        // La classe Request permet de stocker les données http véhiculé par les superglobales ($_post? £8get? £8files etc ...)
        // dump($request);
        // Si les données de $_POST saisie dans le formulaire sont > à 0, cela veut dire que l'internaute a saisi des données dans le formulaire, on entre dans le IF
        // if($request->request->count() > 0) 
        // {
        //     $article = new Article();

        //     $article -> setTitre($request->request->get('titre'))
        //              -> setContenu($request->request->get('contenu'))
        //              -> setImage($request->request->get('image'))
        //              -> setDate(new \DateTime());


        //     dump($article);

        //     $manager->persist($article); // preparation et mise en memoire de la requete SQl d'insertion avec les données de l'entité, c'est à dire les données saisies dans le formulaire
        //     $manager->flush(); // on execute la requete en bdd
        // }
        // Si la variable $articleCreate N'EST PAS, si elle ne contient aucun article de la BDD, cela veut dire nous avons envoyé la route '/blog/new', c'est une insertion, on entre dans le IF et on crée une nouvelle instance de l'entité Article, création d'un nouvel article
        // Si la variable $articleCreate contient un article de la BDD, cela veut dire que nous avons envoyé la route '/blog/id/edit', c'est une modifiction d'article, on entre pas dans le IF
     
        if(!$article)
        {
            $article = new Article;
        }
        

        // $article->setTitre("Titre à la noix")
        //     ->setContenu("Contenu à la noix");

        // on met en fomre le formulaire via la méthode createForm à partir de la class crée ArticleType
        // En 2ème argument, on défini que le formulaire a pour but de remplir l'entité $article  
        $formArticle = $this->createForm(ArticleType::class, $article);

        // Cette méthode permet de récupérer chaque donnée saisie dans le formulaire afin de les envoyer dans les bons setters et propriétées de l'entité $article
        $formArticle->handleRequest($request);

        dump($article);

        if($formArticle->isSubmitted() && $formArticle->isValid()) 
        {
            $article->setDate(new \DateTime());
            $manager->persist($article); // prépare et garde en mémoire la requete SQL d'insertion
            $manager->flush(); // execute la requete SQL d'insertion

            return $this->redirectToRoute('blog_show', [
                'id'=> $article->getId() // blog_show est une route parametrée, il faut lui fournir l'id à transmettre dans l'url
            ]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle'=>$formArticle->createView() // createView() créee un petit objet permettant de mettre en forme et d'afficher le formulaire dans le template.
        ]);
    }


    /**
     * Méhode permettant d'afficher le détail  d'un article  
     * {id} : permet de définir une route paramétrée qui va receptionner un id d'1 article de la BDD
     * $id va receptionner l'id transmis dans la route
     * @route("/blog/{id}", name="blog_show")       
     * 
    */ 
    public function blogShow(Article $article): Response
    {

        // dump($id);

        // $repoArticle = $this->getDoctrine()->getRepository(Article::class);
        // $repoArticle : objet de la classe ArticleRepository

        // find() : méthode issue de la classe ArticleRepository permettant de selectionner un element dans la BDD par son id
        // $article = $repoArticle->find($id);
        dump($article);

        return $this->render('blog/blog_show.html.twig', [
            'article' => $article // on transmet l'article selectionné en BDD au template pour pouvoir le traiter et l'afficher avec Twig

        ]);
    }

   

}
