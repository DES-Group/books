<?php

namespace App\DataFixtures;

/**
 * Fixtures help to generate dumby datas to make easy the development process. 
 * This class work like a controller. 
 * It is used to create fixtures datas and send them to database. 
 */

use App\Entity\Author;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Book;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $listAuthors = []; 
        //Création des auteurs
        for($i=0; $i<10; $i++)
        {
            $author = new Author();
            $author->setFirstName("Prénom ".$i);
            $author->setLastName("Prénom ".$i); 
            $manager->persist($author);

            $listAuthors[] = $author;
        }


        for($i = 0  ; $i < 20 ; $i++){
            $book = new Book ; 
            $book->setTitle("Livre n° ". $i); 
            $book->setCoverText("Couverture n° ". $i); 
            $book->setAuthor($listAuthors[array_rand($listAuthors)]);
            
            $manager->persist($book); 
        }
        
        $manager->flush();
    }
}
