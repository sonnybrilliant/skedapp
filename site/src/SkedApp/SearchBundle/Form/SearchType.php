<?php

namespace SkedApp\SearchBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

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
    private $category = null;

    /**
     *
     * @var Integer
     */
    private $service = null;

    /**
     *
     * @var String
     */
    private $date = null;

    public function __construct($category = null, $date = '', $service = null)
    {

        if (strlen($date) <= 0)
            $date = date('d-m-Y');

        $this->category = $category;
        $this->service = $service;
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

        $category = $this->category;
        $service = $this->service;
        $date = $this->date;

        $builder
            ->add('category', 'entity', array(
                'class' => 'SkedAppCoreBundle:Category',
                'empty_value' => 'Select a category',
                'label' => 'Category:',
                'required' => true,
                'attr' => array('class' => 'span12'),
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.isDeleted = :status')
                        ->setParameter('status', false);
                },
            ))
            ->add('address', 'text', array(
                'label' => 'Type your current location:',
                'attr' => array('class' => 'span12')
            ))
            ->add('locality', 'hidden')
            ->add('country', 'hidden', array ('attr' => array ('value' => 'South Africa')))
            ->add('administrative_area_level_2', 'hidden')
            ->add('administrative_area_level_1', 'hidden')
            ->add('lat', 'hidden')
            ->add('lng', 'hidden')
            ->add('consultantServices', 'entity', array(
                'class' => 'SkedAppCoreBundle:Service',
                'empty_value' => '',
                'label' => 'Services:',
                'multiple' => false,
                'required' => true,
                'attr' => array('class' => 'span12'),

            ))
            ->add('booking_date', 'text', array(
                'label' => 'Date:',
                'required' => true,
                'attr' => array('class' => 'span12', 'value' => $date),

            ))
            ->add('hidden_category', 'hidden', array('attr' => array('value' => $category)))
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

        $category = $this->category;
        $date = $this->date;
        $service = $this->service;

        return array(
            'category' => $category,
            'booking_date' => $date,
            'consultantServices' => $service,
        );
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

        $category = $this->category;
        $date = $this->date;
        $service = $this->service;

        $resolver->setDefaults(array(
            'category' => $category,
            'booking_date' => $date,
            'consultantServices' => $service,
        ));
    }

}

?>
