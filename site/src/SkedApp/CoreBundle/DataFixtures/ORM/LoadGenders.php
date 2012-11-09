<?php

namespace SkedApp\CoreBundle\DataFixtures\ORM ;

use SkedApp\CoreBundle\Entity\Gender ;
use Doctrine\Common\Persistence\ObjectManager ;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface ;
use Doctrine\Common\DataFixtures\AbstractFixture ;

/**
 * Load default system user genders
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SkedAppCoreBundle
 * @subpackage DataFixtures
 * @version 0.0.1
 */
class LoadGenders extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $female = new Gender('Female') ;
        $manager->persist($female) ;

        $male = new Gender('Male') ;
        $manager->persist($male) ;

        $other = new Gender('Other') ;
        $manager->persist($other) ;

        $manager->flush() ;

        $this->addReference('gender-female' , $female) ;
        $this->addReference('gender-male' , $male) ;
    }

    public function getOrder()
    {
        return 1 ;
    }

}
