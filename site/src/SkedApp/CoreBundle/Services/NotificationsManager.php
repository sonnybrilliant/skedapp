<?php

namespace SkedApp\CoreBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;

/**
 * Notifications manager
 *
 * @author Otto Saayman <otto.saayman@kaizania.co.za>
 * @version 1.0
 * @package SuleCoreBundle
 * @subpackage Services
 */
final class NotificationsManager
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
     * @param ContainerInterface $container
     * @param Logger $logger
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
     * Confirm booking to service provider
     * 
     * @param Array $params
     * @return void
     */
    public function confirmationBookingCompany($params)
    {
        $this->container->get('email.manager')->companyBookingCreated($params);
        return; 
    }
 
}