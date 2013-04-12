<?php

namespace SkedApp\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Response;
use SkedApp\BookingBundle\Form\BookingCreateType;
use SkedApp\CustomerBundle\Form\CustomerPotentialType;
use SkedApp\BookingBundle\Form\BookingUpdateType;
use SkedApp\BookingBundle\Form\BookingListFilterType;
use SkedApp\BookingBundle\Form\BookingMessageType;
use SkedApp\BookingBundle\Form\BookingSelectConsultantsType;
use SkedApp\BookingBundle\Form\BookingSelectConsultantsDateType;
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

    /**
     * Manage bookings calender view
     *
     * @Secure(roles="ROLE_CONSULTANT_ADMIN,ROLE_ADMIN")
     */
    public function manageCalenderViewAction()
    {
        $this->get('logger')->info('manage bookings calander view');

        $user = $this->get('member.manager')->getLoggedInUser();
        $company = null;

        if (!$user->isAdmin()) {
            $company = $user->getCompany();
        }

        $form = $this->createForm(new BookingSelectConsultantsType($company ? $company->getId() : null, $user->isAdmin()));

        return $this->render('SkedAppBookingBundle:Booking:manage.calender.view.html.twig', array(
                'form' => $form->createView(),
                'isAdmin' => $user->isAdmin()
            ));
    }

    /**
     * Manage bookings calender show
     *
     * @Secure(roles="ROLE_CONSULTANT_ADMIN,ROLE_ADMIN")
     */
    public function manageCalenderShowAction()
    {
        $this->get('logger')->info('manage bookings calander show');

        $user = $this->get('member.manager')->getLoggedInUser();
        $company = null;
        $consultants = null;
        $consultantsString = null;
        $consultantsInteger = array();


        if (!$user->isAdmin()) {
            $company = $user->getCompany();
        }

        $form = $this->createForm(new BookingSelectConsultantsType($company ? $company->getId() : null, $user->isAdmin()));

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $data = $form->getData();
                $selectedConsultants = $data['consultant'];

                if ($selectedConsultants->count() == 0) {
                    $this->getRequest()->getSession()->setFlash(
                        'error', 'Please select consultants');
                    return $this->redirect($this->generateUrl('sked_app_booking_manage_calender_view'));
                }

                if ($selectedConsultants->count() > 10) {
                    $this->getRequest()->getSession()->setFlash(
                        'error', 'You can only select 10 consultants at a time');
                    return $this->redirect($this->generateUrl('sked_app_booking_manage_calender_view'));
                }


                foreach ($selectedConsultants as $consultant) {
                    $consultantsInteger[] = $consultant->getId();
                }

                $session = $this->getRequest()->getSession();
                $session->set('consultants', $consultantsInteger);
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Form errors while creating booking - ' . $form->getErrorsAsString());
                return $this->redirect($this->generateUrl('sked_app_booking_manage_calender_view'));
            }
        }

        $session = $this->getRequest()->getSession();
        $consultantsInteger = $session->get('consultants');

        if (!is_array($consultantsInteger)) {
            return $this->redirect($this->generateUrl('sked_app_booking_manage_calender_view'));
        }

        for ($x = 0; $x < sizeof($consultantsInteger); $x++) {
            $consultant = $this->get('consultant.manager')->getById($consultantsInteger[$x]);
            $consultantsString .= $consultant->getId() . '-';
            $consultants[] = $consultant;
        }


        return $this->render('SkedAppBookingBundle:Booking:manage.calender.show.html.twig', array(
                'consultants' => $consultants,
                'consultanstString' => $consultantsString
            ));
    }

    /**
     * Manage bookings view
     *
     * @Secure(roles="ROLE_CONSULTANT_ADMIN,ROLE_ADMIN")
     */
    public function manageBookViewAction()
    {
        $this->get('logger')->info('manage bookings view');

        $user = $this->get('member.manager')->getLoggedInUser();
        $company = null;

        if (!$user->isAdmin()) {
            $company = $user->getCompany();
        }

        $form = $this->createForm(new BookingSelectConsultantsDateType($company ? $company->getId() : null, $user->isAdmin()));

        return $this->render('SkedAppBookingBundle:Booking:manage.booking.view.html.twig', array(
                'form' => $form->createView(),
                'isAdmin' => $user->isAdmin()
            ));
    }

    /**
     * Manage bookings view
     *
     * @Secure(roles="ROLE_CONSULTANT_ADMIN,ROLE_ADMIN")
     */
    public function manageBookShowAction($page = 1)
    {
        $this->get('logger')->info('manage bookings show');

        $user = $this->get('member.manager')->getLoggedInUser();
        $company = null;
        $consultants = null;
        $date = null;

        if (!$user->isAdmin()) {
            $company = $user->getCompany();
        }

        $form = $this->createForm(new BookingSelectConsultantsDateType($company ? $company->getId() : null, $user->isAdmin()));
        $paginator = $this->get('knp_paginator');
        $pagination = null;

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $data = $form->getData();

                $selectedConsultants = $data['consultant'];

                if ($selectedConsultants->count() == 0) {
                    $this->getRequest()->getSession()->setFlash(
                        'error', 'Please select consultants');
                    return $this->redirect($this->generateUrl('sked_app_booking_manage_view'));
                }

                if ($selectedConsultants->count() > 10) {
                    $this->getRequest()->getSession()->setFlash(
                        'error', 'You can only select 10 consultants at a time');
                    return $this->redirect($this->generateUrl('sked_app_booking_manage_view'));
                }


                //ladybug_dump($data);

                if ($data['chkDisableDate']) {
                    $date = new \DateTime($data['filterDate']);
                }

                $consultants = array();
                foreach ($selectedConsultants as $consultant) {
                    $consultants[] = $consultant->getId();
                }

                $session = $this->getRequest()->getSession();
                $session->set('consultants', $consultants);
                $session->set('filterDate', $date);
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Form errors while creating booking - ' . $form->getErrorsAsString());
                return $this->redirect($this->generateUrl('sked_app_booking_manage_view'));
            }
        }
        $session = $this->getRequest()->getSession();
        $pagination = $paginator->paginate(
            $this->get('booking.manager')->getBookingsForConsultants($session->get('consultants'), $session->get('filterDate')), $this->getRequest()->query->get('page', $page), 10
        );

        return $this->render('SkedAppBookingBundle:Booking:manage.booking.show.html.twig', array(
                'pagination' => $pagination,
            ));
    }

    /**
     * Manage bookings
     *
     * @Secure(roles="ROLE_CONSULTANT_ADMIN,ROLE_ADMIN")
     */
    public function manageBookingAction()
    {
        $this->get('logger')->info('manage bookings');

        $user = $this->get('member.manager')->getLoggedInUser();
        $company = null;
        $companyId = 0;
        $consultants = null;

        if (!$user->getIsAdmin()) {
            $company = $user->getCompany();
            $companyId = $user->getCompany()->getId();
        }

        $options = array(
            'searchText' => '',
            'sort' => 'c.id',
            'direction' => 'asc',
            'company' => $company,
        );

        $consultants = $this->get('consultant.manager')->listAll($options);

        $form = $this->createForm(new BookingListFilterType($companyId, new \DateTime()));

        return $this->render('SkedAppBookingBundle:Booking:list.html.twig', array(
                'consultants' => $consultants,
                'form' => $form->createView(),
                'companyId' => $companyId
            ));
    }

    /**
     * Reject booking 
     * 
     * @param integer $bookingId
     *
     * @Secure(roles="ROLE_CONSULTANT_ADMIN,ROLE_ADMIN")
     */
    public function rejectAction($bookingId)
    {
        $this->get('logger')->info('reject a booking');

        try {
            $booking = $this->get('booking.manager')->getById($bookingId);
            $this->get('booking.manager')->reject($booking);

            if (!$booking->getCustomerPotential()) {
                //send email
                $options = array(
                    'booking' => $booking,
                    'customerName' => $booking->getCustomer()->getFullName()
                );
                //send booking confirmation emails
                $this->get("notification.manager")->sendBookingRejected($options);
            }

            $this->getRequest()->getSession()->setFlash(
                'success', 'Booking was successfully rejected');
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request - ' . $e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_booking_manage_show'));
        }

        return $this->redirect($this->generateUrl('sked_app_booking_manage_show'));
    }

    /**
     * new booking
     *
     * @param type $agency
     * @return Reponse
     */
    public function newAction($type = 1)
    {
        $this->get('logger')->info('add a new booking');

        $user = $this->get('member.manager')->getLoggedInUser();

        $booking = new Booking();
        $customerPotential = new CustomerPotential(false);

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
        $formCustomerPotential = $this->createForm(new CustomerPotentialType(false), $customerPotential);

        return $this->render('SkedAppBookingBundle:Booking:add.html.twig', array(
                'form' => $form->createView(),
                'formCustomerPotential' => $formCustomerPotential->createView(),
                'type' => $type
            ));
    }

    /**
     * new booking
     *
     * @param type $agency
     * @return Reponse
     */
    public function createAction($type = 1)
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

                    if ((!is_object($booking->getCustomerPotential())) && (!is_object($booking->getCustomer()))) {
                        $this->getRequest()->getSession()->setFlash(
                            'error', 'Please select a customer, or complete the details of an offline customer');
                    } else {

                        $this->get('booking.manager')->save($booking);

                        $this->getRequest()->getSession()->setFlash(
                            'success', 'Created booking successfully');
                        $options = array(
                            'booking' => $booking,
                            'link' => $this->generateUrl("sked_app_booking_edit", array('bookingId' => $booking->getId()), true).".html"
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

                        if (1 == $type) {
                            return $this->redirect($this->generateUrl('sked_app_booking_manage_calender_show').".html");
                        } else {
                            return $this->redirect($this->generateUrl('sked_app_booking_manage_show').".html");
                        }
                    } //if customer and potential is null
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
                'type' => $type
            ));
    }

    /**
     * Edit booking
     *
     * @param integer $bookingId
     * @param string $page
     * @return Response
     */
    public function editAction($bookingId, $page)
    {
        $this->get('logger')->info('edit booking id:' . $bookingId);

        if ((!$this->get('security.context')->isGranted('ROLE_ADMIN')) && (!$this->get('security.context')->isGranted('ROLE_CONSULTANT_ADMIN'))) {
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
                'page' => $page
            ));
    }

    /**
     * update booking
     *
     * @param integer $bookingId
     * @param string $page
     * 
     * @return Response
     */
    public function updateAction($bookingId, $page)
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

                    if ((!is_object($booking->getCustomerPotential())) && (!is_object($booking->getCustomer()))) {
                        $this->getRequest()->getSession()->setFlash(
                            'error', 'Please select a customer, or complete the details of an offline customer');
                    } else {

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

                        if ("calender" == $page) {
                            return $this->redirect($this->generateUrl('sked_app_booking_manage_calender_show'));
                        } else {
                            return $this->redirect($this->generateUrl('sked_app_booking_manage_show'));
                        }
                    } //if customer and potential is null
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
    public function ajaxGetBookingsAction($str)
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
        $consultantsIntergerArray = array();


        $tmp = explode("-", $str);
        for ($x = 0; $x < sizeof($tmp); $x++) {
            if (is_numeric($tmp[$x])) {
                $consultantsIntergerArray[] = $tmp[$x];
            }
        }



        $bookings = $this->get('booking.manager')->getBookingsForConsultants($consultantsIntergerArray, null);

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

                    if ((isset($bookingName)) && (strlen($bookingName) > 0))
                        $bookingName = ' - ' . $bookingName;

                    $bookingName = $booking->getConsultant()->getFullName() . $bookingName;
                }

                if (is_object($booking->getCustomer())) {

                    $bookingTooltip .= '<strong>Customer:</strong> ' . $booking->getCustomer()->getFullName() . "<br />";
                    $bookingTooltip .= '<strong>Customer Contact Number:</strong> ' . $booking->getCustomer()->getMobileNumber() . "<br />";
                    $bookingTooltip .= '<strong>Customer E-Mail:</strong> ' . $booking->getCustomer()->getEmail() . "<br />";

                    if ((isset($bookingName)) && (strlen($bookingName) > 0))
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
                    'url' => $this->generateUrl("sked_app_booking_edit", array("bookingId" => $booking->getId(), "page" => 'calender')) . ".html",
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
            $consultants = array();

            for ($y = 0; $y < sizeof($consultantsIntergerArray); $y++) {
                $consultants[] = $this->get('consultant.manager')->getById($consultantsIntergerArray[$y]);
            }

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
                                    'type'=> 1, 
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

        if ((!$this->get('security.context')->isGranted('ROLE_ADMIN')) && (!$this->get('security.context')->isGranted('ROLE_CONSULTANT_ADMIN'))
            && (!$this->get('security.context')->isGranted('ROLE_CONSULTANT_USER'))) {
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

        if (($this->get('security.context')->isGranted('ROLE_CONSULTANT_ADMIN')) && (!$this->get('security.context')->isGranted('ROLE_ADMIN')))
            $companyId = $user->getCompany()->getId();

        $startDate = new \DateTime($filterDate->format('Y-m-d 00:00:00'));
        $endDate = new \DateTime($filterDate->format('Y-m-d 23:59:59'));

        $em = $this->getDoctrine()->getEntityManager();
        $bookings = $em->getRepository('SkedAppCoreBundle:Booking')->getAllConsultantBookingsByDate($consultantId, $startDate, $endDate, $companyId);

        $form = $this->createForm(new BookingMessageType());

        return $this->render('SkedAppBookingBundle:Booking:ajax.list.html.twig', array(
                'bookings' => $bookings,
                'filterDate' => $filterDate->format('j F Y'),
                'print' => true,
                'form' => $form->createView(),
                'consultantId' => ($consultantId + 0)
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

        try {
            $errMsg = null;
            $user = $this->get('member.manager')->getLoggedInUser();

            if (($companyId == 0) || ($consultantId == 0)) {
                return $this->redirect('sked_app_booking_manager');
            }


            //Format the date correctly
            $bookingStartTime = new \DateTime($date . ' ' . $timeSlotStart);
            $date = $bookingStartTime->format('d-m-Y');




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

            //calculate endtime by factoring service duration
            $bookingEndTime = new \DateTime($date . ' ' . $timeSlotStart);
            $serviceDuration = $service->getAppointmentDuration()->getDuration();
            $serviceDurationInterval = new \DateInterval("PT" . $serviceDuration . "M");
            $bookingEndTime = $bookingEndTime->add($serviceDurationInterval);

            //check inf booking is available
            $isValid = $this->get('booking.manager')->isBookingAvailable(
                $consultant, $bookingStartTime, $bookingEndTime
            );



            if (!$isValid) {
                $errMsg = "Booking not available,some services take longer than other - please choose another time.";
            } else {
                //check if booking does not conflict with consultant log oof time
                $consultantLogOffTime = null;
                $consultantEndTimeSlot = $consultant->getEndTimeslot();

                $endOfDayTime = new \DateTime($date . ' ' . $consultantEndTimeSlot->getSlot());

                if ($bookingEndTime > $endOfDayTime) {
                    $isValid = false;
                    $errMsg = "Booking not available, the service you have chosen violates consultant's closing hours - please choose another time.";
                }
            }

            if (!$isValid) {

                $this->getRequest()->getSession()->setFlash(
                    'error', $errMsg);
            }

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
            $infoWindow->setContent('<p>' . $consultant->getCompany()->getName() . '<br/><small>Telphone: </p>');
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
            $marker->setPosition($consultant->getCompany()->getLat(), $consultant->getCompany()->getLng(), true);
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
                    'booking_time_end' => $bookingEndTime->format('H:i'),
                    'consultant' => $consultant,
                    'booking_service' => $service->getName(),
                    'customer' => $user,
                    'map' => $map,
                    'isValid' => $isValid
                ));
        } catch (\Exception $e) {
            
        }
    }

    /**
     * made booking
     *
     * @return Reponse
     */
    public function madeAction()
    {
        $this->get('logger')->info('add a new booking public');

        try {
            $user = $this->get('member.manager')->getLoggedInUser();

            $values = $this->getRequest()->get('Booking');

            $consultant = $this->get('consultant.manager')->getById($values['consultant']);
            $service = $this->get('service.manager')->getById($values['service']);

            if (is_object($consultant))
                $values['companyId'] = $consultant->getCompany()->getId();

            $booking = new Booking();

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



                    //calculate end time
                    $booking->getStartTimeslot()->getId();

                    //calculate endtime by factoring service duration
                    $bookingEndTime = new \DateTime($values['appointmentDate'] . ' ' . $values['startTimeslot']);
                    $serviceDuration = $service->getAppointmentDuration()->getDuration();
                    $serviceDurationInterval = new \DateInterval("PT" . $serviceDuration . "M");
                    $bookingEndTime = $bookingEndTime->add($serviceDurationInterval);


                    $booking->setEndTimeslot($this->get('timeslots.manager')->getByTime($bookingEndTime->format('H:i')));


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
                            'link' => $this->generateUrl("sked_app_booking_edit", array('bookingId' => $booking->getId()), true) . ".html"
                        );

                        try {
                            //send booking created notification emails
                            $this->get("notification.manager")->createdBooking($options);
                        } catch (Exception $e) {
                            $this->getRequest()->getSession()->setFlash(
                                'error', $e->getMessage());
                        }
                        return $this->redirect($this->generateUrl('sked_app_customer_booking_details', array('id' => $booking->getId())));
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
            $infoWindow->setContent('<p>' . $consultant->getCompany()->getName() . '<br/><small>Telphone: </p>');
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
            $marker->setPosition($consultant->getCompany()->getLat(), $consultant->getCompany()->getLng(), true);
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
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Confirm  booking
     * 
     * @param integer $bookingId
     * @return type
     */
    public function confirmAction($bookingId)
    {
        $this->get('logger')->info('confirm booking id:' . $bookingId);

        try {

            $booking = $this->get('booking.manager')->getById($bookingId);
            $booking->setIsConfirmed(true);
            $this->get('booking.manager')->save($booking);

            $options = array(
                'booking' => $booking,
                'link' => $this->generateUrl("sked_app_booking_edit", array('bookingId' => $booking->getId()), true)
            );
            //send booking confirmation emails
            $this->get("notification.manager")->confirmationBooking($options);
            $this->getRequest()->getSession()->setFlash(
                'success', 'Booking was successfully confirmed');
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request - ' . $e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_booking_manage_show'));
        }

        return $this->redirect($this->generateUrl('sked_app_booking_manage_show'));
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

    /**
     * Edit booking
     *
     * @param integer $bookingId
     * @return Response
     */
    public function cancelAction($bookingId)
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
        return $this->redirect($this->generateUrl('sked_app_booking_manage_show'));
    }

    /**
     * Send message to customers
     *
     * @Secure(roles="ROLE_CONSULTANT_ADMIN,ROLE_ADMIN")
     */
    public function sendMessageAction($str)
    {
        $this->get('logger')->info('Send message/s to booking customers');

        $form = $this->createForm(new BookingMessageType($str));

        return $this->render('SkedAppBookingBundle:Booking:manage.booking.send.message.html.twig', array(
                'form' => $form->createView(),
                'str' => $str
            ));
    }

    /**
     * Send message to customers
     *
     * @Secure(roles="ROLE_CONSULTANT_ADMIN,ROLE_ADMIN")
     */
    public function sendBookingMessageAction($str)
    {
        $this->get('logger')->info('Send message/s to booking customers');

        $form = $this->createForm(new BookingMessageType($str));



        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $data = $form->getData();
                $str = $data['bookings'];
                $tmp = array();
                if (is_numeric($str)) {
                    $tmp[] = $str;
                } else {
                    $tmp = explode('-', $str);
                }

                //get bookings and send message
                for ($x = 0; $x < sizeof($tmp); $x++) {
                    $booking = $this->get('booking.manager')->getById($tmp[$x]);

                    $options = array(
                        'booking' => $booking,
                        'messageText' => strip_tags($data['message']),
                        'link' => $this->generateUrl("sked_app_customer_list_bookings", array('id' => $booking->getCustomer()->getId()), true)
                    );

                    //send booking message notification emails
                    $this->get("notification.manager")->messageBooking($options);
                }


                $this->getRequest()->getSession()->setFlash(
                    'success', sizeof($tmp) . ' messages were successfully sent.');
                return $this->redirect($this->generateUrl('sked_app_booking_manage_show'));
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Form errors while creating booking - ' . $form->getErrorsAsString());
            }
        }

        return $this->render('SkedAppBookingBundle:Booking:manage.booking.send.message.html.twig', array(
                'form' => $form->createView(),
            ));
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

        if (($this->get('security.context')->isGranted('ROLE_CONSULTANT_USER')) && (!$this->get('security.context')->isGranted('ROLE_ADMIN'))) {

            $user = $this->get('member.manager')->getLoggedInUser();

            return $this->redirect($this->generateUrl('sked_app_consultant_show', array('slug' => $user->getSlug())));
        } else {
            return $this->redirect($this->generateUrl('sked_app_booking_manager'));
        }
    }

}
