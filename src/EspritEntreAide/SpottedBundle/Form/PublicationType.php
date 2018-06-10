<?php

namespace EspritEntreAide\SpottedBundle\Form;

use blackknight467\StarRatingBundle\Form\RatingType;
use Ivory\CKEditorBundle\Twig\CKEditorExtension;
use function Sodium\add;
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
use Ivory\CKEditorBundle\Form\Type\CKEditorType;

class PublicationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('descP',CKEditorType::class,array(

            ))
            ->add('tags',TextType::class,array())
            ->add('tags',TextType::class, array('label' => false, 'attr'=> array(

                'placeholder' => 'Ajouter Tag',
                'class'=>'form-control ui-autocomplete-input',
                'auto-complete'=>'off',
                'id'=>'tags'

            )))
            ->add('Ajouter',SubmitType::class, array('label' => false, 'attr'=> array('class'=>'btn btn-default pull-right')));



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
