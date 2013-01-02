<?php

namespace SkedApp\CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * BookingRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class BookingRepository extends EntityRepository
{

    /**
     * Get all bookings
     *
     * @return array
     */
    public function getAllBooking()
    {

        $qb = $this->createQueryBuilder('b')
            ->select('b')
            ->where("b.isDeleted = :delete")
            ->andWhere("b.isActive = :active")
            ->andWhere("b.isCancelled = :cancelled")
            ->setParameters(array(
            'delete' => false,
            'active' => true,
            'cancelled' => false
            ));
        return $qb->getQuery()->execute();
    }

    /**
     * Get all customer bookings
     *
     * @return array
     */
    public function getAllCustomerBooking($options)
    {

        $defaultOptions = array(
            'sort' => 'b.id',
            'direction' => 'asc'
        );

        foreach ($options as $key => $values) {
            if (!$values)
                $options[$key] = $defaultOptions[$key];
        }

        $qb = $this->createQueryBuilder('b')->select('b');
        $qb->where('b.isDeleted =  :delete')
            ->andWhere("b.isActive = :active")
            ->andWhere("b.isCancelled = :cancelled")
            ->andWhere("b.customer = :customer")
            ->setParameters(array(
            'delete' => false,
            'active' => true,
            'cancelled' => false,
            'customer' => $options['customer']
            ));
        $qb->orderBy($options['sort'], $options['direction']);
        return $qb->getQuery()->execute();
    }
    /**
     * Get consultant all bookings
     *
     * @return array
     */
    public function getAllConsultantBookings($consultantId)
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b')
            ->where("b.isDeleted = :delete")
            ->andWhere("b.isActive = :active")
            ->andWhere("b.isCancelled = :cancelled")
            ->andWhere("b.consultant = :consultant")
            ->setParameters(array(
            'delete' => false,
            'active' => true,
            'cancelled' => false,
            'consultant' => $consultantId
            ));
        return $qb->getQuery()->execute();
    }

    /**
     * Get consultant all bookings
     *
     * @return array
     */
    public function getAllConsultantBookingsByDate($consultantId, \DateTime $objStartDate, \DateTime $objEndDate)
    {
        $qb = $this->createQueryBuilder('b')
            ->select('b')
            ->where("b.isDeleted = :delete")
            ->andWhere("b.isActive = :active")
            ->andWhere("b.isCancelled = :cancelled")
            ->andWhere("b.consultant = :consultant")
            ->andWhere("b.hiddenAppointmentStartTime >= :start")
            ->andWhere("b.hiddenAppointmentEndTime <= :end")
            ->setParameters(array(
            'delete' => false,
            'active' => true,
            'cancelled' => false,
            'consultant' => $consultantId,
            'start' => $objStartDate->format('Y-m-d H:i:s'),
            'end' => $objEndDate->format('Y-m-d H:i:s')
            ));
        return $qb->getQuery()->execute();
    }

    /**
     * Is consultant available
     *
     * @param SkedAppCoreBundle:Consultant $consultant
     * @param datetime $bookingStartDate
     * @param datetime $bookingEndDate
     * @return array
     */
    public function isConsultantAvailable($consultant, $bookingStartDate, $bookingEndDate)
    {
        $dql = "SELECT b FROM SkedAppCoreBundle:Booking b
                WHERE b.consultant = ?1 AND b.isDeleted = ?2
                AND b.isActive = ?3 AND b.isCancelled = ?4
                AND ( b.hiddenAppointmentStartTime >= ?5 AND b.hiddenAppointmentStartTime <= ?6 )
                OR  ( b.hiddenAppointmentEndTime >= ?5 AND b.hiddenAppointmentEndTime <= ?6 )
                OR  ( b.hiddenAppointmentStartTime <= ?5 AND b.hiddenAppointmentEndTime >= ?6 )";
        return $this->getEntityManager()->createQuery($dql)
                ->setParameters(array(
                    1 => $consultant,
                    2 => false,
                    3 => true,
                    4 => false,
                    5 => $bookingStartDate,
                    6 => $bookingEndDate
                ))
                ->getResult();
    }

    /**
     * Get all active tomorrow bookings
     *
     * @return array
     */
    public function getAllTomorrowsBookings()
    {
        $currentDate = strtotime("+1 day");
        $tomorrowDate = new \DateTime();
        $tomorrowDate->setTimestamp($currentDate);

        $dql = "SELECT b FROM SkedAppCoreBundle:Booking b
                WHERE b.isDeleted = ?1 AND b.isActive = ?2
                AND b.isCancelled = ?3 AND b.isReminderSent = ?4
                AND b.appointmentDate = ?5";
        return $this->getEntityManager()->createQuery($dql)
                ->setParameters(array(
                    1 => false,
                    2 => true,
                    3 => false,
                    4 => false,
                    5=> $tomorrowDate->format("Y-m-d")
                ))
                ->getResult();

    }

    /**
     * Get available booking slots for consultant
     *
     * @param SkedAppCoreBundle:Consultant $consultant
     * @param datetime $bookingDate
     * @return array
     */
    public function getBookingSlotsForConsultantSearch($consultant, \DateTime $bookingDate, $callback_cnt = 0)
    {

        $arrOut = array('error_message' => null, 'time_slots' => array());

        //check next day consultant is available
        $intDoWAvailable = -1;
        $intCntCheck = 1;
        $booking_day_test = new \DateTime($bookingDate->format('r'));

        //Check if any dates were set for consultant
        while (($intDoWAvailable < 0) && ($intCntCheck <= 7)) {
            $strDayName = $booking_day_test->format('l');
            eval("\$intDoWAvailable = \$consultant->get$strDayName();");
            $booking_day_test->modify("+1 day");
            $intCntCheck++;
        }

        if ($intDoWAvailable < 0) {

            $arrOut['error_message'] = 'This consultant is not available for bookings';

            return $arrOut;
        } else {
            //found the next available day, so set the requested booking date to that day
            $booking_day_test->modify("-1 day");
            $bookingDate = new \DateTime($booking_day_test->format("Y-m-d 00:00:00"));
        }

        $appointment_duration = $consultant->getAppointmentDuration()->getDuration();

        //Get the time the consultant starts and make sure it is not more than 2 hours in advance
        //2 hours because booking can not be cancelled 2 hours in advance
        //Query is for today
        if ($callback_cnt <= 0)
            $bookingDate->setTime(date('H'), date('i'), 0);

        $start_time = new \DateTime($bookingDate->format('r'));
        $time_slot = explode(':', $consultant->getStartTimeslot()->getSlot());
        $start_time->setTime($time_slot[0], $time_slot[1], 0);

        //Get the end time slot
        $end_time = new \DateTime($bookingDate->format('r'));
        $time_slot = explode(':', $consultant->getEndTimeslot()->getSlot());
        $end_time->setTime($time_slot[0], $time_slot[1], 0);

        while ($start_time->getTimestamp() <= (time() + (60 * 60 * 2))) {
            //Add appointment length to start time until more than 2 hours away
            $start_time->modify("+$appointment_duration minute");
        } //while

        $slot_cnt = 0;

        if ($bookingDate->getTimestamp() <= $end_time->getTimeStamp()) {
            //Still enough time to book today
            //Start by identifying time slots on the same day
            while (($slot_cnt < 5) && ($start_time->getTimestamp() <= $end_time->getTimeStamp())) {
                $new_timestamp = $start_time->getTimestamp() + (60 * $appointment_duration);

                $booking_time_slot = $this->isConsultantAvailable($consultant, $start_time->format('Y-m-d H:i:00'), date('Y-m-d H:i:00', $new_timestamp));

                if (!$booking_time_slot) {
                    $arrOut['time_slots'][$slot_cnt] = array(
                        'start_time' => $start_time->format('H:i'), 'end_time' => date('H:i', $new_timestamp), 'date' => $start_time->format('j M'), 'booking_taken' => $booking_time_slot,
                        'dow' => $start_time->format('D'),
                        'year' => $start_time->format('Y'),
                        'date_full' => $start_time->format('j M Y'),
                        'date_form' => $start_time->format('d-m-Y')
                    );
                    $slot_cnt++;
                }

                $start_time->modify("+$appointment_duration minute");
            } //while
        } //if ($bookingDate->getTimestamp() <= $end_time->getTimeStamp())

        if ($slot_cnt <= 0) {
            //No open time slots for consultant. Add 1 day and try again. Up to 30 days, then return fully booked message

            if ($callback_cnt > 30) {
                $arrOut['error_message'] = 'All booking time slots taken until ' . $bookingDate->format('j F Y');
                return $arrOut;
            }

            $bookingDate->modify("+1 day");
            $bookingDate->setTime(0, 0, 0);
            $callback_cnt++;

            $arrOut = $this->getBookingSlotsForConsultantSearch($consultant, $bookingDate, $callback_cnt);
        }

        return $arrOut;
    }

}
