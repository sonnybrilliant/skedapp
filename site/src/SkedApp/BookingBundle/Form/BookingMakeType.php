<?php

namespace SkedApp\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

/**
 * SkedApp\BookingBundle\Form\BookingMakeType
 *
 * @author Otto Saayman <otto.saayman@kaizania.co.za>
 * @package SkedAppBookingBundle
 * @subpackage Form
 * @version 0.0.1
 */
class BookingMakeType extends AbstractType
{

    /**
     *
     * @var Integer
     */
    private $companyId = null;

    /**
     *
     * @var Integer
     */
    private $consultantId = null;

    /**
     *
     * @var Integer
     */
    private $serviceId = null;

    /**
     *
     * @var String
     */
    private $date = null;

    /**
     *
     * @var String
     */
    private $timeSlotStart = null;

    /**
     *
     * @var String
     */
    private $timeSlotEnd = null;

    public function __construct($companyId, $consultantId, $date, $timeSlotStart, $serviceIds)
    {
        $this->companyId = $companyId;
        $this->consultantId = $consultantId;
        $this->date = $date;
        $this->timeSlotStart = $timeSlotStart;
        $this->serviceIds = $serviceIds;
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
        $consultantId = $this->consultantId;
        $date = $this->date;
        $timeSlotStart = $this->timeSlotStart;
        $serviceIds = $this->serviceIds;

        $builder
            ->add('appointmentDate', 'date', array(
                'attr' => array('class' => 'span3 datepicker', 'value' => 'Select date', 'value' => $this->date),
                'label' => 'Booking Date:',
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ))
            ->add('startTimeslot', 'entity', array(
                'class' => 'SkedAppCoreBundle:Timeslots',
                'label' => 'Booking time:',
                'attr' => array('class' => 'span1', 'value' => $this->timeSlotStart)
            ))
            ->add('consultant', 'entity', array(
                'class' => 'SkedAppCoreBundle:Consultant',
                'label' => 'Consultant:',
                'empty_value' => 'Select a consultant',
                'attr' => array('class' => 'span4', 'value' => $this->consultantId),
                'query_builder' => function(EntityRepository $er) use ($companyId) {

                    return $er->createQueryBuilder('c')
                        ->where('c.isDeleted = :status')
                        ->andWhere('c.company = :company')
                        ->setParameters(array(
                            'status' => false,
                            'company' => $companyId
                        ));
                },
            ))
            ->add('service', 'entity', array(
                'class' => 'SkedAppCoreBundle:Service',
                'label' => 'Services:',
                'multiple' => false,
                'required' => false,
                'attr' => array('class' => 'span4' , 'disabled' => 'disabled', 'value' => $this->serviceIds),
            ))
            ->add('description', 'textarea', array(
                'label' => 'Notes:',
                'attr' => array('class' => 'tinymce span4', 'data-theme' => 'simple')
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
