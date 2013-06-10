<?php

namespace SkedApp\ConsultantBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * SkedApp\ConsultantBundle\Form\ConsultantTimeSlotsType
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppConsultantBundle
 * @subpackage Form
 * @version 0.0.1
 */
class ConsultantTimeSlotsType extends AbstractType
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
            ->add('consultant', 'entity', array(
                'class' => 'SkedAppCoreBundle:Consultant',
                'attr' => array('style'=>'display:none')
            ))
            ->add('daysOfTheWeek', 'entity', array(
                'class' => 'SkedAppCoreBundle:DaysOfTheWeek',
                'attr' => array('style'=>'display:none')
            ))
            ->add('tokenDeletedSlot', 'hidden')
            ->add('slots', 'collection', array(
                'type' => new SlotsType(),
                'by_reference' => false,
                'allow_delete' => true,
                'allow_add' => true,
            ));
        

    }

   public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SkedApp\CoreBundle\Entity\ConsultantTimeSlots',
        ));
    }

    public function getName()
    {
        return 'consultant_time_slots';
    }

}

?>
