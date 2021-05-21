<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use App\Form\CommentType;
use App\Form\CategoryType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use ContainerBri0Bmr\EntityManager_9a5be93;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    /**
     * méthode permettant d'afficher l'accueil du backoffice
     * 
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * Méthode permettant d'afficher l'ensemble des articles (liens modification, suppression, ajout etc ...)
     *  
     * @Route("/admin/article", name="admin_articles")
     * @Route("/admin/article/{id}/remove", name="admin_article_remove")
     * 
     */

     public function admin_articles(EntityManagerInterface $manager, ArticleRepository $repoArticle, Article $article = null):Response
     {
        dump($article);

        // via le manager qui permet de manipuler la BDD (insert, upadte, delete etc...), on execute la méthode getClassMetadata() afin de selectionner les méta données (primary key ,not null, noms des champs etc..) d'une entité (donc d'une table SQL), pour selectionner le nom des champs/colonnes de la table grace à la méthode getFieldNames()
        $colonnes = $manager->getClassMetadata(Article::class)->getFieldNames();

        dump($colonnes);

        $articles = $repoArticle->findAll(); // SELECT * FROM article + FETCH_ALL

        // Suppression article en Bdd
        // Si la condition IF retourne TRUE, cela veut dire que $article contient les informations de l'article à supprimer en BDD, on entre dans le if.
        if ($article)
        {
            // Avant de supprimer l'article en BDD, on stock son id dans une variable afin de l'injecter dans le message d'erreur
            $id = $article->getId();
            // remove() : méthode Repository --> DELETE FROM article WHERE id = ...
            $manager->remove($article);
            $manager->flush();

            $this->addFlash('success', "L'article n°$id a été supprimé");
            
            // Une fois l'article supprimé en BDD, on redirige l'utilisateur vers l'affichage des articles
            return $this->redirectToRoute('admin_articles');
        }

         return $this->render('admin/admin_articles.html.twig', [
             'colonnes'=>$colonnes,
             'articles'=>$articles
         ]);
    }

     /**
      * @Route("/admin/categories", name="admin_categories") 
      *
      */

      public function adminCategories(EntityManagerInterface $manager, CategoryRepository $repoCategory): Response
      {
        $colonnes = $manager->getClassMetadata(Category::class)->getFieldNames();

        dump($colonnes);

        $categories = $repoCategory->findAll();


          return $this->render('admin/admin_categories.html.twig', [
              'colonnes' => $colonnes, 
              'categories' => $categories
          ]);
      }

      /**
       * 
       * @Route("/admin/categorie", name="admin_new_categorie") 
       * @Route("/admin/categorie/{id}/edit", name="admin_edit_categorie")
       * 
       */

      public function createCategory(Request $request, EntityManagerInterface $manager, Category $categorie = null):Response
      {
        if(!$categorie)
        {
            $categorie = new Category;
        }        

        $formCategory = $this->createForm(CategoryType::class, $categorie);

        $formCategory->handleRequest($request);

        dump($categorie);

        if($formCategory->isSubmitted() && $formCategory->isValid())
        {
             // Si la catégorie ne possède pas d'ID, c'est donc une insertion, on entre dans elIF
             if(!$categorie->getId())
             $word = 'enregistrée';
         else // Sinon la catégorie possède un ID, c'est donc une modification
             $word = 'modifiée'; 

            $manager->persist($categorie);
            $manager->flush();             

            $this->addFlash('success', "La categorie a bien été $word");

            return $this->redirectToRoute('admin_categories');
        }

        return $this->render('admin/admin_create_category.html.twig', [
            'formCategory' => $formCategory->createView(),
            'editMode' => $categorie->getId()
        ]);
      }  

      /**
       * 
       * @Route("/admin/categories/{id}/remove", name="admin_remove_categorie")
       * 
       */
      public function adminRemoveCategory(Category $categorie, EntityManagerInterface $manager)
      {
        dump($categorie);

        // Si aucun article n'est associé à al catégorie, on entre dan sle IF et on execute la suppression en BDD
        if ($categorie->getArticles()->isEmpty()) 
        {
            $manager->remove($categorie);
            $manager->flush();

            $this->addFlash('success', 'La categorie a été supprimée avec succès');
        }
        else // Sinon, la catégorie est toujours associée à des articles, on affiche un lessage d'erreur
        {
            $this->addFlash('danger', "Il n'est pas possible de supprimer la catégorie car des articles y sont toujours associés !");

        }        

        return $this->redirectToRoute('admin_categories');

      }

      /**
       *  @Route("/admin/commentaires", name="admin_commentaires")
       *  @Route("/admin/commentaire/{id}/remove", name="admin_remove_commentaire")
       */

       public function adminComment(EntityManagerInterface $manager, CommentRepository $repoComment, Comment $commentaire = null): Response 
       {

            $colonnes = $manager->getClassMetadata(Comment::class)->getFieldNames();
            dump($colonnes);

            // selection et affichage sur le template admin_commentaires.html.twigdes commenteires stockés en bdd

            $commentaires = $repoComment->findAll();
            dump($commentaires );
                 

                   // suppression d'un commentaire

                   if($commentaire)
                   {
                       $manager->remove($commentaire);
                       $manager->flush();

                       $this->addFlash('success', "Le commentaire a été supprimé");

                       return $this->redirectToRoute('admin_commentaires');                       
                       
                   }

                   return $this->render('admin/admin_commentaires.html.twig', [

                    'colonnes' => $colonnes,
                    'commentaires'=> $commentaires
                    
           ]);
       }

       /**
        * @Route("/admin/commentaire/{id}/edit", name="admin_edit_commentaire") 
       *
       *
        */
        public function adminEditComment(Comment $commentaire, Request $request, EntityManagerInterface $manager): Response
        {
        dump($commentaire);

            $formComment = $this->createForm(CommentType::class, $commentaire);

            $formComment->handleRequest($request);

            if($formComment ->isSubmitted() && $formComment->isValid())
            {
                $manager->persist($commentaire);
                $manager->flush();

                $this->addFlash('success', "Le commentaire a été modifié avec succès");

                return $this->redirectToRoute('admin_commentaires');
            }

            return $this->render('admin/admin_edit_commentaire.html.twig', [
                'formComment' => $formComment ->createView()
            ]);
        }

        /**
         *  @Route("/admin/users", name="admin_users")
         */

         public function adminUsers(EntityManagerInterface $manager, UserRepository $repoUsers)
         {
            
            $colonnes = $manager->getClassMetadata(User::class)->getFieldNames();

            $users = $repoUsers->findAll();

             return $this->render('admin/admin_users.html.twig', [
                 'colonnes' =>$colonnes,
                 'users' => $users
             ]);
         }
}
