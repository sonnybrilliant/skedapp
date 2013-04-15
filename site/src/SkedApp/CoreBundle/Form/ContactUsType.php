<?php

namespace SkedApp\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * SkedApp\CoreBundle\Form\ContactUsType
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCoreBundle
 * @subpackage Form
 * @version 0.0.1
 */
class ContactUsType extends AbstractType
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
            ->add('fullName', 'text', array(
                'label' => "Full name:",
                'required' => true,
                'attr' => array('class' => 'span4')
            ))
            ->add('emailaddress', 'text', array(
                'label' => "Email address:",
                'required' => true,
                'attr' => array('class' => 'span4')
            ))
            ->add('message', 'textarea', array(
                'label' => "Message:",
                'attr' => array('class'=>'tinymce span4', 'data-theme' => 'simple'),
            ));
        
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return 'ContactUs';
    }

}

?>
