<?php

namespace SkedApp\CompanyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;


/**
 * SkedApp\CompanyBundle\Form\CompanyCreateType
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCompanyBundle
 * @subpackage Form
 * @version 0.0.1
 */
class CompanyCreateType extends AbstractType
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
            ->add('name', 'text', array(
                'label' => 'Name:',
                'attr' => array('class' => 'span12')
            ))
            ->add('picture', 'file', array(
                'label' => 'Profile picture:',
                'attr' => array('class' => 'span12 width'),
                'required' => false,
            ))
            ->add('description', 'textarea', array(
                'label' => 'Description:',
                'attr' => array('class' => 'tinymce span12', 'data-theme' => 'simple'),
                'required' => false,
            ))
            ->add('address', null, array(
                'label' => 'Type your address:',
                'required'      => true,
                'attr' => array('class' => 'span12')
            ))
            ->add('locality', null, array(
                'required'      => false,
                'read_only'      => true,
                'attr' => array('class' => 'span12')
            ))
            ->add('country', null, array(
                'required'      => false,
                'read_only'      => true,
                'attr' => array('class' => 'span12')
            ))
            ->add('contactNumber', 'text', array(
                'label' => 'Contact number:',
                'attr' => array('class' => 'span12')
            ))
            ->add('lat', null, array(
                'required'      => false,
                'read_only'      => true,
                'attr' => array('class' => 'span12', 'value' => '-25.73134')
            ))
            ->add('lng', null, array(
                'required'      => false,
                'read_only'      => true,
                'attr' => array('class' => 'span12' , 'value' => '28.21837000000005')
            ))

        ;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return 'Company';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'SkedApp\CoreBundle\Entity\Company',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SkedApp\CoreBundle\Entity\Company',
        ));
    }

}

?>
