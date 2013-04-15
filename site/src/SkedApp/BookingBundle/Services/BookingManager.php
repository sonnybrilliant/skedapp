<?php

namespace SkedApp\BookingBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;
use SkedApp\CoreBundle\Entity\Company;

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
     * Router
     * @var object
     */
    private $router = null;

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
     * @param                     $router
     * @return void
     */
    public function __construct(
    ContainerInterface $container, Logger $logger, $router)
    {
        $this->setContainer($container);
        $this->setLogger($logger);
        $this->setEm($container->get('doctrine')->getEntityManager('default'));
        $this->setRouter($router);

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
    
    public function getRouter()
    {
        return $this->router;
    }

    public function setRouter($router)
    {
        $this->router = $router;
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
    public function update($booking)
    {
        $this->logger->info("update booking");

        $this->em->persist($booking);
        $this->em->flush();
        return;
    }

    /**
     * Reject booking 
     *
     * @param SkedAppCoreBundle:Booking $booking
     * @return void
     */
    public function reject($booking)
    {
        $this->logger->info("reject booking booking");

        $booking->setIsDeleted(true);
        $booking->setIsRejected(true);
        $booking->setIsClosed(true);
        $booking->setIsCancelled(true);
        $booking->setIsActive(false);

        $this->em->persist($booking);
        $this->em->flush();
        return;
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

        if (is_null($booking->getIsMainReminderSent()))
            $booking->setIsMainReminderSent(false);

        if (is_null($booking->getIsHourReminderSent()))
            $booking->setIsHourReminderSent(false);

        $this->em->persist($booking);
        $this->em->flush();
        return;
    }

    /**
     * Delete booking
     * @param integer $bookingId
     * @return void
     */
    public function delete($bookingId)
    {
        $this->logger->info("delete booking id:$bookingId");
        $booking = $this->getById($bookingId);

        $booking->setIsDeleted(true);
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

        $bookings = $this->em->getRepository("SkedAppCoreBundle:Booking")->getAllBooking();

        return $bookings;
    }

    /**
     * Get consultant bookings
     *
     * @param array $options
     * @return array
     */
    public function getConsultantBookings($options)
    {
        $this->logger->info('Get all consultant bookings');

        $bookings = $this->em->getRepository("SkedAppCoreBundle:Booking")->getAllConsultantBookings($options);
        return $bookings;
    }

    /**
     * Get all bookings between given dates
     *
     * @return Array
     */
    public function getAllBetweenDates(\DateTime $startDateTime, \DateTime $endDateTime, Company $company = null)
    {
        $this->logger->info("get all bookings between dates");

        $bookings = $this->em->getRepository("SkedAppCoreBundle:Booking")->getAllBookingsByDate($startDateTime, $endDateTime, $company);

        return $bookings;
    }

    /**
     * Is booking time slots valid
     *
     * start time must be less than end time
     *
     * @param SkedAppCoreBundle:Booking $booking
     * @return boolean
     */
    public function isTimeValid($booking)
    {
        $isValid = false;

        if ($booking->getStartTimeslot()->getWeight() < $booking->getEndTimeslot()->getWeight()) {
            $isValid = true;
        }

        return $isValid;
    }

    /**
     * Check if booking do not clash
     *
     * @param SkedAppCoreBundle:Booking $booking
     * @return boolean
     */
    public function isBookingDateAvailable($booking)
    {
        $bookingStartDate = "";
        $bookingEndDate = "";

        if ("" == $booking->getHiddenAppointmentStartTime()) {
            $startTime = strtotime("+" . $booking->getStartTimeslot()->getWeight() * 900 . " seconds", $booking->getAppointmentDate()->format('U'));
            $bookingStartDate = new \DateTime();
            $bookingStartDate->setTimestamp($startTime);

            $endTime = strtotime("+" . $booking->getEndTimeslot()->getWeight() * 900 . " seconds", $booking->getAppointmentDate()->format('U'));
            $bookingEndDate = new \DateTime();
            $bookingEndDate->setTimestamp($endTime);
        } else {
            $bookingStartDate = $booking->getHiddenAppointmentStartTime();
            $bookingEndDate = $booking->getHiddenAppointmentEndTime();
        }

        $consultant = $booking->getConsultant();

        $consultantEndTime = $consultant->getEndTimeslot()->getSlot();
        $timeEndTime = explode(":", $consultantEndTime);
        $endTimeObj = new \DateTime();
        $endTimeObj->setTimestamp(mktime($timeEndTime[0], $timeEndTime[1], 00, $bookingStartDate->format('m'), $bookingStartDate->format('d'), $bookingStartDate->format('Y')));

        if ($bookingStartDate >= $endTimeObj) {
            return false;
        }


        $options = array(
            'searchText' => '',
            'sort' => 'b.hiddenAppointmentStartTime',
            'direction' => 'desc',
            'consultantId' => $booking->getConsultant()->getId()
        );


        $bookings = $this->em->getRepository("SkedAppCoreBundle:Booking")->getAllConsultantBookings($options);

        if ($bookings) {

            foreach ($bookings as $oldBooking) {

                if ($bookingStartDate >= $oldBooking->getHiddenAppointmentStartTime() && $bookingStartDate <= $oldBooking->getHiddenAppointmentEndTime()) {
                    if ($booking->getHiddenAppointmentStartTime() > $oldBooking->getHiddenAppointmentStartTime() && $booking->getHiddenAppointmentEndTime() < $oldBooking->getHiddenAppointmentEndTime()) {
                        return false;
                    }

                    if ($bookingStartDate > $oldBooking->getHiddenAppointmentStartTime() && $bookingStartDate < $oldBooking->getHiddenAppointmentEndTime()) {
                        return false;
                    }


                    if ($bookingEndDate > $oldBooking->getHiddenAppointmentStartTime() && $bookingEndDate < $oldBooking->getHiddenAppointmentEndTime()) {
                        return false;
                    } else {
                        return true;
                    }
                } else {
                    return true;
                }
            }
        }

        return true;
    }

    public function isBookingAvailable($consultant, $startTime, $endTime)
    {
        $this->logger->info("check if booking is available");

        $options = array(
            'searchText' => '',
            'sort' => 'b.hiddenAppointmentStartTime',
            'direction' => 'desc',
            'consultantId' => $consultant->getId()
        );

        $bookings = $this->em->getRepository("SkedAppCoreBundle:Booking")->getAllConsultantBookings($options);

        foreach ($bookings as $booking) {
            $currentDate = new \DateTime($startTime->format('Y-m-d'));
            $interval = date_diff($booking->getAppointmentDate(), $currentDate);
            if (0 == $interval->format('%d')) {

                if ($endTime > $booking->getHiddenAppointmentStartTime() && $endTime < $booking->getHiddenAppointmentEndTime()) {
                    return false;
                }

                if (($startTime > $booking->getHiddenAppointmentStartTime() && $startTime < $booking->getHiddenAppointmentEndTime())) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get booking for consultant
     *
     * @param integer $consultantId
     * @param integer $date
     * @return booking
     */
    public static function getBookingsForConsultantSearch($consultantId, $date)
    {

        $this->logger->info("get all bookings for a consultant for search results");

        $bookings = $this->em->getRepository("SkedAppCoreBundle:Booking")->listAllForSearch($consultantId, $date);

        return $bookings;
    }

    /**
     * Get all tomorrow bookings
     *
     * @return array
     */
    public function getTomorrowsBookings()
    {
        $this->logger->info("get tomorrows bookings");

        $bookings = $bookings = $this->em
            ->getRepository("SkedAppCoreBundle:Booking")
            ->getAllTomorrowsBookings();
        return $bookings;
    }

    /**
     * Get all today bookings
     *
     * @return array
     */
    public function getTodayBookings()
    {
        $this->logger->info("get today bookings");

        $bookings = $bookings = $this->em
            ->getRepository("SkedAppCoreBundle:Booking")
            ->getAllTodaysBookings();
        return $bookings;
    }

    /**
     * Get all today bookings before the hour
     *
     * @return array
     */
    public function getTodayHourBookings()
    {
        $this->logger->info("get today bookings before the hour");

        $bookings = $bookings = $this->em
            ->getRepository("SkedAppCoreBundle:Booking")
            ->getAllTodaysHourBookings();
        return $bookings;
    }

    /**
     * Get all bookings
     *
     * @return Array
     */
    public function getAllCustomerBookings($options)
    {
        $this->logger->info("get all customer bookings");

        $securityContext = $this->getContainer()->get('security.context');
        $token = $securityContext->getToken();
        $customer = $token->getUser();

        $options['customer'] = $customer;

        $bookings = $this->em->getRepository("SkedAppCoreBundle:Booking")->getAllCustomerBooking($options);

        return $bookings;
    }

    /**
     * Cancel booking
     *
     * @param SkedAppCoreBundle:Booking $booking
     * @return type
     */
    public function cancelBooking($booking)
    {
        $this->logger->info("cancel booking");

        $booking->setIsDeleted(true);
        $booking->setIsClosed(true);
        $booking->setIsCancelled(true);
        $booking->setIsActive(false);

        $this->em->persist($booking);
        $this->em->flush();
        return;
    }

    /**
     * Get consultant Timeslots
     *
     * @param SkedApp\CoreBundle\Entity\Consultant $consultant
     * @param \Datetime $date
     * @return array
     */
    public function getBookingSlotsForConsultantSearch($consultant, $date)
    {
        $this->logger->info('Get consultant open slots');

        $output = $this->em->getRepository('SkedAppCoreBundle:Booking')
            ->getBookingSlotsForConsultantSearch($consultant, $date);
        return $output;
    }

    public function getBookingsForConsultants($consultants, $date)
    {
        $this->logger->info('Get consultants bookings');

        $output = $this->em->getRepository('SkedAppCoreBundle:Booking')
            ->getBookingByConsultans($consultants, $date);
        return $output;
    }
    
    /**
     * Get occupied slots on the calender
     * 
     * @param array $bookings
     * @return array
     */
    public function getCalenderOccupiedSlots($bookings)
    {
        $this->logger->info('calender occupied slots');

        $results = array();

        foreach ($bookings as $booking) {

            $bookingTitle = '';
            $backgroundColor = "blue";
            $textColor = "white";

            if (!$booking->getIsConfirmed()) {
                $backgroundColor = "red";
            }

            if ($booking->getIsLeave()) {
                $bookingTitle = "Not Available";
                $backgroundColor = "black";
            }

            // set booking details
            $bookingTooltip = '<div class="divBookingTooltip">';

            $bookingTooltip .= '<strong>Start Time:</strong> ' . $booking->getHiddenAppointmentStartTime()->format("H:i") . "<br />";
            $bookingTooltip .= '<strong>End Time:</strong> ' . $booking->getHiddenAppointmentEndTime()->format("H:i") . "<br />";
            $bookingTooltip .= '<strong>Confirmed:</strong> ' . $booking->getIsConfirmedString() . "<br />";


            if (is_object($booking->getConsultant())) {
                $bookingTitle = $booking->getConsultant()->getFullName() . " - " . $bookingTitle;
                $bookingTooltip .= '<strong>Consultant:</strong> ' . $booking->getConsultant()->getFullName() . "<br />";
                $bookingTooltip .= '<strong>Consultant E-Mail:</strong> ' . $booking->getConsultant()->getEmail() . "<br />";
            }

            if (is_object($booking->getCustomer())) {
                $bookingTooltip .= '<strong>Customer:</strong> ' . $booking->getCustomer()->getFullName() . "<br />";
                $bookingTooltip .= '<strong>Customer Contact Number:</strong> ' . $booking->getCustomer()->getMobileNumber() . "<br />";
                $bookingTooltip .= '<strong>Customer E-Mail:</strong> ' . $booking->getCustomer()->getEmail() . "<br />";
                $bookingTitle = $booking->getCustomer()->getFullName() . " - " . $bookingTitle;
            }

            if (is_object($booking->getService())) {
                $bookingTooltip .= '<strong>Service:</strong> ' . $booking->getService()->getName() . "<br />";
                $bookingTitle = $bookingTitle . $booking->getService()->getName();
            } else {
                $backgroundColor = "black";
            }

            $bookingTooltip .= '</div>';
            
            $results[] = array(
                'allDay' => false,
                'title' => $bookingTitle,
                'start' => $booking->getHiddenAppointmentStartTime()->format("c"),
                'end' => $booking->getHiddenAppointmentEndTime()->format("c"),
                'resourceId' => 'resource-' . $booking->getConsultant()->getId(),
                'url' => $this->router->generate("sked_app_booking_edit", array("bookingId" => $booking->getId(), "page" => 'calender')) . ".html",
                'description' => $bookingTooltip,
                'color' => $backgroundColor,
                'textColor' => $textColor
            );
        }//end foreach
        
        return $results;
    }

}
