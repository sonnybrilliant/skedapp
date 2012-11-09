<?php

namespace SkedApp\CoreBundle\DataFixtures\ORM ;

use SkedApp\CoreBundle\Entity\Group ;
use Doctrine\Common\Persistence\ObjectManager ;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface ;
use Doctrine\Common\DataFixtures\AbstractFixture ;

/**
 * Load default system user groups
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SkedAppCoreBundle
 * @subpackage DataFixtures
 * @version 0.0.1
 */
class LoadGroups extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {

        $admin = new Group('Administrator') ;
        $admin->setDescription('Super user, has access to everything, please do not grant this role to every one - sensitive data will be compromised');
        $admin->addRole($this->getReference('role-admin')) ;
        $admin->addRole($this->getReference('role-member')) ;
        $admin->addRole($this->getReference('role-report')) ;
        $admin->addRole($this->getReference('role-content')) ;
        $manager->persist($admin) ;
        
        $manager->flush() ;
        
        $this->addReference('group-admin' , $admin) ;
    }

    public function getOrder()
    {
        return 2 ;
    }

}
