<?php

namespace SkedApp\CustomerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

/**
 * SkedApp\CustomerBundle\Form\CustomerCreateType
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppConsultantBundle
 * @subpackage Form
 * @version 0.0.1
 */
class CustomerPotentialType extends AbstractType
{

    private $firstNameRequired;

    public function __construct($firstNameRequired = true)
    {
        $this->firstNameRequired = $firstNameRequired;
    }

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
                'required' => $this->firstNameRequired,
                'attr' => array('class' => 'span12')
            ))
            ->add('lastName', 'text', array(
                'label' => 'Last name:',
                'required' => false,
                'attr' => array('class' => 'span12')
            ))
            ->add('mobileNumber', 'text', array(
                'label' => 'Mobile number:',
                'required' => false,
                'attr' => array('class' => 'span12')
            ))
            ->add('landLineNumber', 'text', array(
                'label' => 'Land line number:',
                'required' => false,
                'attr' => array('class' => 'span12')
            ))
            ->add('email', 'text', array(
                'label' => 'E-Mail:',
                'required' => false,
                'attr' => array('class' => 'span12')
            ))
            ->add('gender', 'entity', array(
                'class' => 'SkedAppCoreBundle:Gender',
                'label' => 'Gender:',
                'required' => false,
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
        return 'CustomerPotential';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'SkedApp\CoreBundle\Entity\CustomerPotential',
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SkedApp\CoreBundle\Entity\CustomerPotential',
        ));
    }

}

?>
