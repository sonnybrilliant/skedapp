<?php

namespace SkedApp\CoreBundle\DataFixtures\ORM;

use SkedApp\CoreBundle\Entity\Service;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;

/**
 * Load default system user services
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SkedAppCoreBundle
 * @subpackage DataFixtures
 * @version 0.0.1
 */
class LoadService extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $service1 = new Service('Mens Cut');
        $service1->setCategory($this->getReference('category-hair'));
        $service1->setAppointmentDuration($this->getReference('service-30'));
        $manager->persist($service1);

        $service2 = new Service('Womans Cut');
        $service2->setCategory($this->getReference('category-hair'));
        $service2->setAppointmentDuration($this->getReference('service-60'));
        $manager->persist($service2);

        $service3 = new Service('Blow wave');
        $service3->setCategory($this->getReference('category-hair'));
        $service3->setAppointmentDuration($this->getReference('service-60'));
        $manager->persist($service3);

        $service4 = new Service('Facial');
        $service4->setCategory($this->getReference('category-beauty'));
        $service4->setAppointmentDuration($this->getReference('service-60'));
        $manager->persist($service4);

        $service5 = new Service('Pedicure');
        $service5->setCategory($this->getReference('category-beauty'));
        $service5->setAppointmentDuration($this->getReference('service-60'));
        $manager->persist($service5);

        $service6 = new Service('Chiropractor');
        $service6->setCategory($this->getReference('category-health'));
        $service6->setAppointmentDuration($this->getReference('service-60'));
        $manager->persist($service6);

        $service7 = new Service('Physio');
        $service7->setCategory($this->getReference('category-health'));
        $service7->setAppointmentDuration($this->getReference('service-60'));
        $manager->persist($service7);

        $service8 = new Service('Family law');
        $service8->setCategory($this->getReference('category-law'));
        $service8->setAppointmentDuration($this->getReference('service-60'));
        $manager->persist($service8);

        $service9 = new Service('Corporate law');
        $service9->setCategory($this->getReference('category-law'));
        $service9->setAppointmentDuration($this->getReference('service-60'));
        $manager->persist($service9);

        $manager->flush();

        $this->addReference('service-womans-cut' , $service2);

    }

    public function getOrder()
    {
        return 2;
    }

}
