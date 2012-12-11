<?php

namespace SkedApp\CustomerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

/**
 * SkedApp\CustomerBundle\Form\CustomerCreateType
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppConsultantBundle
 * @subpackage Form
 * @version 0.0.1
 */
class CustomerCreateType extends AbstractType
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
            ->add('email', 'repeated', array(
                'type' => 'email',
                'first_name' => 'first',
                'first_options' => array('label' => 'Email address:'),
                'options' => array('attr' => array('class' => 'span4')),
                'second_name' => 'second',
                'second_options' => array('label' => 'Confirm Email address:'),
                'invalid_message' => 'Email addresses do not match',
            ))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'first_name' => 'first',
                'first_options' => array('label' => 'Password:'),
                'options' => array('attr' => array('class' => 'span4')),
                'second_name' => 'second',
                'second_options' => array('label' => 'Confirm Password:'),
                'invalid_message' => 'Passwords do not match',
            ))
            ->add('captcha', 'genemu_recaptcha')
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

?>
