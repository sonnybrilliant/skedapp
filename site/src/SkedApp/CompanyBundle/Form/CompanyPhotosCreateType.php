<?php

namespace SkedApp\CompanyBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;


/**
 * SkedApp\CompanyBundle\Form\CompanyPhotosCreateType
 *
 * @author Otto Saayman <otto.saayman@kaizania.co.za>
 * @package SkedAppCompanyBundle
 * @subpackage Form
 * @version 0.0.1
 */
class CompanyPhotosCreateType extends AbstractType
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
            ->add('caption', 'text', array(
                'label' => 'Caption:',
                'attr' => array('class' => 'span4')
            ))
            ->add('picture', 'file', array(
                'label' => 'Upload Photo:',
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
        return 'CompanyPhotos';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'SkedApp\CoreBundle\Entity\CompanyPhotos',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SkedApp\CoreBundle\Entity\CompanyPhotos',
        ));
    }

}

?>
