<?php

namespace SkedApp\ConsultantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SkedApp\CoreBundle\Entity\Consultant;
use SkedApp\ConsultantBundle\Form\ConsultantCreateType;
use SkedApp\ConsultantBundle\Form\ConsultantUpdateType;
use SkedApp\BookingBundle\Form\BookingListFilterType;
use SkedApp\SearchBundle\Form\SearchType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use SkedApp\BookingBundle\Form\BookingShowType;
use Ivory\GoogleMap\Overlays\Animation;
use Ivory\GoogleMap\MapTypeId;
use Ivory\GoogleMap\Events\MouseEvent;

/**
 * SkedApp\ConsultantBundle\Controller\ConsultantController
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppConsultantBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class ConsultantController extends Controller
{

    /**
     * List consultants
     * 
     * @param Integer $page paginator
     * 
     * @Secure(roles="ROLE_ADMIN,ROLE_CONSULTANT_ADMIN")
     */
    public function listAction($page = 1)
    {

        $this->get('logger')->info('list consultants');

        $isDirectionSet = $this->get('request')->query->get('direction', false);
        $searchText = $this->get('request')->query->get('searchText');
        $sort = $this->get('request')->query->get('sort', 'c.id');
        $direction = $this->get('request')->query->get('direction', 'asc');

        $options = array('searchText' => $searchText,
            'sort' => $sort,
            'direction' => $direction,
        );

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $this->container->get('consultant.manager')->listAll($options), $this->getRequest()->query->get('page', $page), 10
        );

        return $this->render('SkedAppConsultantBundle:Consultant:list.html.twig', array(
                'pagination' => $pagination,
                'direction' => $direction,
                'isDirectionSet' => $isDirectionSet
            ));
    }

    /**
     * add a consultant
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
                    'success', 'Created consultant successfully');
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

        $companyId = $consultant->getCompany()->getId();
        $consultantId = $consultant->getId();

        $form = $this->createForm(new BookingListFilterType($companyId, new \DateTime()));

        return $this->render(
                'SkedAppConsultantBundle:Consultant:show.bookings.html.twig', array('consultant' => $consultant, 'form' => $form->createView(), 'companyId' => $companyId)
        );
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
                    'success', 'Updated consultant successfully');
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
            'success', 'Deleted consultant successfully');
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
                    if (is_object($booking->getService()))
                        $bookingName = $booking->getService()->getName();
                    else
                        $bookingName = 'Unknown Service';
                }

                $bookingTooltip = '<div class="divBookingTooltip">';

                if (is_object($booking->getCustomer())) {

                    $bookingTooltip .= '<strong>Customer:</strong> ' . $booking->getCustomer()->getFullName() . "<br />";
                    $bookingTooltip .= '<strong>Customer Contact Number:</strong> ' . $booking->getCustomer()->getMobileNumber() . "<br />";
                    $bookingTooltip .= '<strong>Customer E-Mail:</strong> ' . $booking->getCustomer()->getEmail() . "<br />";

                    $bookingName = $booking->getCustomer()->getFullName() . ' - ' . $bookingName;
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
                    'url' => $this->generateUrl("sked_app_consultant_booking_show_to_consultant", array("bookingId" => $booking->getId())),
                    'description' => $bookingTooltip,
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
    public function viewAction($slug, $id)
    {
        $this->get('logger')->info('consultant public profile');

        $options = array();

        try {
            $consultant = $this->get('consultant.manager')->getById($id);

            $service = $this->get('service.manager')->getById($this->getRequest()->get('serviceId'));
            $category = $this->get('category.manager')->getById($this->getRequest()->get('categoryId'));

            $options['lat'] = $this->getRequest()->get('lat');
            $options['lng'] = $this->getRequest()->get('lng');
            $options['radius'] = 20;
            $options['category'] = $category;
            $options['categoryId'] = $category->getId();
            $options['service'] = $service;
            $options['serviceId'] = $service->getId();
            $options['date'] = $this->getRequest()->get('date');

            $date = new \DateTime($options['date']);
            $slots = $this->get('booking.manager')->getBookingSlotsForConsultantSearch($consultant, $date);
            $consultant->setAvailableBookingSlots($slots);


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
        } catch (\Exception $e) {
            return $this->createNotFoundException($e->getMessage());
        }

        return $this->render('SkedAppConsultantBundle:Consultant:public.profile.html.twig', array(
                'consultant' => $consultant,
                'map'=>$map,
                'options' => $options
            ));
    }

    /**
     * View consultant (Public)
     *
     * @return View
     *
     */
    public function viewOldAction($slug, $id)
    {
        $this->get('logger')->info('consultant public profile');

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

        $company_photos = $this->container->get('company.photos.manager')->listAll(array('company_id' => $company->getId(), 'sort' => 'c.caption', 'direction' => 'asc'));

        $arrBookingDate = $this->getRequest()->get('Search', array('booking_date' => ''));

        $strBookingDate = $this->getRequest()->get('booking_date', $arrBookingDate['booking_date']);

        if (strlen($strBookingDate) > 0) {
            $objDateSend = new \DateTime($strBookingDate);
            $consultant->setAvailableBookingSlots($em->getRepository('SkedAppCoreBundle:Booking')->getBookingSlotsForConsultantSearch($consultant, $objDateSend));
        }

        $otherConsultants = $this->get('consultant.manager')->listAllByCompany($company, array('sort' => 'c.lastName', 'direction' => 'Asc'));
        $otherConsultantsArray = array();

        foreach ($otherConsultants as $otherConsultant) {
            if ($otherConsultant->getId() != $consultant->getId()) {
                $objDateSend = new \DateTime($strBookingDate);
                $otherConsultant->setAvailableBookingSlots($em->getRepository('SkedAppCoreBundle:Booking')->getBookingSlotsForConsultantSearch($otherConsultant, $objDateSend));
                $otherConsultantsArray[] = $otherConsultant;
            }
        }

        $form = $this->createForm(new SearchType());

        return $this->render('SkedAppConsultantBundle:Consultant:view.html.twig', array(
                'consultant' => $consultant,
                'company' => $company,
                'company_photos' => $company_photos,
                'intPositionLat' => $this->getRequest()->get('pos_lat', 0),
                'intPositionLong' => $this->getRequest()->get('pos_lng', 0),
                'dateFull' => $strBookingDate,
                'category_id' => $this->getRequest()->get('category_id', 0),
                'serviceIds' => $this->getRequest()->get('serviceIds'),
                'form' => $form->createView(),
                'otherConsultants' => $otherConsultantsArray,
            ));
    }

    /**
     * Show consultant
     *
     * @return Print
     * @throws AccessDeniedException
     *
     * @Secure(roles="ROLE_ADMIN,ROLE_CONSULTANT_ADMIN,ROLE_CONSULTANT_USER")
     */
    public function printAction($id)
    {
        $this->get('logger')->info('view consultant');

        $em = $this->getDoctrine()->getEntityManager();
        $consultant = $em->getRepository('SkedAppCoreBundle:Consultant')->find($id);

        if (!$consultant) {
            $this->get('logger')->warn("consultant not found $id");
            return $this->createNotFoundException();
        }

        $arrSearch = $this->getRequest()->get('Search');
        $objDateSelected = new \DateTime($arrSearch['booking_date']);

        $form = $this->createForm(new SearchType(0, $objDateSelected->format('d-m-Y')));

        $form->bindRequest($this->getRequest());

        $bookings = $em->getRepository('SkedAppCoreBundle:Booking')->getAllConsultantBookingsByDate($consultant, $objDateSelected->setTime(0, 0, 0), $objDateSelected->setTime(23, 59, 59));

        $arrTwigOptions = array(
            'consultant' => $consultant,
            'print_bookings' => true,
            'form' => $form->createView(),
            'bookings' => $bookings,
            'selected_date' => $objDateSelected->format('d-m-Y'),
            'print' => $this->getRequest()->get('print_out', 0)
        );

        if ($this->getRequest()->get('print_out', 0) <= 0) {
            $twigName = 'SkedAppConsultantBundle:Consultant:show.bookings.day.html.twig';
        } else {
            $twigName = 'SkedAppConsultantBundle:Consultant:print.bookings.day.html.twig';
        }

        return $this->render($twigName, $arrTwigOptions);
    }

}

