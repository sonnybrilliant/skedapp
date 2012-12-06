<?php

namespace SkedApp\ServiceBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

/**
 * SkedApp\ServiceBundle\Form\ServiceCreateType
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppConsultantBundle
 * @subpackage Form
 * @version 0.0.1
 */
class ServiceCreateType extends AbstractType
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
                'attr' => array('class' => 'span4')
            ))
            ->add('category', 'entity', array(
                'class' => 'SkedAppCoreBundle:Category',
                'label' => 'Category:',
                'empty_value' => 'Select a category',
                'attr' => array('class' => 'span4 chosen'),
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.isDeleted = :status')
                        ->setParameter('status', false);
                },
            ))
            ->add('appointmentDuration', 'entity', array(
                'class' => 'SkedAppCoreBundle:AppointmentDuration',
                'label' => 'Length:',
                'attr' => array('class' => 'span4 chosen')
            ))
            ->add('description', 'textarea', array(
                'label' => 'Description:',
                'attr' => array('class' => 'tinymce span4', 'data-theme' => 'simple')
            ))

        ;
    }

    /**
     * Get name
     * @return string 
     */
    public function getName()
    {
        return 'Service';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'SkedApp\CoreBundle\Entity\Service',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SkedApp\CoreBundle\Entity\Service',
        ));
    }

}

?>
