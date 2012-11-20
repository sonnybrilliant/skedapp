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
        $appDuration30 = new AppointmentDuration('1/2 hour');
        $appDuration30->setDuration(30);
        $manager->persist($appDuration30);

        $appDuration60 = new AppointmentDuration('1 hour');
        $appDuration60->setDuration(60);
        $manager->persist($appDuration60);

        $manager->flush();
        
         $this->addReference('service-30' , $appDuration30) ;
         $this->addReference('service-60' , $appDuration60) ;
    }

    public function getOrder()
    {
        return 1;
    }

}
