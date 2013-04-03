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

    /**
     * Build consultant days of the week
     * 
     * @return array
     */
    private function buildConsultantDaysOfWeek()
    {
        return array(
            array(
                'day' => 'Monday',
                'available' => true
            ),
            array(
                'day' => 'Tuesday',
                'available' => true
            ),
            array(
                'day' => 'Wednesday',
                'available' => true
            ),
            array(
                'day' => 'Thursday',
                'available' => true
            ),
            array(
                'day' => 'Friday',
                'available' => true
            ),
            array(
                'day' => 'Saturday',
                'available' => true
            ),
            array(
                'day' => 'Sunday',
                'available' => true
            ),
        );
    }

    /**
     * Build week days slots
     * 
     * @param array $ConsultantDaysOfWeek
     * @return array
     */
    public function buildWeekDays()
    {
        $dates = array();

        //build an array of 30 days starting today
        for ($x = 0; $x < 7; $x++) {
            $epoch = '';

            if ($x == 0) {
                $epoch = strtotime("now");
            } else {
                $epoch = strtotime("+$x day");
            }
            $date = new \DateTime('@' . $epoch);

            $tmp = new \stdClass();
            $tmp->date = $date->format("Y-m-d");
            $tmp->dateObject = $date;
            $tmp->dayOfWeek = $date->format("D");

            $dates[] = $tmp;
        }

        return $dates;
    }

    /**
     * Build days slots
     * 
     * @param array $ConsultantDaysOfWeek
     * @return array
     */
    private function buildDaysSlots($ConsultantDaysOfWeek)
    {
        $dates = array();

        //build an array of 30 days starting today
        for ($x = 0; $x < 7; $x++) {
            $epoch = '';

            if ($x == 0) {
                $epoch = strtotime("now");
            } else {
                $epoch = strtotime("+$x day");
            }
            $date = new \DateTime('@' . $epoch);

            foreach ($ConsultantDaysOfWeek as $day) {
                if ($date->format("l") == $day['day']) {
                    if ($day['available']) {
                        $tmp = array(
                            'date' => $date->format("Y-m-d"),
                            'dateObject' => $date,
                            'dayOfWeek' => $date->format("l")
                        );

                        $dates[] = $tmp;
                    }
                }
            }
        }

        return $dates;
    }

    /**
     * Build time slots
     * 
     * @param SkedAppCoreBundle:Consultant $consultant
     * @param array $dates
     * @return array
     */
    private function buildTimeSlots($consultant, $dates)
    {
        $consultantStartTime = $consultant->getStartTimeslot()->getSlot();
        $consultantEndTime = $consultant->getEndTimeslot()->getSlot();
        $consultantSessionDuration = $consultant->getAppointmentDuration()->getDuration();

        foreach ($dates as &$day) {
            $tmpDate = explode("-", $day['date']);
            $timeStartTime = explode(":", $consultantStartTime);
            $timeEndTime = explode(":", $consultantEndTime);

            $startTimeObj = new \DateTime();
            $startTimeObj->setTimestamp(mktime($timeStartTime[0], $timeStartTime[1], 00, $tmpDate[1], $tmpDate[2], $tmpDate[0]));

            $endTimeObj = new \DateTime();
            $endTimeObj->setTimestamp(mktime($timeEndTime[0], $timeEndTime[1], 00, $tmpDate[1], $tmpDate[2], $tmpDate[0]));

            $isValid = true;
            $timeSlots = array();
            $currentTimeObject = $startTimeObj;

            while ($isValid) {
                //todays dates
                if ($currentTimeObject->getTimestamp() < $endTimeObj->getTimestamp()) {
                    $doSlot = false;
                    $endTimeSlotEpoch = strtotime("+$consultantSessionDuration Minutes", $currentTimeObject->getTimestamp());

                    $endTimeSlotObject = new \DateTime();
                    $endTimeSlotObject->setTimestamp($endTimeSlotEpoch);

                    $date = date('d/m/Y', strtotime("now"));
                    if ($date == date('d/m/Y', $currentTimeObject->getTimestamp())) {
                        if ((($currentTimeObject->getTimestamp() - strtotime("now")) / 3600) > 2) {
                            $doSlot = true;
                        } else {
                            $doSlot = false;
                        }
                    } else {
                        $doSlot = true;
                    }

                    if ($doSlot) {
                        $timeSlots[] = array(
                            'startTime' => $currentTimeObject,
                            'formatedStartTime' => $currentTimeObject->format('Y-m-d H:i:s'),
                            'endTime' => $endTimeSlotObject,
                            'formatedEndTime' => $endTimeSlotObject->format('Y-m-d H:i:s'),
                            'code' => uniqid(),
                        );
                    }

                    $currentTimeObject = $endTimeSlotObject;
                } else {
                    $isValid = false;
                }
            }

            $day['timeSlots'] = $timeSlots;
        }

        return $dates;
    }

    /**
     * Remove all slots not available
     * 
     * @param array $slots
     * @param array $bookings
     * @return array
     */
    private function removeUsedSlots($slots, $bookings)
    {
        foreach ($slots as &$daySlot) {
            foreach ($bookings as $booking) {
                $timeSlots = &$daySlot['timeSlots'];
                foreach ($timeSlots as $key => $slot) {
                    $currentDate = new \DateTime($slot['startTime']->format('Y-m-d'));
                    $interval = date_diff($booking->getAppointmentDate(), $currentDate);
                    if (0 == $interval->format('%d')) {
                        if ($slot['startTime'] >= $booking->getHiddenAppointmentStartTime() && $slot['startTime'] <= $booking->getHiddenAppointmentEndTime()) {
                            unset($timeSlots[$key]);
                        } else {
                            continue;
                        }
                    }
                }
            }
        }

        return $slots;
    }

    public function generateTimeSlots($consultant)
    {
        $this->logger->info('generate timeslots for consultant:' . $consultant->getFullName());

        $dates = array();

        $ConsultantDaysOfWeek = $this->buildConsultantDaysOfWeek();

        //Check if any dates were set for consultant
        foreach ($ConsultantDaysOfWeek as &$day) {
            $isAvailable = true;
            $dayOfWeek = $day['day'];
            eval("\$isAvailable = \$consultant->get$dayOfWeek();");
            $day['available'] = $isAvailable;
        }

        $dates = $this->buildTimeSlots($consultant, $this->buildDaysSlots($ConsultantDaysOfWeek));

        $options = array(
            'searchText' => '',
            'sort' => 'b.hiddenAppointmentStartTime',
            'direction' => 'desc',
            'consultantId' => $consultant->getId()
        );
        $bookings = $this->em->getRepository("SkedAppCoreBundle:Booking")->getAllConsultantBookings($options);

        $slots = $this->removeUsedSlots($dates, $bookings);
        return $slots;
    }

}
