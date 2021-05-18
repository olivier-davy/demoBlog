<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
    //    // on boucle 10 fois pour créer 10 articles
    //    for($i = 1; $i <=10; $i++)
    //    {
    //        // pour importer la class, raccourci ctrl + alt + i
    //        $article = new Article;

    //        $article->setTitre("Titre de l'article n°$i")
    //                ->setContenu("<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod 
    //                tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud 
    //                exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor 
    //                in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur 
    //                sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>")
    //                ->setImage("https://picsum.photos/id/237/200/300")
    //                ->setDate(new \DateTime());

    //         $manager->persist($article); // prepare et on garde en mémoire les requetes d'insertion       
    //    }

    //    $manager->flush(); // permet d'executer les requetes sql en bdd

    //    // Une class Manager en Symfony permet de manipuler, entre autre, les lignes de la BDD (INSERT, UPDATE, DELETE)

            // On importe la librairie Faker pour les fixtures, les articles, commentaires et catégories
            $faker = \Faker\Factory::create('fr_FR');

            // Création de 3 catégories
            for($i = 1; $i <= 3; $i++)
            {
                $category = new Category;

                $category->setTitre($faker->word)
                         ->setDescription($faker->paragraph());


                $manager->persist($category);

                // Création de 4 à 10 articles pour
                for($j = 1; $j <= mt_rand(4,10); $j++)
                {
                       $article = new Article; 

                       $content = '<p>' . join($faker->paragraphs(5), '</p><p>') . '</p>';

                       $article ->setTitre($faker->sentence())
                                ->setContenu($content)
                                ->setImage("https://picsum.photos/id/23$j/300/300")
                                ->setDate($faker->dateTimeBetween('-6 months'))
                                ->setCategory($category);

                       $manager->persist($article);
                       
                       // Création de 4 à 10 commentaires

                       for ($k=1; $k <= mt_rand(4,10); $k++)
                       {
                            $comment = new Comment;

                            $now = new DateTime;
                            $interval = $now->diff($article->getDate()); // retourne la date en timestamp (temps en seconde) entre la date de créatino de l'article et today
                            $days = $interval->days; // retourne le nombre de jour entre la date de création des articles et today
                            $minimum = "-$days days"; /*-100 days | le but est d'avoir des dates de commentaires entre la date de cration des articles et today */

                           $comment ->setAuteur($faker->name)
                                    ->setCommentaire($content)
                                    ->setDate($faker->dateTimeBetween($minimum))
                                    ->setArticle($article);

                           $manager->persist($comment);         
                       }

                }           
                
            }

            $manager->flush();
    }
}
