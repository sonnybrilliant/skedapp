<?php

namespace SkedApp\ConsultantBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;
use SkedApp\CoreBundle\Entity\ConsultantTimeSlots;

/**
 * Consultant timeslots manager
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppServiceBundle
 * @subpackage Consultant
 * @version 0.0.1
 */
final class ConsultantTimeslotsManager
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
     * Get consultant timeslot by id
     *
     * @param integer $id
     * @return SkedAppCoreBundle:ConsultantTimeSlots
     * @throws \Exception
     */
    public function getById($id)
    {
        $this->logger->info("Get consultant timeslot by id" . $id);
        $consultantTimeslot = $this->em->getRepository('SkedAppCoreBundle:ConsultantTimeSlots')
            ->find($id);

        if (!$consultantTimeslot) {
            throw new \Exception('Consultant timeslot not found for id: ' . $id);
            $this->logger->err('Failed to find Consultant timeslot by id: ' . $id);
        }

        return $consultantTimeslot;
    }

    /**
     * Get all consultant timeSlots
     * 
     * @param SkedAppCoreBundle:Consultant $consultant
     * @return array
     */
    public function getAll($consultant)
    {
        $this->logger->info("Get consultant timeslots");

        $consultantTimeslots = $this->em->getRepository('SkedAppCoreBundle:ConsultantTimeSlots')
            ->getConsultantSlots($consultant);
        
        return $consultantTimeslots;
    }

    /**
     * Get consultant day slot by day
     * 
     * @param SkedAppCoreBundle:Consultant $consultant
     * @param SkedAppCoreBundle:DaysOfTheWeek $dayOfTheWeek
     * 
     * @return boolean
     */
    public function getConsultantDaySlotByDay($consultant, $dayOfTheWeek)
    {
        $this->logger->info("get consultant dayslot by day");

        $consultantTimeslot = $this->em->getRepository('SkedAppCoreBundle:ConsultantTimeSlots')
            ->getConsultantSlotByDayOfTheWeek($consultant, $dayOfTheWeek);

        if ($consultantTimeslot) {
            return $consultantTimeslot[0];
        }

        return false;
    }

    /**
     * Create timeslots
     * 
     * @param SkedAppCoreBundle:Consultant $consultant
     * @return void
     */
    public function createTimeSlots($consultant)
    {
        $this->logger->info("create consultant timeslots");

        $monday = $this->container->get('days_of_the_week_manager.manager')->monday();
        $mondayDaySlot = $this->getConsultantDaySlotByDay($consultant, $monday);
        //check monday
        if ($consultant->getMonday()) {
            if (!is_object($mondayDaySlot)) {
                //create a day slot for monday
                $this->addDayOfTimeSlot($consultant, $monday);
            }
        } else {
            if (is_object($mondayDaySlot)) {
                $this->removeDayOfTimeSlot($mondayDaySlot);
            }
        }


        return;
    }

    /**
     * Add day timeslot
     * 
     * @param SkedAppCoreBundle:Consultant $consultant
     * @param SkedAppCoreBundle:DaysOfTheWeek $dayOfTheWeek
     * @return void
     */
    public function addDayOfTimeSlot($consultant, $dayOfTheWeek)
    {
        $this->logger->info("add day of the week against consultant");

        $timeSlot = new ConsultantTimeSlots();
        $timeSlot->setConsultant($consultant);
        $timeSlot->setDaysOfTheWeek($dayOfTheWeek);

        $this->em->persist($timeSlot);
        $this->em->flush();
        return;
    }

    /**
     * Add day timeslot
     * 
     * @param SkedAppCoreBundle:ConsultantTimeSlots $timeslot
     *
     * @return void
     */
    public function removeDayOfTimeSlot($timeslot)
    {
        $this->logger->info("remove day of the week against consultant");

        $this->em->remove($timeslot);
        $this->em->flush();
        return;
    }
    
    /**
     * Save consultant time slot
     * @param SkedAppCoreBundle:ConsultantTimeSlots $consultantTimeSlot
     * @return void
     */
    public function save($consultantTimeSlot)
    {
       $this->logger->info("save consultant time slot");

        $this->em->persist($consultantTimeSlot);
        $this->em->flush();
        return; 
    }

}
