<?php

namespace SkedApp\CoreBundle\DataFixtures\ORM ;

use SkedApp\CoreBundle\Entity\Category ;
use Doctrine\Common\Persistence\ObjectManager ;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface ;
use Doctrine\Common\DataFixtures\AbstractFixture ;

/**
 * Load default system user categories
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SkedAppCoreBundle
 * @subpackage DataFixtures
 * @version 0.0.1
 */
class LoadCategory extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $category1 = new Category('Beauty');
        $manager->persist($category1) ;

        $category2 = new Category('Law');
        $manager->persist($category2) ;

        $category3 = new Category('Dentistry');
        $manager->persist($category3) ;

        $manager->flush() ;

         $this->addReference('category-beauty' , $category1) ;

    }

    public function getOrder()
    {
        return 1 ;
    }

}
