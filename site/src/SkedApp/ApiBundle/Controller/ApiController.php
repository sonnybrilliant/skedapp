<?php

namespace SkedApp\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use SkedApp\CoreBundle\Entity\Customer;
use SkedApp\CustomerBundle\Form\CustomerCreateApiType;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

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


        if (isset($params->code)) {
            $return->code = $params->code;
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
     * Get consultant by slug
     *
     * @return json response
     */
    public function getConsultantAction($session, $slug)
    {
        $this->get('logger')->info('get consultant by slug');
        $isValid = true;
        $results = array();
        
        try {
            $consultant = $this->get('consultant.manager')->getBySlug($slug);

            $consultantService = array();

            foreach ($consultant->getConsultantServices() as $service) {
                $tmp = array(
                    'id' => $service->getId(),
                    'name' => $service->getName()
                );
                $consultantService[] = $tmp;
            }


            $results = array(
                'id' => $consultant->getId(),
                'slug' => $consultant->getSlug(),
                'fullName' => $consultant->getFullName(),
                'gender' => $consultant->getGender()->getName(),
                'address' => $consultant->getCompany()->getAddress(),
                'image' => '/uploads/consultants/' . $consultant->getId() . '.' . $consultant->getPath(),
                'services' => $consultantService,
                'servicesProvider' => $consultant->getCompany()->getName(),
                'contact' => $consultant->getCompany()->getContactNumber(),
            );
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request: ' . $e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_consultant_list') . '.html');
        }

        $response = new \stdClass();
        $response->status = $isValid;
        $response->request = 'consultant';
        $response->results = $results;
        if (isset($_GET['callback'])) {
            $response->callback = $_GET['callback'];
        } else {
            $response->callback = 'test';
        }

        return $this->respond($response);
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
        $consultantsList = array();

        $options = array('sort' => $sort,
            'direction' => $direction
        );

        //if address is not empty, do geo encode
        if (($address != 'null')) {
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
            $options['radius'] = 20;
            $options['category'] = $category;
            $options['service'] = $service;

            $consultants = $this->container->get('consultant.manager')->listAllWithinRadius($options);

            foreach ($consultants['results'] as $consultant) {
                $slots = $this->get('booking.manager')->getBookingSlotsForConsultantSearch($consultant, new \DateTime($date));
                $strdate = new \DateTime($date);



                $tmp = array(
                    'id' => $consultant->getId(),
                    'slug' => $consultant->getSlug(),
                    'fullName' => $consultant->getFullName(),
                    'gender' => $consultant->getGender()->getName(),
                    'address' => $consultant->getCompany()->getAddress(),
                    'servicesProvider' => $consultant->getCompany()->getName(),
                    'image' => '/uploads/consultants/' . $consultant->getId() . '.' . $consultant->getPath(),
                    'distance' => round($consultant->getDistanceFromPosition($lat, $long)),
                    'date' => $strdate->format('l') . ', ' . $strdate->format('j') . ' ' . $strdate->format('M'),
                    'slots' => $slots
                );
                //ladybug_dump();
                if ($slots['time_slots']) {
                    $consultantsList[] = $tmp;
                }
            }

//            $paginator = $this->get('knp_paginator');
//            $pagination = $paginator->paginate(
//                $consultants['arrResult'], $this->getRequest()->query->get('page', $page), 10
//            );
        }


        $response = new \stdClass();
        $response->status = $isValid;
        $response->request = 'search';
        $response->results = $consultantsList;
        if (isset($_GET['callback'])) {
            $response->callback = $_GET['callback'];
        } else {
            $response->callback = 'test';
        }

        return $this->respond($response);
    }

    /**
     * Register customer
     * 
     * @param string $session
     * @param string $firstName
     * @param string $lastName
     * @param string $mobile
     * @param string $password
     * @param string $email
     * @return string JSON
     */
    public function registerCustomerAction($session, $firstName, $lastName, $mobile, $password, $email)
    {
        $this->get('logger')->info('register an account');
        $isValid = true;
        $code = 1;
        //check if email is unique;
        $customer = $this->get("customer.manager")->getByEmail($email);
        if ($customer) {
            //email address is already in use
            $code = 2;
        } else {
            $customer = new Customer();
            $customer->setFirstName($firstName);
            $customer->setLastName($lastName);
            $customer->setMobileNumber($mobile);
            $customer->setPassword($password);
            $customer->setEmail($email);
            $customer->isEnabled(true);

            $token = $this->container->get('token.generator')->generateToken();
            $customer->setConfirmationToken($token);

            $this->get('customer.manager')->createCustomer($customer);

            $tmp = array(
                'fullName' => $customer->getFirstName() . ' ' . $customer->getLastName(),
                'link' => $this->generateUrl("sked_app_customer_account_activate", array('token' => $token), true)
            );

            $options = array();
            $emailBodyHtml = $this->render(
                    'SkedAppCoreBundle:EmailTemplates:customer.account.register.html.twig', $tmp
                )->getContent();


            $emailBodyTxt = $this->render(
                    'SkedAppCoreBundle:EmailTemplates:customer.account.register.txt.twig', $tmp
                )->getContent();

            $options['bodyHTML'] = $emailBodyHtml;
            $options['bodyTEXT'] = $emailBodyTxt;
            $options['bodyTEXT'] = 'hello';
            $options['email'] = $customer->getEmail();
            $options['fullName'] = $tmp['fullName'];

            $this->get("notification.manager")->customerAccountVerification($options);
        }

        $response = new \stdClass();
        $response->status = $isValid;
        $response->request = 'register';
        $response->code = $code;
        $response->results = array(
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email
        );
        if (isset($_GET['callback'])) {
            $response->callback = $_GET['callback'];
        } else {
            $response->callback = 'test';
        }

        return $this->respond($response);
    }

    public function loginAction($session, $password, $email)
    {
        $this->get('logger')->info('customer login');
        $isValid = true;
        $code = 1;
        $member = array();

        $customer = $this->get("customer.manager")->getByEmail($email);

        if ($customer) {
            $salt = $customer->getSalt();
            $encoder = new MessageDigestPasswordEncoder('sha512', true, 10);
            $password = $encoder->encodePassword($password, $salt);

            if ($password != $customer->getPassword()) {
                //invalid password
                $code = 3;
            } else {
                if (!$customer->getIsActive()) {
                    //account not active
                    $code = 4;
                } elseif (!$customer->IsEnabled()) {
                    //account not enabled 
                    $code = 5;
                } else {
                    $member['firstName'] = $customer->getFirstName();
                    $member['lastName'] = $customer->getLastName();
                    $member['email'] = $customer->getEmail();
                    $member['mobile'] = $customer->getMobileNumber();
                    $member['id'] = $customer->getId();
                    $member['type'] = 'customer';

                    $session = $this->get('mobile.session.manager')->getBySession($session);

                    if ($session) {
                        $session->setCustomer($customer);
                        $session = $this->get('mobile.session.manager')->updateSession($session);
                    }
                }
            }
        } else {
            //email account not found
            $code = 2;
        }

        $response = new \stdClass();
        $response->status = $isValid;
        $response->request = 'login';
        $response->code = $code;
        $response->results = $member;
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

        $return = new \stdClass();

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
            $return->results = array();
            $return->status = false;
            $return->error = 'Unable to geo encode the address. Please try again.';
        } else {
            $return->results = array('address' => $address);
            $return->status = true;
        }

        $return->request = 'registerCustomer';
        $return->callback = '';

        return $this->respond($return);
    }

    /**
     * Register a user
     *
     * @return json response
     */
    public function registerCustomersAction()
    {

        $return = new \stdClass();
        $formData = $this->getRequest()->get('Customer');
        $isValid = true;
        $errorMessage = '';

        if (!isset($formData['firstName']))
            $formData['firstName'] = '';

        if (!isset($formData['lastName']))
            $formData['lastName'] = '';

        if (!isset($formData['email']))
            $formData['email'] = '';

        if (!isset($formData['password']))
            $formData['password'] = '';

        if (!isset($formData['mobileNumber']))
            $formData['mobileNumber'] = '';

        $customer = new Customer();

        if (strlen($formData['firstName']) <= 0) {
            $isValid = false;
            $errorMessage = 'First name is required';
        }

        if (strlen($formData['email']) <= 0) {
            $isValid = false;
            $errorMessage = 'E-Mail is required';
        }

        if (strlen($formData['password']) <= 0) {
            $isValid = false;
            $errorMessage = 'Password is required';
        }

        if (strlen($formData['mobileNumber']) <= 0) {
            $isValid = false;
            $errorMessage = 'Mobile number is required';
        }

        if ($isValid) {

            $customer->setFirstName($formData['firstName']);
            $customer->setLastName($formData['lastName']);
            $customer->setEmail($formData['email']);
            $customer->setPassword($formData['password']);
            $customer->setMobileNumber($formData['mobileNumber']);

            $this->get('customer.manager')->createCustomer($customer);

            //Set customer to active on mobi/ app registration
            $customer->setIsActive(true);
            $customer->setEnabled(true);
            $customer->setConfirmationToken('');
            $this->container->get('customer.manager')->update($customer);

            //TODO send email
            $tmp = array(
                'fullName' => $customer->getFirstName() . ' ' . $customer->getLastName(),
                'link' => $this->generateUrl("_security_login", array(), true)
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

            $return->status = true;
            $return->results = array('message' => 'You have successfully registered and activated your account. You are now logged in.', 'customer' => $customer->getObjectAsArray());
        } else {
            $return->status = false;
            $return->error = $errorMessage;
        }

        $return->request = 'registerCustomer';
        $return->callback = '';

        return $this->respond($return);
    }

    /**
     * Check if a consultant is available
     *
     * @return json response
     */
    public function checkConsultantAvailableAction($consultantId, $bookingDateTime)
    {

        $bookingStartDateTime = new \DateTime($bookingDateTime);

        //Instantiate a mock booking entity to check availability
        $booking = new \SkedApp\CoreBundle\Entity\Booking();
        $consultant = $this->get('consultant.manager')->getById($consultantId);
        $startTimeSlot = $this->get('timeslots.manager')->getByTime($bookingStartDateTime->format('H:i'));
        $bookingEndDateTime = $bookingStartDateTime->add(new \DateInterval('P' . $consultant->getAppointmentDuration()->getDuration() . 'M'));
        $endTimeSlot = $this->get('timeslots.manager')->getByTime($bookingStartDateTime->format('H:i'));

        $booking->setConsultant($consultant);
        $booking->setAppointmentDate($bookingStartDateTime);
        $booking->setStartTimeslot($startTimeSlot);
        $booking->setEndTimeslot($endTimeSlot);

        //Send the text string address typed by the user and any other information received from the search form to be properly geo-coded
        $available = $this->get('booking.manager')->isBookingDateAvailable($booking);

        $return = new \stdClass();
        $return->request = 'consultantAvailable';
        $return->callback = null;

        if ($available) {
            $return->results = array(1);
            $return->status = true;
        } else {
            $return->results = array();
            $return->status = false;
            $return->error = 'This consultant is not available at that time';
        }

        return $this->respond($return);
    }

    /**
     * Make a booking
     *
     * @return json response
     */
    public function makeBookingAction($consultantId, $serviceId, $bookingDateTime, $customerId)
    {

        $bookingStartDateTime = new \DateTime($bookingDateTime);

        //Instantiate a mock booking entity to check availability
        $booking = new \SkedApp\CoreBundle\Entity\Booking();
        $consultant = $this->get('consultant.manager')->getById($consultantId);
        $service = $this->get('service.manager')->getById($serviceId);
        $customer = $this->get('customer.manager')->getById($customerId);
        $startTimeSlot = $this->get('timeslots.manager')->getByTime($bookingStartDateTime->format('H:i'));
        $bookingEndDateTime = $bookingStartDateTime->add(new \DateInterval('PT' . $consultant->getAppointmentDuration()->getDuration() . 'M'));
        $endTimeSlot = $this->get('timeslots.manager')->getByTime($bookingStartDateTime->format('H:i'));

        $isValid = true;

        $booking->setConsultant($consultant);
        $booking->setService($service);
        $booking->setAppointmentDate($bookingStartDateTime);
        $booking->setStartTimeslot($startTimeSlot);
        $booking->setEndTimeslot($endTimeSlot);
        $booking->setHiddenAppointmentStartTime($bookingStartDateTime->format('Y-m-d H:i'));
        $booking->setHiddenAppointmentEndTime($bookingEndDateTime->format('Y-m-d H:i'));
        $booking->setIsConfirmed(false);
        $booking->setIsLeave(false);

        if (!$this->get('booking.manager')->isTimeValid($booking)) {
            $errMsg = "End time must be greater than start time";
            $isValid = false;
        }

        if (!$this->get('booking.manager')->isBookingDateAvailable($booking)) {
            $errMsg = "Booking not available, please choose another time.";
            $isValid = false;
        }

        if (!$customer instanceOf Customer) {
            $errMsg = "Please register on the site and log in to create a booking.";
            $isValid = false;
        }

        $return = new \stdClass();
        $return->request = 'makeBooking';
        $return->callback = null;

        if ($isValid) {

            $this->get('booking.manager')->save($booking);

            $options = array(
                'booking' => $booking,
                'link' => $this->generateUrl("sked_app_booking_edit", array('bookingId' => $booking->getId()), true)
            );

            //send booking created notification emails
            $this->get("notification.manager")->createdBooking($options);

            $return->results = array(1);
            $return->status = true;
        } else {
            $return->results = array();
            $return->status = false;
            $return->error = $errMsg;
        }

        return $this->respond($return);
    }

}
