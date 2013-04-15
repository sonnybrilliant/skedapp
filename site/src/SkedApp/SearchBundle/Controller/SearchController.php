<?php

namespace SkedApp\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SkedApp\SearchBundle\Form\SearchType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Ivory\GoogleMap\Services\Geocoding\Geocoder;

/**
 * SkedApp\SearchBundle\Controller\ConsultantController
 *
 * @author Otto Saayman <otto.saayman@creativecloud.co.za>
 * @package SkedAppSearchBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class SearchController extends Controller
{

    public function resultsAction($page = 1)
    {

        $this->get('logger')->info('search results');

        $options = array();

        //In order to display the default value of TODAY for the booking date and to display the chosen dat in search result, the form post data needs to be passed to
        // the search form if it is there

        $data = $this->getRequest()->get('Search');
        $options['lat'] = null;
        $options['lng'] = null;
        $options['date'] = null;


        if (!isset($data['administrative_area_level_1'])) {
            $params = $this->getRequest()->get('Search');
            if ($this->getRequest()->get('Search')) {
                $data['lat'] = $params['lat'];
                $data['lng'] = $params['lng'];
                $data['consultantServices'] = $params['serviceId'];
                $data['category'] = $params['categoryId'];
                $data['booking_date'] = $params['date'];
            }else{
                $data['lat'] = $this->getRequest()->get('lat');
                $data['lng'] = $this->getRequest()->get('lng');
                $data['consultantServices'] = $this->getRequest()->get('serviceId');
                $data['category'] = $this->getRequest()->get('categoryId');
                $data['booking_date'] = $this->getRequest()->get('date');
            }
        }

        $form = $this->createForm(new SearchType($data['category'], $data['booking_date'], $data['consultantServices']));

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {

                $data = $form->getData();

                $options['lat'] = $data['lat'];
                $options['lng'] = $data['lng'];
                $options['radius'] = 20;
                $options['category'] = $data['category'];
                $options['categoryId'] = $data['category']->getId();
                $options['service'] = $data['consultantServices'];
                $options['serviceId'] = $data['consultantServices']->getId();
                $options['date'] = $data['booking_date'];
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed search');
            }
        } else {

            $service = $this->get('service.manager')->getById($data['consultantServices']);
            $category = $this->get('category.manager')->getById($data['category']);

            $options['lat'] = $data['lat'];
            $options['lng'] = $data['lng'];
            $options['radius'] = 20;
            $options['category'] = $category;
            $options['categoryId'] = $category->getId();
            $options['service'] = $service;
            $options['serviceId'] = $service->getId();
            $options['date'] = $data['booking_date'];

            //Get address from lat/ long
            $geocoder = new Geocoder();
            $adapter = new \Geocoder\HttpAdapter\BuzzHttpAdapter();

            $geocoder->registerProviders(array(
                new \Geocoder\Provider\GoogleMapsProvider(
                    $adapter
                ),
            ));

            $address = $geocoder->reverse($data['lat'], $data['lng']);

            $addressString = $address->getStreetNumber();

            if (strlen($addressString) > 0)
                $addressString .= ' ';

            $addressString .= $address->getStreetName();

            if (strlen($addressString) > 0)
                $addressString .= ', ';

            $addressString .= $address->getCityDistrict();

            if (strlen($addressString) > 0)
                $addressString .= ', ';

            $addressString .= $address->getCity();

            if (strlen($addressString) > 0)
                $addressString .= ', ';

            $addressString .= $address->getZipcode();

            if (strlen($addressString) > 0)
                $addressString .= ', ';

            $addressString .= $address->getRegion();

            if (strlen($addressString) > 0)
                $addressString .= ', ';

            $addressString .= $address->getCountry();

            $form->setData(array('address' => $addressString, 'lat' => $options['lat'], 'lng' => $options['lng'], 'service' => $service, 'category' => $category));
        }

        $searchResults = $this->container
            ->get('consultant.manager')
            ->listAllWithinRadius($options);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $searchResults['results'], $this->getRequest()->query->get('page', $page), 5
        );

        $paginationParams = array();

        //Read form variables into an array
        foreach ($options as $strKey => $strValue) {
            $paginationParams['Search[' . $strKey . ']'] = $strValue;
        }

        foreach ($pagination as $consultant) {
            $date = new \DateTime($options['date']);
            $slots = $this->get('booking.manager')->getBookingSlotsForConsultantSearch($consultant, $date);
            $consultant->setAvailableBookingSlots($slots);
        }

        return $this->render('SkedAppSearchBundle:Search:search.html.twig', array(
                'form' => $form->createView(),
                'pagination' => $pagination,
                'options' => $options,
                'paginationParams' => $paginationParams
            ));
    }

    public function resultAction($page = 1)
    {

        $this->get('logger')->info('search results');

        $options = array();
        $form = $this->createForm(new SearchType());
        $consultants = array();

        $options['lat'] = null;
        $options['lng'] = null;

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {

                $data = $form->getData();

                $options['lat'] = $data('lat');
                $options['lng'] = $data('lng');
                $options['radius'] = 20;
                $options['category'] = $data('category');


                ladybug_dump($data);
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed search');
            }
        }


        $formData = $this->getRequest()->get('Search');

        if (!isset($formData['booking_date'])) {
            $formData['booking_date'] = $this->getRequest()->get('date', '');
        }

        if (!isset($formData['lat'])) {
            $formData['lat'] = $this->getRequest()->get('pos_lat', null);
        }

        if (!isset($formData['lng']))
            $formData['lng'] = $this->getRequest()->get('pos_lng', null);

        if (!isset($formData['consultantServices']))
            $formData['consultantServices'] = $this->getRequest()->get('service_ids', array());
        if (!is_array($formData['consultantServices']))
            $formData['consultantServices'] = explode(',', $formData['consultantServices']);

        if (!isset($formData['category'])) {
            $formData['category'] = $this->getRequest()->get('category_id', 0);
        }

        $form = $this->createForm(new SearchType($formData['category'], $formData['booking_date']));

        if ((!is_null($formData['lat'])) && (!is_null($formData['lng'])) && ($formData['category'] > 0)) {

            if ($this->getRequest()->getMethod() == 'POST')
                $form->bindRequest($this->getRequest());

            $options['lat'] = $formData['lat'];
            $options['lng'] = $formData['lng'];
            $options['radius'] = 20;
            $options['categoryId'] = $formData['category'];

            if ((isset($formData['consultantServices'])) && (count($formData['consultantServices']) > 0))
                $options['consultantServices'] = $formData['consultantServices'];
        }

        $arrResults = $this->container->get('consultant.manager')->listAllWithinRadius($options);

        $variables = array();

        //Read form variables into an array
        foreach ($formData as $strKey => $strValue) {
            $variables['Search[' . $strKey . ']'] = $strValue;
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $arrResults['arrResult'], $this->getRequest()->query->get('page', $page), 10
        );

        $objDate = new \DateTime($formData['booking_date']);

        if ($objDate->getTimestamp() <= 0)
            $objDate = new \DateTime();

        $em = $this->getDoctrine()->getEntityManager();

        foreach ($pagination as $objConsultant) {
            $consultants[] = $objConsultant;
            $objDateSend = new \DateTime($formData['booking_date']);
            $objConsultant->setAvailableBookingSlots($em->getRepository('SkedAppCoreBundle:Booking')->getBookingSlotsForConsultantSearch($objConsultant, $objDateSend));
        }

        return $this->render('SkedAppSearchBundle:Search:search.html.twig', array(
                'pagination' => $pagination,
                'form' => $form->createView(),
                'intPositionLat' => $formData['lat'],
                'intPositionLong' => $formData['lng'],
                'objDate' => $objDate,
                'dateFull' => $objDate->format('d-m-Y'),
                'category_id' => $formData['category'],
                'serviceIds' => implode(',', $formData['consultantServices']),
                'paginatorVariables' => $variables,
            ));
    }

    /**
     * Ajax call services by category
     *
     * @param integer $categoryId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     */
    public function ajaxGetServicesByCategoryAction($categoryId)
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->get('logger')->info('get services by category');
            $results = array();

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

    public function showResultsAction($paginator, $serviceId, $options)
    {
        $this->get('logger')->info('show consultants results');

        $searchDate = new \DateTime($options['date']);
        $searchDate->add(new \DateInterval('P1D'));

        $data = array();
        $weekDays = $this->get('timeslots.manager')->buildWeekDays($searchDate);
        foreach ($paginator as $consultant) {
            //$consultants[] = $consultant;

            $slots = $this->get('timeslots.manager')->generateTimeSlots($consultant, $searchDate,7);
            $data[] = array(
                'consultant' => $consultant,
                'slots' => $slots
            );
        }
        return $this->render('SkedAppSearchBundle:Search:show.results.html.twig', array(
                'weekDays' => $weekDays,
                'data' => $data,
                'service' => $this->get('service.manager')->getById($serviceId),
                'options' => $options
            ));
    }

}

