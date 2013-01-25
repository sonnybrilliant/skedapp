<?php

namespace SkedApp\CustomerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

/**
 * SkedApp\CustomerBundle\Form\CustomerCreateApiType
 *
 * @author Otto Saayman <otto.saayman@creativecloud.co.za>
 * @package SkedAppConsultantBundle
 * @subpackage Form
 * @version 0.0.1
 */
class CustomerCreateApiType extends AbstractType
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
            ->add('mobileNumber', 'text', array(
                'label' => 'Mobile number:',
                'attr' => array('class' => 'span4')
            ))
            ->add('landLineNumber', 'text', array(
                'label' => 'Land line number:',
                'required' => false,
                'attr' => array('class' => 'span4')
            ))
            ->add('email', 'email', array(
                'attr' => array('class' => 'span4'),
                'label' => 'Email address:'
            ))
            ->add('password', 'password', array(
                'attr' => array('class' => 'span4'),
                'label' => 'Password:'
            ))
            ->add('internet', 'checkbox', array(
                'label' => 'Internet:',
                'required' => false,
           ))
            ->add('tv', 'checkbox', array(
                'label' => 'Television:',
                'required' => false,
           ))
            ->add('twitter', 'checkbox', array(
                'label' => 'Twitter:',
                'required' => false,
            ))
            ->add('facebook', 'checkbox', array(
                'label' => 'Facebook:',
                'required' => false,
            ))
            ->add('printedMedia', 'checkbox', array(
                'label' => 'Printed Media:',
                'required' => false,
            ))
            ->add('wordOfMouth', 'checkbox', array(
                'label' => 'Word of Mouth:',
                'required' => false,
            ))
        ;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return 'Customer';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'SkedApp\CoreBundle\Entity\Customer',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SkedApp\CoreBundle\Entity\Customer',
        ));
    }

}
