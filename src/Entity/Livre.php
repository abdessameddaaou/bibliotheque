<?php

namespace App\Entity;

use App\Repository\LivreRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=LivreRepository::class)
 * @UniqueEntity("isbn" , message="ce isbn existe déjà !! ")
 */
class Livre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=25)
     * @Assert\Isbn(
     *     type = "isbn13",
     *     message = "ce Isbn n'est pas correcte."
     * )
     * 
     * @Assert\NotBlank(message="Selectionnez l'isbn d'un livre !")
     */
    private $isbn;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Selectionnez le nombre de page !")
     * @Assert\Regex("/^[0-9]+$/", message="Vous n'avez pas utilisé le format correct")
     */
    private $nbpages;

    /**
     * @ORM\Column(type="date")
     * @Assert\LessThan("today", message ="Vous devez choisir une date inférieure à la date d'aujourd'hui")
     */
    private $date_de_parution;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @Assert\LessThanOrEqual(20, message="La note doit être inférieur a 20")  
     * @Assert\GreaterThanOrEqual(0, message="La note doit être supérieur a 0 ") 
     * @Assert\Regex("/^[0-9]$/", message= "vous devez siasir le bon format")
     * 
     */
    private $note;

    /**
     * @ORM\ManyToMany(targetEntity=Auteur::class, mappedBy="ecrire")
     */
    private $auteurs;

    /**
     * @ORM\ManyToMany(targetEntity=Genre::class, mappedBy="Appartenir")
     */
    private $genres;

    public function __construct()
    {
        $this->auteurs = new ArrayCollection();
        $this->genres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getNbpages(): ?int
    {
        return $this->nbpages;
    }

    public function setNbpages(int $nbpages): self
    {
        $this->nbpages = $nbpages;

        return $this;
    }

    public function getDateDeParution(): ?\DateTimeInterface
    {
        return $this->date_de_parution;
    }

    public function setDateDeParution(\DateTimeInterface $date_de_parution): self
    {
        $this->date_de_parution = $date_de_parution;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(?int $note): self
    {
        $this->note = $note;

        return $this;
    }

    /**
     * @return Collection|Auteur[]
     */
    public function getAuteurs(): Collection
    {
        return $this->auteurs;
    }

    public function addAuteur(Auteur $auteur): self
    {
        if (!$this->auteurs->contains($auteur)) {
            $this->auteurs[] = $auteur;
            $auteur->addEcrire($this);
        }

        return $this;
    }

    public function removeAuteur(Auteur $auteur): self
    {
        if ($this->auteurs->removeElement($auteur)) {
            $auteur->removeEcrire($this);
        }

        return $this;
    }

    /**
     * @return Collection|Genre[]
     */
    public function getGenres(): Collection
    {
        return $this->genres;
    }

    public function addGenre(Genre $genre): self
    {
        if (!$this->genres->contains($genre)) {
            $this->genres[] = $genre;
            $genre->addAppartenir($this);
        }

        return $this;
    }

    public function removeGenre(Genre $genre): self
    {
        if ($this->genres->removeElement($genre)) {
            $genre->removeAppartenir($this);
        }

        return $this;
    }

    public function getAuteurgenres(){

        $tab  = array();

        $genres = $this->getGenres();

        $tab[0] = $genres[0]->getNom();
       // dd($genres[1]->getNom());
        for ($i=1; $i<$genres->count(); $i++) { 

            if(in_array($genres[$i]->getNom(), $tab))
            {
               echo('NON');

                array_push($tab,$genres[$i]->getNom());
            }
            else{

            }

        }

        return $tab;

    }
    
}
