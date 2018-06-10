<?php

namespace EspritEntreAide\SpottedBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RechercheSpottedType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('descP')
        ->add('descP',TextType::class, array('label' => false, 'attr'=> array('placeholder' => 'Rechercher','class'=>'search-box')));

        // ->add('dateCreation')
                //->add('dateModif');
                //->add('idUser')
               // ->add('idPublication');
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EspritEntreAide\SpottedBundle\Entity\Publication'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'espritentreaide_spottedbundle_publication';
    }


}
