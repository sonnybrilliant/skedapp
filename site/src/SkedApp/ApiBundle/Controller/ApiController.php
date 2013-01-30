<?php

namespace SkedApp\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use SkedApp\CoreBundle\Entity\Customer;
use SkedApp\CustomerBundle\Form\CustomerCreateApiType;

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
     * Create a json response object
     *
     * @param stdClass $params
     */
    private function respond($params)
    {
        $return = new \stdClass();

        if ($params->status) {
            $return->status = true;
            $return->count = sizeof($params->results);
            $return->results = $params->results;
        } else {
            $return->status = false;
            $return->message = $params->error;
        }

        $return->request = $params->request;

        $str = $params->callback . '(' . json_encode($return) . ');';
        $response = new Response($str);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    /**
     * Start a new session
     *
     * @return json response
     */
    public function initAction()
    {
        $this->get('logger')->info('start mobile session');
        //create session
        $session = $this->get('mobile.session.manager')->init();

        $response = new \stdClass();
        $response->status = true;
        $response->request = 'init';
        if (isset($_GET['callback'])) {
            $response->callback = $_GET['callback'];
        } else {
            $response->callback = 'test';
        }


        $response->results = array(
            'session' => $session->getSession(),
            'isLoggedIn' => '',
        );

        return $this->respond($response);
    }

    /**
     * Get categories
     *
     * @return json response
     */
    public function getCategoriesAction($session)
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

        $response = new \stdClass();
        $response->status = true;
        $response->request = 'categories';
        $response->results = $results;
        if (isset($_GET['callback'])) {
            $response->callback = $_GET['callback'];
        } else {
            $response->callback = 'test';
        }

        return $this->respond($response);
    }

    /**
     * Get service
     *
     * @return json response
     */
    public function getServicesAction($session, $id = 1)
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

        $response = new \stdClass();
        $response->status = true;
        $response->request = 'services';
        $response->results = $results;
        if (isset($_GET['callback'])) {
            $response->callback = $_GET['callback'];
        } else {
            $response->callback = 'test';
        }

        return $this->respond($response);
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

        if ($consultant) {
            $results[] = $this->buildConsultant($consultant);
        }

        return $this->respond($results);
    }
    
    /**
     * Search for consultant
     * 
     * @param string $session
     * @param string $category
     * @param string $service
     * @param string $address
     * @param string $date
     * @param string $lat
     * @param string $long
     * @param string $page
     * @return string JSONP
     */
    public function searchConsultantAction($session, $category, $service, $address = null, $date = null, $lat = null, $long = null, $page = 1)
    {
        $this->get('logger')->info('search for consultant by service id and location');

        $isValid = true;
        $sort = $this->get('request')->query->get('sort');
        $direction = $this->get('request')->query->get('direction', 'desc');

        $options = array('sort' => $sort,
            'direction' => $direction
        );

        //if address is not empty, do geo encode
        if (($address != 'undefined')) {
            $results = $this->get('geo_encode.manager')->getGeoEncodedAddress($address);
            if ($results['isValid']) {
                $lat = $results['lat'];
                $long = $results['long'];
            } else {
                $isValid = false;
            }
        } else {
            if (($lat == 'undefined') || ($long == 'undefined')) {
                $isValid = false;
            }
        }

        if ($isValid) {
            $options['lat'] = $lat;
            $options['lng'] = $long;
            $options['radius'] = 5;
            $options['category'] = $category;
            $options['consultantServices'] = $service;

            $consultants = $this->container->get('consultant.manager')->listAllWithinRadius($options);

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $consultants['arrResult'], $this->getRequest()->query->get('page', $page), 10
            );
        }

        $response = new \stdClass();
        $response->status = $isValid;
        $response->request = 'search';
        $response->results = $results = array();
        if (isset($_GET['callback'])) {
            $response->callback = $_GET['callback'];
        } else {
            $response->callback = 'test';
        }

        return $this->respond($response);
    }

    /**
     * Get consultant by service Id and other criteria
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
        $address = array();

        $page = $this->getRequest()->get('page', 1);

        if (!isset($formData['booking_date'])) {
            $formData['booking_date'] = $this->getRequest()->get('date', '');
        }

        if (!isset($formData['lat'])) {
            $formData['lat'] = $this->getRequest()->get('pos_lat', null);
        }

        if (!isset($formData['lng']))
            $formData['lng'] = $this->getRequest()->get('pos_lng', null);

        if (!isset($formData['address'])) {
            $formData['address'] = $this->getRequest()->get('address', null);
        }

        if (!isset($formData['category']))
            $formData['category'] = $this->getRequest()->get('category_id', 0);

        if ((strlen($formData['address']) > 0) && ( (is_null($formData['lng'])) || (is_null($formData['lat'])) )) {
            //Latitude and Longitude not available, so attempt to get those from text location
            //Send the text string address typed by the user and any other information received from the search form to be properly geo-coded
            $address = $this->get('geo_encode.manager')->getGeoEncodedAddress($formData);

            if (!is_null($address['error_message'])) {
                return $this->respond(array('error_message' => $address['error_message'], 'results' => array()), 'results');
            }

            $formData['lat'] = $address['lat'];
            $formData['lng'] = $address['lng'];
        }

        if ((!is_null($formData['lat'])) && (!is_null($formData['lng'])) && ($serviceId > 0)) {

            $options['lat'] = $formData['lat'];
            $options['lng'] = $formData['lng'];
            $options['radius'] = 5;
            $options['category'] = $formData['category'];
            $options['consultantServices'] = $serviceId;
        }

        if ((is_null($formData['lat'])) || (is_null($formData['lng'])) || ($serviceId <= 0) || ($formData['category'] <= 0)) {
            return $this->respond(array('error_message' => 'Please provide some search criteria', 'results' => array()), 'results');
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

        return $this->respond($arrResponse, 'consultantsFound');
    }

    /**
     * Build consultant response
     * @param object $consultant
     * @return array
     */
    private function buildConsultant($consultant)
    {
        return array('firstName' => $consultant->getFirstName(),
            'lastName' => $consultant->getLastName(),
            'gender' => $consultant->getGender()->getName(),
            'speciality' => $consultant->getSpeciality(),
            'professionalStatement' => $consultant->getProfessionalStatement(),
        );
    }

    /**
     * Get Google maps geocoded data from information given
     *
     * @return json response
     */
    public function geoEncodeAddressAction()
    {

        $formData = $this->getRequest()->get('Search');

        if (!isset($formData['lat'])) {
            $formData['lat'] = $this->getRequest()->get('pos_lat', null);
        }

        if (!isset($formData['lng']))
            $formData['lng'] = $this->getRequest()->get('pos_lng', null);

        if (!isset($formData['address'])) {
            $formData['address'] = $this->getRequest()->get('address', null);
        }

        //Send the text string address typed by the user and any other information received from the search form to be properly geo-coded
        $address = $this->get('geo_encode.manager')->getGeoEncodedAddress($formData);

        if (isset($address['error_message'])) {
            $address['results'] = array();
            $results_field = 'results';
        } else {
            $address['results'] = array(1);
            $results_field = 'results';
        }

        return $this->respond($address, $results_field);
    }

    /**
     * Register a user
     *
     * @return json response
     */
    public function registerCustomerAction()
    {
        $return = new \stdClass();
        $formData = $this->getRequest()->get('Customer');

        $customer = new Customer();
        $form = $this->createForm(new CustomerCreateApiType(), $customer);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {

                $this->get('customer.manager')->createCustomer($customer);

                //Set customer to active on mobi/ app registration
                $customer->setIsActive(true);
                $customer->setEnabled(true);
                $customer->setConfirmationToken('');
                $this->container->get('customer.manager')->update($customer);

                //TODO send email
                $tmp = array(
                    'fullName' => $customer->getFirstName() . ' ' . $customer->getLastName(),
                    'link' => $this->generateUrl("_security_login", null, true)
                );

                $options = array();
                $emailBodyHtml = $this->render(
                        'SkedAppCoreBundle:EmailTemplates:customer.account.register.active.html.twig', $tmp
                    )->getContent();


                $emailBodyTxt = $this->render(
                        'SkedAppCoreBundle:EmailTemplates:customer.account.register.active.txt.twig', $tmp
                    )->getContent();

                $options['bodyHTML'] = $emailBodyHtml;
                $options['bodyTEXT'] = $emailBodyTxt;
                $options['email'] = $customer->getEmail();
                $options['fullName'] = $tmp['fullName'];

                $this->get("notification.manager")->customerAccountVerification($options);

                $return->results = array('message' => 'You have successfully registered and activated your account. You are now logged in.', 'customer' => $customer->getObjectAsArray());
            } else {
                $return->status = false;
                $return->error = $form->getErrorsAsString();
            }
        } else {
            $return->status = false;
            $return->error = 'Form submission failed';
        }

        $return->request = 'registerCustomer';
        $return->callback = '';

        return $this->respond($return);
    }

}
