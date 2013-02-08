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
        $admin->addRole($this->getReference('role-consultant-admin')) ;
        $admin->addRole($this->getReference('role-consultant-user')) ;
        $admin->addRole($this->getReference('role-site-user')) ;
        $manager->persist($admin) ;
        
        $consultantAdmin = new Group('Consultant administrator') ;
        $consultantAdmin->setDescription('Consultant administrator');
        $consultantAdmin->addRole($this->getReference('role-consultant-admin')) ;
        $manager->persist($consultantAdmin) ;
        
        $consultant = new Group('Consultant') ;
        $consultant->setDescription('Consultant user');
        $consultant->addRole($this->getReference('role-consultant-user')) ;
        $manager->persist($consultant) ;        
        
        $siteUser = new Group('Site user') ;
        $siteUser->setDescription('Site user user');
        $siteUser->addRole($this->getReference('role-site-user')) ;
        $manager->persist($siteUser) ;        
        
        $manager->flush() ;
        
        $this->addReference('group-super-admin' , $admin) ;
        $this->addReference('group-consultant-admin' , $consultantAdmin) ;
        $this->addReference('group-consultant' , $consultant) ;
    }

    public function getOrder()
    {
        return 2 ;
    }

}
