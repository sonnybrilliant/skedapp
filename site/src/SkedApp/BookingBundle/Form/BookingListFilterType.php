<?php

namespace SkedApp\BookingBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

/**
 * SkedApp\ConsultantBundle\Form\BookingListFilterType
 *
 * @author Otto Saayman <otto.saayman@creativecloud.co.za>
 * @package SkedAppBookingBundle
 * @subpackage Form
 * @version 0.0.1
 */
class BookingListFilterType extends AbstractType
{

    /**
     *
     * @var Integer
     */
    private $companyId = null;

    /**
     *
     * @var \DateTime
     */
    private $filterDate = null;

    public function __construct($companyId = 0, $filterDate = null)
    {
        $this->companyId = $companyId;

        if (!is_object($this->filterDate))
                $this->filterDate = new \DateTime();

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

        $builder
            ->add('filterDate', 'date', array(
                'attr' => array('class' => 'span3 datepicker', 'value' => $this->filterDate->format('Y-m-d')),
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ))
            ->add('consultant', 'entity', array(
                'class' => 'SkedAppCoreBundle:Consultant',
                'label' => 'Consultant:',
                'empty_value' => 'Select a consultant',
                'attr' => array('class' => 'span4'),
                'query_builder' => function(EntityRepository $er) use ($companyId) {

                    if ($companyId <= 0) {
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

        ;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return 'BookingListFilter';
    }
}
