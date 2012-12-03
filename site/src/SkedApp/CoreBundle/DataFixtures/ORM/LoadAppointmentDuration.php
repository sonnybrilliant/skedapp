<?php

namespace SkedApp\CoreBundle\DataFixtures\ORM;

use SkedApp\CoreBundle\Entity\AppointmentDuration;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;

/**
 * Load default system appoointment duration
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SkedAppCoreBundle
 * @subpackage DataFixtures
 * @version 0.0.1
 */
class LoadAppointmentDuration extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $appDuration15 = new AppointmentDuration('15 mins');
        $appDuration15->setDuration(15);
        $manager->persist($appDuration15);

        $appDuration30 = new AppointmentDuration('30 mins');
        $appDuration30->setDuration(30);
        $manager->persist($appDuration30);

        $appDuration45 = new AppointmentDuration('45 mins');
        $appDuration45->setDuration(45);
        $manager->persist($appDuration45);

        $appDuration60 = new AppointmentDuration('1 hour');
        $appDuration60->setDuration(60);
        $manager->persist($appDuration60);

        $manager->flush();
        
         $this->addReference('service-15' , $appDuration15) ;
         $this->addReference('service-30' , $appDuration30) ;
         $this->addReference('service-45' , $appDuration45) ;
         $this->addReference('service-60' , $appDuration60) ;
    }

    public function getOrder()
    {
        return 1;
    }

}
