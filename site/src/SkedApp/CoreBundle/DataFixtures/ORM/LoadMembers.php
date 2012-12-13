<?php

namespace SkedApp\CoreBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;

/**
 * Load default system users
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SkedAppCoreBundle
 * @subpackage DataFixtures
 * @version 0.0.1
 */
class  LoadMembers extends AbstractFixture implements OrderedFixtureInterface , ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        $memberManagerService = $this->container->get('member.manager');

        $adminUserMfana = array(
            'firstName' => 'Mfana',
            'lastName' => 'Conco',
            'email' => 'ronald.conco@kaizania.co.za',
            'mobile' => '27713264125',
            'password' => '654321',
            'company' => $this->getReference('company-default'),
            'title' => $this->getReference('title-mr'),
            'gender' => $this->getReference('gender-male'),
            'group' => $this->getReference('group-admin'),
            'isAdmin' => true, 
        );

        $memberManagerService->createDefaultMember($adminUserMfana);

        $adminUserOtto = $adminUserOtto = array(
            'firstName' => 'Otto',
            'lastName' => 'Saayman',
            'email' => 'otto.saayman@kaizania.co.za',
            'mobile' => '27713264122',
            'password' => '654321',
            'company' => $this->getReference('company-default'),
            'title' => $this->getReference('title-mr'),
            'gender' => $this->getReference('gender-male'),
            'group' => $this->getReference('group-admin'),
            'isAdmin' => true, 
        );

        $savedAdminUserOtto = $memberManagerService->createDefaultMember($adminUserOtto);

        $adminUserRyan = array(
            'firstName' => 'Ryan',
            'lastName' => 'Webster',
            'email' => 'ryan@skedapp.co.za',
            'mobile' => '2771326412',
            'password' => '654321',
            'company' => $this->getReference('company-default'),
            'title' => $this->getReference('title-mr'),
            'gender' => $this->getReference('gender-male'),
            'group' => $this->getReference('group-admin'),
            'isAdmin' => true, 
        );

        $memberManagerService->createDefaultMember($adminUserRyan);


        $adminUserBernard = array(
            'firstName' => 'Bernard',
            'lastName' => 'Brand',
            'email' => 'bernard.brand@kaizania.co.za',
            'mobile' => '27713264124',
            'password' => '654321',
            'company' => $this->getReference('company-default'),
            'title' => $this->getReference('title-mr'),
            'gender' => $this->getReference('gender-male'),
            'group' => $this->getReference('group-admin'),
            'isAdmin' => true, 
        );

        $memberManagerService->createDefaultMember($adminUserBernard);

        $adminUserWynand = array(
            'firstName' => 'Wynand',
            'lastName' => 'Wynand',
            'email' => 'wynand@skedapp.co.za',
            'mobile' => '27713264124',
            'password' => '654321',
            'company' => $this->getReference('company-default'),
            'title' => $this->getReference('title-mr'),
            'gender' => $this->getReference('gender-male'),
            'group' => $this->getReference('group-admin'),
            'isAdmin' => true, 
        );

        $memberManagerService->createDefaultMember($adminUserWynand);

         $adminUserBrilliant = array(
            'firstName' => 'Sonny',
            'lastName' => 'Brilliant',
            'email' => 'dev@kaizania.co.za',
            'mobile' => '27713265638',
            'password' => '654321',
            'company' => $this->getReference('company-default'),
            'title' => $this->getReference('title-mr'),
            'gender' => $this->getReference('gender-male'),
            'group' => $this->getReference('group-admin'),
            'isAdmin' => false, 
        );

        $memberManagerService->createDefaultMember($adminUserBrilliant);

        $this->addReference('member-default' , $savedAdminUserOtto) ;
    }

    public function getOrder()
    {
        return 3;
    }

}
