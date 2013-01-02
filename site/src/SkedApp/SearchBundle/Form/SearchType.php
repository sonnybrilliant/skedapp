<?php

namespace SkedApp\SearchBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;


/**
 * SkedApp\ConsultantBundle\Form\SearchType
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppSearchBundle
 * @subpackage Form
 * @version 0.0.1
 */
class SearchType extends AbstractType
{

    /**
     *
     * @var Integer
     */
    private $categoryId = null;

    /**
     *
     * @var String
     */
    private $date = null;

    public function __construct($categoryId = 0, $date = '')
    {

        if (strlen($date) <= 0)
            $date = date('d-m-Y');

        $this->categoryId = $categoryId;
        $this->date = $date;
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

        $categoryId = $this->categoryId;
        $date = $this->date;

        $builder
            ->add('category', 'entity', array(
                'class' => 'SkedAppCoreBundle:Category',
                'empty_value' => 'Select a category',
                'label' => 'Category:',
                'required' => true,
                'attr' => array('class' => 'span4'),
            ))
            ->add('address', 'text', array(
                'label' => 'Type your current location:',
                'attr' => array('class' => 'span4')
            ))
            ->add('locality', 'hidden')
            ->add('country', 'hidden', array ('attr' => array ('value' => 'South Africa')))
            ->add('administrative_area_level_2', 'hidden')
            ->add('administrative_area_level_1', 'hidden')
            ->add('lat', 'hidden')
            ->add('lng', 'hidden')
            ->add('consultantServices', 'entity', array(
                'class' => 'SkedAppCoreBundle:Service',
                'label' => 'Services:',
                'multiple' => false,
                'required' => true,
                'attr' => array('class' => 'span4'),

            ))
            ->add('booking_date', 'text', array(
                'label' => 'Date:',
                'required' => true,
                'attr' => array('class' => 'span4', 'value' => $date),

            ))
            ->add('hidden_category', 'hidden', array('attr' => array('value' => $categoryId)))
        ;
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return 'Search';
    }

    public function getDefaultOptions(array $options)
    {

        $categoryId = $this->categoryId;
        $date = $this->date;

        return array(
            'category' => $categoryId,
            'booking_date' => $date,
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $categoryId = $this->categoryId;
        $date = $this->date;

        $resolver->setDefaults(array(
            'category' => $this->categoryId,
            'booking_date' => $date,
        ));
    }

}

?>
