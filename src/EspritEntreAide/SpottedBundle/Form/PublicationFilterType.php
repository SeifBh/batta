<?php

namespace EspritEntreAide\SpottedBundle\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DomCrawler\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
class PublicationFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('All', SubmitType::class, array('label' => 'All','attr'=> array('class'=>'col-md-4 btn btn-default pull-right')))
            ->add('publicationsecrete', SubmitType::class, array('label' => 'Publication ','attr'=> array('class'=>'col-md-4 btn btn-default pull-right')))
            ->add('publicationImage', SubmitType::class, array('label' => 'Image','attr'=> array('class'=>'col-md-4 btn btn-default pull-right')));

       // ->add('Ajouter un commentaire',SubmitType::class, array('label' => false, 'attr'=> array('class'=>'btn btn-default pull-right')));

       /* ->add('idUser',EntityType::class,array(
        'class'=>"EspritEntreAide\UserBundle\Entity\User"
    ))*/


    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
       /* $resolver->setDefaults(array(
            'data_class' => 'EspritEntreAide\SpottedBundle\Entity\Publication'
        ));*/
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        //return 'espritentreaide_spottedbundle_publication';
    }


}
