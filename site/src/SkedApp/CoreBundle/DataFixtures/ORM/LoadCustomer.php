<?php

namespace SkedApp\CoreBundle\DataFixtures\ORM ;

use SkedApp\CoreBundle\Entity\Customer ;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager ;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface ;
use Doctrine\Common\DataFixtures\AbstractFixture ;

/**
 * Load default system customer
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppCoreBundle
 * @subpackage DataFixtures
 * @version 0.0.1
 */
class LoadCustomer extends AbstractFixture implements OrderedFixtureInterface , ContainerAwareInterface
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

    public function load( ObjectManager $manager )
    {

        $customerService = $this->container->get('customer.manager');
        
        $customer = array(
                'gender' => $this->getReference('gender-female'),
                'firstName' => 'Sally',
                'lastName' => 'Bruno',
                'email' => 'qa3@creativecloud.co.za',
                'password' => '654321',
                'mobile' => '27713264133',
                'landLine' => '0123456789',
                'role' => $this->getReference('role-site-user'),
            );

        $customerService->createFromFixture($customer);

    }

    public function getOrder()
    {
        return 4 ;
    }

}
