<?php

namespace SkedApp\ConsultantBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

/**
 * SkedApp\ConsultantBundle\Form\ConsultantCreateType
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppConsultantBundle
 * @subpackage Form
 * @version 0.0.1
 */
class ConsultantCreateType extends AbstractType
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
            ->add('firstName', 'text', array(
                'label' => 'First name:',
                'attr' => array('class' => 'span4')
            ))
            ->add('lastName', 'text', array(
                'label' => 'Last name:',
                'attr' => array('class' => 'span4')
            ))
            ->add('email', 'repeated', array(
                'type' => 'email',
                'first_name' => 'first',
                'first_options'  => array('label' => 'Email address:'),
                'options' => array('attr' => array('class' => 'span4')),
                'second_name' => 'second',
                'second_options' => array('label' => 'Confirm Email address:'),
                'invalid_message' => 'Email addresses do not match',

            ))
            ->add('company', 'entity', array(
                'class' => 'SkedAppCoreBundle:Company',
                'label' => 'Company:',
                'attr' => array('class' => 'span4 chosen')
            ))
            ->add('gender', 'entity', array(
                'class' => 'SkedAppCoreBundle:Gender',
                'label' => 'Gender:',
                'attr' => array('class' => 'span4 chosen')
            ))
            ->add('category', 'entity', array(
                'class' => 'SkedAppCoreBundle:Category',
                'empty_value' => 'Select a category',
                'label' => 'Category:',
                'attr' => array('class' => 'span4 chosen'),
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.isDeleted = :status')
                        ->setParameter('status', false);
                },
            ))
            ->add('consultantServices', 'entity', array(
                'class' => 'SkedAppCoreBundle:Service',
                'label' => 'Services:',
                'multiple' => true,
                'required' => false,
                'attr' => array('class' => 'span4' , 'disabled' => 'disabled'),

            ))
            ->add('speciality', 'textarea', array(
                'label' => 'Speciality:',
                'required' => false,
                'attr' => array('class' => 'tinymce span4', 'data-theme' => 'simple')
            ))
            ->add('professionalStatement', 'textarea', array(
                'label' => 'Professional Statement:',
                'required' => false,
                'attr' => array('class' => 'tinymce span4', 'data-theme' => 'simple')
            ))
            ->add('picture', 'file', array(
                'label' => 'Profile picture:',
                'required' => false,
                'attr' => array('class' => 'span4')
            ))
            ->add('monday', 'checkbox', array(
                'label' => 'M',
                'required'  => false,
            ))
            ->add('tuesday', 'checkbox', array(
                'label' => 'T',
                'required'  => false,
            ))
            ->add('wednesday', 'checkbox', array(
                'label' => 'W',
                'required'  => false,
            ))
            ->add('thursday', 'checkbox', array(
                'label' => 'T',
                'required'  => false,
            ))
            ->add('friday', 'checkbox', array(
                'label' => 'F',
                'required'  => false,
            ))
            ->add('saturday', 'checkbox', array(
                'label' => 'F',
                'required'  => false,
            ))
            ->add('sunday', 'checkbox', array(
                'label' => 'S',
                'required'  => false,
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
            ->add('appointmentDuration', 'entity', array(
                'class' => 'SkedAppCoreBundle:AppointmentDuration',
                'label' => 'Session length:',
                'attr' => array('class' => 'span1')
            ))
        ;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return 'Consultant';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'SkedApp\CoreBundle\Entity\Consultant',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SkedApp\CoreBundle\Entity\Consultant',
        ));
    }

}

?>
