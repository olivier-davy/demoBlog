<?php

namespace App\DataFixtures;

use App\Entity\Article;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
       // on boucle 10 fois pour créer 10 articles
       for($i = 1; $i <=10; $i++)
       {
           // pour importer la class, raccourci ctrl + alt + i
           $article = new Article;

           $article->setTitre("Titre de l'article n°$i")
                   ->setContenu("<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod 
                   tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud 
                   exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor 
                   in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur 
                   sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>")
                   ->setImage("https://picsum.photos/id/237/200/300")
                   ->setDate(new \DateTime());

            $manager->persist($article); // prepare et on garde en mémoire les requetes d'insertion       
       }

       $manager->flush(); // permet d'executer les requetes sql en bdd

       // Une class Manager en Symfony permet de manipuler, entre autre, les lignes de la BDD (INSERT, UPDATE, DELETE)

    }
}
