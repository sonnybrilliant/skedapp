<?php

namespace SkedApp\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use SkedApp\CoreBundle\Entity\Timeslots;

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
     *
     * @var boolean
     */
    private $isAdmin = false;

    /**
     *
     * @var Integer
     */
    private $companyId = null;

    /**
     *
     * @var \DateTime
     */
    private $appointmentDate = null;

    /**
     *
     * @var SkedAppCoreBundle/Entity/Consultant
     */
    private $consultant = null;

    public function __construct($companyId, $isAdmin = false, $appointmentDate = null, $consultant = null)
    {
        $this->companyId = $companyId;
        $this->isAdmin = $isAdmin;
        $this->appointmentDate = $appointmentDate;
        $this->consultant = $consultant;

        if (!is_object($this->appointmentDate)) {
            $this->appointmentDate = new \DateTime();
        }
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
        $consultant = $this->consultant;
        $attrServices = array();
        
        if($consultant){
          $attrServices = array(
             'class' => 'span12',
          );
        }else{
          $attrServices = array(
             'class' => 'span12',
             'disabled' => 'disabled' 
          );  
        }
        
        $builder
            ->add('appointmentDate', 'text', array(
                'attr' => array('class' => 'span12 datePicker', 'value' => $this->appointmentDate->format('Y-m-d')),
                'label' => 'Date:',
            ))
            ->add('startTimeslot', 'entity', array(
                'class' => 'SkedAppCoreBundle:Timeslots',
                'label' => 'Start time:',
                'attr' => array('class' => 'span12')
            ))
            ->add('endTimeslot', 'entity', array(
                'class' => 'SkedAppCoreBundle:Timeslots',
                'label' => 'End time:',
                'attr' => array('class' => 'span12')
            ))
            ->add('consultant', 'entity', array(
                'class' => 'SkedAppCoreBundle:Consultant',
                'label' => 'Consultant:',
                'required' => true,
                'empty_value' => 'Select a consultant',
                'attr' => array('class' => 'span12'),
                'query_builder' => function(EntityRepository $er) use ($companyId, $isAdmin) {

                    if ($isAdmin) {
                        return $er->createQueryBuilder('c')
                            ->where('c.isDeleted = :status')
                            ->setParameter('status', false);
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
                'label' => 'Please select customer type:',
                'required' => true,
                'expanded' => true,
                'choices' => array(true => 'Link an existing customer', false => 'Add customer details'),
            ))
            ->add('customer', 'entity', array(
                'class' => 'SkedAppCoreBundle:Customer',
                'label' => 'Customer:',
                'empty_value' => 'Select a customer',
                'attr' => array('class' => 'span12'),
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
                'empty_value' => 'Select an off-line customer',
                'attr' => array('class' => 'span12'),
                'required' => false,
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.isDeleted = :status')
                        ->andWhere('c.enabled  = :enabled')
                        ->andWhere('c.isActive  = :isActive')
                        ->setParameters(array(
                            'status' => false,
                            'enabled' => false,
                            'isActive' => true
                        ));
                },
            ))
            ->add('description', 'textarea', array(
                'label' => 'Description:',
                'required' => false,
                'attr' => array('class' => 'tinymce span12', 'data-theme' => 'simple'),
                'required' => false
            ))
            ->add('isConfirmed', 'checkbox', array(
                'label' => 'Is confirmed:',
                'required' => false,
            ))
            ->add('service', 'entity', array(
                'class' => 'SkedAppCoreBundle:Service',
                'label' => 'Services:',
                'multiple' => false,
                'required' => true,
                'attr' => $attrServices,
                'query_builder' => function(EntityRepository $er) use ($consultant) {

                    if (is_null($consultant)) {
                        return $er->createQueryBuilder('s')
                            ->where('s.isDeleted = :status')
                            ->setParameter('status', false);
                    } else {
                        return $er->createQueryBuilder('s')
                            ->innerJoin('s.consultants', 'c')
                            ->where('s.isDeleted = :status')
                            ->andWhere('c.id = :consultant')
                            ->setParameters(array(
                                'status' => false,
                                'consultant' => $consultant
                            ));
                    }
                },
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
