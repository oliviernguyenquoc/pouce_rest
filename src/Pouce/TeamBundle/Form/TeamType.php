<?php

namespace Pouce\TeamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Pouce\UserBundle\Entity\User;
use Pouce\UserBundle\Entity\School;

class TeamType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('teamName', TextType::class, array(
                'label'=> 'Nom de l\'équipe',
                'required'    => true
            ))
            ->add('targetDestination', TextType::class, array(
                'label'=> 'Jusqu\'où pensez vous arrivez',
                'required'    => true
            ))
            ->add('comment', TextareaType::class, array(
                'required'    => true,
                'label' => 'Un commentaire'
            ))
            // ->add('users', EntityType::class, array(
            //     'class'=>'PouceUserBundle:User',
            //     'label' => 'Co-équipié',
            //     'choice_value'=>'email',
            //     'required'  => true,
            // ))
            ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Pouce\TeamBundle\Entity\Team',
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pouce_teambundle_team';
    }
}
