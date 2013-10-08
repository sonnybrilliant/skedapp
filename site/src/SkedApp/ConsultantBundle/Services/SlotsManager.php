<?php

namespace SkedApp\ConsultantBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;

/**
 * Slots manager
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppServiceBundle
 * @subpackage Consultant
 * @version 0.0.1
 */
final class SlotsManager
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
     * Get slot by id
     *
     * @param integer $id
     * @return SkedAppCoreBundle:Slots
     * @throws \Exception
     */
    public function getById($id)
    {
        $this->logger->info("Get slots by id" . $id);
        $slot = $this->em->getRepository('SkedAppCoreBundle:Slots')
            ->find($id);

        if (!$slot) {
            throw new \Exception('slot not found for id: ' . $id);
            $this->logger->err('Failed to find slot by id: ' . $id);
        }

        return $slot;
    }

    /**
     * Get all consultant timeSlots
     * 
     * @param SkedAppCoreBundle:ConsultantTimeSlots $consultantTimeSlot
     * @return void
     */
    public function getCurrentConsultantTimeSlots($consultantTimeSlot)
    {
        $this->logger->info("clean Slots");

        $slots = $this->em->getRepository('SkedAppCoreBundle:Slots')
            ->getCurrentConsultantTimeSlots($consultantTimeSlot);
        
        return $slots;
    }

    

}
