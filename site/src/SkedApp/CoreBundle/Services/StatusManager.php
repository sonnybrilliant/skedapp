<?php

namespace SkedApp\CoreBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;

/**
 * Status manager
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SuleCoreBundle
 * @subpackage Services
 */
final class StatusManager
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
    ContainerInterface $container , Logger $logger)
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
     * Get status by name
     * 
     * @param type $statusName
     * @return 
     * @throws \LogicException 
     */
    public function getStatusByName($statusName)
    {
        $this->logger->info('get ' . $statusName.' status');

        $status = $this->em
                ->getRepository('SkedAppCoreBundle:Status')
                ->getStatus($statusName);

        if (!$status) {
            $this->logger->err('Failed to get ' . $statusName . ' status');
            throw new \LogicException('Logical exception, no ' . $statusName . ' status found');
        }

        return $status;
    }

    /**
     * get active status
     * @return object 
     */
    public function active()
    {
        $this->logger->info('get active status');
        return $this->getStatusByName('Active');
    }

    /**
     * get pending status
     * @return object 
     */
    public function pending()
    {
        $this->logger->info('get pending status');
        return $this->getStatusByName('Pending');
    }
    
    /**
     * get deleted status
     * @return object 
     */
    public function deleted()
    {
        $this->logger->info('get deleted status');
        return $this->getStatusByName('Deleted');
    }    
    
    /**
     * get confirmed status
     * @return object 
     */
    public function confirmed()
    {
        $this->logger->info('get confirmed status');
        return $this->getStatusByName('Confirmed');
    }    
    
}