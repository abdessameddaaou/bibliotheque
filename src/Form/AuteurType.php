<?php

namespace App\Form;

use App\Entity\Auteur;
use Doctrine\Common\Collections\Expr\Value;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Intl\Intl;

class AuteurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $countries = Intl::getRegionBundle()->getCountryNames();

        $builder
            ->add('nom_prenom',TextType::class)
            ->add('sexe',ChoiceType::class,['choices'  => [
                'Homme' => true,
                'Femme' => false,
            ]],)
            ->add('date_de_naissance', DateType::class, [
                'placeholder'=>'Date de naissance',
                // renders it as a single text box
                'widget' => 'single_text',
            ])
            ->add('nationalite',ChoiceType::class,[
                'choices' => array_flip($countries),
                'placeholder' => 'Selectionner votre pays',
                'preferred_choices' => array('FR'),
                
            ])
            ->add('submit',SubmitType::class,array('label'=>'Ajouter Auteur'))
 
            // ->add('ecrire',TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Auteur::class,
        ]);
    }
}
