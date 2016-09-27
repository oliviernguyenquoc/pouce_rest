<?php

namespace Pouce\TeamBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ResultType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        for($i=0;$i<=48;$i++)
        { 
            $choix[] = $i . ' h'; 
        }
        $choix[] = '> 48 h';
        $builder
            ->add('position', new PositionType())
            ->add('isValid', 'checkbox', array(
                'label'     => false,
                'required'  => false
            ))
            ->add('lateness', ChoiceType::class, array(
                'choices'   => $choix,
                'label'     => false
            ))
            ->add('nbCar', NumberType::class, array(
                'label'     => 'Combien de véhicule avez-vous pris ?',
                'required'  => true
            ))
            ->add('avis', TextareaType::class, array(
                'label'     => 'Donne-nous ton ressentiment sur l\'évènement',
                'required'  => true,
                'attr'=> array('class'=>'materialize-textarea')
            ))
            ->add('sponsort', CheckboxType::class, array(
                'label'     => 'Aurais-tu été d\'accord de porter un gillet jaune, fourni gratuitement, avec un sponsort du Pouce d\'Or imprimé coté torse ou coté dos :  ',
                'required'  => false
            ))
            ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Pouce\TeamBundle\Entity\Result'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pouce_teambundle_result';
    }
}
