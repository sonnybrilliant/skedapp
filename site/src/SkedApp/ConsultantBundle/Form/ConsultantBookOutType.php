<?php

namespace SkedApp\ConsultantBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

/**
 * SkedApp\ConsultantBundle\Form\ConsultantBookOutType
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppConsultantBundle
 * @subpackage Form
 * @version 0.0.1
 */
class ConsultantBookOutType extends AbstractType
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
            ->add('startTimeslot', 'entity', array(
                'class' => 'SkedAppCoreBundle:Timeslots',
                'label' => 'Start time:',
                'required' => true,
                'attr' => array('class' => 'span12')
            ))
            ->add('endTimeslot', 'entity', array(
                'class' => 'SkedAppCoreBundle:Timeslots',
                'label' => 'End time:',
                'required' => true,
                'attr' => array('class' => 'span12')
            ))
            ->add('start_date', 'text', array(
                'label' => 'Start date:',
                'required' => true,
                'attr' => array('class' => 'span12 datePicker'),

            ))
            ->add('end_date', 'text', array(
                'label' => 'End date:',
                'required' => true,
                'attr' => array('class' => 'span12 datePicker'),

            ))
        ;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return 'BookOut';
    }

}

?>
