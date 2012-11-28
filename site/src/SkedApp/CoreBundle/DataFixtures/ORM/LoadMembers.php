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

//        $adminJustin = new Member();
//        $adminJustin->setFirstName("Justin");
//        $adminJustin->setLastName("Doyle");
//        $adminJustin->setEmail("justin.doyle@kaizania.co.za");
//        $adminJustin->setMobileNumber("27713264122");
//        $adminJustin->setPassword('654321');
//
//        $adminJustin->getMemberRoles()->add($this->getReference('role-admin'));
//        $adminJustin->getMemberRoles()->add($this->getReference('role-member'));
//        $adminJustin->getMemberRoles()->add($this->getReference('role-report'));
//        $adminJustin->getMemberRoles()->add($this->getReference('role-content'));
//
//        $adminJustin->setCompany($this->getReference('company-default'));
//        $adminJustin->setTitle($this->getReference('title-mr'));
//        $adminJustin->setGender($this->getReference('gender-male'));
//        $adminJustin->setGroup($this->getReference('group-admin'));
//        $adminJustin->setStatus($this->getReference('status-active'));
//        $manager->persist($adminJustin);

        $adminRoelf = new Member();
        $adminRoelf->setFirstName("Otto");
        $adminRoelf->setLastName("Saayman");
        $adminRoelf->setEmail("otto.saayman@kaizania.co.za");
        $adminRoelf->setMobileNumber("27828219119");
        $adminRoelf->setPassword('gert');

        $adminRoelf->getMemberRoles()->add($this->getReference('role-admin'));
        $adminRoelf->getMemberRoles()->add($this->getReference('role-member'));
        $adminRoelf->getMemberRoles()->add($this->getReference('role-report'));
        $adminRoelf->getMemberRoles()->add($this->getReference('role-content'));

        $adminRoelf->setCompany($this->getReference('company-default'));
        $adminRoelf->setTitle($this->getReference('title-mr'));
        $adminRoelf->setGender($this->getReference('gender-male'));
        $adminRoelf->setGroup($this->getReference('group-admin'));
        $adminRoelf->setStatus($this->getReference('status-active'));
        $manager->persist($adminRoelf);

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


        $manager->flush() ;

    }

    public function getOrder()
    {
        return 3 ;
    }

}
