<?php

namespace SkedApp\MemberBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

/**
 * SkedApp\MemberBundle\Form\MemberCreateType
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppMemberBundle
 * @subpackage Form
 * @version 0.0.1
 */
class MemberCreateType extends AbstractType
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
                'attr' => array('class' => 'span12')
            ))
            ->add('lastName', 'text', array(
                'label' => 'Last name:',
                'attr' => array('class' => 'span12')
            ))
            ->add('mobileNumber', 'text', array(
                'label' => 'Cellphone:',
                'attr' => array('class' => 'span12')
            ))
            ->add('email', 'repeated', array(
                'type' => 'email',
                'first_name' => 'first',
                'first_options'  => array('label' => 'Email address:'),
                'options' => array('attr' => array('class' => 'span12')),
                'second_name' => 'second',
                'second_options' => array('label' => 'Confirm Email address:'),
                'invalid_message' => 'Email addresses do not match',

            ))
            ->add('password', 'repeated', array(
                'type' => 'password',
                'first_name' => 'first',
                'first_options'  => array('label' => 'Password:'),
                'options' => array('attr' => array('class' => 'span12')),
                'second_name' => 'second',
                'second_options' => array('label' => 'Confirm password:'),
                'invalid_message' => 'passwords do not match',

            ))
            ->add('company', 'entity', array(
                'class' => 'SkedAppCoreBundle:Company',
                'label' => 'Service provider:',
                'attr' => array('class' => 'span12 chosen'),
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.isDeleted = :status')
                        ->setParameter('status', false);
                },
            ))
            ->add('gender', 'entity', array(
                'class' => 'SkedAppCoreBundle:Gender',
                'label' => 'Gender:',
                'attr' => array('class' => 'span12 chosen')
            ))
            ->add('group', 'entity', array(
                'class' => 'SkedAppCoreBundle:Group',
                'label' => 'Role:',
                'attr' => array('class' => 'span12 chosen'),
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('g')
                        ->where('g.isAdmin = :status')
                        ->setParameter('status', true);
                },    
            ))
            ->add('title', 'entity', array(
                'class' => 'SkedAppCoreBundle:Title',
                'label' => 'Title:',
                'attr' => array('class' => 'span12 chosen')
            ))
        ;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return 'Member';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'SkedApp\CoreBundle\Entity\Member',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SkedApp\CoreBundle\Entity\Member',
        ));
    }

}

?>
