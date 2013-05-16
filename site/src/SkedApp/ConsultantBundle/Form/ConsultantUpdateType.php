<?php

namespace SkedApp\ConsultantBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

/**
 * SkedApp\ConsultantBundle\Form\ConsultantUpdateType
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppConsultantBundle
 * @subpackage Form
 * @version 0.0.1
 */
class ConsultantUpdateType extends AbstractType
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
                'attr' => array('class' => 'span5')
            ))
            ->add('lastName', 'text', array(
                'label' => 'Last name:',
                'attr' => array('class' => 'span5')
            ))
            ->add('gender', 'entity', array(
                'class' => 'SkedAppCoreBundle:gender',
                'label' => 'Gender:',
                'attr' => array('class' => 'span5 chosen')
            ))
            ->add('picture', 'file', array(
                'label' => 'Profile picture:',
                'required' => false,
                'attr' => array('class' => 'span5')
            ))
            ->add('email', 'email', array(
                'label' => 'Email address:',
                'attr' => array('class' => 'span5')                
            ))            
             ->add('category', 'entity', array(
                'class' => 'SkedAppCoreBundle:Category',
                'label' => 'Category:',
                'attr' => array('class' => 'span5 chosen'),
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.isDeleted = :status')
                        ->setParameter('status', false)
                        ->orderBy('c.name');
                },
            ))
            ->add('consultantServices', 'entity', array(
                'class' => 'SkedAppCoreBundle:Service',
                'label' => 'Services:',
                'multiple' => true,
                'required' => false,
                'attr' => array('class' => 'span5' ),
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->where('s.isDeleted = :status')
                        ->setParameter('status', false)
                        ->orderBy('s.name');
                },
            ))
            ->add('speciality', 'textarea', array(
                'label' => 'Speciality:',
                'required' => false,
                'attr' => array('class' => 'tinymce span5', 'data-theme' => 'simple')
            ))
            ->add('professionalStatement', 'textarea', array(
                'label' => 'Professional Statement:',
                'required' => false,
                'attr' => array('class' => 'tinymce span5', 'data-theme' => 'simple')
            ))
            ->add('monday', 'checkbox', array(
                'label' => 'M'> false,
                'required'  => false,
            ))
            ->add('tuesday', 'checkbox', array(
                'label' => 'T',
                'required'  => false,
            ))
            ->add('wednesday', 'checkbox', array(
                'label' => 'W',
                'required'  => false,
            ))
            ->add('thursday', 'checkbox', array(
                'label' => 'T',
                'required'  => false,
            ))
            ->add('friday', 'checkbox', array(
                'label' => 'F',
                'required'  => false,
            ))
            ->add('saturday', 'checkbox', array(
                'label' => 'S',
                'required'  => false,
            ))
            ->add('sunday', 'checkbox', array(
                'label' => 'S',
                'required'  => false,
            ))
            ->add('appointmentDuration', 'entity', array(
                'class' => 'SkedAppCoreBundle:AppointmentDuration',
                'label' => 'Session length:',
                'attr' => array('class' => 'span5')
            ))
            ->add('currentId', 'hidden')


        ;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return 'Consultant';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'SkedApp\CoreBundle\Entity\Consultant',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SkedApp\CoreBundle\Entity\Consultant',
        ));
    }

}

?>
