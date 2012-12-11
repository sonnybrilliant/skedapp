<?php

namespace SkedApp\CustomerBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;
use SkedApp\CoreBundle\Entity\Customer;

/**
 * Customer manager
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 0.0.1
 * @package SkedAppCustomerBundle
 * @subpackage Services
 */
final class CustomerManager
{

    /**
     * Service Container
     * @var object
     */
    private $container = null;

    /**
     * Monolog logger
     * @var object
     */
    private $logger = null;

    /**
     * Entity manager
     * @var object
     */
    private $em;

    /**
     * Class construct
     *
     * @param  ContainerInterface $container
     * @param  Logger             $logger
     * @return void
     */
    public function __construct(
    ContainerInterface $container, Logger $logger)
    {
        $this->setContainer($container);
        $this->setLogger($logger);
        $this->setEm($container->get('doctrine')->getEntityManager('default'));

        return;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function getEm()
    {
        return $this->em;
    }

    public function setEm($em)
    {
        $this->em = $em;
    }

    /**
     * Get member by id
     * @param integer $id
     * @return SkedAppCoreBundle:Customer
     * @throws \Exception
     */
    public function getById($id)
    {
        $customer = $this->em->getRepository('SkedAppCoreBundle:Customer')
            ->find($id);

        if (!$customer) {
            throw new \Exception('Customer not found for id:' . $id);
            $this->logger->err('Failed to find customer by id:' . $id);
        }

        return $customer;
    }

    /**
     * Get customer by email address
     *
     * @param string $email
     * @return boolean
     */
    public function getByEmail($email)
    {
        $customers = $this->em->getRepository('SkedAppCoreBundle:Customer')
            ->findByEmail($email);

        if ($customers) {
            return $customers[0];
        }
        return false;
    }

    /**
     * Get customer by token
     * @param string $token
     * @return boolean
     */
    public function getByToken($token)
    {
        $customers = $this->em->getRepository('SkedAppCoreBundle:Customer')
            ->findByConfirmationToken($token);

        if ($customers) {
            return $customers[0];
        }
        return false;
    }

    /**
     * Create customer
     * 
     * @param SkedAppCoreBundle:Customer $customer
     * @return void
     * @throws \Exception
     */
    public function createCustomer($customer)
    {
        $this->logger->info("create customer");

        $groupName = "Site user";

        $groups = $this->em->getRepository("SkedAppCoreBundle:Group")->findByName($groupName);

        if ($groups) {
            $group = $groups[0];
            foreach ($group->getRoles() as $role) {
                $customer->addCustomerRole($role);
            }
        } else {
            throw new \Exception("Could not find groups matching name:" . $groupName);
        }

        $this->em->persist($customer);
        $this->em->flush();
        return;
    }

    /**
     * Update customer object
     */
    public function update($customer)
    {
        $this->em->persist($customer);
        $this->em->flush();
        return;
    }

    /**
     * Get logged in user
     *
     * @return SkedAppCoreBundle:Member
     */
    public function getLoggedInUser()
    {
        $securityContext = $this->container->get('security.context');
        $user = $securityContext->getToken()->getUser();
        return $user;
    }

}
