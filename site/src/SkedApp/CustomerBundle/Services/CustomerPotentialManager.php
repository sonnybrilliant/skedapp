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
final class CustomerPotentialManager
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
     * Update customer object
     */
    public function update($customerPotential)
    {
        $this->em->persist($customerPotential);
        $this->em->flush();
        return;
    }

    /**
     * Delete customer potential
     *
     * @param type $customerPotential
     * @return void
     */
    public function delete($customerPotential)
    {
        $this->logger->info('delete customer potential');

        $customerPotential->setIsDeleted(true);
        $customerPotential->setIsActive(false);
        $customerPotential->setIsLocked(true);
        $customerPotential->setEnabled(false);

        $this->em->persist($customerPotential);
        $this->em->flush();
        return;
    }
}
