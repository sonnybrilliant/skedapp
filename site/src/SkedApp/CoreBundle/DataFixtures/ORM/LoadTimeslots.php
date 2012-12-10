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

        $slot115 = new Timeslots('00:15');
        $slot115->setWeight(1);
        $manager->persist($slot115);

        $slot130 = new Timeslots('00:30');
        $slot130->setWeight(1);
        $manager->persist($slot130);

        $slot145 = new Timeslots('00:45');
        $slot145->setWeight(1);
        $manager->persist($slot145);

        $slot2 = new Timeslots('01:00');
        $slot2->setWeight(2);
        $manager->persist($slot2);

        $slot215 = new Timeslots('01:15');
        $slot215->setWeight(1);
        $manager->persist($slot215);

        $slot230 = new Timeslots('01:30');
        $slot230->setWeight(1);
        $manager->persist($slot230);

        $slot245 = new Timeslots('01:45');
        $slot245->setWeight(1);
        $manager->persist($slot245);

        $slot3 = new Timeslots('02:00');
        $slot3->setWeight(3);
        $manager->persist($slot3);

        $slot315 = new Timeslots('02:15');
        $slot315->setWeight(1);
        $manager->persist($slot315);

        $slot330 = new Timeslots('02:30');
        $slot330->setWeight(1);
        $manager->persist($slot330);

        $slot345 = new Timeslots('02:45');
        $slot345->setWeight(1);
        $manager->persist($slot345);

        $slot4 = new Timeslots('03:00');
        $slot4->setWeight(4);
        $manager->persist($slot4);

        $slot415 = new Timeslots('03:15');
        $slot415->setWeight(1);
        $manager->persist($slot415);

        $slot430 = new Timeslots('03:30');
        $slot430->setWeight(1);
        $manager->persist($slot430);

        $slot445 = new Timeslots('03:45');
        $slot445->setWeight(1);
        $manager->persist($slot445);

        $slot5 = new Timeslots('04:00');
        $slot5->setWeight(5);
        $manager->persist($slot5);

        $slot515 = new Timeslots('04:15');
        $slot515->setWeight(1);
        $manager->persist($slot515);

        $slot530 = new Timeslots('04:30');
        $slot530->setWeight(1);
        $manager->persist($slot530);

        $slot545 = new Timeslots('04:45');
        $slot545->setWeight(1);
        $manager->persist($slot545);

        $slot6 = new Timeslots('05:00');
        $slot6->setWeight(6);
        $manager->persist($slot6);

        $slot615 = new Timeslots('05:15');
        $slot615->setWeight(1);
        $manager->persist($slot615);

        $slot630 = new Timeslots('05:30');
        $slot630->setWeight(1);
        $manager->persist($slot630);

        $slot645 = new Timeslots('05:45');
        $slot645->setWeight(1);
        $manager->persist($slot645);

        $slot7 = new Timeslots('06:00');
        $slot7->setWeight(7);
        $manager->persist($slot7);

        $slot715 = new Timeslots('06:15');
        $slot715->setWeight(1);
        $manager->persist($slot715);

        $slot730 = new Timeslots('06:30');
        $slot730->setWeight(1);
        $manager->persist($slot730);

        $slot745 = new Timeslots('06:45');
        $slot745->setWeight(1);
        $manager->persist($slot745);

        $slot8 = new Timeslots('07:00');
        $slot8->setWeight(8);
        $manager->persist($slot8);

        $slot815 = new Timeslots('07:15');
        $slot815->setWeight(1);
        $manager->persist($slot815);

        $slot830 = new Timeslots('07:30');
        $slot830->setWeight(1);
        $manager->persist($slot830);

        $slot845 = new Timeslots('07:45');
        $slot845->setWeight(1);
        $manager->persist($slot845);

        $slot9 = new Timeslots('08:00');
        $slot9->setWeight(9);
        $manager->persist($slot9);

        $slot915 = new Timeslots('08:15');
        $slot915->setWeight(1);
        $manager->persist($slot915);

        $slot930 = new Timeslots('08:30');
        $slot930->setWeight(1);
        $manager->persist($slot930);

        $slot945 = new Timeslots('08:45');
        $slot945->setWeight(1);
        $manager->persist($slot945);

        $slot10 = new Timeslots('09:00');
        $slot10->setWeight(10);
        $manager->persist($slot10);

        $slot1015 = new Timeslots('09:15');
        $slot1015->setWeight(1);
        $manager->persist($slot1015);

        $slot1030 = new Timeslots('09:30');
        $slot1030->setWeight(1);
        $manager->persist($slot1030);

        $slot1045 = new Timeslots('09:45');
        $slot1045->setWeight(1);
        $manager->persist($slot1045);

        $slot11 = new Timeslots('10:00');
        $slot11->setWeight(11);
        $manager->persist($slot11);

        $slot1115 = new Timeslots('10:15');
        $slot1115->setWeight(1);
        $manager->persist($slot1115);

        $slot1130 = new Timeslots('10:30');
        $slot1130->setWeight(1);
        $manager->persist($slot1130);

        $slot1145 = new Timeslots('10:45');
        $slot1145->setWeight(1);
        $manager->persist($slot1145);

        $slot12 = new Timeslots('11:00');
        $slot12->setWeight(12);
        $manager->persist($slot12);

        $slot1215 = new Timeslots('11:15');
        $slot1215->setWeight(1);
        $manager->persist($slot1215);

        $slot1230 = new Timeslots('11:30');
        $slot1230->setWeight(1);
        $manager->persist($slot1230);

        $slot1245 = new Timeslots('11:45');
        $slot1245->setWeight(1);
        $manager->persist($slot1245);

        $slot13 = new Timeslots('12:00');
        $slot13->setWeight(13);
        $manager->persist($slot13);

        $slot1315 = new Timeslots('12:15');
        $slot1315->setWeight(1);
        $manager->persist($slot1315);

        $slot1330 = new Timeslots('12:30');
        $slot1330->setWeight(1);
        $manager->persist($slot1330);

        $slot1345 = new Timeslots('12:45');
        $slot1345->setWeight(1);
        $manager->persist($slot1345);

        $slot14 = new Timeslots('13:00');
        $slot14->setWeight(14);
        $manager->persist($slot14);

        $slot1415 = new Timeslots('13:15');
        $slot1415->setWeight(1);
        $manager->persist($slot1415);

        $slot1430 = new Timeslots('13:30');
        $slot1430->setWeight(1);
        $manager->persist($slot1430);

        $slot1445 = new Timeslots('13:45');
        $slot1445->setWeight(1);
        $manager->persist($slot1445);

        $slot15 = new Timeslots('14:00');
        $slot15->setWeight(15);
        $manager->persist($slot15);

        $slot1515 = new Timeslots('14:15');
        $slot1515->setWeight(1);
        $manager->persist($slot1515);

        $slot1530 = new Timeslots('14:30');
        $slot1530->setWeight(1);
        $manager->persist($slot1530);

        $slot1545 = new Timeslots('14:45');
        $slot1545->setWeight(1);
        $manager->persist($slot1545);

        $slot16 = new Timeslots('15:00');
        $slot16->setWeight(16);
        $manager->persist($slot16);

        $slot1615 = new Timeslots('15:15');
        $slot1615->setWeight(1);
        $manager->persist($slot1615);

        $slot1630 = new Timeslots('15:30');
        $slot1630->setWeight(1);
        $manager->persist($slot1630);

        $slot1645 = new Timeslots('15:45');
        $slot1645->setWeight(1);
        $manager->persist($slot1645);

        $slot17 = new Timeslots('16:00');
        $slot17->setWeight(17);
        $manager->persist($slot17);

        $slot1715 = new Timeslots('16:15');
        $slot1715->setWeight(1);
        $manager->persist($slot1715);

        $slot1730 = new Timeslots('16:30');
        $slot1730->setWeight(1);
        $manager->persist($slot1730);

        $slot1745 = new Timeslots('16:45');
        $slot1745->setWeight(1);
        $manager->persist($slot1745);

        $slot18 = new Timeslots('17:00');
        $slot18->setWeight(18);
        $manager->persist($slot18);

        $slot1815 = new Timeslots('17:15');
        $slot1815->setWeight(1);
        $manager->persist($slot1815);

        $slot1830 = new Timeslots('17:30');
        $slot1830->setWeight(1);
        $manager->persist($slot1830);

        $slot1845 = new Timeslots('17:45');
        $slot1845->setWeight(1);
        $manager->persist($slot1845);

        $slot19 = new Timeslots('18:00');
        $slot19->setWeight(19);
        $manager->persist($slot19);

        $slot1915 = new Timeslots('18:15');
        $slot1915->setWeight(1);
        $manager->persist($slot1915);

        $slot1930 = new Timeslots('18:30');
        $slot1930->setWeight(1);
        $manager->persist($slot1930);

        $slot1945 = new Timeslots('18:45');
        $slot1945->setWeight(1);
        $manager->persist($slot1945);

        $slot20 = new Timeslots('19:00');
        $slot20->setWeight(20);
        $manager->persist($slot20);

        $slot2015 = new Timeslots('19:15');
        $slot2015->setWeight(1);
        $manager->persist($slot2015);

        $slot2030 = new Timeslots('19:30');
        $slot2030->setWeight(1);
        $manager->persist($slot2030);

        $slot2045 = new Timeslots('19:45');
        $slot2045->setWeight(1);
        $manager->persist($slot2045);

        $slot21 = new Timeslots('20:00');
        $slot21->setWeight(21);
        $manager->persist($slot21);

        $slot2115 = new Timeslots('20:15');
        $slot2115->setWeight(1);
        $manager->persist($slot2115);

        $slot2130 = new Timeslots('20:30');
        $slot2130->setWeight(1);
        $manager->persist($slot2130);

        $slot2145 = new Timeslots('20:45');
        $slot2145->setWeight(1);
        $manager->persist($slot2145);

        $slot22 = new Timeslots('21:00');
        $slot22->setWeight(22);
        $manager->persist($slot22);

        $slot2215 = new Timeslots('21:15');
        $slot2215->setWeight(1);
        $manager->persist($slot2215);

        $slot2230 = new Timeslots('21:30');
        $slot2230->setWeight(1);
        $manager->persist($slot2230);

        $slot2245 = new Timeslots('21:45');
        $slot2245->setWeight(1);
        $manager->persist($slot2245);

        $slot23 = new Timeslots('22:00');
        $slot23->setWeight(23);
        $manager->persist($slot23);

        $slot2315 = new Timeslots('22:15');
        $slot2315->setWeight(1);
        $manager->persist($slot2315);

        $slot2330 = new Timeslots('22:30');
        $slot2330->setWeight(1);
        $manager->persist($slot2330);

        $slot2345 = new Timeslots('22:45');
        $slot2345->setWeight(1);
        $manager->persist($slot2345);

        $slot24 = new Timeslots('23:00');
        $slot24->setWeight(24);
        $manager->persist($slot24);

        $slot2415 = new Timeslots('23:15');
        $slot2415->setWeight(1);
        $manager->persist($slot2415);

        $slot2430 = new Timeslots('23:30');
        $slot2430->setWeight(1);
        $manager->persist($slot2430);

        $slot2445 = new Timeslots('23:45');
        $slot2445->setWeight(1);
        $manager->persist($slot2445);

        $manager->flush();

        $this->addReference('time-slot-8-00' , $slot9) ;
        $this->addReference('time-slot-17-00' , $slot18) ;
    }

    public function getOrder()
    {
        return 1;
    }

}
