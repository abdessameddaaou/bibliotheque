<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Genre;
use App\Entity\Livre;
use Symfony\Component\Form\Extension\Core\Type\TextType ; 
use Symfony\Component\Form\Extension\Core\Type\SubmitType; 

class GenreController extends AbstractController
{
    /**
     * @Route("/genre", name="genre")
     */

    public function index(): Response
    {
        return $this->render('genre/index.html.twig', [
            'controller_name' => 'AuteurController',
        ]);
    }

    public function listerGenres()
    {
        //Récupérer les genres 
        $genreRepository = $this->getDoctrine()->getRepository(Genre::class) ; 
        $genres = $genreRepository->findAll() ; 
        
        if(isset($error))
            $error = true ; 
        else
            $error = false ;       

        //Afficher le template
        return $this->render('genre/liste.html.twig', [
            'genres' => $genres,
            'error' => $error
        ]);
    }



    public function supprimerGenre($id){
        //Récupérer le genre concerné
        $genreRepository = $this->getDoctrine()->getRepository(Genre::class) ;
        
        //Regarder s'il existe des livres avec ce genre 
        $livreRepository = $this->getDoctrine()->getRepository(Livre::class) ; 
        $livres = $livreRepository->findAll() ; 
        
        //Si on trouve le genre recherché dans un livre, alors il passera a vrai
        $genreExist = false ; 
        
        //Parcourir les genres de chaques livres pour voir si le genre existe
        foreach($livres as $livre){
            $genresLivre = $livre->getGenres() ; 
            foreach($genresLivre as $genre){
                if($genre->getId() == $id)
                $genreExist = true ; 
            }
        }
        
        if($genreExist == true){
            // Si le genre existe dans un livre, retourner une erreur
            $genres = $genreRepository->findAll() ; 

            return $this->render("genre/liste.html.twig" , [
                'genres' => $genres, 
                'error' => true
            ]) ; 
        }else{
            // Sinon supprimer le genre 
            $em = $this->getDoctrine()->getManager() ;    
            $genreSuppr = $genreRepository->find($id) ; 

            if($genreSuppr){
                $em->remove($genreSuppr) ; 
                $em->flush() ; 
            }
            return $this->redirectToRoute("lister_genres") ; 
        }

    }


    public function ajouterGenre(Request $request){
        $genre = new Genre ; 

        // Création du formulaire 
        $form = $this->createFormBuilder($genre) 
                     ->add('nom', TextType::class)
                     ->add('enregistrer', SubmitType::class)
                     ->getForm() ;
        $form->handleRequest($request);

        // Action du formulaire 
        if( $form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager() ; 
            $em->persist($genre) ; 
            $em->flush() ; 
            return $this->render('genre/ajouter.html.twig' , [
                'form' => $form->createView(), 
                'success' => 1, 
                'nom' => $genre->getNom()
            ]) ; 
        }

        return $this->render('genre/ajouter.html.twig' , [
            'form' => $form->createView(), 
            'success' => 0 
        ]) ;  
    }
}
