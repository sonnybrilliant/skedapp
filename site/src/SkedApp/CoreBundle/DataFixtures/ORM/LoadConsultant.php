<?php

namespace SkedApp\CoreBundle\DataFixtures\ORM ;

use SkedApp\CoreBundle\Entity\Consultant ;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager ;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface ;
use Doctrine\Common\DataFixtures\AbstractFixture ;

/**
 * Load default system default company
 *
 * @author Otto Saayman <otto.saayman@kaizania.co.za>
 * @package SkedAppCoreBundle
 * @subpackage DataFixtures
 * @version 0.0.1
 */
class LoadConsultant extends AbstractFixture implements OrderedFixtureInterface , ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load( ObjectManager $manager )
    {

        $consultantManagerService = $this->container->get('consultant.manager');

        $arrConsultant = array(
                'gender' => $this->getReference('gender-female'),
                'company' => $this->getReference('company-sonny'),
                'startTimeSlot' => $this->getReference('time-slot-8-00'),
                'endTimeSlot' => $this->getReference('time-slot-17-00'),
                'appointmentDuration' => $this->getReference('service-30'),
                'firstName' => 'Lisa',
                'lastName' => 'Joy',
                'email' => 'qa2@creativecloud.co.za',
                'username' => 'qa2@creativecloud.co.za',
                'password' => '654321',
                'enabled' => true,
                'expired' => false,
                'isActive' => true,
                'isDeleted' => false,
                'monday' => false,
                'tuesday' => true,
                'wednesday' => true,
                'thursday' => true,
                'friday' => true,
                'saturday' => true,
                'sunday' => false,
                'sunday' => false,
                'consultantService' => $this->getReference('service-womans-cut'),
                'consultantRoleUser' => $this->getReference('role-consultant-user'),
            );

        $consultant = $consultantManagerService->createDefaultConsultant($arrConsultant);

        $this->addReference('consultant-default' , $consultant) ;
    }

    public function getOrder()
    {
        return 4 ;
    }

}
