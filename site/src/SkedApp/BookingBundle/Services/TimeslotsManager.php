<?php

namespace SkedApp\BookingBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;

/**
 * Timeslots manager
 *
 * @author Otto Saayman <otto.saayman@kaizania.co.za>
 * @package SkedAppBookingBundle
 * @subpackage Services
 * @version 0.0.1
 */
final class TimeslotsManager
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
     * Get timeslots by id
     * @param integer $id
     * @return SkedAppCoreBundle:Timeslots
     * @throws \Exception
     */
    public function getById($id)
    {
        $timeslot = $this->em->getRepository('SkedAppCoreBundle:Timeslots')
            ->find($id);

        if (!$timeslot) {
            throw new \Exception('Timeslot not found for id:' . $id);
            $this->logger->err('Failed to find timeslot by id:' . $id);
        }

        return $timeslot;
    }

    /**
     * Get timeslot by time
     * 
     * @param string $time
     * @return SkedAppCoreBundle:Timeslots
     * @throws \Exception
     */
    public function getByTime($time)
    {
        $timeslot = $this->em->getRepository('SkedAppCoreBundle:Timeslots')
            ->findOneBy(array('slot' => $time));

        if (!$timeslot) {
            throw new \Exception('Timeslot not found for time:' . $time);
            $this->logger->err('Failed to find timeslot by time:' . $time);
        }

        return $timeslot;
    }


}
