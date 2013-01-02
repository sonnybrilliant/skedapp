<?php

namespace SkedApp\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use SkedApp\BookingBundle\Form\DataTransformer\StringToDateTransformer;
use SkedApp\BookingBundle\Form\DataTransformer\StringToTimeslotTransformer;
use SkedApp\ConsultantBundle\Form\DataTransformer\StringToConsultantTransformer;
use SkedApp\ServiceBundle\Form\DataTransformer\StringToServiceTransformer;

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

        $entityManager = $options['em'];
        $stringTimeslotTransformer = new StringToTimeslotTransformer($entityManager);
        $dateTimeTransformer = new StringToDateTransformer();
        $stringConsultantTransformer = new StringToConsultantTransformer($entityManager);
        $stringServiceTransformer = new StringToServiceTransformer($entityManager);

        $builder
            ->add(
                    $builder->create('appointmentDate', 'hidden')
                      ->addModelTransformer($dateTimeTransformer)
                    )
            ->add(
                    $builder->create('startTimeslot', 'hidden', array('data_class' => null))
                      ->addModelTransformer($stringTimeslotTransformer)
                    )
            ->add(
                    $builder->create('consultant', 'hidden', array('data_class' => null))
                      ->addModelTransformer($stringConsultantTransformer)
                    )
            ->add(
                    $builder->create('service', 'hidden', array('data_class' => null))
                      ->addModelTransformer($stringServiceTransformer)
                    )
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

        $resolver->setRequired(array(
            'em',
        ));

        $resolver->setAllowedTypes(array(
            'em' => 'Doctrine\Common\Persistence\ObjectManager',
        ));
    }

}

?>
