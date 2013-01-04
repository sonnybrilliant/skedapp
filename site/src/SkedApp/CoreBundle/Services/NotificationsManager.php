<?php

namespace SkedApp\CoreBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;

/**
 * Notifications manager
 *
 * @author Otto Saayman <otto.saayman@kaizania.co.za>
 * @version 1.0
 * @package SuleCoreBundle
 * @subpackage Services
 */
final class NotificationsManager
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
     * Confirm booking
     *
     * @param Array $params
     * @return void
     */
    public function confirmationBooking($params)
    {

        //Create ICS attachment file for mail

        $objCurrentDate = new \DateTime();
        $objStartDate = $params['booking']->getHiddenAppointmentStartTime();
        $objEndDate = $params['booking']->getHiddenAppointmentEndTime();

        $engine = $this->container->get('templating');
        $icalText = $engine->render(
                'SkedAppCoreBundle:EmailTemplates:booking.ics.twig',
                array(
                    'booking' => $params['booking'],
                    'customer' => $params['booking']->getCustomer(),
                    'date_year_month_day' => $objCurrentDate->format('Ymd'),
                    'date_hour_minute_second' => $objCurrentDate->format('His'),
                    'date_start_year_month_day' => $objStartDate->format('Ymd'),
                    'date_start_hour_minute_second' => $objStartDate->format('His'),
                    'date_end_year_month_day' => $objEndDate->format('Ymd'),
                    'date_end_hour_minute_second' => $objEndDate->format('His'),
                    'summary' => 'Appointment at ' . $params['booking']->getConsultant()->getCompany()->getName()
                            . ' with ' . $params['booking']->getConsultant()->getFullName() . ' for ' . $params['booking']->getService()->getCategory()->getName()
                            . ' - ' . $params['booking']->getService()->getName(),
                    'description' => 'Address: ' . $params['booking']->getConsultant()->getCompany()->getAddress() . '; GPS: '
                            . $params['booking']->getConsultant()->getCompany()->getCompleteGPS() . '; Contact Number: '
                            . $params['booking']->getConsultant()->getCompany()->getContactNumber()
                    )
                );

        $this->container->get('email.manager')->bookingConfirmationCompany($params);
        $this->container->get('email.manager')->bookingConfirmationConsultant($params);

        $params['attachments_data'][] = array('file_data' => $icalText, 'file_mime' => 'text/calendar', 'file_name' => 'SkedApp-Booking-' . $params['booking']->getId() . '.ics');

        $this->container->get('email.manager')->bookingConfirmationCustomer($params);
        return;
    }

    /**
     * Confirm booking to service provider
     *
     * @param Array $params
     * @return void
     */
    public function customerAccountVerification($params)
    {
        $this->container->get('email.manager')->verifyCustomerAccount($params);
        return;
    }

    /**
     * Send booking cancellation
     *
     * @param type $params
     * @return type
     */
    public function sendBookingCancellation($params)
    {
         $this->container->get('email.manager')->bookingCancellationCustomer($params);
         $this->container->get('email.manager')->bookingCancellationCompany($params);
         $this->container->get('email.manager')-> bookingCancellationConsultant($params);
         return;
    }

    /**
     * Send booking reminders to customers
     *
     * @return void
     */
    public function sendBookingReminders()
    {
        $this->logger->info("send booking reminders");
        $bookings = $this->container->get("booking.manager")->getTomorrowsBookings();

        foreach($bookings as $booking){
            $this->container->get('email.manager')->bookingReminderCustomer(array('booking' => $booking));

            //update booking
            $booking->setIsReminderSent(true);
            $this->em->persist($booking);
            $this->em->flush();
        }

        return;
    }

}