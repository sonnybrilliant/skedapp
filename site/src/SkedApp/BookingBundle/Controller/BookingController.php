<?php

namespace SkedApp\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use SkedApp\BookingBundle\Form\BookingCreateType;
use SkedApp\CustomerBundle\Form\CustomerPotentialType;
use SkedApp\BookingBundle\Form\BookingUpdateType;
use SkedApp\BookingBundle\Form\BookingListFilterType;
use SkedApp\BookingBundle\Form\BookingMessageType;
use SkedApp\CoreBundle\Entity\Booking;
use SkedApp\CoreBundle\Entity\Customer;
use SkedApp\CoreBundle\Entity\CustomerPotential;
use SkedApp\CoreBundle\Entity\Timeslots;
use SkedApp\CoreBundle\Entity\Consultant;
use SkedApp\BookingBundle\Form\BookingMakeType;
use Ivory\GoogleMap\Overlays\Animation;
use Ivory\GoogleMap\MapTypeId;
use Ivory\GoogleMap\Events\MouseEvent;

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

        $user = $this->get('member.manager')->getLoggedInUser();
        $company = null;

        if ( ($this->get('security.context')->isGranted('ROLE_CONSULTANT_ADMIN')) && (!$this->get('security.context')->isGranted('ROLE_ADMIN')) )
            $company = $user->getCompany();

        $em = $this->getDoctrine()->getEntityManager();
        $consultants = $em->getRepository('SkedAppCoreBundle:Consultant')->getAllActiveQuery(array('company' => $company));

        if (is_object($company))
            $companyId = $user->getCompany()->getId();
        else
            $companyId = 0;

        $form = $this->createForm(new BookingListFilterType($companyId, new \DateTime()));

        return $this->render('SkedAppBookingBundle:Booking:list.html.twig', array(
                'consultants' => $consultants,
                'form' => $form->createView(),
                'companyId' => $companyId
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
        $customerPotential = new CustomerPotential();

        $bookingValues = $this->getRequest()->get('Booking');

        if (!isset($bookingValues['appointmentDate']))
            $bookingValues['appointmentDate'] = date('Y-m-d');

        if (isset($bookingValues['startTimeslot']))
            $booking->setStartTimeslot($this->get('timeslots.manager')->getById($bookingValues['startTimeslot']));

        if (isset($bookingValues['endTimeslot']))
            $booking->setEndTimeslot($this->get('timeslots.manager')->getById($bookingValues['endTimeslot']));

        if (isset($bookingValues['consultant']))
            $booking->setConsultant($this->get('consultant.manager')->getById($bookingValues['consultant']));

        $form = $this->createForm(new BookingCreateType(
                $user->getCompany()->getId(),
                $this->get('member.manager')->isAdmin(),
                new \DateTime($bookingValues['appointmentDate'])
            ), $booking);
        $formCustomerPotential = $this->createForm(new CustomerPotentialType(), $customerPotential);

        return $this->render('SkedAppBookingBundle:Booking:add.html.twig', array(
                'form' => $form->createView(),
                'formCustomerPotential' => $formCustomerPotential->createView(),
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
        $customerPotential = new CustomerPotential();
        $form = $this->createForm(new BookingCreateType(
                $user->getCompany()->getId(),
                $this->get('member.manager')->isAdmin()
            ), $booking);
        $formCustomerPotential = $this->createForm(new CustomerPotentialType(), $customerPotential);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            $formCustomerPotential->bindRequest($this->getRequest());

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

                    if (strlen($customerPotential->getFirstName()) > 0) {
                        $this->get('customer.potential.manager')->update($customerPotential);
                        $booking->setCustomerPotential($customerPotential);
                    }

                    $this->get('booking.manager')->save($booking);

                    $this->getRequest()->getSession()->setFlash(
                        'success', 'Created booking successfully');
                    $options = array(
                        'booking' => $booking,
                        'link' => $this->generateUrl("sked_app_booking_edit", array('bookingId' => $booking->getId()), true)
                    );

                    if (is_object($booking->getCustomer())) {
                        if ($booking->getIsConfirmed()) {
                            //send booking confirmation emails
                            $this->get("notification.manager")->confirmationBooking($options);
                        } else {
                            //send booking created notification emails
                            $this->get("notification.manager")->createdByCompanyBooking($options);
                        }
                    }

                    return $this->redirect($this->generateUrl('sked_app_booking_manager'));
                } else {
                    $this->getRequest()->getSession()->setFlash(
                        'error', $errMsg);
                }
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Form errors while creating booking - ' . $form->getErrorsAsString());
            }
        } else {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Failed to create booking');
        }

        return $this->render('SkedAppBookingBundle:Booking:add.html.twig', array(
                'form' => $form->createView(),
                'formCustomerPotential' => $formCustomerPotential->createView(),
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
            $customerPotential = $booking->getCustomerPotential();

            $form = $this->createForm(new BookingUpdateType(
                    $user->getCompany()->getId(),
                    $this->get('member.manager')->isAdmin()
                ), $booking);
            $formCustomerPotential = $this->createForm(new CustomerPotentialType(false), $customerPotential);

        } catch (\Exception $e) {
            $this->get('logger')->err("booking id:$bookingId invalid");
            $this->createNotFoundException($e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_booking_manager'));
        }

        $customer = new Customer();

        if (is_object($booking->getCustomer()))
            $customer = $booking->getCustomer();

        return $this->render('SkedAppBookingBundle:Booking:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $booking->getId(),
                'customer' => $customer,
                'formCustomerPotential' => $formCustomerPotential->createView(),
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

            $customerPotential = $booking->getCustomerPotential();

            if (!is_object($customerPotential))
                $customerPotential = new CustomerPotential();

            $form = $this->createForm(new BookingUpdateType(
                    $user->getCompany()->getId(),
                    $this->get('member.manager')->isAdmin()
                ), $booking);
            $formCustomerPotential = $this->createForm(new CustomerPotentialType(false), $customerPotential);

            if ($this->getRequest()->getMethod() == 'POST') {
                $form->bindRequest($this->getRequest());
                $formCustomerPotential->bindRequest($this->getRequest());

                if ($form->isValid()) {

                    if (strlen($customerPotential->getFirstName()) > 0) {
                        $this->get('customer.potential.manager')->update($customerPotential);
                        $booking->setCustomerPotential($customerPotential);
                        $booking->setCustomer(null);
                    }

                    $this->get('booking.manager')->save($booking);
                    $this->getRequest()->getSession()->setFlash(
                        'success', 'Updated booking successfully');

                    if ((!$oldIsConfirmed) && ($booking->getIsConfirmed())) {
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
            $this->get('logger')->err("booking id:$bookingId invalid");
            $this->getRequest()->getSession()->setFlash(
                        'error', $e->getMessage());
            $this->createNotFoundException($e->getMessage());
        }

        if (!is_object($booking->getCustomer()))
            $booking->setCustomer(new Customer());

        return $this->render('SkedAppBookingBundle:Booking:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $booking->getId(),
                'customer' => $booking->getCustomer(),
                'formCustomerPotential' => $formCustomerPotential->createView(),
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
                'success', 'Deleted booking successfully');
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
        $company = null;

        $user = $this->get('member.manager')->getLoggedInUser();

        if ( ($this->get('security.context')->isGranted('ROLE_CONSULTANT_ADMIN')) && (!$this->get('security.context')->isGranted('ROLE_ADMIN')) )
            $company = $user->getCompany();

        $bookings = $this->get("booking.manager")->getAllBetweenDates($startSlotsDateTime, $endSlotsDateTime, $company);

        if (($endSlotsDateTime->getTimeStamp() - $startSlotsDateTime->getTimestamp()) >= (60 * 60 * 24 * 28)) {
            $isMonth = true;
        } else {
            $isMonth = false;
        }

        if ($bookings) {
            foreach ($bookings as $booking) {
                $allDay = false;

                $bookingName = '';

                if (!$isMonth) {
                    if (true == $booking->getIsLeave()) {
                        $allDay = true;
                        $bookingName = "On leave";
                    } else {
                        if (is_object($booking->getService()))
                            $bookingName = $booking->getService()->getName();
                        else
                            $bookingName = 'Unknown Service';
                    }
                }

                $bookingTooltip = '<div class="divBookingTooltip">';

                if (is_object($booking->getConsultant())) {

                    if ((isset($bookingName)) && (strlen($bookingName) > 0) )
                        $bookingName = ' - ' . $bookingName;

                    $bookingName = $booking->getConsultant()->getFullName() . $bookingName;
                }

                if (is_object($booking->getCustomer())) {

                    $bookingTooltip .= '<strong>Customer:</strong> ' . $booking->getCustomer()->getFullName() . "<br />";
                    $bookingTooltip .= '<strong>Customer Contact Number:</strong> ' . $booking->getCustomer()->getMobileNumber() . "<br />";
                    $bookingTooltip .= '<strong>Customer E-Mail:</strong> ' . $booking->getCustomer()->getEmail() . "<br />";

                    if ((isset($bookingName)) && (strlen($bookingName) > 0) )
                        $bookingName = ' - ' . $bookingName;

                    $bookingName = $booking->getCustomer()->getFullName() . $bookingName;
                }

                $bookingTooltip .= '<strong>Start Time:</strong> ' . $booking->getHiddenAppointmentStartTime()->format("H:i") . "<br />";
                $bookingTooltip .= '<strong>End Time:</strong> ' . $booking->getHiddenAppointmentEndTime()->format("H:i") . "<br />";
                $bookingTooltip .= '<strong>Confirmed:</strong> ' . $booking->getIsConfirmedString() . "<br />";

                if (is_object($booking->getConsultant())) {
                    $bookingTooltip .= '<strong>Consultant:</strong> ' . $booking->getConsultant()->getFullName() . "<br />";
                    $bookingTooltip .= '<strong>Consultant E-Mail:</strong> ' . $booking->getConsultant()->getEmail() . "<br />";
                }

                if (is_object($booking->getService()))
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

        if ((!is_null($start)) && (!is_null($end))) {
            //Adding empty slots
            $consultants = $this->get("consultant.manager")->listAll(array('sort' => 'c.lastName', 'direction' => 'Asc', 'company' => $company));

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

                if (($isSingleDay) && ($endSlotsDateTime->getTimestamp() > time())) {
                    //If its a single day, add empty slots for each resource
                    //Make sure start time slot is in the future
                    while ($startSlotsDateTime->getTimestamp() < time()) {

                        $durationInterval = new \DateInterval('PT' . $consultant->getAppointmentDuration()->getDuration() . 'M');
                        $startSlotsDateTime->add($durationInterval);
                    }

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

                        unset($booking);

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
                                'url' => $this->generateUrl("sked_app_booking_new", array(
                                    'Booking[appointmentDate]' => $startSlot->format("Y-m-d"),
                                    'Booking[startTimeslot]' => $this->get('timeslots.manager')->getByTime($startSlot->format('H:i'))->getId(),
                                    'Booking[endTimeslot]' => $this->get('timeslots.manager')->getByTime($endSlot->format('H:i'))->getId(),
                                    'Booking[consultant]' => $consultant->getId(),
                                )),
                                'description' => $bookingTooltip,
                                'className' => 'addBookingTimeSlot'
                            );
                        } //if slot is available

                        $startSlotsDateTime->add($durationInterval);

                        unset($startSlot);
                        unset($endSlot);
                    } //while
                } //if is a single day
            } //foreach consultant

            if ((!$isSingleDay) && ($latestEnd->getTimestamp() > time())) {

                //Make sure start time slot is more than 2 hours in the future
                while ($earliestStart->getTimestamp() < (time() + (60 * 60 * 2))) {

                    $durationInterval = new \DateInterval('PT15M');
                    $earliestStart->add($durationInterval);
                }

                while ($earliestStart->getTimestamp() < $latestEnd->getTimestamp()) {
                    //Loop through the timeslots for each day and check if the consultant is available

                    $durationInterval = new \DateInterval('PT15M');

                    $startSlot = new \DateTime($earliestStart->format('Y-m-d 06:00:00'));
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
                        'url' => $this->generateUrl("sked_app_booking_new", array(
                            'Booking[appointmentDate]' => $startSlot->format("Y-m-d")
                        )),
                        'description' => $bookingTooltip,
                        'className' => 'addBookingTimeSlot'
                    );

                    $earliestStart->add($durationInterval);

                    unset($startSlot);
                    unset($endSlot);
                } //while
            } //Single day
        } //Dates are set

        $response = new Response(json_encode($results));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     *  Get active bookings by Company and/ or Consultant and/ or Date
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function ajaxGetBookingsListAction()
    {
        $this->get('logger')->info('see list of bookings');

        if ((!$this->get('security.context')->isGranted('ROLE_ADMIN')) && (!$this->get('security.context')->isGranted('ROLE_CONSULTANT_USER'))) {
            $this->get('logger')->warn('Ajax list bookings, access denied.');
            throw new AccessDeniedException();
        }

        if (strtotime($this->getRequest()->get('filterDate')) > 0)
            $filterDate = new \DateTime($this->getRequest()->get('filterDate'));
        else
            $filterDate = new \DateTime();

        $companyId = $this->getRequest()->get('company', 0);
        $consultantId = $this->getRequest()->get('consultant', 0);

        $user = $this->get('member.manager')->getLoggedInUser();

        if ( ($this->get('security.context')->isGranted('ROLE_CONSULTANT_ADMIN')) && (!$this->get('security.context')->isGranted('ROLE_ADMIN')) )
            $companyId = $user->getCompany()->getId();

        $startDate = new \DateTime($filterDate->format('Y-m-d 00:00:00'));
        $endDate = new \DateTime($filterDate->format('Y-m-d 23:59:59'));

        $em = $this->getDoctrine()->getEntityManager();
        $bookings = $em->getRepository('SkedAppCoreBundle:Booking')->getAllConsultantBookingsByDate($consultantId, $startDate, $endDate, $companyId);

        if (is_object($user->getCompany()))
            $companyId = $user->getCompany()->getId();
        else
            $companyId = 0;

        $form = $this->createForm(new BookingMessageType());

        return $this->render('SkedAppBookingBundle:Booking:ajax.list.html.twig', array(
                'bookings' => $bookings,
                'filterDate' => $filterDate->format('j F Y'),
                'print' => true,
                'form' => $form->createView(),
                'consultantId' => $consultantId
            ));
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

        if(($companyId == 0) || ($consultantId == 0)){
            return $this->redirect('sked_app_booking_manager');
        }


        //Format the date correctly
        $date = new \DateTime($date);
        $date = $date->format('d-m-Y');

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
            ), $booking, array('em' => $this->getDoctrine()->getEntityManager())
        );



         $infoWindow = $this->get('ivory_google_map.info_window');

        // Configure your info window options
        $infoWindow->setPrefixJavascriptVariable('info_window_');
        $infoWindow->setPosition(0, 0, true);
        $infoWindow->setPixelOffset(1.1, 2.1, 'px', 'pt');
        $infoWindow->setContent('<p>'.$consultant->getCompany()->getName().'<br/><small>Telphone: </p>');
        $infoWindow->setOpen(false);
        $infoWindow->setAutoOpen(true);
        $infoWindow->setOpenEvent(MouseEvent::CLICK);
        $infoWindow->setAutoClose(false);
        $infoWindow->setOption('disableAutoPan', true);
        $infoWindow->setOption('zIndex', 10);
        $infoWindow->setOptions(array(
            'disableAutoPan' => true,
            'zIndex' => 10
        ));



        $marker = $this->get('ivory_google_map.marker');


        // Configure your marker options
        $marker->setPrefixJavascriptVariable('marker_');
        $marker->setPosition($consultant->getCompany()->getLat(), $consultant->getCompany()->getLng(),true);
        $marker->setAnimation(Animation::DROP);
        $marker->setOptions(array(
            'clickable' => true,
            'flat' => true
        ));
        $marker->setIcon('/img/assets/icons/skedapp-map-icon.png');
        $marker->setShadow('/img/assets/icons/skedapp-map-icon.png');

        $map = $this->get('ivory_google_map.map');
        // Configure your map options
        $map->setPrefixJavascriptVariable('map_');
        $map->setHtmlContainerId('map_canvas');

        $map->setAsync(false);

        $map->setAutoZoom(false);

        $map->setCenter($consultant->getCompany()->getLat(), $consultant->getCompany()->getLng(), true);
        $map->setMapOption('zoom', 16);

        $map->setBound(0, 0, 0, 0, false, false);

        // Sets your map type
        $map->setMapOption('mapTypeId', MapTypeId::ROADMAP);
        $map->setMapOption('mapTypeId', 'roadmap');

        $map->setMapOption('disableDefaultUI', false);
        $map->setMapOption('disableDoubleClickZoom', false);
        $map->setStylesheetOptions(array(
            'width' => '96%',
            'height' => '372px'
        ));

        $map->setLanguage('en');


        $map->addMarker($marker);
        $marker->setInfoWindow($infoWindow);

        return $this->render('SkedAppBookingBundle:Booking:make.html.twig', array(
                'form' => $form->createView(),
                'booking_date' => $date,
                'booking_time_start' => $timeSlotStart,
                'booking_time_end' => $timeSlotEnd->format('H:i'),
                'consultant' => $consultant,
                'booking_service' => $service->getName(),
                'customer' => $user,
                'map' => $map
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
            ), $booking, array('em' => $this->getDoctrine()->getEntityManager())
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
                        'success', 'Created booking successfully. You will be notified once the booking is confirmed.');

                    $options = array(
                        'booking' => $booking,
                        'link' => $this->generateUrl("sked_app_booking_edit", array('bookingId' => $booking->getId()), true)
                    );

                    //send booking created notification emails
                    $this->get("notification.manager")->createdBooking($options);

                    return $this->redirect($this->generateUrl('sked_app_customer_booking_details',array('id'=>$booking->getId())));
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



         $infoWindow = $this->get('ivory_google_map.info_window');

        // Configure your info window options
        $infoWindow->setPrefixJavascriptVariable('info_window_');
        $infoWindow->setPosition(0, 0, true);
        $infoWindow->setPixelOffset(1.1, 2.1, 'px', 'pt');
        $infoWindow->setContent('<p>'.$consultant->getCompany()->getName().'<br/><small>Telphone: </p>');
        $infoWindow->setOpen(false);
        $infoWindow->setAutoOpen(true);
        $infoWindow->setOpenEvent(MouseEvent::CLICK);
        $infoWindow->setAutoClose(false);
        $infoWindow->setOption('disableAutoPan', true);
        $infoWindow->setOption('zIndex', 10);
        $infoWindow->setOptions(array(
            'disableAutoPan' => true,
            'zIndex' => 10
        ));



        $marker = $this->get('ivory_google_map.marker');


        // Configure your marker options
        $marker->setPrefixJavascriptVariable('marker_');
        $marker->setPosition($consultant->getCompany()->getLat(), $consultant->getCompany()->getLng(),true);
        $marker->setAnimation(Animation::DROP);
        $marker->setOptions(array(
            'clickable' => true,
            'flat' => true
        ));
        $marker->setIcon('http://maps.gstatic.com/mapfiles/markers/marker.png');
        $marker->setShadow('http://maps.gstatic.com/mapfiles/markers/marker.png');

        $map = $this->get('ivory_google_map.map');
        // Configure your map options
        $map->setPrefixJavascriptVariable('map_');
        $map->setHtmlContainerId('map_canvas');

        $map->setAsync(false);

        $map->setAutoZoom(false);

        $map->setCenter($consultant->getCompany()->getLat(), $consultant->getCompany()->getLng(), true);
        $map->setMapOption('zoom', 16);

        $map->setBound(0, 0, 0, 0, false, false);

        // Sets your map type
        $map->setMapOption('mapTypeId', MapTypeId::ROADMAP);
        $map->setMapOption('mapTypeId', 'roadmap');

        $map->setMapOption('disableDefaultUI', false);
        $map->setMapOption('disableDoubleClickZoom', false);
        $map->setStylesheetOptions(array(
            'width' => '100%',
            'height' => '300px'
        ));

        $map->setLanguage('en');


        $map->addMarker($marker);
        $marker->setInfoWindow($infoWindow);

        return $this->render('SkedAppBookingBundle:Booking:make.html.twig', array(
                'form' => $form->createView(),
                'booking_date' => $values['appointmentDate'],
                'booking_time_start' => $values['startTimeslot'],
                'booking_time_end' => $timeSlotEnd->format('H:i'),
                'consultant' => $consultant,
                'booking_service' => $service->getName(),
                'customer' => $user,
                'map' => $map
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
            'success', 'Booking cancellation successfully');
        return $this->redirect($this->generateUrl('sked_app_customer_list_bookings', array('id' => $customer->getId())));
    }

    public function messagesAction()
    {
        $this->get('logger')->info('Send messages and/ or delete selected bookings');

        if ((!$this->get('security.context')->isGranted('ROLE_ADMIN')) && (!$this->get('security.context')->isGranted('ROLE_CONSULTANT_USER'))) {
            $this->get('logger')->warn('Send messages and/ or delete selected bookings, access denied.');
            throw new AccessDeniedException();
        }

        $bookingsSelected = $this->getRequest()->get('selectBookings', array());
        $bookingsCancel = $this->getRequest()->get('cancelBookings', array());
        $bookingMessage = $this->getRequest()->get('BookingMessage', array('messageText' => ''));
        $messageString = '';

        if ((count($bookingsSelected) > 0) || (count($bookingsCancel) > 0)) {
            //Some Bookings selected or marked for delete

            $countSelectedBookings = 0;
            $countCancelBookings = 0;
            $sendAndCancel = array();

            if (count($bookingsSelected) > 0) {

                foreach ($bookingsSelected as $bookingId) {

                    $booking = $this->get('booking.manager')->getById($bookingId);

                    if (in_array($bookingId, $bookingsCancel)) {
                        //Booking must also be cancelled

                        $options = array(
                            'booking' => $booking,
                            'messageText' => $bookingMessage['messageText'],
                        );

                        //send booking message notification emails
                        $this->get("notification.manager")->messageAndCancelBooking($options);

                        $sendAndCancel[] = $bookingId;

                        $countCancelBookings++;
                    } else {

                        $options = array(
                            'booking' => $booking,
                            'messageText' => $bookingMessage['messageText'],
                            'link' => $this->generateUrl("sked_app_customer_list_bookings", array('id' => $booking->getCustomer()->getId()), true)
                        );

                        //send booking message notification emails
                        $this->get("notification.manager")->messageBooking($options);
                    }

                    $countSelectedBookings++;
                }

                if ($countSelectedBookings == 1)
                    $messageString = sprintf('Sent messages for %s booking. ', $countSelectedBookings);
                else
                    $messageString = sprintf('Sent messages for %s bookings. ', $countSelectedBookings);
            }

            if (count($bookingsCancel) > 0) {

                foreach ($bookingsSelected as $bookingId) {

                    $booking = $this->get('booking.manager')->getById($bookingId);

                    $this->get('booking.manager')->cancelBooking($booking);

                    if (!in_array($bookingId, $sendAndCancel)) {
                        //No message was selected, just send cancellation
                        $this->get('notification.manager')->sendBookingCancellation(array('booking' => $booking));
                    }

                    $countCancelBookings++;
                }

                if ($countCancelBookings == 1)
                    $messageString .= sprintf('Sent messages for %s cancelled booking. ', $countSelectedBookings);
                else
                    $messageString .= sprintf('Sent messages for %s cancelled bookings. ', $countSelectedBookings);
            }

            $this->getRequest()->getSession()->setFlash('success', $messageString);
        } else {
            //No bookings selected
            $this->getRequest()->getSession()->setFlash('error', 'Please select at least one booking.');
        }

        if ($this->get('security.context')->isGranted('ROLE_CONSULTANT_USER')) {

            $user = $this->get('member.manager')->getLoggedInUser();

            return $this->redirect($this->generateUrl('sked_app_consultant_booking_show', array('id' => $user->getId())));
        } else {
            return $this->redirect($this->generateUrl('sked_app_booking_manager'));
        }
    }

}
