<?php

namespace App\Entity;

use App\Repository\GenreRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert ; 
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GenreRepository::class)
 * @UniqueEntity("nom" , message="Ce nom existe deja !")
 */
class Genre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom ne doit pas être vide !")
     * @Assert\Regex("/^[a-zA-Z ]+$/", message=" le nom ne doit pas contenir des chiffre caracère spéciaux")
     * 
     */
    private $nom;

    /**
     * @ORM\ManyToMany(targetEntity=Livre::class, inversedBy="genres")
     *
     * 
     */
    private $Appartenir;

    public function __construct()
    {
        $this->Appartenir = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|Livre[]
     */
    public function getAppartenir(): Collection
    {
        return $this->Appartenir;
    }

    public function addAppartenir(Livre $appartenir): self
    {
        if (!$this->Appartenir->contains($appartenir)) {
            $this->Appartenir[] = $appartenir;
        }

        return $this;
    }

    public function removeAppartenir(Livre $appartenir): self
    {
        $this->Appartenir->removeElement($appartenir);

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom;
    }
    
    public function getNbpages()
    {
        // Récupérer les livres concernés
        $mesLivres = $this->getAppartenir() ; 
        
        //Calculer le nombre de pages total 
        $nbPagesTot = 0 ; 
        foreach($mesLivres as $livre){
            $nbPagesTot += $livre->getNbpages() ; 
        }

        return $nbPagesTot ; 
    }

    public function getNbpagesmoy()
    {
        // Récupérer les livres concernés 
        $mesLivres = $this->getAppartenir() ; 

        // Calculer la moyenne des pages pour chaque livres
        $somme = 0 ; 
        $nbLivre = 0 ; 
        
        foreach($mesLivres as $livre){
            $somme += $livre->getNbpages() ; 
            $nbLivre++ ; 
        }

        
        if($nbLivre != 0){
            $moyenne = $somme/$nbLivre ; 
            return (int)$moyenne ; 
        }
        else
            return null ; 
    }
    
    public function getAuteurs(){

        // Récupérer les livres 
        $livres = $this->getAppartenir() ; 
        $tab = array() ; 

        foreach($livres as $livre){
            // Récupérer les auteurs
            $auteurs = $livre->getAuteurs() ; 
            foreach($auteurs as $auteur){
                array_push($tab, $auteur) ; 
            }
        }

        //Trier le tableau 



        return array_unique($tab) ; 

    }
}
