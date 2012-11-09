<?php

namespace SkedApp\CoreBundle\DataFixtures\ORM ;

use SkedApp\CoreBundle\Entity\Title ;
use Doctrine\Common\Persistence\ObjectManager ;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface ;
use Doctrine\Common\DataFixtures\AbstractFixture ;

/**
 * Load default system user titles
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SkedAppCoreBundle
 * @subpackage DataFixtures
 * @version 0.0.1
 */
class LoadTitles extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $mr = new Title('Mr') ;
        $manager->persist($mr) ;

        $mrs = new Title('Mrs') ;
        $manager->persist($mrs) ;

        $miss = new Title('Miss') ;
        $manager->persist($miss) ;

        $madam = new Title('Madam') ;
        $manager->persist($madam) ;

        $dr = new Title('Dr.') ;
        $manager->persist($dr) ;

        $prof = new Title('Prof.') ;
        $manager->persist($prof) ;

        $rev = new Title('Rev.') ;
        $manager->persist($rev) ;

        $manager->flush() ;

         $this->addReference('title-dr' , $dr) ;
         $this->addReference('title-madam' , $madam) ;
         $this->addReference('title-miss' , $miss) ;
         $this->addReference('title-mr' , $mr) ;
         $this->addReference('title-mrs' , $mrs) ;
         $this->addReference('title-prof' , $prof) ;
         $this->addReference('title-rev' , $rev) ;
    }

    public function getOrder()
    {
        return 1 ;
    }

}
