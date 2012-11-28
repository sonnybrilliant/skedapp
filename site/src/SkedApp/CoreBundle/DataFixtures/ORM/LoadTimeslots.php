<?php

namespace SkedApp\CoreBundle\DataFixtures\ORM;

use SkedApp\CoreBundle\Entity\Timeslots;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;

/**
 * Load default system timeslots
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SkedAppCoreBundle
 * @subpackage DataFixtures
 * @version 0.0.1
 */
class LoadTimeslots extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $slot1 = new Timeslots('00:00');
        $slot1->setWeight(1);
        $manager->persist($slot1);

        $slot2 = new Timeslots('01:00');
        $slot2->setWeight(2);
        $manager->persist($slot2);

        $slot3 = new Timeslots('02:00');
        $slot3->setWeight(3);
        $manager->persist($slot3);

        $slot4 = new Timeslots('03:00');
        $slot4->setWeight(4);
        $manager->persist($slot4);

        $slot5 = new Timeslots('04:00');
        $slot5->setWeight(5);
        $manager->persist($slot5);

        $slot6 = new Timeslots('05:00');
        $slot6->setWeight(6);
        $manager->persist($slot6);

        $slot7 = new Timeslots('06:00');
        $slot7->setWeight(7);
        $manager->persist($slot7);

        $slot8 = new Timeslots('07:00');
        $slot8->setWeight(8);
        $manager->persist($slot8);

        $slot9 = new Timeslots('08:00');
        $slot9->setWeight(9);
        $manager->persist($slot9);

        $slot10 = new Timeslots('09:00');
        $slot10->setWeight(10);
        $manager->persist($slot10);

        $slot11 = new Timeslots('10:00');
        $slot11->setWeight(11);
        $manager->persist($slot11);

        $slot12 = new Timeslots('11:00');
        $slot12->setWeight(12);
        $manager->persist($slot12);

        $slot13 = new Timeslots('12:00');
        $slot13->setWeight(13);
        $manager->persist($slot13);

        $slot14 = new Timeslots('13:00');
        $slot14->setWeight(14);
        $manager->persist($slot14);

        $slot15 = new Timeslots('14:00');
        $slot15->setWeight(15);
        $manager->persist($slot15);

        $slot16 = new Timeslots('15:00');
        $slot16->setWeight(16);
        $manager->persist($slot16);

        $slot17 = new Timeslots('16:00');
        $slot17->setWeight(17);
        $manager->persist($slot17);

        $slot18 = new Timeslots('17:00');
        $slot18->setWeight(18);
        $manager->persist($slot18);

        $slot19 = new Timeslots('18:00');
        $slot19->setWeight(19);
        $manager->persist($slot19);
        
        $slot20 = new Timeslots('19:00');
        $slot20->setWeight(20);
        $manager->persist($slot20);        

        $slot21 = new Timeslots('20:00');
        $slot21->setWeight(21);
        $manager->persist($slot21);        

        $slot22 = new Timeslots('21:00');
        $slot22->setWeight(22);
        $manager->persist($slot22);        

        $slot23 = new Timeslots('22:00');
        $slot23->setWeight(23);
        $manager->persist($slot23);        

        $slot24 = new Timeslots('23:00');
        $slot24->setWeight(24);
        $manager->persist($slot24);        
    
        $manager->flush();

    }

    public function getOrder()
    {
        return 1;
    }

}
