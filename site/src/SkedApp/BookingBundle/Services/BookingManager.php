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
     * Get all bookings between given dates
     *
     * @return Array
     */
    public function getAllBetweenDates(\DateTime $startDateTime, \DateTime $endDateTime)
    {
        $this->logger->info("get all bookings between dates");

        $bookings = $this->em->getRepository("SkedAppCoreBundle:Booking")->getAllBookingsByDate($startDateTime, $endDateTime);

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

        $results = $this->em->getRepository("SkedAppCoreBundle:Booking")
            ->isConsultantAvailable($booking->getConsultant(), $bookingStartDate, $bookingEndDate);

        if (is_null($results))
          return false;

        /*
         * confirm if the new appointment start time is equal to the
         * already booked appointment end time
         */

        if (sizeof($results) == 1) {
            return false;
            //Caused problems when checking availability on the calendar
            $oldBooking = $results[0];
            if ($oldBooking->getHiddenAppointmentEndTime()->getTimestamp() == $bookingStartDate->getTimestamp()) {
                return true;
            } elseif ($oldBooking->getHiddenAppointmentStartTime()->getTimestamp() == $bookingEndDate->getTimestamp()) {
                return true;
            }
        } else if (sizeof($results) > 1) {
            return false;
        } else if (sizeof($results) == 0) {
            return true;
        }

        return false;
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
        $booking->setIsActive(false);
        $booking->setIsCancelled(true);

        $this->em->persist($booking);
        $this->em->flush();
        return;
    }

}
