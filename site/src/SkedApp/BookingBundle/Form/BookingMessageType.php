<?php

namespace SkedApp\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

/**
 * SkedApp\ConsultantBundle\Form\BookingMessageType
 *
 * @author Otto Saayman <otto.saayman@creativecloud.co.za>
 * @package SkedAppBookingBundle
 * @subpackage Form
 * @version 0.0.1
 */
class BookingMessageType extends AbstractType
{
    /**
     * A hypen delimted string with booking ids
     * 
     * @var string 
     */
    private $bookings = null;

    public function __construct($bookings)
    {
       $this->bookings = $bookings ;
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
        
        $builder
            ->add('message', 'textarea', array(
                'required' => false,
                'label' => 'Type a short message to be sent to each selected booking\'s customer',
                'attr' => array('class' => 'span4','rows'=> '5' ),
            ))
            ->add('bookings', 'hidden', array(
                'attr' => array('value' => $this->bookings),
            ));
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return 'BookingMessage';
    }

}
