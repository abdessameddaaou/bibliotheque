<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Entity\Livre;

use App\Form\AuteurType;
use App\Form\NoteLivreType;
use phpDocumentor\Reflection\Types\Integer;
use PhpParser\Node\Scalar\MagicConst\Line;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;

use function PHPUnit\Framework\throwException;

//use App\Entity\Auteur;

class AuteurController extends AbstractController
{
    // Action 3: lister tous les auteurs
    public function listerAuteurs() { 
        // on récupere la liste de tous les auteurs
        $auteurs = $this->getDoctrine()->getRepository(Auteur::class)->findAll();        
        return $this->render('auteur/liste.html.twig',array('auteurs'=>$auteurs , 'action' => 3));
    }

    //Action 4 : Afficher les détails de chaque auteur 
    public function afficherAuteur($id){
        // Récupérer l'auteur par son id 
        $auteur  = $this->getDoctrine()->getRepository(Auteur::class)->find($id); 
        if(!$auteur){
            // Si l'auteur n'existe pas il va afficher un message d'erreur
           return $this->render('error.html.twig');
        }

       //récuperer les livres de l'auteur
        $ecrires = $auteur->getEcrire();
       return $this->render("auteur/detail.html.twig",array('auteur'=>$auteur,'ecrires'=>$ecrires));
    }

    
    //Action 5 : Ajouter un auteur
    public function ajouterAuteur(Request $request){
        $auteur = new Auteur();
        // créer le formulaire avec les form type
        $form = $this->createForm(AuteurType::class, $auteur,['action'=>$this->generateUrl('ajouter_auteur')]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $nation = $form['nationalite']->getData();
            $country = Intl::getRegionBundle()->getCountryName($nation);
            $auteur->setNationalite($country);
            $em = $this->getDoctrine()->getManager();
            $em->persist($auteur);
            $em->flush();
            $this->addFlash(
                'success',
                'Auteur Bien Ajouté!'
            );
            return $this->redirectToRoute('lister_auteurs');
        }
        return $this->render('auteur/ajouter.html.twig',array('monFormulaire'=>$form->createView()));
    }

    // Action 6 : Modifier un auteur
    // Récupérer le formulaire avec les informations de l'auteur
    public function modifierAuteur($id){
        $auteur = $this->getDoctrine()->getRepository(Auteur::class)->find($id);
        if($auteur){
            $form = $this->createForm(AuteurType::class, $auteur,['action'=>$this->generateUrl('modifier_auteur_suite',array('id'=>$auteur->getId()))]);
            $form->add('submit', SubmitType::class, array('label'=>'Modifier'));
            return $this->render('auteur/modifier.html.twig', array('monFormulaire'=>$form->createView()));
        }
        else{
            return $this->render('error.html.twig');
        }
    }

