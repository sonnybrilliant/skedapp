<?php

namespace SkedApp\ServiceBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;
use SkedApp\CoreBundle\Entity\Service;

/**
 * Service manager
 *
 * @author Ronald Conco <ronald.conco@kaizania.com>
 * @package SkedAppServiceBundle
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
     * Get service by id
     *
     * @param integer $id
     * @return SkedAppCoreBundle:Service
     * @throws \Exception
     */
    public function getById($id)
    {
        $service = $this->em->getRepository('SkedAppCoreBundle:Service')
            ->find($id);

        if (!$service) {
            throw new \Exception('Service not found for id:' . $id);
            $this->logger->err('Failed to find Service by id:' . $id);
        }

        return $service;
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

    /**
     * get services by category
     *
     * @param type $category
     * @return type
     */
    public function getServicesByCategory($category)
    {
        return $this->em
                ->getRepository('SkedAppCoreBundle:Service')
                ->getServicesByCategory($category);
    }

    /**
     * Delete services by category
     *
     * @param type $category
     * @return type
     */
    public function deleteServicesByCategory($category)
    {
        return $this->em
                ->getRepository('SkedAppCoreBundle:Service')
                ->deleteServicesByCategory($category);
    }

}
