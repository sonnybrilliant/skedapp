<?php

namespace SkedApp\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use SkedApp\BookingBundle\Form\BookingCreateType;
use SkedApp\BookingBundle\Form\BookingMakeType;
use SkedApp\BookingBundle\Form\BookingUpdateType;
use SkedApp\CoreBundle\Entity\Booking;
use SkedApp\CoreBundle\Entity\Customer;
use SkedApp\CoreBundle\Entity\Timeslots;
use SkedApp\CoreBundle\Entity\Consultant;

/**
 * SkedApp\BookingBundle\Controller\BookingController
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppBookingBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class BookingController extends Controller
{

    public function manageBookingAction()
    {
        $this->get('logger')->info('manage bookings');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('list consultants, access denied.');
            throw new AccessDeniedException();
        }

        $user = $this->get('member.manager')->getLoggedInUser();

        $em = $this->getDoctrine()->getEntityManager();
        $consultants = $em->getRepository('SkedAppCoreBundle:Consultant')->getAllActiveQuery($user->getCompany());

        return $this->render('SkedAppBookingBundle:Booking:list.html.twig', array(
                'consultants' => $consultants
            ));
    }

    /**
     * new booking
     *
     * @param type $agency
     * @return Reponse
     */
    public function newAction()
    {
        $this->get('logger')->info('add a new booking');

        $user = $this->get('member.manager')->getLoggedInUser();

        $booking = new Booking();

        $bookingValues = $this->getRequest()->get('Booking');

        if (!isset ($bookingValues['appointmentDate']))
          $bookingValues['appointmentDate'] = date('Y-m-d');

        if (!isset ($bookingValues['startTimeslot']))
          $booking->setStartTimeslot(new Timeslots(''));
        else
          $booking->setStartTimeslot($this->get('timeslots.manager')->getById($bookingValues['startTimeslot']));

        if (!isset ($bookingValues['endTimeslot']))
          $booking->setEndTimeslot(new Timeslots(''));
        else
          $booking->setEndTimeslot($this->get('timeslots.manager')->getById($bookingValues['endTimeslot']));

        if (!isset ($bookingValues['consultant']))
          $booking->setConsultant(new Consultant());
        else
          $booking->setConsultant($this->get('consultant.manager')->getById($bookingValues['consultant']));

        $form = $this->createForm(new BookingCreateType(
                $user->getCompany()->getId(),
                $this->get('member.manager')->isAdmin(),
                new \DateTime($bookingValues['appointmentDate'])
            ), $booking);

        return $this->render('SkedAppBookingBundle:Booking:add.html.twig', array(
                'form' => $form->createView(),
            ));
    }

    /**
     * new booking
     *
     * @param type $agency
     * @return Reponse
     */
    public function createAction($agency = 1)
    {
        $this->get('logger')->info('add a new booking');

        $user = $this->get('member.manager')->getLoggedInUser();

        $booking = new Booking();
        $form = $this->createForm(new BookingCreateType(
                $user->getCompany()->getId(),
                $this->get('member.manager')->isAdmin()
            ), $booking);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {

                $isValid = true;
                $errMsg = "";

                if (!$booking->getIsLeave()) {
                    //service must be seletced
                    if (!$booking->getService()) {
                        $errMsg = "Please select a service";
                        $isValid = false;
                    }
                }

                if (!$this->get('booking.manager')->isTimeValid($booking)) {
                    $errMsg = "End time must be greater than start time";
                    $isValid = false;
                }

                if (!$this->get('booking.manager')->isBookingDateAvailable($booking)) {
                    $errMsg = "Booking not available, please choose another time.";
                    $isValid = false;
                }

                if ($isValid) {
                    $this->get('booking.manager')->save($booking);
                    $this->getRequest()->getSession()->setFlash(
                        'success', 'Created booking sucessfully');
                    $options = array(
                        'booking' => $booking,
                        'link' => $this->generateUrl("sked_app_booking_edit", array('bookingId' => $booking->getId()), true)
                    );

                    if ($booking->getIsConfirmed()) {
                        //send booking confirmation emails
                        $this->get("notification.manager")->confirmationBooking($options);
                    } else {
                        //send booking created notification emails
                        $this->get("notification.manager")->createdByCompanyBooking($options);
                    }

                    return $this->redirect($this->generateUrl('sked_app_booking_manager'));
                } else {
                    $this->getRequest()->getSession()->setFlash(
                        'error', $errMsg);
                }
            }
        } else {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Failed to create booking');
        }

        return $this->render('SkedAppBookingBundle:Booking:add.html.twig', array(
                'form' => $form->createView(),
            ));
    }

    /**
     * Edit booking
     *
     * @param integer $bookingId
     * @return Response
     */
    public function editAction($bookingId)
    {
        $this->get('logger')->info('edit booking id:' . $bookingId);

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('list consultants, access denied.');
            throw new AccessDeniedException();
        }

        try {

            $user = $this->get('member.manager')->getLoggedInUser();
            $booking = $this->get('booking.manager')->getById($bookingId);

            $form = $this->createForm(new BookingUpdateType(
                    $user->getCompany()->getId(),
                    $this->get('member.manager')->isAdmin()
                ), $booking);
        } catch (\Exception $e) {
            $this->get('logger')->err("booking id:$booking invalid");
            $this->createNotFoundException($e->getMessage());
        }

        $customer = new Customer();

        if (is_object($booking->getCustomer()))
                $customer = $booking->getCustomer();

        return $this->render('SkedAppBookingBundle:Booking:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $booking->getId(),
                'customer' => $customer,
            ));
    }

    /**
     * update booking
     *
     * @param integer $bookingId
     * @return Response
     */
    public function updateAction($bookingId)
    {
        $this->get('logger')->info('update booking id:' . $bookingId);

        try {

            $user = $this->get('member.manager')->getLoggedInUser();
            $booking = $this->get('booking.manager')->getById($bookingId);

            $oldIsConfirmed = $booking->getIsConfirmed();

            $form = $this->createForm(new BookingUpdateType(
                    $user->getCompany()->getId(),
                    $this->get('member.manager')->isAdmin()
                ), $booking);

            if ($this->getRequest()->getMethod() == 'POST') {
                $form->bindRequest($this->getRequest());

                if ($form->isValid()) {
                    $this->get('booking.manager')->save($booking);
                    $this->getRequest()->getSession()->setFlash(
                        'success', 'Updated booking sucessfully');

                    if ( (!$oldIsConfirmed) && ($booking->getIsConfirmed()) ) {
                        $options = array(
                            'booking' => $booking,
                            'link' => $this->generateUrl("sked_app_booking_edit", array('bookingId' => $booking->getId()), true)
                        );
                        //send booking confirmation emails
                        $this->get("notification.manager")->confirmationBooking($options);
                    }

                    return $this->redirect($this->generateUrl('sked_app_booking_manager'));
                } else {
                    $this->getRequest()->getSession()->setFlash(
                        'error', 'Failed to update booking');
                }
            }
        } catch (\Exception $e) {
            $this->get('logger')->err("booking id:$booking invalid");
            $this->createNotFoundException($e->getMessage());
        }

        return $this->render('SkedAppBookingBundle:Booking:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $booking->getId()
            ));
    }

    /**
     * Delete booking
     *
     * @param integer $bookingId
     * @return Response
     */
    public function deleteAction($bookingId)
    {
        $this->get('logger')->info('delete booking id:' . $bookingId);

        try {

            $this->get('booking.manager')->delete($bookingId);
            $this->getRequest()->getSession()->setFlash(
                'success', 'Deleted booking sucessfully');
            return $this->redirect($this->generateUrl('sked_app_booking_manager'));
        } catch (\Exception $e) {
            $this->get('logger')->err("booking id:$booking invalid");
            $this->createNotFoundException($e->getMessage());
        }
    }

    /**
     * Ajax call services by category
     *
     * @param integer $consultantId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     */
    public function ajaxGetByConsultantAction($consultantId)
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->get('logger')->info('get services by consultant');
            $results = array();

            $consultant = $this->get('consultant.manager')->getById($consultantId);

            if ($consultant) {
                $services = $consultant->getConsultantServices();
                foreach ($services as $service) {
                    $results[] = array(
                        'id' => $service->getId(),
                        'name' => $service->getName()
                    );
                }
            }

            $return = new \stdClass();
            $return->status = 'success';
            $return->count = sizeof($results);
            $return->results = $results;

            $response = new Response(json_encode($return));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            $this->get('logger')->warn('not a valid request, expected ajax call');
            throw new AccessDeniedException();
        }
    }

    /**
     *  Get active bookings
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxGetBookingsAction()
    {
        $this->get('logger')->info('get bookings');
        $results = array();

        $start = $this->getRequest()->get('start', null);
        $end = $this->getRequest()->get('end', null);

        //Test if its a day or month view
        $startSlotsDateTime = new \Datetime(date('Y-m-d H:i:00', $start));
        $endSlotsDateTime = new \Datetime(date('Y-m-d H:i:00', $end));
        $earliestStart = new \Datetime($startSlotsDateTime->format('Y-m-d H:i:00'));
        $latestEnd = new \Datetime($endSlotsDateTime->format('Y-m-d H:i:00'));
        $isSingleDay = false;

        $bookings = $this->get("booking.manager")->getAllBetweenDates($startSlotsDateTime, $endSlotsDateTime);

        if ($bookings) {
            foreach ($bookings as $booking) {
                $allDay = false;


                if (true == $booking->getIsLeave()) {
                    $allDay = true;
                    $bookingName = "On leave";
                } else {
                    if (is_object($booking->getService()))
                            $bookingName = $booking->getService()->getName();
                    else
                            $bookingName = 'Unknown Service';
                }

                $bookingTooltip = '<div class="divBookingTooltip">';

                if (is_object ($booking->getConsultant())) {

                    $bookingName = $booking->getConsultant()->getFullName() . ' - ' . $bookingName;

                }

                if (is_object ($booking->getCustomer())) {

                    $bookingTooltip .= '<strong>Customer:</strong> ' . $booking->getCustomer()->getFullName() . "<br />";
                    $bookingTooltip .= '<strong>Customer Contact Number:</strong> ' . $booking->getCustomer()->getMobileNumber() . "<br />";
                    $bookingTooltip .= '<strong>Customer E-Mail:</strong> ' . $booking->getCustomer()->getEmail() . "<br />";

                    $bookingName = $booking->getCustomer()->getFullName() . ' - ' . $bookingName;

                }

                $bookingTooltip .= '<strong>Start Time:</strong> ' . $booking->getHiddenAppointmentStartTime()->format("H:i") . "<br />";
                $bookingTooltip .= '<strong>End Time:</strong> ' . $booking->getHiddenAppointmentEndTime()->format("H:i") . "<br />";
                $bookingTooltip .= '<strong>Confirmed:</strong> ' . $booking->getIsConfirmedString() . "<br />";

                if (is_object ($booking->getConsultant())) {
                    $bookingTooltip .= '<strong>Consultant:</strong> ' . $booking->getConsultant()->getFullName() . "<br />";
                    $bookingTooltip .= '<strong>Consultant E-Mail:</strong> ' . $booking->getConsultant()->getEmail() . "<br />";
                }

                if (is_object ($booking->getService()))
                        $bookingTooltip .= '<strong>Service:</strong> ' . $booking->getService()->getName() . "<br />";

                $bookingTooltip .= '<strong>Notes:</strong> ' . $booking->getDescription() . "<br />";

                $bookingTooltip .= '</div>';

                $results[] = array(
                    'allDay' => $allDay,
                    'title' => $bookingName,
                    'start' => $booking->getHiddenAppointmentStartTime()->format("c"),
                    'end' => $booking->getHiddenAppointmentEndTime()->format("c"),
                    //'start' => "2012-11-29",
                    'resourceId' => 'resource-' . $booking->getConsultant()->getId(),
                    'url' => $this->generateUrl("sked_app_booking_edit", array("bookingId" => $booking->getId())),
                    'description' => $bookingTooltip,
                    //'color' => 'pink',
                    //'textColor' => 'black'
                );
            } //foreach booking found

        } //if bookings found

        if (($endSlotsDateTime->getTimeStamp() - $startSlotsDateTime->getTimestamp()) <= (60 * 60 * 24)) {
            $isSingleDay = true;
        }

        if ( (!is_null($start)) && (!is_null($end)) ) {
            //Adding empty slots
            $consultants = $this->get("consultant.manager")->listAll(array('sort' => 'c.lastName', 'direction' => 'Asc'));

            foreach ($consultants as $consultant) {

                $startSlotsDateTime = new \Datetime(date('Y-m-d H:i:00', $start));
                $endSlotsDateTime = new \Datetime(date('Y-m-d H:i:00', $end));

                $startSlot = $consultant->getStartTimeslot()->getSlot();
                $startSlot = explode(':', $startSlot);

                $endSlot = $consultant->getEndTimeslot()->getSlot();
                $endSlot = explode(':', $endSlot);

                $startSlotsDateTime->setTime($startSlot[0], $startSlot[1], 0);
                $endSlotsDateTime->setTime($endSlot[0], $endSlot[1], 0);

                //Check which consultant starts the earliest and which ends the latest
                if ($earliestStart->getTimestamp() > $startSlotsDateTime->getTimestamp()) {
                   $earliestStart = new \DateTime($startSlotsDateTime->format('Y-m-d H:i:00'));
                }

                if ($latestEnd->getTimestamp() < $endSlotsDateTime->getTimestamp()) {
                   $latestEnd = new \DateTime($endSlotsDateTime->format('Y-m-d H:i:00'));
                }

                if ($isSingleDay) {
                    //If its a single day, add empty slots for each resource - Need to distinguish between resource and day view
                    while ($startSlotsDateTime->getTimestamp() < $endSlotsDateTime->getTimestamp()) {
                        //Loop through the timeslots for each day and check if the consultant is available

                        $durationInterval = new \DateInterval('PT' . $consultant->getAppointmentDuration()->getDuration() . 'M');

                        $startSlot = new \DateTime($startSlotsDateTime->format('Y-m-d H:i:00'));
                        $endSlot = new \DateTime($startSlotsDateTime->format('Y-m-d H:i:00'));
                        $endSlot->add($durationInterval);
                        $appointmentDate = new \DateTime($startSlotsDateTime->format('Y-m-d 00:00:00'));

                        $booking = new Booking();

                        $booking->setConsultant($consultant);
                        $booking->setAppointmentDate($appointmentDate);
                        $booking->setStartTimeslot($this->get('timeslots.manager')->getByTime($startSlot->format('H:i')));
                        $booking->setEndTimeslot($this->get('timeslots.manager')->getByTime($endSlot->format('H:i')));
                        $booking->setHiddenAppointmentStartTime($startSlot);
                        $booking->setHiddenAppointmentEndTime($endSlot);

                        $isAvailable = $this->get('booking.manager')->isBookingDateAvailable($booking);

                        unset ($booking);

                        if ($isAvailable) {

                            $bookingTooltip = '<div class="divBookingTooltip">';

                            $bookingTooltip .= '<strong>Start Time:</strong> ' . $startSlot->format("H:i") . "<br />";
                            $bookingTooltip .= '<strong>End Time:</strong> ' . $endSlot->format("H:i") . "<br />";

                            $bookingTooltip .= '<strong>Consultant:</strong> ' . $consultant->getFullName() . "<br />";
                            $bookingTooltip .= '<strong>Consultant E-Mail:</strong> ' . $consultant->getEmail() . "<br />";

                            $services = $consultant->getConsultantServices();

                            $bookingTooltip .= '<strong>Service(s): </strong>';

                            foreach ($services as $service)
                              $bookingTooltip .= $service->getName() . " ";

                            $bookingTooltip .= "<br />";

                            $bookingTooltip .= '</div>';

                            $results[] = array(
                                'allDay' => false,
                                'title' => 'Add Booking',
                                'start' => $startSlot->format("c"),
                                'end' => $endSlot->format("c"),
                                'resourceId' => 'resource-' . $consultant->getId(),
                                'url' => $this->generateUrl("sked_app_booking_new",
                                        array(
                                            'Booking[appointmentDate]' => $startSlot->format("Y-m-d"),
                                            'Booking[startTimeslot]' => $this->get('timeslots.manager')->getByTime($startSlot->format('H:i'))->getId(),
                                            'Booking[endTimeslot]' => $this->get('timeslots.manager')->getByTime($endSlot->format('H:i'))->getId(),
                                            'Booking[consultant]' => $consultant->getId(),
                                                    )),
                                'description' => $bookingTooltip,
                                'color' => 'white',
                                'textColor' => 'black'
                            );

                        } //if slot is available

                        $startSlotsDateTime->add($durationInterval);

                        unset ($startSlot);
                        unset ($endSlot);

                    } //while
                } //if is a single day

            } //foreach consultant

            if (!$isSingleDay) {
                //Week or month view - Not sure if this is practical
                while ($earliestStart->getTimestamp() < $latestEnd->getTimestamp()) {
                    //Loop through the timeslots for each day and check if the consultant is available

                    $durationInterval = new \DateInterval('PT15M');

                    $startSlot = new \DateTime($earliestStart->format('Y-m-d H:i:00'));
                    $endSlot = new \DateTime($startSlotsDateTime->format('Y-m-d H:i:00'));
                    $endSlot->add($durationInterval);

                    $durationInterval = new \DateInterval('P1D');

                    $bookingTooltip = '<div class="divBookingTooltip">';

                    $bookingTooltip .= "Click to Add a booking<br />";

                    $bookingTooltip .= '</div>';

                    $results[] = array(
                        'allDay' => true,
                        'title' => 'Add Booking',
                        'start' => $startSlot->format("c"),
//                        'end' => $endSlot->format("c"),
                        //'start' => "2012-11-29",
                        'url' => $this->generateUrl("sked_app_booking_new",
                                array(
                                    'Booking[appointmentDate]' => $startSlot->format("Y-m-d")
                            )),
                        'description' => $bookingTooltip,
                        'color' => 'white',
                        'textColor' => 'black'
                    );

                    $earliestStart->add($durationInterval);

                    unset ($startSlot);
                    unset ($endSlot);

                } //while
            } //Single day
        } //Dates are set

        $response = new Response(json_encode($results));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Make a new booking on the public site
     *
     * @param integer $consultantId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     */
    public function makeAction($companyId, $consultantId, $date, $timeSlotStart, $serviceIds)
    {
        $this->get('logger')->info('add a new booking public');

        $user = $this->get('member.manager')->getLoggedInUser();

        //Format the date correctly

        $date = new \DateTime($date);
        $date = $date->format('Y-m-d');

        if (!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            //User is not logged in
            return $this->redirect($this->generateUrl('_security_login', array(
                        'booking_attempt' => 1,
                        'company_id' => $companyId,
                        'consultant_id' => $consultantId,
                        'booking_date' => $date,
                        'timeslot_start' => $timeSlotStart,
                        'service_ids' => $serviceIds,
                        )
                    ));
        }

        $booking = new Booking();
        $consultant = $this->get('consultant.manager')->getById($consultantId);
        $service = $this->get('service.manager')->getById($serviceIds);

        $booking->setConsultant($consultant);
        $booking->setService($service);
        $booking->setAppointmentDate($date);
        $booking->setStartTimeslot($this->get('timeslots.manager')->getByTime($timeSlotStart));
        $timeSlotEnd = new \DateTime($timeSlotStart);

        $timeSlotEnd = $timeSlotEnd->add(new \DateInterval('PT' . $consultant->getAppointmentDuration()->getDuration() . 'M'));

        $form = $this->createForm(new BookingMakeType(
                $companyId,
                $consultantId,
                $date,
                $timeSlotStart,
                $serviceIds
            ),
            $booking,
            array('em' => $this->getDoctrine()->getEntityManager())
            );

        return $this->render('SkedAppBookingBundle:Booking:make.html.twig', array(
                'form' => $form->createView(),
                'booking_date' => $date,
                'booking_time_start' => $timeSlotStart,
                'booking_time_end' => $timeSlotEnd->format('H:i'),
                'booking_consultant' => $consultant->getFullName(),
                'booking_service' => $service->getName(),
                'customer' => $user,
            ));
    }

    /**
     * made booking
     *
     * @return Reponse
     */
    public function madeAction()
    {
        $this->get('logger')->info('add a new booking public');

        $user = $this->get('member.manager')->getLoggedInUser();

        $values = $this->getRequest()->get('Booking');

        $consultant = $this->get('consultant.manager')->getById($values['consultant']);
        $service = $this->get('service.manager')->getById($values['service']);

        if (is_object($consultant))
            $values['companyId'] = $consultant->getCompany()->getId();

        $booking = new Booking();
//        $booking->setStartTimeslot($this->get('timeslots.manager')->getById($values['startTimeslotString']));
//        $booking->setAppointmentDate(new \DateTime($values['appointmentDate']));

        $form = $this->createForm(new BookingMakeType(
                $values['companyId'],
                $values['consultant'],
                $values['appointmentDate'],
                $values['startTimeslot'],
                $values['service']
            ),
            $booking,
            array('em' => $this->getDoctrine()->getEntityManager())
            );

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {

                $isValid = true;
                $errMsg = "";

//                $booking->setStartTimeslot($this->get('timeslots.manager')->getById($values['startTimeslot']));
                $booking->setEndTimeslot($this->get('timeslots.manager')->getById($booking->getStartTimeslot()->getId() + $consultant->getAppointmentDuration()->getId()));

                if (!$this->get('booking.manager')->isTimeValid($booking)) {
                    $errMsg = "End time must be greater than start time";
                    $isValid = false;
                }

                if (!$this->get('booking.manager')->isBookingDateAvailable($booking)) {
                    $errMsg = "Booking not available, please choose another time.";
                    $isValid = false;
                }

                if (!$user instanceOf Customer) {
                    $errMsg = "Please register on the site and log in to create a booking.";
                    $isValid = false;
                }

                if ($isValid) {

                    //The Customer has created the booking, so it is not confirmed
                    $booking->setIsConfirmed(false);

                    $booking->setCustomer($user);

                    $this->get('booking.manager')->save($booking);
                    $this->getRequest()->getSession()->setFlash(
                        'success', 'Created booking sucessfully. You will be notified once the booking is confirmed.');

                    $options = array(
                      'booking' => $booking,
                      'link' => $this->generateUrl("sked_app_booking_edit", array('bookingId' => $booking->getId()), true)
                    );

                   //send booking created notification emails
                    $this->get("notification.manager")->createdBooking($options);

                    return $this->redirect($this->generateUrl('_welcome'));

                } else {
                    $this->getRequest()->getSession()->setFlash(
                        'error', $errMsg);
                }
            }
        } else {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Failed to create booking');
        }

        $timeSlotEnd = new \DateTime($values['startTimeslot']);
        $timeSlotEnd = $timeSlotEnd->add(new \DateInterval('PT' . $consultant->getAppointmentDuration()->getDuration() . 'M'));

        return $this->render('SkedAppBookingBundle:Booking:make.html.twig', array(
                'form' => $form->createView(),
                'booking_date' => $values['appointmentDate'],
                'booking_time_start' => $values['startTimeslot'],
                'booking_time_end' => $timeSlotEnd->format('H:i'),
                'booking_consultant' => $consultant->getFullName(),
                'booking_service' => $service->getName(),
            ));
    }

    /**
     * Edit booking
     *
     * @param integer $bookingId
     * @return Response
     */
    public function cancelBookingAction($bookingId)
    {
        $this->get('logger')->info('cancel booking id:' . $bookingId);

        try {

            $booking = $this->get('booking.manager')->getById($bookingId);
            $customer = $booking->getCustomer();
            $this->get('booking.manager')->cancelBooking($booking);

            //send cofirmation emails
            $this->get('notification.manager')->sendBookingCancellation(array('booking' => $booking));
        } catch (\Exception $e) {
            $this->get('logger')->err("booking id:$bookingId invalid");
            $this->createNotFoundException($e->getMessage());
        }

        $this->getRequest()->getSession()->setFlash(
            'success', 'Booking cancellation sucessfully');
        return $this->redirect($this->generateUrl('sked_app_customer_list_bookings', array('id' => $customer->getId())));
    }

}
