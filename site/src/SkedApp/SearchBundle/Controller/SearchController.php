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

        return $this->render('SkedAppSearchBundle:Search:search.html.twig', array(
                'form' => $form->createView(),
                'pagination' => $pagination,
                'options' => $options,
                'paginationParams' => $paginationParams
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

