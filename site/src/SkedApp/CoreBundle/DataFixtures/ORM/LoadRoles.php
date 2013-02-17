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

        $consultantAdmin = new Role('ROLE_CONSULTANT_ADMIN') ;
        $manager->persist($consultantAdmin) ;

        $consultantUser = new Role('ROLE_CONSULTANT_USER') ;
        $manager->persist($consultantUser) ;

        $siteUser = new Role('ROLE_SITE_USER') ;
        $manager->persist($siteUser) ;

       

        $manager->flush() ;

        $this->addReference('role-admin' , $admin) ;
        $this->addReference('role-consultant-admin' , $consultantAdmin) ;
        $this->addReference('role-consultant-user' , $consultantUser) ;
        $this->addReference('role-site-user' , $siteUser) ;

    }

    public function getOrder()
    {
        return 1 ;
    }

}
