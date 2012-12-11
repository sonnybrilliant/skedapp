<?php

namespace SkedApp\ConsultantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SkedApp\CoreBundle\Entity\Consultant;
use SkedApp\ConsultantBundle\Form\ConsultantCreateType;
use SkedApp\ConsultantBundle\Form\ConsultantUpdateType;
use SkedApp\SearchBundle\Form\SearchType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use SkedApp\BookingBundle\Form\BookingShowType;

/**
 * SkedApp\ConsultantBundle\Controller\ConsultantController
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppConsultantBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class ConsultantController extends Controller
{

    /**
     * list consultants
     *
     * @return View
     * @throws AccessDeniedException
     *
     * @Secure(roles="ROLE_ADMIN,ROLE_CONSULTANT_ADMIN")
     */
    public function listAction($page = 1)
    {

        $this->get('logger')->info('list consultants');

        $sort = $this->get('request')->query->get('sort');
        $direction = $this->get('request')->query->get('direction', 'desc');

        $options = array('sort' => $sort,
            'direction' => $direction
        );


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $this->container->get('consultant.manager')->listAll($options), $this->getRequest()->query->get('page', $page), 10
        );

        return $this->render('SkedAppConsultantBundle:Consultant:list.html.twig', array(
                'pagination' => $pagination,
                'sort_img' => '/img/sort_' . $direction . '.png',
                'sort' => $direction,
            ));
    }

    /**
     * Create a new consultant
     *
     * @return View
     * @throws AccessDeniedException
     *
     * @Secure(roles="ROLE_ADMIN,ROLE_CONSULTANT_ADMIN")
     */
    public function newAction()
    {
        $this->get('logger')->info('create a new consultant');

        $consultant = new Consultant();
        $form = $this->createForm(new ConsultantCreateType(), $consultant);

        return $this->render('SkedAppConsultantBundle:Consultant:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * Create a new consultant
     *
     * @return View
     * @throws AccessDeniedException
     *
     * @Secure(roles="ROLE_ADMIN,ROLE_CONSULTANT_ADMIN")
     */
    public function createAction()
    {
        $this->get('logger')->info('create a new consultant');

        $consultant = new Consultant();
        $password = $this->get('utility.manager')->generatePassword(16);
        $consultant->setPassword($password);
        $form = $this->createForm(new ConsultantCreateType(), $consultant);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $this->get('consultant.manager')->createNewConsultant($consultant);

                $params = array(
                    'fullName' => $consultant->getFirstName() . ' ' . $consultant->getLastName(),
                    'email' => $consultant->getEmail(),
                    'company' => $consultant->getCompany()->getName(),
                    'password' => $password,
                    'link' => $this->generateUrl(
                        '_security_login', array(), true)
                );

                $emailBodyHtml = $this->render(
                        'SkedAppCoreBundle:EmailTemplates:member.created.html.twig', $params
                    )->getContent();

                $emailBodyTxt = $this->render(
                        'SkedAppCoreBundle:EmailTemplates:member.created.txt.twig', $params
                    )->getContent();

                $params['bodyHTML'] = $emailBodyHtml;
                $params['bodyTEXT'] = $emailBodyTxt;

                //send mail
                $this->get('email.manager')->memberRegistration($params);


                $this->getRequest()->getSession()->setFlash(
                    'success', 'Created consultant sucessfully');
                return $this->redirect($this->generateUrl('sked_app_consultant_list'));
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed to create consultant');
            }
        }

       return $this->render('SkedAppConsultantBundle:Consultant:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * Show consultant
     *
     * @return View
     * @throws AccessDeniedException
     *
     * @Secure(roles="ROLE_ADMIN,ROLE_CONSULTANT_ADMIN,ROLE_CONSULTANT_USER")
     */
    public function showAction($id)
    {
        $this->get('logger')->info('view consultant');

        $em = $this->getDoctrine()->getEntityManager();
        $consultant = $em->getRepository('SkedAppCoreBundle:Consultant')->find($id);

        if (!$consultant) {
            $this->get('logger')->warn("consultant not found $id");
            return $this->createNotFoundException();
        }

        return $this->render('SkedAppConsultantBundle:Consultant:show.personal.details.html.twig', array('consultant' => $consultant));
    }

    /**
      /**
     * Show consultant
     *
     * @return View
     * @throws AccessDeniedException
     *
     * @Secure(roles="ROLE_ADMIN,ROLE_CONSULTANT_ADMIN,ROLE_CONSULTANT_USER")
     */
    public function showBookingsAction($id)
    {
        $this->get('logger')->info('show consultant booking details');

        $em = $this->getDoctrine()->getEntityManager();
        $consultant = $em->getRepository('SkedAppCoreBundle:Consultant')->find($id);

        if (!$consultant) {
            $this->get('logger')->warn("consultant not found $id");
            return $this->createNotFoundException();
        }

        return $this->render('SkedAppConsultantBundle:Consultant:show.bookings.html.twig', array('consultant' => $consultant));
    }

    /**
     * Show booking to consultant
     *
     * @return View
     * @throws AccessDeniedException
     *
     * @Secure(roles="ROLE_ADMIN,ROLE_CONSULTANT_ADMIN,ROLE_CONSULTANT_USER")
     */
    public function showBookingToConsultantAction($bookingId)
    {
        $this->get('logger')->info('show consultant booking id:' . $bookingId);

        try {
            $booking = $this->get('booking.manager')->getById($bookingId);
            $form = $this->createForm(new BookingShowType(), $booking);
        } catch (\Exception $e) {
            $this->get('logger')->err("booking id:$booking invalid");
            $this->createNotFoundException($e->getMessage());
        }

        return $this->render('SkedAppConsultantBundle:Consultant:show.booking.to.consultant.html.twig', array(
                'form' => $form->createView(),
                'booking' => $booking
            ));
    }

    /**
     * Edit consultant
     *
     * @return View
     * @throws AccessDeniedException
     *
     * @Secure(roles="ROLE_ADMIN,ROLE_CONSULTANT_ADMIN,ROLE_CONSULTANT_USER")
     */
    public function editAction($id)
    {
        $this->get('logger')->info('edit consultant id:' . $id);

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('edit consultant id:' . $id . ', access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $consultant = $em->getRepository('SkedAppCoreBundle:Consultant')->find($id);

        if (!$consultant) {
            $this->get('logger')->warn("consultant not found $id");
            return $this->createNotFoundException();
        }

        $form = $this->createForm(new ConsultantUpdateType(), $consultant);

        return $this->render('SkedAppConsultantBundle:Consultant:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $consultant->getId()
            ));
    }

    /**
     * Update consultant
     *
     * @return View
     * @throws AccessDeniedException
     *
     * @Secure(roles="ROLE_ADMIN,ROLE_CONSULTANT_ADMIN,ROLE_CONSULTANT_USER")
     */
    public function updateAction($id)
    {
        $this->get('logger')->info('update consultant id:' . $id);

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('update consultant id:' . $id . ', access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $consultant = $em->getRepository('SkedAppCoreBundle:Consultant')->find($id);

        if (!$consultant) {
            $this->get('logger')->warn("consultant not found $id");
            return $this->createNotFoundException();
        }

        $form = $this->createForm(new ConsultantUpdateType(), $consultant);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($consultant);
                $em->flush();
                $this->getRequest()->getSession()->setFlash(
                    'success', 'Updated consultant sucessfully');
                return $this->redirect($this->generateUrl('sked_app_consultant_list'));
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed to update consultant');
            }
        }

        return $this->render('SkedAppConsultantBundle:Consultant:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $consultant->getId()
            ));
    }

    /**
     * Delete consultant
     *
     * @return View
     * @throws AccessDeniedException
     *
     * @Secure(roles="ROLE_ADMIN,ROLE_CONSULTANT_ADMIN")
     */
    public function deleteAction($id)
    {
        $this->get('logger')->info('delete consultant id:' . $id);

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('delete consultant id:' . $id . ', access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $consultant = $em->getRepository('SkedAppCoreBundle:Consultant')->find($id);

        if (!$consultant) {
            $this->get('logger')->warn("consultant not found $id");
            return $this->createNotFoundException();
        }

        $this->container->get('consultant.manager')->delete($consultant);
        $this->getRequest()->getSession()->setFlash(
            'success', 'Deleted consultant sucessfully');
        return $this->redirect($this->generateUrl('sked_app_consultant_list'));
    }

    /**
     * Get all consultant active bookings
     *
     * @param integer $consultantId
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @Secure(roles="ROLE_ADMIN,ROLE_CONSULTANT_ADMIN,ROLE_CONSULTANT_USER")
     */
    public function ajaxGetAllBookingsAction($consultantId)
    {
        $this->get('logger')->info('get all consultant active bookings');
        $results = array();

        $bookings = $this->get("consultant.manager")->getConsultantBookings($consultantId);

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
                    'url' => $this->generateUrl("sked_app_consultant_booking_show_to_consultant", array("bookingId" => $booking->getId())),
                );
            }
        }

        $response = new Response(json_encode($results));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Ajax call services by category
     *
     * @param integer $categoryId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     *
     */
    public function ajaxGetByCategoryAction($categoryId)
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->get('logger')->info('get services by category');
            $results = array();

