<?php

// src/Pouce/UserBundle/Form/Type/RegistrationType.php

namespace Pouce\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\OptionsResolver\OptionsResolver;
 
class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $year= 2015; //date('Y'); //TODO: Change the year
        
        // add your custom field
        $builder
            ->remove('username')
            ->add('first_name', TextType::class, array(
                'label'=> 'Prénom',
                'required'    => true
            ))
            ->add('last_name' ,TextType::class, array(
                'label'=> 'Nom',
                'required'    => true
            ))
            ->add('sex', ChoiceType::class, array(
                'choices' => array(
                    'Homme' => 'Homme',
                    'Femme' => 'Femme'
                ),
                'required'    => true,
                'label' => 'Sexe'
            ))
            ->add('school', EntityType::class, array(
                'class' => 'PouceUserBundle:School',
                'choice_value' => 'name',
                'label' => 'Ecole/Université',
                'query_builder' => function(\Pouce\UserBundle\Entity\SchoolRepository $er) use($year) {
                    return $er-> getAllSchools($year); //TODO: Change method
                },
            ))
            ->add('telephone', TextType::class, array(
                'label'=> 'Numéro de téléphone',
                'required'    => true
            ));
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Pouce\UserBundle\Entity\User',
            'csrf_protection' => false
        ));
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    // public function getName()
    // {
    //     return 'pouce_user_registration';
    // }

    public function getBlockPrefix()
    {
        return 'pouce_user_registration';
    }
}
