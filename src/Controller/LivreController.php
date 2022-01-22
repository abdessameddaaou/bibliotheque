<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
//use App\Form\search2dateType;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use App\Repository\LivreRepository;


class LivreController extends AbstractController
{

    //private $repos;
    public function __construct(LivreRepository $repo)
    {
        $this->repos = $repo;
    }

    /**
     * @Route("/livre", name="livre")
     */
    public function index(): Response
    {
        return $this->render('livre/index.html.twig', [
            'controller_name' => 'LivreController',
        ]);
    }

    // lister livre
    public function listerLivres(Request $request)
    {
        $livres = $this->getDoctrine()->getRepository(Livre::class)->findAll();

        $form = $this->createFormBuilder()
            ->add('motcle', TextType::class)
            ->add('submit', SubmitType::class)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $motcle = $form["motcle"]->getData();
        
            $livres= $this->getDoctrine()->getRepository(Livre::class)->findAll();
            $list_livres = array();
            foreach($livres as $livre){
                $titres = $livre->getTitre();
                if( strpos($titres, $motcle) !== false ){
                    array_push($list_livres, $livre);
                }
            }

            return $this->render('livre/affichersearchtitre.html.twig', ['livres'=>$list_livres]);
        }

        return $this->render('livre/liste.html.twig', array('livres' => $livres, 'monFormulaire' => $form->createView()));
    }

    // afficher un livre 
    public function afficherLivre($id)
    {
        //Récupérer le livre correspondant à l'id
        $livre  = $this->getDoctrine()->getRepository(Livre::class)->find($id);

        //Retourner une erreur si le livre n'existe pas 
        if (!$livre) {
            throw $this->createNotFoundException('le livre ' . $id . 'n\'existe pas');
        }
        //Récupérer les genres et auteurs de ce livre 
        $auteurs = $livre->getAuteurs();
        $genres = $livre->getGenres();

        return $this->render('livre/detail.html.twig', ['livre' => $livre, 'auteurs' => $auteurs, 'genres' => $genres]);
    }


    #rechercher livre
    public function rechercheLivre($id)
    {
        $livre = $this->getDoctrine()->getManager();

        $chercherlivre = $this->getDoctrine()->getRepository(Livre::class)->find($id);

        if ($chercherlivre) {
            return $this->redirectToRoute('afficher_livre', array('id' => $chercherlivre));
        } else {
            throw $this->createNotFoundException("ce livre n'éxiste pas!");
        }
    }

    public function ajouterLivre(Request $request)
    {
        $livre = new Livre();
        $form = $this->createForm(LivreType::class, $livre, ['action' => $this->generateUrl('ajouter_livre')]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $livre = $form->getData();

            $auteurs = $form["auteurs"]->getData();
            foreach ($auteurs as $auteures) {
                $livre->addAuteur($auteures);
                $auteures->addEcrire($livre);
            }

            $genres = $form["genres"]->getData();
            foreach ($genres as $genrees) {
                $livre->addGenre($genrees);
                $genrees->addAppartenir($livre);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($livre);
            $entityManager->flush();
            $livres = $this->getDoctrine()->getRepository(Livre::class)->findAll();
            return $this->redirectToRoute('lister_livres');
        }

        return $this->render('livre/ajouter.html.twig', array('monFormulaire' => $form->createView()));
    }

    #diminuer une note
    public function diminuerNote($id)
    {
        $livre = $this->getDoctrine()->getManager();
        $chercherlivre = $this->getDoctrine()->getRepository(Livre::class)->find($id);
        if ($chercherlivre) {
            $chercherlivre->setNote($chercherlivre->getNote() - 1);
            $livre->flush();
            $livre2 = $this->getDoctrine()->getRepository(Livre::class)->find($chercherlivre->getId());
            dump($livre2);
            return new Response('<html><body></body></html>');
        } else {
            throw $this->createNotFoundException("ce livre n'éxiste pas!");
        }
    }

    #augmenter une note
    public function augmenterNote($id)
    {
        $livre = $this->getDoctrine()->getManager();
        $chercherlivre = $this->getDoctrine()->getRepository(Livre::class)->find($id);
        if ($chercherlivre) {
            $chercherlivre->setNote($chercherlivre->getNote() + 1);
            $livre->flush();
            $livre2 = $this->getDoctrine()->getRepository(Livre::class)->find($chercherlivre->getId());
            dump($livre2);
            return new Response('<html><body></body></html>');
        } else {
            throw $this->createNotFoundException("ce livre n'éxiste pas!");
        }
    }

    //Action 17 
    public function listerLivresParite()
    {
        $livres = $this->getDoctrine()->getRepository(Livre::class)->findAll();
        $lists = array();

        foreach ($livres as $livre) {
            $auteurs = $livre->getAuteurs();
            $homme = 0;
            $femme = 0;
            foreach ($auteurs as $aut) {
                if ($aut->getSexe() == 1) {
                    $homme++;
                } else {
                    $femme++;
                }
            }
            if ($homme == $femme && count($auteurs))
                array_push($lists, $livre);
        }

        return $this->render('livre/afficherparite.html.twig', ['livres' => $lists]);
    }

    //Action 13
    public function listerLivresPeriodes(Request $request)
    {

        $form = $this->createFormBuilder()
            ->add('date1', DateType::class, [
                'placeholder' => 'Date1',
                // renders it as a single text box
                'widget' => 'single_text',
            ])
            ->add('date2', DateType::class, [
                'placeholder' => 'Date2',
                // renders it as a single text box
                'widget' => 'single_text',
            ])
            ->add('submit', SubmitType::class)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date1 = $form["date1"]->getData();
            $date2 = $form["date2"]->getData();
            $repo = $this->getDoctrine()->getManager()->getRepository(Livre::class);
            $result = $repo->listerlivresperiodes($date1, $date2);

            return $this->render('livre/afficherdates.html.twig', ['livres' => $result, 'date1' => $date1, 'date2' => $date2]);
        }

        return $this->render('livre/search2dates.html.twig', array('monFormulaire' => $form->createView()));
    }
    //Action 15
    public function listerLivresPeriodesNotes(Request $request)
    {

        $form = $this->createFormBuilder()
            ->add('date1', DateType::class, [
                'placeholder' => 'Date1',
                // renders it as a single text box
                'widget' => 'single_text',
            ])
            ->add('date2', DateType::class, [
                'placeholder' => 'Date2',
                // renders it as a single text box
                'widget' => 'single_text',
            ])
            ->add('note1', IntegerType::class, [
                'data' => 1,
                'required'   => false,
                'empty_data' => 0,
                'attr' => [
                    'min' => 0,
                    'max' => 20
                ]

            ])
            ->add('note2', IntegerType::class, [
                'data' => 1,
                'required'   => false,
                'empty_data' => 0,
                'attr' => [
                    'min' => 0,
                    'max' => 20
                ]

            ])
            ->add('submit', SubmitType::class)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date1 = $form["date1"]->getData();
            $date2 = $form["date2"]->getData();
            $note1 = $form["note1"]->getData();
            $note2 = $form["note2"]->getData();
            $repo = $this->getDoctrine()->getManager()->getRepository(Livre::class);
            $result = $repo->listerlivresperiodesnotes($date1, $date2, $note1, $note2);

            return $this->render('livre/afficherdatesnotes.html.twig', ['livres' => $result, 'date1' => $date1, 'date2' => $date2, 'note1' => $note1, 'note2' => $note2]);
        }

        return $this->render('livre/search2datesnotes.html.twig', array('monFormulaire' => $form->createView()));
    }

    //Action 14
    public function listerLivresNationalite() {
        $list2=array();   
        $livres = $this->getDoctrine()->getRepository(Livre::class)->findAll();

        foreach ($livres as $livre) { 
            $list1=array();
            $auteurs = $livre->getAuteurs();
            $boll = false;
            array_push($list1, $auteurs[0]);

            for ($i=1; $i < count($auteurs); $i++) { 
                foreach ($list1 as $list) {

                    if($list->getNationalite() === $auteurs[$i]->getNationalite()){
                        $boll =true;
                    }
                    else if(!$boll){
                        array_push($list1, $auteurs[$i] );
                    }
                }
            }
            if(!$boll){
                array_push($list2, $livre);
            }
        }

        return $this->render('livre/affichernationalite.html.twig', ['livres' => $list2]);
    }
    
    //Modifier un livre
    public function modifierLivre($id)
    {
        $livre = $this->getDoctrine()->getRepository(Livre::class)->find($id);
        if (!$livre)
            throw $this->createNotFoundException('Livre[id=' . $id . '] inexistant');

        $form = $this->createForm(
            LivreType::class,
            $livre,
            ['action' => $this->generateUrl(
                'modifier_livre_suite',
                array('id' => $livre->getId())
            )]
        );
        $form->add('submit', SubmitType::class, array('label' => 'Modifier'));
        // return $this->render('livre/modifier.html.twig', 
        // array('monFormulaire' => $form->createView()));
        return $this->render('livre/modifier.html.twig', 
            ['monFormulaire' => $form->createView(), 'titrelivre'=>$livre->getTitre()]); 
    }

    public function modifierLivreSuite(Request $request, $id)
    {
        $livre = $this->getDoctrine()->getRepository(Livre::class)->find($id);
        if (!$livre)
            throw $this->createNotFoundException('Livre[id=' . $id . '] inexistant');
        $lesauteurs = $livre->getAuteurs();
        $lesgenres = $livre->getGenres();
        foreach ($lesauteurs as $lesauteeurs) {
            $livre->removeAuteur($lesauteeurs);
            $lesauteeurs->removeEcrire($livre);
        }
        foreach ($lesgenres as $lesgenrees) {
            $livre->removeGenre($lesgenrees);
            $lesgenrees->removeAppartenir($livre);
        }
        //****************************************** */
        $form = $this->createForm(
            LivreType::class,
            $livre,
            ['action' => $this->generateUrl(
                'modifier_livre_suite',
                array('id' => $livre->getId())
            )]
        );
        $form->add('submit', SubmitType::class, array('label' => 'Modifier'));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $auteurs = $form["auteurs"]->getData();
            foreach ($auteurs as $auteures) {
                $livre->addAuteur($auteures);
                $auteures->addEcrire($livre);
            }

            $genres = $form["genres"]->getData();
            foreach ($genres as $genrees) {
                $livre->addGenre($genrees);
                $genrees->addAppartenir($livre);
            }
            $entityManager->persist($livre);
            $entityManager->flush();
            // $url = $this->generateUrl('afficher_livre',
            // array('id' => $livre->getId())); 
            return $this->redirectToRoute("lister_livres");
        }

        return $this->render(
            'livre/modifier.html.twig',
            array('monFormulaire' => $form->createView())
        );
    }

    public function supprimerLivre($id)
    {
        $livre = $this->getDoctrine()->getRepository(Livre::class)->find($id);
        if (!$livre)
            throw $this->createNotFoundException('Livre[id=' . $id . '] inexistant');
        $lesauteurs = $livre->getAuteurs();
        $lesgenres = $livre->getGenres();
        foreach ($lesauteurs as $lesauteeurs) {
            $livre->removeAuteur($lesauteeurs);
            $lesauteeurs->removeEcrire($livre);
        }
        foreach ($lesgenres as $lesgenrees) {
            $livre->removeGenre($lesgenrees);
            $lesgenrees->removeAppartenir($livre);
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($livre);
        $em->flush();

        //return $this->render('livre/list.html.twig');
        return $this->redirectToRoute("lister_livres");
    }

    public function chercherlivretitre(Request $request){
        
        $form = $this->createFormBuilder()
        ->add('motcle',TextType::class)
        ->add('submit',SubmitType::class)->getForm();
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $motcle = $form["motcle"]->getData();
        
        $livres= $this->getDoctrine()->getRepository(Livre::class)->findAll();
        $list_livres = array();
        foreach($livres as $livre){
            $titres = $livre->getTitre();
            if( strpos($titres, $motcle) !== false ){
                array_push($list_livres, $livre);
            }
            
        }
        
        return $this->render('livre/affichersearchtitre.html.twig', ['livres'=>$list_livres]);
    }

        return $this->render('livre/searchtitre.html.twig', array('monFormulaire'=>$form->createView()));

    }

}
