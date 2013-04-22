<?php

namespace SkedApp\ConsultantBundle\Form\EventListener;

use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;

/**
 * Add company field
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppConsultantBundle
 * @subpackage Form
 * @version 0.0.1
 */
class AddCompanyFieldSubscriber implements EventSubscriberInterface
{

    private $factory;
    private $container;

    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public static function getSubscribedEvents()
    {
        return array(FormEvents::POST_SET_DATA => 'preSetData');
    }

    public function preSetData(DataEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        // During form creation setData() is called with null as an argument
        // by the FormBuilder constructor. We're only concerned with when
        // setData is called with an actual Entity object in it (whether new,
        // or fetched with Doctrine). This if statement let's us skip right
        // over the null condition.
        if (null === $data) {
            return;
        }

        if (!$data->getId()) {
            if ($this->container->get('security.context')->isGranted('ROLE_ADMIN')) {
                $form->add($this->factory->createNamed(
                        'company', 'entity', null, array(
                        'class' => 'SkedAppCoreBundle:Company',
                        'attr' => array('class' => 'span12 chosen'),
                        'query_builder' => function(EntityRepository $er) {
                            return $er->createQueryBuilder('c')
                                    ->where('c.isDeleted = :status')
                                    ->setParameter('status', false);
                        },
                    )));
            }
        }
    }

}
