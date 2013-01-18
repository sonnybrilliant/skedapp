<?php

namespace SkedApp\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * SkedApp\ApiBundle\Controller\ApiController
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppApiBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class ApiController extends Controller
{

    /**
     * Get categories
     *
     * @return json response
     */
    public function getCategoriesAction()
    {
        $this->get('logger')->info('get categories');

        $em = $this->getDoctrine()->getEntityManager();
        $categories = $em->getRepository('SkedAppCoreBundle:Category')->findAll();
        $results = array();

        if ($categories) {
            foreach ($categories as $category) {
                $results[] = array('id' => $category->getId(), 'name' => $category->getName());
            }
        }

        return $this->respond($results);
    }

    /**
     * Get service
     *
     * @return json response
     */
    public function getServicesAction($id = 1)
    {
        $this->get('logger')->info('get services');
        $em = $this->getDoctrine()->getEntityManager();
        $services = $em->getRepository('SkedAppCoreBundle:Service')->findByCategory($id);
        $results = array();

        if ($services) {
            foreach ($services as $service) {
                $results[] = array('id' => $service->getId(), 'name' => $service->getName());
            }
        }
        return $this->respond($results);
    }

   /**
     * Get consultant by Id
     *
     * @return json response
     */
    public function getConsultantAction($id = 1)
    {
        $this->get('logger')->info('get consultant by id');
        $em = $this->getDoctrine()->getEntityManager();
        $consultant = $em->getRepository('SkedAppCoreBundle:Consultant')->find($id);
        $results = array();

        if($consultant){
            $results[] = $this->buildConsultant($consultant);
        }

        return $this->respond($results);
    }

    /**
     * Get consultant by service Id
     *
     * @return json response
     */
    public function getConsultantsAction($serviceId = 1)
    {
        $this->get('logger')->info('get consultant by category, service id and location');

        $sort = $this->get('request')->query->get('sort');
        $direction = $this->get('request')->query->get('direction', 'desc');

        $options = array('sort' => $sort,
            'direction' => $direction
        );

        //Instantiate search form
        $formData = $this->getRequest()->get('Search');

        if (!isset($formData['booking_date'])){
          $formData['booking_date'] = $this->getRequest()->get('date', '');
        }

        if (!isset($formData['lat'])){
          $formData['lat'] = $this->getRequest()->get('pos_lat', null);
        }

        if (!isset($formData['lng']))
          $formData['lng'] = $this->getRequest()->get('pos_lng', null);

        if (!isset($formData['address'])){
          $formData['address'] = $this->getRequest()->get('address', null);
        }

        if (!isset ($formData['category']))
            $formData['category'] = $this->getRequest()->get('category_id', 0);

        if ( (strlen($formData['address']) > 0) && ( (is_null ($formData['lng'])) || (is_null ($formData['lat'])) ) ) {
            //Latitude and Longitude not available, so attempt to get those from text location

            $geocoder = $this->get('ivory_google_map.geocoder');

            $response = $geocoder->geocode($formData['address']);

            if (!is_object ($response)) {
                return $this->respond(array('error_message' => 'Unable to get latitude and longitude from this address'));
            }

            $results = $response->getResults();

            foreach($results as $result) {
                // Get the formatted address
                $location = $result->getGeometry()->getLocation();
                if (is_null($formData['lat']))
                    $formData['lat'] = $location->getLatitude();
                if (is_null($formData['lng']))
                    $formData['lng'] = $location->getLongitude();
            }

        }

        return $this->respond($formData);
        exit;

        if ( (!is_null($formData['lat'])) && (!is_null($formData['lng'])) && ($serviceId > 0) ) {

            $options['lat'] = $formData['lat'];
            $options['lng'] = $formData['lng'];
            $options['radius'] = 5;
            $options['category'] = $formData['category'];
            $options['consultantServices'] = $serviceId;

        }

        if ( (is_null($formData['lat'])) || (is_null($formData['lng'])) || ($serviceId <= 0) ) {
            return $this->respond(array('error_message' => 'Please provide some search criteria'));
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

        $consultantsFound = array();

        foreach ($pagination as $objConsultant) {
            $objDateSend = new \DateTime($formData['booking_date']);
            $oneConsultant = $objConsultant->getObjectAsArray();
            $oneConsultant['availableSlots'] = $em->getRepository('SkedAppCoreBundle:Booking')->getBookingSlotsForConsultantSearch($objConsultant, $objDateSend);
            $consultantsFound[] = $oneConsultant;
        }

        return $this->respond(array ('consultantsFound' => $consultantsFound, 'formData' => $formData));
    }

    /**
     * Build consultant response
     * @param object $consultant
     * @return array
     */
    private function buildConsultant($consultant)
    {
        return array('firstName' => $consultant->getFirstName(),
                     'lastName'  => $consultant->getLastName(),
                     'gender'    => $consultant->getGender()->getName(),
                     'speciality' =>  $consultant->getSpeciality(),
                     'professionalStatement' => $consultant->getProfessionalStatement(),


        );
    }

    /**
     * Create a json response object
     * @param array $results
     */
    private function respond($results = array())
    {
        $return = new \stdClass();
        $return->status = 'success';
        $return->count = sizeof($results);
        $return->results = $results;

        $response = new Response(json_encode($return));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
