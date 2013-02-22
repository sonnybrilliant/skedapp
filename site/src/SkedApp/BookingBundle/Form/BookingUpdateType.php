<?php

namespace SkedApp\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

/**
 * SkedApp\ConsultantBundle\Form\BookingUpdateType
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppBookingBundle
 * @subpackage Form
 * @version 0.0.1
 */
class BookingUpdateType extends AbstractType
{

    /**
     *
     * @var boolean
     */
    private $isAdmin = false;

    /**
     *
     * @var Integer
     */
    private $companyId = null;

    public function __construct($companyId, $isAdmin = false)
    {
        $this->companyId = $companyId;
        $this->isAdmin = $isAdmin;
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
        $companyId = $this->companyId;
        $isAdmin = $this->isAdmin;

        $builder
            ->add('appointmentDate', 'date', array(
                'attr' => array('class' => 'span4 datepicker'),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ))
            ->add('consultant', 'entity', array(
                'class' => 'SkedAppCoreBundle:Consultant',
                'label' => 'Consultant:',
                'attr' => array('class' => 'span12'),
                'query_builder' => function(EntityRepository $er) use ($companyId, $isAdmin) {

                    if ($isAdmin) {
                        return $er->createQueryBuilder('c')
                        ->where('c.isDeleted = :status')
                        ->setParameter('status',false);
                    } else {
                        return $er->createQueryBuilder('c')
                            ->where('c.isDeleted = :status')
                            ->andWhere('c.company = :company')
                            ->setParameters(array(
                                'status' => false,
                                'company' => $companyId
                            ));
                    }

                },
            ))
            ->add('customerOrNot', 'choice', array(
                'label' => 'Please select:',
                'required' => true,
                'expanded' => true,
                'choices' => array(true => 'Link an existing customer', false => 'Add customer details'),
            ))
            ->add('customer', 'entity', array(
                'class' => 'SkedAppCoreBundle:Customer',
                'label' => 'Customer:',
                'empty_value' => 'Select a customer',
                'attr' => array('class' => 'span12 chosen'),
                'required' => false,
                'query_builder' => function(EntityRepository $er) {
                     return $er->createQueryBuilder('c')
                        ->where('c.isDeleted = :status')
                        ->andWhere('c.enabled  = :enabled')
                        ->andWhere('c.isActive  = :isActive')
                        ->setParameters(array(
                            'status' => false,
                            'enabled' => true,
                            'isActive' => true
                        ));
                },
            ))
            ->add('customerPotential', 'entity', array(
                'class' => 'SkedAppCoreBundle:CustomerPotential',
                'label' => 'Offline Customer:',
                'empty_value' => 'Select an offline customer',
                'attr' => array('class' => 'span12 chosen'),
                'required' => false,
                'query_builder' => function(EntityRepository $er) {
                     return $er->createQueryBuilder('c')
                        ->where('c.isDeleted = :status')
                        ->andWhere('c.enabled  = :enabled')
                        ->andWhere('c.isActive  = :isActive')
                        ->setParameters(array(
                            'status' => false,
                            'enabled' => true,
                            'isActive' => true
                        ));
                },
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
                'attr' => array('class' => 'tinymce span12', 'data-theme' => 'simple'),
                'required' => false
            ))
            ->add('isLeave', 'checkbox', array(
                'label' => 'Is leave:',
                'required' => false,
            ))
            ->add('isConfirmed', 'checkbox', array(
                'label' => 'Is confirmed:',
                'required' => false,
            ))
            ->add('service', 'entity', array(
                'class' => 'SkedAppCoreBundle:Service',
                'label' => 'Services:',
                'multiple' => false,
                'required' => false,
                'attr' => array('class' => 'span12'),
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

