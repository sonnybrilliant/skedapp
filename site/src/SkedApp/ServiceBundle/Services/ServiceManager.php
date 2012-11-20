<?php

namespace SkedApp\ServiceBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;
use SkedApp\CoreBundle\Entity\Service;

/**
 * Service manager 
 * 
 * @author Ronald Conco <ronald.conco@kaizania.com>
 * @package SkedAppCoreBundle
 * @subpackage Services
 * @version 0.0.1
 */
final class ServiceManager
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
     * Create and update service
     * 
     * @param type $service
     * @return void
     */
    public function createAndUpdateService($service)
    {
        $this->logger->info('Save service');
        $this->em->persist($service);
        $this->em->flush();
        return;
    }


    
    /**
     * Get all services query
     * 
     * @param array $options
     * @return query
     */
    public function listAll($options = array())
    {
        return $this->em
                    ->getRepository('SkedAppCoreBundle:Service')
                    ->getAllActiveServiceQuery($options);
    }

}
