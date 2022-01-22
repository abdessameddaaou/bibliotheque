<?php

namespace App\Entity;

use App\Repository\AuteurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=AuteurRepository::class)
 * @UniqueEntity("nom_prenom" , message="Le nom de ce auteur existe déjà !! ")

 */
class Auteur
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Le nom ne doit pas être vide !")
     * @Assert\Length(min=2,max=100, 
     *         minMessage="Le nom de l'acteur doit faire au moins {{ limit }} caractère !", 
     *         maxMessage="Le nom ne doit pas dépasser {{ limit }} caractères !")
     * 
     * @Assert\Regex("/^[a-zA-Z ]+$/", message=" le nom ne doit pas contenir des chiffre ou caracère spéciaux")
     */
    private $nom_prenom;

    /**
     * @ORM\Column(type="boolean")
     * 
     */
    private $sexe;

    /**
     * @ORM\Column(type="date")
     * @Assert\LessThan("today",message ="Vous devez choisir une date inférieure à la date d'aujourd'huit")
     */
    private $date_de_naissance;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Selectionnez la nationnalité de l'auteur !")
     */
    private $nationalite;

    /**
     * @ORM\ManyToMany(targetEntity=Livre::class, inversedBy="auteurs")
     */
    private $ecrire;

    public function __construct()
    {
        $this->ecrire = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPrenom(): ?string
    {
        return $this->nom_prenom;
    }

    public function setNomPrenom(string $nom_prenom): self
    {
        $this->nom_prenom = $nom_prenom;

        return $this;
    }

    public function getSexe(): ?bool
    {
        return $this->sexe;
    }

    public function setSexe(bool $sexe): self
    {
        $this->sexe = $sexe;

        return $this;
    }

    public function getDateDeNaissance(): ?\DateTimeInterface
    {
        return $this->date_de_naissance;
    }

    public function setDateDeNaissance(\DateTimeInterface $date_de_naissance): self
    {
        $this->date_de_naissance = $date_de_naissance;

        return $this;
    }

    public function getNationalite(): ?string
    {
        return $this->nationalite;
    }

    public function setNationalite(string $nationalite): self
    {
        $this->nationalite = $nationalite;

        return $this;
    }

    /**
     * @return Collection|Livre[]
     */
    public function getEcrire(): Collection
    {
        return $this->ecrire;
    }

    public function addEcrire(Livre $ecrire): self
    {
        if (!$this->ecrire->contains($ecrire)) {
            $this->ecrire[] = $ecrire;
        }

        return $this;
    }

    public function removeEcrire(Livre $ecrire): self
    {
        $this->ecrire->removeElement($ecrire);

        return $this;
    }

    public function __toString(): string
    {
        return $this->nom_prenom;
    }

    public function getNbecrits()
    {
        // Récrupérer la collections de livres 
        $mesLivres = $this->getEcrire() ; 
        $nbLivres = sizeof($mesLivres) ; 
        return $nbLivres ; 
    }
    
}
