<?php

namespace EspritEntreAide\SpottedBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentaireType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('contentComm',TextareaType::class, array('label' => false, 'attr'=> array('placeholder' => 'Ecrire un commentaire','class'=>'feedback-input')))

            ->add('Ajouter un commentaire',SubmitType::class, array('label' => false, 'attr'=> array('class'=>'btn btn-default pull-right')));

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
            'data_class' => 'EspritEntreAide\SpottedBundle\Entity\Commentaire'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'espritentreaide_spottedbundle_commentaire';
    }


}
