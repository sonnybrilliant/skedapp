<?php

namespace SkedApp\MemberBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * SkedApp\MemberBundle\Form\PasswordUpdateType
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppMemberBundle
 * @subpackage Form
 * @version 0.0.1
 */
class PasswordUpdateType extends AbstractType
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
        $builder->add('password' , 'repeated' , array (
          'attr'=> array('placeholder' => 'password'),
          'type' => 'password' ,
          'first_name' => 'first' ,
          'first_options'  => array('label' => 'New password:', 'attr' => array('class'=>'span12')),
          'second_name' => 'second' ,
          'second_options' => array('label' => 'Confirm new password:' ,'attr' => array('class'=>'span12')),
          'invalid_message' =>'Passwords do not match' ,
        ));
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