<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Genre;
use App\Entity\Auteur;
use App\Entity\Livre;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        //      AJOUT des fixtures de genre         
        $tab_Genre = ["Science fiction" , "Policier", "Philosophie", "Economie" , "Psychologie"] ; 

        foreach($tab_Genre as $key => $i){
            $genre = new Genre() ; 
            $genre->setNom($i) ; 
            $manager->persist($genre) ; 

            $this->addReference('genre_'.$key, $genre) ; 
        }
        

        //      AJOUT des fixtures d'auteur
        $tabAuteur = array(
            0 => ["Richard Thaler" , 0 , date_create("12/12/1945"),  "USA"],
            1 => ["Cass Sunstein" , 0 , date_create("11/23/1943"),  "Allemagne"],
            2 => ["Francis Gabrelot", 0 , date_create("01/29/1967"), "France"],
            3 => ["Ayn Rand", 1 , date_create("06/21/1950"), "Russie"],
            4 => ["Dushmol", 0 , date_create("12/23/2001"), "Groland"],
            5 => ["Nancy Grave", 1 , date_create("10/24/1952"), "USA"],
            6 => ["James Enckling", 0 , date_create("07/03/1970"), "USA"],
            7 => ["Jean Dupont", 0 , date_create("07/03/1970"), "France"]
        ) ;
        
        
        foreach($tabAuteur as $champs => $value){
            $auteur = new Auteur() ; 
            $auteur->setNomPrenom($value[0]) ; 
            $auteur->setSexe($value[1]) ; 
            $auteur->setDateDeNaissance($value[2]) ; 
            $auteur->setNationalite($value[3]) ; 
            $manager->persist($auteur) ;
            
            $this->addReference('auteur_'.$champs,$auteur) ; 
        }      
        
        // Auteurs 
        $auteur0 = $this->getReference("auteur_0") ; 
        $auteur1 = $this->getReference("auteur_1") ; 
        $auteur2 = $this->getReference("auteur_2") ; 
        $auteur3 = $this->getReference("auteur_3") ; 
        $auteur4 = $this->getReference("auteur_4") ; 
        $auteur5 = $this->getReference("auteur_5") ; 
        $auteur6 = $this->getReference("auteur_6") ; 
        $auteur7 = $this->getReference("auteur_7") ; 

        // Genres
        $genre0 = $this->getReference("genre_0") ;
        $genre1 = $this->getReference("genre_1") ;
        $genre2 = $this->getReference("genre_2") ;
        $genre3 = $this->getReference("genre_3") ;
        $genre4 = $this->getReference("genre_4") ;

        // AJOUT DES LIVRES             
        $tabLivre = array(
            0 => ["Symfonystique", "978-2-07-036822-8", 117, date_create("01/20/2008") , 8 ], 
            1 => ["La grève", "978-2-251-44417-8", 1245, date_create("06/12/1961") , 19], 
            2 => ["Symfonyland" , "978-2-212-55652-0", 131, date_create("09/17/1980") , 15],
            3 => ["Négociation Complexe" , "978-2-0807-1057-4" , 234 , date_create("09/25/1992") , 16 ],
            4 => ["Ma vie" , "978-0-300-12223-7" , 5 , date_create("11/08/2021") , 3 ] , 
            5 => ["Ma vie : suite" , "978-0-141-18776-1" , 5 , date_create("11/09/2021") , 1 ] , 
            6 => ["Le monde comme volonté et comme représentation" , "978-0-141-18786-0" , 1987 , date_create("11/09/1821") , 19 ] 
        ) ; 


        foreach($tabLivre as $key => $value){
            $livre = new Livre() ; 
            $livre->setTitre($tabLivre[$key][0]) ; 
            $livre->setIsbn($tabLivre[$key][1]) ; 
            $livre->setNbPages($tabLivre[$key][2]) ; 
            $livre->setDateDeParution($tabLivre[$key][3]) ; 
            $livre->setNote($tabLivre[$key][4]) ; 

            if($key == 0){
                $livre->addAuteur($auteur2) ; 
                $livre->addAuteur($auteur3) ; 
                $livre->addAuteur($auteur5) ; 

                $livre->addGenre($genre1) ; 
                $livre->addGenre($genre2) ;
            }else if($key == 1){
                $livre->addAuteur($auteur3) ; 
                $livre->addAuteur($auteur6) ; 

                $livre->addGenre($genre2) ;
            }else if($key == 2){
                $livre->addAuteur($auteur7) ; 
                $livre->addAuteur($auteur6) ; 
                $livre->addAuteur($auteur3) ; 

                $livre->addGenre($genre0) ;
            }else if($key == 3){
                $livre->addAuteur($auteur0) ; 
                $livre->addAuteur($auteur1) ;

                $livre->addGenre($genre4) ;
            }else if($key == 4){
                $livre->addAuteur($auteur7) ;   

                $livre->addGenre($genre1) ;
            }else if($key == 5){
                $livre->addAuteur($auteur7) ; 

                $livre->addGenre($genre1) ;
            }else if($key == 6){
                $livre->addAuteur($auteur5) ; 
                $livre->addAuteur($auteur2) ; 

                $livre->addGenre($genre2) ;
            }

            $manager->persist($livre) ; 
        }

        $manager->flush();
    }
}
