<?php

namespace SkedApp\CoreBundle\DataFixtures\ORM ;

use SkedApp\CoreBundle\Entity\Member ;
use Doctrine\Common\Persistence\ObjectManager ;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface ;
use Doctrine\Common\DataFixtures\AbstractFixture ;

/**
 * Load default system users
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SkedAppCoreBundle
 * @subpackage DataFixtures
 * @version 0.0.1
 */
class LoadMembers extends AbstractFixture implements OrderedFixtureInterface
{

    public function load( ObjectManager $manager )
    {
        $admin = new Member();

        $admin->setFirstName("Mfana");
        $admin->setLastName("Conco");
        $admin->setEmail("ronald.conco@kaizania.co.za");
        $admin->setMobileNumber("27713264125");
        $admin->setPassword('654321');

        $admin->getMemberRoles()->add($this->getReference('role-admin'));
        $admin->getMemberRoles()->add($this->getReference('role-member'));
        $admin->getMemberRoles()->add($this->getReference('role-report'));
        $admin->getMemberRoles()->add($this->getReference('role-content'));

        $admin->setCompany($this->getReference('company-default'));
        $admin->setTitle($this->getReference('title-mr'));
        $admin->setGender($this->getReference('gender-male'));
        $admin->setGroup($this->getReference('group-admin'));
        $admin->setStatus($this->getReference('status-active'));
        $manager->persist($admin);
        
        $adminOtto = new Member();
        $adminOtto->setFirstName("Otto");
        $adminOtto->setLastName("Saayman");
        $adminOtto->setEmail("otto.saayman@kaizania.co.za");
        $adminOtto->setMobileNumber("27713264122");
        $adminOtto->setPassword('654321');

        $adminOtto->getMemberRoles()->add($this->getReference('role-admin'));
        $adminOtto->getMemberRoles()->add($this->getReference('role-member'));
        $adminOtto->getMemberRoles()->add($this->getReference('role-report'));
        $adminOtto->getMemberRoles()->add($this->getReference('role-content'));

        $adminOtto->setCompany($this->getReference('company-default'));
        $adminOtto->setTitle($this->getReference('title-mr'));
        $adminOtto->setGender($this->getReference('gender-male'));
        $adminOtto->setGroup($this->getReference('group-admin'));
        $adminOtto->setStatus($this->getReference('status-active'));
        $manager->persist($adminOtto);        
        
        $adminRyan = new Member();
        $adminRyan->setFirstName("Ryan");
        $adminRyan->setLastName("Webster");
        $adminRyan->setEmail("ryan@skedapp.co.za");
        $adminRyan->setMobileNumber("27713264121");
        $adminRyan->setPassword('654321');

        $adminRyan->getMemberRoles()->add($this->getReference('role-admin'));
        $adminRyan->getMemberRoles()->add($this->getReference('role-member'));
        $adminRyan->getMemberRoles()->add($this->getReference('role-report'));
        $adminRyan->getMemberRoles()->add($this->getReference('role-content'));

        $adminRyan->setCompany($this->getReference('company-default'));
        $adminRyan->setTitle($this->getReference('title-mr'));
        $adminRyan->setGender($this->getReference('gender-male'));
        $adminRyan->setGroup($this->getReference('group-admin'));
        $adminRyan->setStatus($this->getReference('status-active'));
        $manager->persist($adminRyan); 
        

        $adminBernard = new Member();
        $adminBernard->setFirstName("Bernard");
        $adminBernard->setLastName("Brand");
        $adminBernard->setEmail("bernard.brand@kaizania.co.za");
        $adminBernard->setMobileNumber("27713264124");
        $adminBernard->setPassword('654321');

        $adminBernard->getMemberRoles()->add($this->getReference('role-admin'));
        $adminBernard->getMemberRoles()->add($this->getReference('role-member'));
        $adminBernard->getMemberRoles()->add($this->getReference('role-report'));
        $adminBernard->getMemberRoles()->add($this->getReference('role-content'));

        $adminBernard->setCompany($this->getReference('company-default'));
        $adminBernard->setTitle($this->getReference('title-mr'));
        $adminBernard->setGender($this->getReference('gender-male'));
        $adminBernard->setGroup($this->getReference('group-admin'));
        $adminBernard->setStatus($this->getReference('status-active'));
        $manager->persist($adminBernard); 
        
        $adminWynand = new Member();
        $adminWynand->setFirstName("Wynand");
        $adminWynand->setLastName("Wynand");
        $adminWynand->setEmail("wynand@skedapp.co.za");
        $adminWynand->setMobileNumber("27713264124");
        $adminWynand->setPassword('654321');

        $adminWynand->getMemberRoles()->add($this->getReference('role-admin'));
        $adminWynand->getMemberRoles()->add($this->getReference('role-member'));
        $adminWynand->getMemberRoles()->add($this->getReference('role-report'));
        $adminWynand->getMemberRoles()->add($this->getReference('role-content'));

        $adminWynand->setCompany($this->getReference('company-default'));
        $adminWynand->setTitle($this->getReference('title-mr'));
        $adminWynand->setGender($this->getReference('gender-male'));
        $adminWynand->setGroup($this->getReference('group-admin'));
        $adminWynand->setStatus($this->getReference('status-active'));
        $manager->persist($adminWynand);         
        
        $manager->flush() ;

    }

    public function getOrder()
    {
        return 3 ;
    }

}
