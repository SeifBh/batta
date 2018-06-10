<?php

namespace EspritEntreAide\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('username', null, array('empty_data' => 'John Doe','label' => false, 'translation_domain' => 'FOSUserBundle','attr'=> array('placeholder' => 'Username','class'=>'input_perso')))
            ->add('email', EmailType::class, array('label' => false, 'translation_domain' => 'FOSUserBundle','attr'=> array('placeholder' => 'Email','class'=>'input_perso')))
            ->add('nom', null, array('label' => false, 'translation_domain' => 'FOSUserBundle','attr'=> array('placeholder' => 'Nom','class'=>'input_perso')))
            ->add('prenom', null, array('label' => false, 'translation_domain' => 'FOSUserBundle','attr'=> array('placeholder' => 'Prenom','class'=>'input_perso')))
            ->add('sexe', ChoiceType::class, array(
                    'attr'  =>  array('class' => 'form-control',
                        'style' => 'margin:5px 0;'),
                    'choices' =>
                        array
                        (
                            'male' => 'male',
                            'female' => 'female',
                        ) ,
                    'multiple' => false,
                    'required' => true,
                )
            )
            ->add('roles', ChoiceType::class, array(
                    'attr'  =>  array('class' => 'form-control',
                        'style' => 'margin:5px 0;'),
                    'choices' =>
                        array
                        (
                            'ROLE_ADMIN' => 'ROLE_ADMIN',
                            'ROLE_USER' => 'ROLE_USER',
                            'ROLE_ENSEIGNANT' => 'ROLE_ENSEIGNANT',
                            'ROLE_ETUDIANT' => 'ROLE_ETUDIANT',
                            'ROLE_RESPONSABLE_CLUB' => 'ROLE_RESPONSABLE_CLUB',
                            'ROLE_RESPONSABLE_STORE' => 'ROLE_RESPONSABLE_STORE',
                            'ROLE_RESPONSABLE_SUPER_ADMIN' => 'ROLE_RESPONSABLE_SUPER_ADMIN'
                        ) ,
                    'multiple' => true,
                    'required' => true,
                )
            )

            /*  ->add('roles', ChoiceType::class, array(
          'label' => false,
          'attr'=> array('class'=>'input_perso'),
          'choices'  => array(
          'Etudiant' => true,
          'Enseignat' => false,
          'Responsable Club 00' => false,
          'Responsable Store' => false,
          'Admin' => false,


      )))*/

            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'attr'=> array('class'=>'input_perso'),
                'options' => array(
                    'translation_domain' => 'FOSUserBundle',
                    'attr' => array(
                        'autocomplete' => 'new-password',
                    ),
                ),
                'first_options' => array('label' => false,'attr'=> array('placeholder' => 'Password','class'=>'input_perso')),
                'second_options' => array('label' => false,'attr'=> array('placeholder' => 'Re-type password','class'=>'input_perso')),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('Ajouter', SubmitType::class)
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EspritEntreAide\UserBundle\Entity\User'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'espritentreaide_userbundle_user';
    }


}
