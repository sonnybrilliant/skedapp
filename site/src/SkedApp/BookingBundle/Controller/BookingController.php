<?php

namespace SkedApp\BookingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use SkedApp\BookingBundle\Form\BookingCreateType;
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
            ), 
            $booking);

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
            ), 
            $booking);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $booking->setStatus($this->get('status.manager')->confirmed());
                $this->get('booking.manager')->save($booking);
                $this->getRequest()->getSession()->setFlash(
                    'success', 'Created booking sucessfully');
                return $this->redirect($this->generateUrl('sked_app_booking_manager'));
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed to create booking');
            }
        }


        return $this->render('SkedAppBookingBundle:Booking:add.html.twig', array(
                'form' => $form->createView(),
            ));
    }

    /**
     * Edit booking
     * 
     * @param type $bookingId
     * @return type
     */
    public function editAction($bookingId)
    {
        $this->get('logger')->info('edit booking id:' . $bookingId);
        
        $user = $this->get('member.manager')->getLoggedInUser();
        
        $booking = new Booking();
        $form = $this->createForm(new BookingUpdateType(
            $user->getCompany()->getId(),
            $this->get('member.manager')->isAdmin()
            ), 
            $booking);

        return $this->render('SkedAppBookingBundle:Booking:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $booking->getId()
            ));
    }

    /**
     * update booking
     * 
     * @param type $bookingId
     * @return type
     */
    public function updateAction($bookingId)
    {
        $this->get('logger')->info('update booking id:' . $bookingId);

        $user = $this->get('member.manager')->getLoggedInUser();
        
        $booking = new Booking();
        $form = $this->createForm(new BookingUpdateType(
            $user->getCompany()->getId(),
            $this->get('member.manager')->isAdmin()
            ), 
            $booking);

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


        return $this->render('SkedAppBookingBundle:Booking:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $booking->getId()
            ));
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
                    'start' => $booking->getAppointmentDate()->format("c"),
                    'end' => $booking->getAppointmentDate()->format("c"),
                    //'start' => "2012-11-29",
                    'resourceId' => 'resource-' . $booking->getConsultant()->getId(),
                    'url' => $this->generateUrl("sked_app_booking_edit", array("bookingId"=>$booking->getId()) ),
                    //'color' => 'pink',
                    //'textColor' => 'black'
                );
            }
        }

        $response = new Response(json_encode($results));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
