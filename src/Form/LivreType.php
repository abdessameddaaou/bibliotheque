<?php

namespace App\Form;

use App\Entity\Auteur;
use App\Entity\Genre;
use App\Entity\Livre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;


class LivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
            ->add('isbn',TextType::class)
            ->add('titre',TextType::class)
            ->add('nbpages',TextType::class)
            ->add('date_de_parution', DateType::class, [
                'placeholder'=>'Date de parution',
                // renders it as a single text box
                'widget' => 'single_text',
            ])
            ->add('note',IntegerType::class,[
                'data'=>1,
                'required'   => false,
                'empty_data' => 0
    
            ])
            ->add('auteurs', EntityType::class,
            ['class' => Auteur::class, 'multiple' => true,])
            ->add('genres', EntityType::class,
            ['class' => Genre::class, 'multiple' => true,])
            ->add('submit',SubmitType::class,array('label'=>'Ajouter Livre'))
 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}
