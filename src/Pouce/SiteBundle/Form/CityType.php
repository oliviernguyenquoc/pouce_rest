<?php

namespace Pouce\SiteBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array(
                'required'  => true
            ))
            ->add('country', EntityType::class, array(
                'class' => 'PouceSiteBundle:Country',
                'required'  => true
            ))
            ->add('province', TextType::class, array(
                'required'  => true
            ))
            ->add('population', NumberType::class)
            ->add('longitude', NumberType::class, array(
                'required'  => true
            ))
            ->add('latitude', NumberType::class, array(
                'required'  => true
            ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Pouce\SiteBundle\Entity\City',
            'csrf_protection' => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pouce_sitebundle_city';
    }
}
