<?php

namespace SkedApp\SearchBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;


/**
 * SkedApp\ConsultantBundle\Form\ConsultantCreateType
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppConsultantBundle
 * @subpackage Form
 * @version 0.0.1
 */
class SearchType extends AbstractType
{

    /**
     * Build Form
     *
     * @param FormBuilder $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('category', 'entity', array(
                'class' => 'SkedAppCoreBundle:Category',
                'empty_value' => 'Select a category',
                'label' => 'Category:',
                'required' => true,
                'attr' => array('class' => 'span4')
            ))
            ->add('address', 'text', array(
                'label' => 'Type your current location:',
                'attr' => array('class' => 'span4')
            ))
            ->add('locality', 'hidden')
            ->add('country', 'hidden', array ('attr' => array ('value' => 'South Africa')))
            ->add('administrative_area_level_2', 'hidden')
            ->add('administrative_area_level_1', 'hidden')
            ->add('lat', 'hidden')
            ->add('lng', 'hidden')
            ->add('consultantServices', 'entity', array(
                'class' => 'SkedAppCoreBundle:Service',
                'label' => 'Services:',
                'multiple' => true,
                'required' => false,
                'attr' => array('class' => 'span4'),

            ))
            ->add('booking_date', 'text', array(
                'label' => 'Date:',
                'required' => true,
                'attr' => array('class' => 'span4', 'value' => date ('Y-m-d')),

            ))
        ;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return 'Search';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'category' => '0',
            'booking_date' => date ('d-m-Y'),
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'category' => '0',
            'booking_date' => date ('d-m-Y'),
        ));
    }

}

?>
