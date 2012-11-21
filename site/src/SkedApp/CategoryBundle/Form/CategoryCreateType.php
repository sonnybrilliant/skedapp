<?php

namespace SkedApp\CategoryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * SkedApp\CategoryBundle\Form\ServiceCreateType
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCategoryBundle
 * @subpackage Form
 * @version 0.0.1
 */
class CategoryCreateType extends AbstractType
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
            ->add('description', 'textarea', array(
                'label' => 'Description:',
                'attr' => array('class' => 'tinymce span4', 'data-theme' => 'simple')
            ))
            ->add('picture', 'file', array(
                'label' => 'Category image:',
                'attr' => array('class' => 'span4')
            ))

        ;
    }

    /**
     * Get name
     * @return string 
     */
    public function getName()
    {
        return 'Category';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'SkedApp\CoreBundle\Entity\Category',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SkedApp\CoreBundle\Entity\Category',
        ));
    }

}

?>
