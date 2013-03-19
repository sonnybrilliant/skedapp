<?php

namespace SkedApp\ConsultantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;

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

}

