<?php

namespace SkedApp\CoreBundle\DataFixtures\ORM ;

use SkedApp\CoreBundle\Entity\DaysOfTheWeek ;
use Doctrine\Common\Persistence\ObjectManager ;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface ;
use Doctrine\Common\DataFixtures\AbstractFixture ;

/**
 * Load default system days of the week
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SkedAppCoreBundle
 * @subpackage DataFixtures
 * @version 0.0.1
 */
class LoadDaysOfTheWeek extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {

        $monday = new DaysOfTheWeek("Monday") ;
        $manager->persist($monday) ;
        
        $tuesday = new DaysOfTheWeek("Tuesday") ;
        $manager->persist($tuesday) ;
        
        $wednesday = new DaysOfTheWeek("Wednesday") ;
        $manager->persist($wednesday ) ;
        
        $thursday = new DaysOfTheWeek("Thursday") ;
        $manager->persist($thursday ) ;
        
        $friday = new DaysOfTheWeek("Friday") ;
        $manager->persist($friday) ;
        
        $saturday = new DaysOfTheWeek("Saturday") ;
        $manager->persist($saturday) ;
        
        $sunday = new DaysOfTheWeek("Sunday") ;
        $manager->persist($sunday) ;
        
        $manager->flush() ;

        
    }

    public function getOrder()
    {
        return 1 ;
    }

}
