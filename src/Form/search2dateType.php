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


class search2dateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        
        $builder
            
            ->add('date1', DateType::class, [
                'placeholder'=>'Date1',
                // renders it as a single text box
                'widget' => 'single_text',
            ])
            ->add('date2', DateType::class, [
                'placeholder'=>'Date2',
                // renders it as a single text box
                'widget' => 'single_text',
            ])
            ->add('submit',SubmitType::class,array('label'=>'Chercher les livres'))
 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livre::class,
        ]);
    }
}
