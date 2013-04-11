<?php

namespace SkedApp\CoreBundle\DataFixtures\ORM ;

use SkedApp\CoreBundle\Entity\BookingStatus ;
use Doctrine\Common\Persistence\ObjectManager ;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface ;
use Doctrine\Common\DataFixtures\AbstractFixture ;

/**
 * Load default system status
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SkedAppCoreBundle
 * @subpackage DataFixtures
 * @version 0.0.1
 */
class LoadBookingStatus extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {

        //create statuses
        $show = new BookingStatus() ;
        $show->setName('Client showed up') ;
        $manager->persist($show) ;
        
        $noShow = new BookingStatus() ;
        $noShow->setName('Client did not show') ;
        $manager->persist($noShow) ;
        
        $cancelled = new BookingStatus() ;
        $cancelled->setName('Client cancelled') ;
        $manager->persist($cancelled) ;

        $manager->flush() ;
    }

    public function getOrder()
    {
        return 1 ;
    }

}
