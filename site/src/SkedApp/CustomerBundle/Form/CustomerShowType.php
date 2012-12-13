<?php

namespace SkedApp\CustomerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

/**
 * SkedApp\CustomerBundle\Form\CustomerShowType
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppConsultantBundle
 * @subpackage Form
 * @version 0.0.1
 */
class CustomerShowType extends AbstractType
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
                'attr' => array('class' => 'span4' , 'disabled' => 'disabled')
            ))
            ->add('lastName', 'text', array(
                'label' => 'Last name:',
                'attr' => array('class' => 'span4', 'disabled' => 'disabled')
            ))
            ->add('mobileNumber', 'text', array(
                'label' => 'Mobile number:',
                'attr' => array('class' => 'span4' , 'disabled' => 'disabled')
            ))
            ->add('email', 'text' ,array(
                'label' => 'Email address:',
                'attr' => array('class' => 'span4' , 'disabled' => 'disabled')
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

?>