    public function modifierAuteurSuite(Request $request, $id){
        $auteur = $this->getDoctrine()->getRepository(Auteur::class)->find($id);
        if ($auteur){
            $form = $this->createForm(AuteurType::class,$auteur,['action'=>$this->generateUrl('modifier_auteur_suite',array('id' =>$auteur->getId()))]);
            $form->add('submit', SubmitType::class, array('label'=>'Modifier'));
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $data = $form->getData();
                $nation = $form['nationalite']->getData();
                $country = Intl::getRegionBundle()->getCountryName($nation);
                $auteur->setNationalite($country);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($auteur);
                $entityManager->flush();
                $url = $this->generateUrl('lister_auteurs');
                $this->addFlash(
                    'success',
                    'Auteur Bien modifié!'
                );
                return $this->redirect($url);
            }
            return $this->render('auteur/modifier.html.twig', array('monFormulaire'=>$form->createView()));
        }
        else {
            return $this->render('error.html.twig');
        }
    }


    // Action 7: Supprimer un auteur 
    public function supprimerAuteur($id) { 
        $em = $this->getDoctrine()->getManager();

        //Récupérer l'auteur concerné 

        $auteur = $this->getDoctrine()->getRepository(Auteur::class)->find($id);
        // vérifier si id passer dans URL existe
        if(!$auteur){
            return $this->render('error.html.twig');
        }
        // on récupere la liste des livres 
        $livres = $auteur->getEcrire();
        // si l'auteur n'a écrit aucun livre il le supprime directement 
        if($livres->count()==0){
            $em->remove($auteur);
                    $em->flush();
                    $this->addFlash(
                        'success',
                        'Auteur Bien Supprimé!'
            );
            return $this->redirectToRoute('lister_auteurs');
        }

        foreach($livres as $livre){
            $auteurs = $livre->getAuteurs();
            // s'il y a pluieurs auteurs d'un livre, il supprime l'auteur 
            if($auteurs->count()>1){
                $auteur->removeEcrire($livre);
                $livre->removeAuteur($auteur);

                $em->remove($auteur);
                $em->flush();
                $this->addFlash(
                    'success',
                    'Auteur Bien Supprimé!'
                );
                 return $this->redirectToRoute('lister_auteurs');
            }
            elseif($auteurs->count()==1){
                // si l'auteur est seule qui a écrit le livre il le rederige vers une autre page
                return $this->render("auteur/listlivresupprimer.html.twig",array('auteur'=>$auteur));
                $this->addFlash(
                    'error',
                    'Vous ne pouvez pas supprimé cet auteur '
                );
                 return $this->redirectToRoute('lister_auteurs');
            }
        }
     }


    public function supprimerAuteurLivre($id){
        $auteur = $this->getDoctrine()->getRepository(Auteur::class)->find($id);
        if(!$auteur){
            return $this->render('error.html.twig');
        }
        $livres = $auteur->getEcrire();
        $em = $this->getDoctrine()->getManager();
        foreach($livres as $livre){           
                $auteur->removeEcrire($livre);
                $livre->removeAuteur($auteur);
                $em->remove($auteur);
                $em->remove($livre);    
                $em->flush();
            }
        $this->addFlash(
            'success',
            'Auteur Bien Supprimé!'
        );
        return $this->redirectToRoute('lister_auteurs');
         
    }

    // Action 16 : Lister tous les auteurs qui ont écrit au moins dans Trois livres 

    public function auteurTroisLivres(){
        $lists = array();
        // récuperer les auteurs
        $auteurs = $this->getDoctrine()->getRepository(Auteur::class)->findAll();

        foreach($auteurs as $aut){
            // on récupere pour chaque auteur la liste des livres
            $ecrire = $aut->getEcrire();
            // on vérifie si le nombre de livre au moins ou égale à 3
            if($ecrire->count()>= 3){
                array_push($lists,$aut);
               
            }
        }
        return $this->render('auteur/liste.html.twig', array('auteurs'=>$lists , 'action' => 16));
    }

    //Action 20
    public function listerAuteursGenresOrdreChrono(){


        // $auteurs = $this->getDoctrine()->getRepository(Auteur::class)->findAll();
        // $listAuteur = array();
        // $listLivres = array();
        // $listGenre = array();
        // foreach($auteurs as $auteur){
        //     $livres = $auteur->getEcrire();
        //     if($livres->count()>0){
        //         array_push($listAuteur,$auteur);
        //         foreach($livres as $livre){
        //             array_push($listLivres,$livre);
        //             $genres = $livre->getGenres();
        //             foreach($genres as $genre){
        //                  array_push($listGenre,$genre);
        //             }
        //         }
        //     }

        //Récupérer les auteurs 
        // $auteurRepository = $this->getDoctrine()->getRepository(Auteur::class) ; 
        // $auteurs = $auteurRepository->findAll() ; 
        
        // // Construire le tableau d'auteurs ayant écrit au moins un livre 
        // $tabAuteurs = array() ; 
        // foreach($auteurs as $auteur){
        //     //Regarder si l'auteur a ecrit au moins un livre 
        //     if($auteur->getNbecrits() != 0){
        //         // Envoyer la valeur dans notre tableau
        //         array_push($tabAuteurs, $auteur) ; 
        //     } 
        // }
        
        // $test = $auteurRepository->findAuteursGenres(8) ;  
        
        // return $this->render("auteur/auteursGenres.html.twig", [
        //     "auteurs" => $tabAuteurs , 
        //     'test' => $test
        // ]) ;
        $repo = $this->getDoctrine()->getManager()->getRepository(Auteur::class)->findAll();
        
            //dd($aut->getId());
            //$genres = $repo->findAuteursGenres();
         
            
        //dump($genres);
        return $this->render("auteur/auteursGenres.html.twig", [
                 "auteurs" => $repo , 
                 
             ]) ;
    }
    
    // Action 21 
    public function listerAuteurGenres($id){
        $auteur = $this->getDoctrine()->getRepository(Auteur::class)->find($id);
        if(!$auteur){
            // si l'auteur n'existe pas il rederige vers la page d'erreur
             return $this->render('error.html.twig');
        }
        $listGenres = array();
        
        $livres = $auteur->getEcrire();
        if($livres->count()>0){
            //dd($livres->count());
            foreach($livres as $livre){
                $genres = $livre->getGenres();
                foreach($genres as $genre){
                    array_push($listGenres,$genre);
                    
                }
                // supprimer les doublons 
                $unique = array_unique($listGenres);
                
            }
            return $this->render('auteur/auteurGenre.html.twig',array('auteur'=>$auteur,'genres'=>$unique));

        }
        else{
            $this->addFlash(
                'warning',
                'Ce auteur n\'a écrit aucun livre'
            );
                return $this->redirectToRoute('lister_auteurs');
        }
        // dd($unique);

        
    }
    // Action 26

    public function augmenterNote(Request $request,$id){
        $auteur = $this->getDoctrine()->getRepository(Auteur::class)->find($id);
        if(!$auteur){
            return $this->render('error.html.twig');
        }
        $livre = $auteur->getEcrire();
        //dd($livre);
        

        $form = $this->createFormBuilder()
            ->add('note',IntegerType::class,[
                'data'=>1,
                'required'   => false,
                'empty_data' => 1,
                'attr' => [
                    'min' => 1,
                    "max"=>20
                ]
  
            ])
            ->add('submit',SubmitType::class,['label'=>'Augmenter'])->getForm();
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $auteur = $this->getDoctrine()->getRepository(Auteur::class)->find($id);
            $livres = $auteur->getEcrire();
            $success= 0;
            foreach($livres as $livre){
               
                $noteex = $livre->getNote();
                $noteajoute = $form['note']->getData();   
                if($noteex<20){
                    if($noteex + $noteajoute<=20){
                        $add = $livre->setNote($noteex + $noteajoute);
                        $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($add);
                        $entityManager->flush();
                        $success = 1;
                    }
                    else{
                        $this->addFlash(
                           'error',
                           'Avec cette note le livre "'.$livre->getTitre().'" dépasse la note 20'
                        );

                    }
                }
                else{

                    $this->addFlash(
                       'error',
                       'La note du livre '.$livre->getTitre().' est égale déjà à 20'
                    );

                }                                 
            }
            if($success==1){
                $this->addFlash(
                   'success',
                   'Note Bien Ajouté'
                );
            }
            return $this->render('auteur/augmenter.html.twig',array('form' => $form->createView(),'auteur'=>$auteur));


        }
        return $this->render('auteur/augmenter.html.twig',array('form' => $form->createView(),'auteur'=>$auteur));
    }

 }


    