//            if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
//                $this->get('logger')->warn('view agency, access denied.');
//                throw new AccessDeniedException();
//            }

            $em = $this->getDoctrine()->getEntityManager();
            $category = $em->getRepository('SkedAppCoreBundle:Category')->find($categoryId);

            if ($category) {
                $services = $this->get('service.manager')->getServicesByCategory($category);

                if ($services) {
                    foreach ($services as $service) {
                        $results[] = array(
                            'id' => $service->getId(),
                            'name' => $service->getName()
                        );
                    }
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
     * View consultant (Public)
     *
     * @return View
     *
     */
    public function viewAction($id)
    {
        $this->get('logger')->info('view consultant public');

        $em = $this->getDoctrine()->getEntityManager();
        $consultant = $em->getRepository('SkedAppCoreBundle:Consultant')->find($id);

        if (!$consultant) {
            $this->get('logger')->warn("consultant not found $id");
            return $this->createNotFoundException();
        }

        $company = $em->getRepository('SkedAppCoreBundle:Company')->find($consultant->getCompany()->getId());

        if (!$company) {
            $this->get('logger')->warn("company not found $id");
            return $this->createNotFoundException();
        }

        $company_photos = $this->container->get('company_photos.manager')->listAll (array ('company_id' => $company->getId (), 'sort' => 'c.caption', 'direction' => 'asc'));

        $arrBookingDate = $this->getRequest()->get('Search', array('booking_date' => ''));

        $strBookingDate = $this->getRequest()->get('booking_date', $arrBookingDate['booking_date']);

        if (strlen ($strBookingDate) > 0) {
          $objDateSend = new \DateTime($strBookingDate);
          $consultant->setAvailableBookingSlots ($em->getRepository('SkedAppCoreBundle:Booking')->getBookingSlotsForConsultantSearch($consultant, $objDateSend));
        }

        $form = $this->createForm(new SearchType());

        return $this->render('SkedAppConsultantBundle:Consultant:view.html.twig',
                array(
                    'consultant' => $consultant,
                    'company' => $company,
                    'company_photos' => $company_photos,
                    'intPositionLat' => $this->getRequest()->get('pos_lat', 0),
                    'intPositionLong' => $this->getRequest()->get('pos_lng', 0),
                    'dateFull' => $strBookingDate,
                    'category_id' => $this->getRequest()->get('category_id', 0),
                    'serviceIds' => '',
                    'form' => $form->createView(),
                    ));
    }

}

