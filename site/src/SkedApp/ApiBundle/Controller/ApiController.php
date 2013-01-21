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
     * Get consultant by service Id and other criteria
     *
     * @return json response
     */
    public function getConsultantsAction($serviceId = 1)
    {
        $this->get('logger')->info('get consultant by category, service id and location');

        $user = null;

        if ($this->getRequest()->get('user_id'))
          $user = $this->get('customer.manager')->getById($this->getRequest()->get('user_id'));

        $sort = $this->get('request')->query->get('sort');
        $direction = $this->get('request')->query->get('direction', 'desc');

        $options = array('sort' => $sort,
            'direction' => $direction
        );

        //Instantiate search form
        $formData = $this->getRequest()->get('Search');
        $address = array();

        $page = $this->getRequest()->get('page', 1);

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

            //Send the text string address typed by the user and any other information received from the search form to be properly geo-coded
            $address = $this->get('geo_encode.manager')->getGeoEncodedAddress($formData);

            if (!is_null ($address['error_message'])) {
                return $this->respond(array('error_message' => $address['error_message']));
            }

            $formData['lat'] = $address['lat'];
            $formData['lng'] = $address['lng'];

        }

        if ( (!is_null($formData['lat'])) && (!is_null($formData['lng'])) && ($serviceId > 0) ) {

            $options['lat'] = $formData['lat'];
            $options['lng'] = $formData['lng'];
            $options['radius'] = 5;
            $options['category'] = $formData['category'];
            $options['consultantServices'] = $serviceId;

        }

        if ( (is_null($formData['lat'])) || (is_null($formData['lng'])) || ($serviceId <= 0) || ($formData['category'] <= 0) ) {
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

        $arrResponse = array('consultantsFound' => $consultantsFound, 'formData' => $formData, 'parametersToSearch' => $options, 'fullAddress' => $address);

        if (is_object($user))
            $arrResponse['user'] = $user->getObjectAsArray();

        return $this->respond($arrResponse);
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

    /**
     * Get Google maps geocoded data from information given
     *
     * @return json response
     */
    public function geoEncodeAddressAction()
    {

        $formData = $this->getRequest()->get('Search');

        if (!isset($formData['lat'])){
          $formData['lat'] = $this->getRequest()->get('pos_lat', null);
        }

        if (!isset($formData['lng']))
          $formData['lng'] = $this->getRequest()->get('pos_lng', null);

        if (!isset($formData['address'])){
          $formData['address'] = $this->getRequest()->get('address', null);
        }

        //Send the text string address typed by the user and any other information received from the search form to be properly geo-coded
        $address = $this->get('geo_encode.manager')->getGeoEncodedAddress($formData);

        return $this->respond($address);
    }
}
