<?php

namespace SkedApp\CoreBundle\DataFixtures\ORM ;

use SkedApp\CoreBundle\Entity\Role ;
use Doctrine\Common\Persistence\ObjectManager ;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface ;
use Doctrine\Common\DataFixtures\AbstractFixture ;

/**
 * Load default system user roles
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SkedAppCoreBundle
 * @subpackage DataFixtures
 * @version 0.0.1
 */
class LoadRoles extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $admin = new Role('ROLE_ADMIN') ;
        $manager->persist($admin) ;

        $report = new Role('ROLE_REPORT') ;
        $manager->persist($report) ;

        $members = new Role('ROLE_MEMBER_MANAGER') ;
        $manager->persist($members) ;

        $content = new Role('ROLE_CONTENT_MANAGER') ;
        $manager->persist($content) ;

        $manager->flush() ;

        $this->addReference('role-admin' , $admin) ;
        $this->addReference('role-member' , $members) ;
        $this->addReference('role-report' , $report) ;
        $this->addReference('role-content' , $content) ;

    }

    public function getOrder()
    {
        return 1 ;
    }

}
