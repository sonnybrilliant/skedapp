<?php

namespace SkedApp\MemberBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * SkedApp\MemberBundle\Form\ResetPasswordType
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppMemberBundle
 * @subpackage Form
 * @version 0.0.1
 */
class ResetPasswordType extends AbstractType
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
            ->add('email', 'email', array(
                'label' => 'Email address:',
                'attr' => array('class' => 'span12')
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
        return 'ResetPassword';
    }

}