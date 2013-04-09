<?php

namespace SkedApp\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use SkedApp\BookingBundle\Form\EventListener\AddServiceProviderFieldSubscriber;

/**
 * SkedApp\ConsultantBundle\Form\BookingSelectConsultantsDateType
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppBookingBundle
 * @subpackage Form
 * @version 0.0.1
 */
class BookingSelectConsultantsDateType extends AbstractType
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
    
    private $date = null;


    public function __construct($companyId, $isAdmin = false)
    {
        $this->companyId = $companyId;
        $this->isAdmin = $isAdmin;
        $this->date = new \DateTime();
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

        $attr = array();

        if ($isAdmin) {
            $attr = array('class' => 'span4', 'disabled' => true);
        } else {
            $attr = array('class' => 'span4 chosen');
        }

        
        if($isAdmin){
           $builder
            ->add('company', 'entity', array(
                'required' => false,
                'class' => 'SkedAppCoreBundle:Company',
                'label' => 'Company:',
                'attr' => array('class' => 'span4 chosen'),
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.isDeleted = :status')
                        ->setParameter('status', false);
                },
            )); 
        }
        

        $builder
            ->add('filterDate', 'text', array(
                'attr' => array('class' => 'span4 datepicker', 'value' => $this->date->format('d-m-Y')),
            ))
            ->add('consultant', 'entity', array(
                'class' => 'SkedAppCoreBundle:Consultant',
                'label' => 'Consultant:',
                'multiple' => true,
                'attr' => $attr,
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
            ));
                
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return 'Booking_Consultants';
    }

}

