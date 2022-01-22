<?php 
namespace App\Controller; 
use Symfony\Component\HttpFoundation\Response; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController ;
use App\Entity\Genre;
use App\Entity\Auteur;
use App\Entity\Livre;

class HomeController extends AbstractController { 
    
    public function index(){
        return $this->redirectToRoute('home');
    }

    public function home() {
        return $this->render('home.html.twig');
    } 

    public function init(){

        // Supprimer la base de données 
            // Récupérer les données 
            $livres = $this->getDoctrine()->getRepository(Livre::class)->findAll() ; 
            $auteurs = $this->getDoctrine()->getRepository(Auteur::class)->findAll() ; 
            $genres = $this->getDoctrine()->getRepository(Genre::class)->findAll() ; 

            $em = $this->getDoctrine()->getManager() ; 
            //Supprimer les livres 
            foreach($livres as $livre){
                $em->remove($livre) ; 
            }
            //Supprimer les auteurs 
            foreach($auteurs as $auteur){
                $em->remove($auteur) ; 
            }
            //Supprimer les genres
            foreach($genres as $genre){
                $em->remove($genre) ; 
            }

        // Remplir la base de données 
            // Ajouter les données de auteurs 
            $genres = ["Science fiction" , "Policier", "Philosophie", "Economie" , "Psychologie"] ; 
            $refGenres = array() ; 
            foreach($genres as $key => $val){
                $genre = new Genre() ; 
                $genre->setNom($val) ; 
                $em->persist($genre) ;

                // On place les références des genres dans le tableau
                $refGenres[$key] = $genre ;   
            }

            // Ajouter les données auteurs
            $auteurs = array(
                ["Richard Thaler" , 0 , date_create("12/12/1945"),  "USA"],
                ["Cass Sunstein" , 0 , date_create("11/23/1943"),  "Allemagne"],
                ["Francis Gabrelot", 0 , date_create("01/29/1967"), "France"],
                ["Ayn Rand", 1 , date_create("06/21/1950"), "Russie"],
                ["Dushmol", 0 , date_create("12/23/2001"), "Groland"],
                ["Nancy Grave", 1 , date_create("10/24/1952"), "USA"],
                ["James Enckling", 0 , date_create("07/03/1970"), "USA"],
                ["Jean Dupont", 0 , date_create("07/03/1970"), "France"]
            ) ;

            $refAuteurs = array() ; 
            foreach($auteurs as $key => $val){
                $auteur = new Auteur() ; 
                $auteur->setNomPrenom($val[0]) ; 
                $auteur->setSexe($val[1]) ; 
                $auteur->setDateDeNaissance($val[2]) ; 
                $auteur->setNationalite($val[3]) ; 
                $em->persist($auteur) ;

                // On place les références des auteurs dans le tableau
                $refAuteurs[$key] = $auteur ; 
            } 

            // Ajouter les données livres 
            $livres = array(
                ["Symfonystique", "978-2-07-036822-8", 117, date_create("01/20/2008") , 8 ], 
                ["La grève", "978-2-251-44417-8", 1245, date_create("06/12/1961") , 19], 
                ["Symfonyland" , "978-2-212-55652-0", 131, date_create("09/17/1980") , 15],
                ["Négociation Complexe" , "978-2-0807-1057-4" , 234 , date_create("09/25/1992") , 16 ],
                ["Ma vie" , "978-0-300-12223-7" , 5 , date_create("11/08/2021") , 3 ] , 
                ["Ma vie : suite" , "978-0-141-18776-1" , 5 , date_create("11/09/2021") , 1 ] , 
                ["Le monde comme volonté et comme représentation" , "978-0-141-18786-0" , 1987 , date_create("11/09/1821") , 19 ] 
            ) ; 

            foreach($livres as $key => $val){
                $livre = new Livre() ; 
                $livre->setTitre($livres[$key][0]) ; 
                $livre->setIsbn($livres[$key][1]) ; 
                $livre->setNbPages($livres[$key][2]) ; 
                $livre->setDateDeParution($livres[$key][3]) ; 
                $livre->setNote($livres[$key][4]) ; 

                if($key == 0){
                    $livre->addAuteur($refAuteurs[2]) ; 
                    $livre->addAuteur($refAuteurs[3]) ; 
                    $livre->addAuteur($refAuteurs[5]) ; 
    
                    $livre->addGenre($refGenres[1]) ; 
                    $livre->addGenre($refGenres[2]) ;
                }else if($key == 1){
                    $livre->addAuteur($refAuteurs[3]) ; 
                    $livre->addAuteur($refAuteurs[6]) ; 
    
                    $livre->addGenre($refGenres[2]) ;
                }else if($key == 2){
                    $livre->addAuteur($refAuteurs[7]) ; 
                    $livre->addAuteur($refAuteurs[6]) ; 
                    $livre->addAuteur($refAuteurs[3]) ; 
    
                    $livre->addGenre($refGenres[0]) ;
                }else if($key == 3){
                    $livre->addAuteur($refAuteurs[0]) ; 
                    $livre->addAuteur($refAuteurs[1]) ;
    
                    $livre->addGenre($refGenres[4]) ;
                }else if($key == 4){
                    $livre->addAuteur($refAuteurs[7]) ;   
    
                    $livre->addGenre($refGenres[1]) ;
                }else if($key == 5){
                    $livre->addAuteur($refAuteurs[7]) ; 
    
                    $livre->addGenre($refGenres[1]) ;
                }else if($key == 6){
                    $livre->addAuteur($refAuteurs[5]) ; 
                    $livre->addAuteur($refAuteurs[2]) ; 
    
                    $livre->addGenre($refGenres[2]) ;
                }

                $em->persist($livre) ; 
            }
        $em->flush() ; 
        //return new Response("<html><body></body></html>") ;
        return $this->redirectToRoute("home") ;  
    }
}