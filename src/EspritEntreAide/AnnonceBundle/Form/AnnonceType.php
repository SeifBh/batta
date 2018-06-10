<?php

namespace EspritEntreAide\AnnonceBundle\Form;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AnnonceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('categorieA',ChoiceType::class, array('choices'
                      => array('permutation' => 'permutation','collocation' => 'collocation' ,
                               'covoiturage' => 'covoiturage' ,'ObjetTrouve' => 'ObjetTrouve' ,'ObjetPerdu' => 'ObjetPerdu' )))
                ->add('titreA')
                ->add('descA')
                ->add('numTel')
                ->add('Ajouter',SubmitType::class);

        /*->add('dateA', 'Symfony\Component\Form\Extension\Core\Type\DateTimeType', array(
                'widget' => 'single_text',
                'input' => 'datetime',
                'required' => 'false',
                'format' => 'yyyy-MM-dd HH:mm',
                'attr' => array('data-date-format' => 'YYYY-MM-DD HH:mm', 'readonly' => true)))*/

    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EspritEntreAide\AnnonceBundle\Entity\Annonce'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'espritentreaide_annoncebundle_annonce';
    }


}
