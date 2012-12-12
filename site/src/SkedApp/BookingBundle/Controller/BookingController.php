<?php

namespace SkedApp\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use SkedApp\BookingBundle\Form\BookingCreateType;
use SkedApp\BookingBundle\Form\BookingMakeType;
use SkedApp\BookingBundle\Form\BookingUpdateType;
use SkedApp\CoreBundle\Entity\Booking;

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

        $em = $this->getDoctrine()->getEntityManager();
        $consultants = $em->getRepository('SkedAppCoreBundle:Consultant')->getAllActiveQuery();

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
        $form = $this->createForm(new BookingCreateType(
                $user->getCompany()->getId(),
                $this->get('member.manager')->isAdmin()
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
                    
                    //send emails
                    $this->get("notification.manager")->confirmationBookingCompany($options);
                    $this->get("notification.manager")->confirmationBookingCustomer($options);
                  
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

        return $this->render('SkedAppBookingBundle:Booking:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $booking->getId()
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

        $bookings = $this->get("booking.manager")->getAll();

        if ($bookings) {
            foreach ($bookings as $booking) {
                $allDay = false;


                if (true == $booking->getIsLeave()) {
                    $allDay = true;
                    $bookingName = "On leave";
                } else {
                    $bookingName = $booking->getService()->getName();
                }

                $results[] = array(
                    'allDay' => $allDay,
                    'title' => $bookingName,
                    'start' => $booking->getHiddenAppointmentStartTime()->format("c"),
                    'end' => $booking->getHiddenAppointmentEndTime()->format("c"),
                    //'start' => "2012-11-29",
                    'resourceId' => 'resource-' . $booking->getConsultant()->getId(),
                    'url' => $this->generateUrl("sked_app_booking_edit", array("bookingId" => $booking->getId())),
                    //'color' => 'pink',
                    //'textColor' => 'black'
                );
            }
        }

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
    public function makeAction($companyId, $consultantId, $date, $timeSlotStart, $serviceIds = array())
    {
        $this->get('logger')->info('add a new booking public');

        $user = $this->get('member.manager')->getLoggedInUser();

        $booking = new Booking();

        $booking->setConsultant($this->get('consultant.manager')->getById($consultantId));
        $booking->setStartTimeslot($this->get('timeslots.manager')->getByTime($timeSlotStart));

        $form = $this->createForm(new BookingMakeType(
                $companyId,
                $consultantId,
                $date,
                $timeSlotStart,
                $serviceIds
            ), $booking);

        return $this->render('SkedAppBookingBundle:Booking:make.html.twig', array(
                'form' => $form->createView(),
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

        $objConsultant = $this->get('consultant.manager')->getById($values['consultant']);

        if (is_object($objConsultant))
            $values['companyId'] = $objConsultant->getCompany()->getId();

        $booking = new Booking();

        $form = $this->createForm(new BookingMakeType(
                $values['companyId'],
                $values['consultant'],
                $values['appointmentDate'],
                $values['startTimeslot'],
                $values['service']
            ), $booking);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {

                $isValid = true;
                $errMsg = "";

                $booking->setEndTimeslot($this->get('timeslots.manager')->getById($booking->getStartTimeslot()->getId() + $objConsultant->getAppointmentDuration()->getId()));

                if (!$this->get('booking.manager')->isTimeValid($booking)) {
                    $errMsg = "End time must be greater than start time";
                    $isValid = false;
                }

                if (!$this->get('booking.manager')->isBookingDateAvailable($booking)) {
                    $errMsg = "Booking not available, please choose another time.";
                    $isValid = false;
                }

                if ($isValid) {

//                    $booking->setStatus($this->get('status.manager')->confirmed());

                    $this->get('booking.manager')->save($booking);
                    $this->getRequest()->getSession()->setFlash(
                        'success', 'Created booking sucessfully');
                    return $this->redirect($this->generateUrl('sked_app_search_index'));
                } else {
                    $this->getRequest()->getSession()->setFlash(
                        'error', $errMsg);
                }
            } else {

                echo $appointmentDate->format('Y-m-d') . ' invalid ' . $form->getErrorsAsString();
                exit;
            }
        } else {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Failed to create booking');
        }

        return $this->render('SkedAppBookingBundle:Booking:make.html.twig', array(
                'form' => $form->createView(),
            ));
    }

}
