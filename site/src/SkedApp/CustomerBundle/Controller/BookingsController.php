<?php

namespace SkedApp\CustomerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Ivory\GoogleMap\Overlays\Animation;
use Ivory\GoogleMap\MapTypeId;
use Ivory\GoogleMap\Events\MouseEvent;

/**
 * SkedApp\CustomerBundle\Controller\BookingsController
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCustomerBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class BookingsController extends Controller
{

    /**
     * list customer's bookings
     *
     * @return View
     *
     * @Secure(roles="ROLE_SITE_USER")
     */
    public function listAction($page = 1)
    {

        //Check if a booking exists in the session and redirect to the booking confirm screen if it does
        if (isset($_SESSION['booking_params'])) {

            $bookingParameters = unserialize($_SESSION['booking_params']);

            unset($_SESSION['booking_params']);

            return $this->redirect($this->generateUrl(
                    'sked_app_booking_make',
                    array(
                        'companyId' => $bookingParameters['company_id'],
                        'consultantId' => $bookingParameters['consultant_id'],
                        'date' => $bookingParameters['booking_date'],
                        'timeSlotStart' => $bookingParameters['timeslot_start'],
                        'serviceIds' => $bookingParameters['service_ids'],
                        )
                    ));

        }

        $this->get('logger')->info('list customer bookings');

        $isDirectionSet = $this->get('request')->query->get('direction', false);
        $searchText = $this->get('request')->query->get('searchText');
        $sort = $this->get('request')->query->get('sort', 'b.id');
        $direction = $this->get('request')->query->get('direction', 'asc');


        $securityContext = $this->get('security.context');
        $token = $securityContext->getToken();
        $user = $token->getUser();

        $options = array('searchText' => $searchText,
            'sort' => $sort,
            'direction' => $direction,
        );


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $this->container->get('booking.manager')->getAllCustomerBookings($options), $this->getRequest()->query->get('page', $page), 10
        );

        return $this->render('SkedAppCustomerBundle:Bookings:customer.list.html.twig', array(
                'pagination' => $pagination,
                'direction' => $direction,
                'isDirectionSet' => $isDirectionSet,
                'customer' => $user
            ));
    }

    public function detailsAction($id)
    {
        $this->get('logger')->info('booking details');

        $securityContext = $this->get('security.context');
        $token = $securityContext->getToken();
        $user = $token->getUser();

        try {
            $booking = $this->get('booking.manager')->getById($id);

            $infoWindow = $this->get('ivory_google_map.info_window');

            // Configure your info window options
            $infoWindow->setPrefixJavascriptVariable('info_window_');
            $infoWindow->setPosition(0, 0, true);
            $infoWindow->setPixelOffset(1.1, 2.1, 'px', 'pt');
            $infoWindow->setContent('<p>' . $booking->getConsultant()->getCompany()->getName() . '<br/><small>Telphone:'.$booking->getConsultant()->getCompany()->getContactNumber().' </p>');
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
            $marker->setPosition($booking->getConsultant()->getCompany()->getLat(), $booking->getConsultant()->getCompany()->getLng(), true);
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

            $map->setCenter($booking->getConsultant()->getCompany()->getLat(), $booking->getConsultant()->getCompany()->getLng(), true);
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

        }

        return $this->render('SkedAppCustomerBundle:Bookings:booking.detail.html.twig', array(
                'booking' => $booking,
                'customer' => $user,
                'map' => $map
            ));
    }

}
