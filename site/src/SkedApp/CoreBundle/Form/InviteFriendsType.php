<?php

namespace SkedApp\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * SkedApp\CoreBundle\Form\InviteFriendsType
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCoreBundle
 * @subpackage Form
 * @version 0.0.1
 */
class InviteFriendsType extends AbstractType
{

    private $isAuthenticated = false;
    
    public function __construct($isAuthenticated = true)
    {
        $this->isAuthenticated = $isAuthenticated;
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
            ->add('friendName', 'text', array(
                'label' => "Friend's name:",
                'attr' => array('class' => 'span12')
            ))
            ->add('email', 'email', array(
                'label' => "Friend's email address:",
                'attr' => array('class' => 'span12'),
            ));
        
    }

    /**
     * Get name
     * @return string
     */
    public function getName()
    {
        return 'InviteFriend';
    }

}

?>
