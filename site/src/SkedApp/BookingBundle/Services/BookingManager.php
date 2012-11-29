<?php

namespace SkedApp\BookingBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;

/**
 * Booking manager 
 * 
 * @author Ronald Conco <ronald.conco@kaizania.com>
 * @package SkedAppBookingBundle
 * @subpackage Services
 * @version 0.0.1
 */
final class BookingManager
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
     * Get consultant by id
     * @param integer $id
     * @return SkedAppCoreBundle:Booking
     * @throws \Exception 
     */
    public function getById($id)
    {
        $booking = $this->em->getRepository('SkedAppCoreBundle:Booking')
            ->find($id);

        if (!$booking) {
            throw new \Exception('Booking not found for id:' . $id);
            $this->logger->err('Failed to find Booking by id:' . $id);
        }

        return $booking;
    }    
    
    /**
     * Save booking object
     * 
     * @param SkedAppCoreBundle:Booking $booking
     * @return void
     */
    public function save($booking)
    {
        $this->logger->info("save booking");
        $this->em->persist($booking);
        $this->em->flush();
        return;
    }

    /**
     * Get all bookings
     * 
     * @return Array
     */
    public function getAll()
    {
        $this->logger->info("get all bookings");

        $booking = $this->em->getRepository("SkedAppCoreBundle:Booking")->findAll();

        return $booking;
    }

}
