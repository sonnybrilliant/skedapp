<?php

namespace SkedApp\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;


/**
 * SkedApp\ConsultantBundle\Form\BookingCreateType
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppBookingBundle
 * @subpackage Form
 * @version 0.0.1
 */
class BookingCreateType extends AbstractType
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
          ->add('appointmentDate','text', array(
              'attr' => array('class' => 'datepicker')
          ))  
            
           ->add('consultant', 'entity', array(
                'class' => 'SkedAppCoreBundle:Consultant',
                'label' => 'Consultant:',
                'attr' => array('class' => 'span4')
            ))
            ->add('startTimeslot', 'entity', array(
                'class' => 'SkedAppCoreBundle:Timeslots',
                'label' => 'Time from:',
                'attr' => array('class' => 'span1')
            ))
            ->add('endTimeslot', 'entity', array(
                'class' => 'SkedAppCoreBundle:Timeslots',
                'label' => 'Time to:',
                'attr' => array('class' => 'span1')
            )) 
            ->add('description', 'textarea', array(
                'label' => 'Description:',
                'attr' => array('class' => 'tinymce span4', 'data-theme' => 'simple')
            ))
            ->add('service', 'entity', array(
                'class' => 'SkedAppCoreBundle:Service',
                'label' => 'Services:',
                'multiple' => true,
                'required' => false,
                'attr' => array('class' => 'span4'),
                
            ))            
            
        ;
    }

    /**
     * Get name
     * @return string 
     */
    public function getName()
    {
        return 'Booking';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'SkedApp\CoreBundle\Entity\Booking',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SkedApp\CoreBundle\Entity\Booking',
        ));
    }

}

?>
