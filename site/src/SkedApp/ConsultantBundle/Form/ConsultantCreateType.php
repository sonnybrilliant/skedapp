<?php

namespace SkedApp\ConsultantBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use SkedApp\ConsultantBundle\Form\EventListener\AddCompanyFieldSubscriber;
use Doctrine\ORM\EntityRepository;

/**
 * SkedApp\ConsultantBundle\Form\ConsultantCreateType
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppConsultantBundle
 * @subpackage Form
 * @version 0.0.1
 */
class ConsultantCreateType extends AbstractType
{

    private $container;

    /**
     *
     * @param type $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Build Form
     *
     * @param FormBuilder $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $subscriber = new AddCompanyFieldSubscriber($builder->getFormFactory());
        $subscriber->setContainer($this->container);
        
        $builder
            ->addEventSubscriber($subscriber)
            ->add('firstName', 'text', array(
                'label' => 'First name:',
                'attr' => array('class' => 'span12')
            ))
            ->add('lastName', 'text', array(
                'label' => 'Last name:',
                'attr' => array('class' => 'span12')
            ))
            ->add('email', 'repeated', array(
                'type' => 'email',
                'first_name' => 'first',
                'first_options' => array('label' => 'Email address:'),
                'options' => array('attr' => array('class' => 'span12')),
                'second_name' => 'second',
                'second_options' => array('label' => 'Confirm Email address:'),
                'invalid_message' => 'Email addresses do not match',
            ))
//            ->add('company', 'entity', array(
//                'class' => 'SkedAppCoreBundle:Company',
//                'label' => 'Company:',
//                'attr' => array('class' => 'span12 chosen'),
//                'query_builder' => function(EntityRepository $er) {
//                    return $er->createQueryBuilder('c')
//                        ->where('c.isDeleted = :status')
//                        ->setParameter('status', false);
//                },
//            ))
            ->add('gender', 'entity', array(
                'class' => 'SkedAppCoreBundle:Gender',
                'label' => 'Gender:',
                'attr' => array('class' => 'span12 chosen')
            ))
            ->add('category', 'entity', array(
                'class' => 'SkedAppCoreBundle:Category',
                'empty_value' => 'Select a category',
                'label' => 'Category:',
                'attr' => array('class' => 'span12 chosen'),
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
                'attr' => array('class' => 'span12', 'disabled' => 'disabled'),
            ))
            ->add('speciality', 'textarea', array(
                'label' => 'Speciality:',
                'required' => false,
                'attr' => array('class' => 'tinymce span12', 'data-theme' => 'simple')
            ))
            ->add('professionalStatement', 'textarea', array(
                'label' => 'Professional Statement:',
                'required' => false,
                'attr' => array('class' => 'tinymce span12', 'data-theme' => 'simple')
            ))
            ->add('picture', 'file', array(
                'label' => 'Profile picture:',
                'required' => false,
                'attr' => array('class' => 'span12')
            ))
            ->add('monday', 'checkbox', array(
                'label' => 'M',
                'required' => false,
            ))
            ->add('tuesday', 'checkbox', array(
                'label' => 'T',
                'required' => false,
            ))
            ->add('wednesday', 'checkbox', array(
                'label' => 'W',
                'required' => false,
            ))
            ->add('thursday', 'checkbox', array(
                'label' => 'T',
                'required' => false,
            ))
            ->add('friday', 'checkbox', array(
                'label' => 'F',
                'required' => false,
            ))
            ->add('saturday', 'checkbox', array(
                'label' => 'F',
                'required' => false,
            ))
            ->add('sunday', 'checkbox', array(
                'label' => 'S',
                'required' => false,
            ))
            ->add('startTimeslot', 'entity', array(
                'class' => 'SkedAppCoreBundle:Timeslots',
                'label' => 'Time from:',
                'attr' => array('class' => 'span6')
            ))
            ->add('endTimeslot', 'entity', array(
                'class' => 'SkedAppCoreBundle:Timeslots',
                'label' => 'Time to:',
                'attr' => array('class' => 'span3')
            ))
            ->add('appointmentDuration', 'entity', array(
                'class' => 'SkedAppCoreBundle:AppointmentDuration',
                'label' => 'Session length:',
                'attr' => array('class' => 'span4')
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
