<?php

namespace SkedApp\ConsultantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use SkedApp\ConsultantBundle\Form\ConsultantBookOutType;
use SkedApp\CoreBundle\Entity\Booking;

/**
 * SkedApp\ConsultantBundle\Controller\BookingController
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppConsultantBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class BookingController extends Controller
{

    /**
     * List consultants
     *
     * @param String $slug consultant slug
     * @param Integer $page paginator
     *
     * @Secure(roles="ROLE_ADMIN,ROLE_CONSULTANT_ADMIN,ROLE_CONSULTANT_USER")
     */
    public function listAction($slug, $page = 1)
    {

        $this->get('logger')->info('list consultants booking:' . $slug);

        try {
            $consultant = $this->get('consultant.manager')->getBySlug($slug);

            $isDirectionSet = $this->get('request')->query->get('direction', false);
            $searchText = $this->get('request')->query->get('searchText');
            $sort = $this->get('request')->query->get('sort', 'b.hiddenAppointmentStartTime');
            $direction = $this->get('request')->query->get('direction', 'desc');

            $options = array('searchText' => $searchText,
                'sort' => $sort,
                'direction' => $direction,
                'consultantId' => $consultant->getId()
            );

            $consultants = $this->container->get('booking.manager')->getConsultantBookings($options);

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate($consultants, $this->getRequest()->query->get('page', $page), 10
            );
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request: ' . $e->getMessage());
            return $this->redirect($this->generateUrl('_welcome'));
        }

        return $this->render('SkedAppConsultantBundle:Booking:list.html.twig', array(
                'consultant' => $consultant,
                'pagination' => $pagination,
                'direction' => $direction,
                'isDirectionSet' => $isDirectionSet
            ));
    }

    /**
     * Show booking details
     *
     * @param Integer $id
     *
     * @Secure(roles="ROLE_ADMIN,ROLE_CONSULTANT_ADMIN,ROLE_CONSULTANT_USER")
     */
    public function showAction($id)
    {
        $this->get('logger')->info('show booking:' . $id);

        try {
            $booking = $this->get('booking.manager')->getById($id);
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request: ' . $e->getMessage());
            return $this->redirect($this->generateUrl('_welcome'));
        }

        return $this->render('SkedAppConsultantBundle:Booking:show.html.twig', array(
                'booking' => $booking
            ));
    }

    /**
     * Block out consultant
     *
     * @param String $slug
     *
     * @Secure(roles="ROLE_ADMIN,ROLE_CONSULTANT_ADMIN,ROLE_CONSULTANT_USER")
     */
    public function blockAction($slug)
    {
        $this->get('logger')->info('block consultant :' . $slug);

        $form = $this->createForm(new ConsultantBookOutType());

        try {
            $consultant = $this->get('consultant.manager')->getBySlug($slug);

            if ($this->getRequest()->getMethod() == 'POST') {
                $form->bindRequest($this->getRequest());

                if ($form->isValid()) {
                    $data = $form->getData();
                    $startTimeSlot = explode(':',$data['startTimeslot']->getSlot());
                    $startDate = explode('-',$data['start_date']);
                    $starTimestamp = mktime($startTimeSlot[0], $startTimeSlot[1],0, $startDate[1], $startDate[0], $startDate[2]);
                    
                    $startDateObject = new \DateTime();
                    $startDateObject->setTimestamp($starTimestamp);
                    
                    $endTimeSlot = explode(':',$data['endTimeslot']->getSlot());
                    $endDate = explode('-',$data['end_date']);
                    $endTimestamp = mktime($endTimeSlot[0], $endTimeSlot[1],0, $endDate[1], $endDate[0], $endDate[2]);
                    
                    $endDateObject = new \DateTime();
                    $endDateObject->setTimestamp($endTimestamp);
                    
                   
                    $isValid = true;
                    
                    if($startDateObject > $endDateObject){
                       $isValid = false; 
                       $this->getRequest()->getSession()->setFlash(
                        'error', 'Start date must be less than end date');  
                    }
                    
                    if($isValid){
                        $booking = new Booking();
                        $booking->setConsultant($consultant);
                        $booking->setAppointmentDate($startDateObject);
                        //$booking->setIsLeave(true);
                        $booking->setStartTimeslot($data['startTimeslot']);
                        $booking->setEndTimeslot($data['endTimeslot']);
                        $booking->setHiddenAppointmentStartTime($startDateObject);
                        $booking->setHiddenAppointmentEndTime($endDateObject);
                        
                        $this->get('booking.manager')->save($booking);

                        $this->getRequest()->getSession()->setFlash(
                            'success', 'Booked out consultant successfully');
                        
                        return $this->redirect($this->generateUrl('sked_app_consultant_list') . '.html');

                    }
                    

                } else {
                    $this->getRequest()->getSession()->setFlash(
                        'error', 'Failed to book out consultant');
                }
            }
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request: ' . $e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_consultant_list') . '.html');
        }



        return $this->render('SkedAppConsultantBundle:Booking:booking.out.html.twig', array(
                'form' => $form->createView(),
                'slug' => $slug
            ));
    }

}

