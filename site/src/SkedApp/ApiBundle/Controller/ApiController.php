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
 * @author Ronald Mfana Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppApiBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class ApiController extends Controller
{

    /**
     * Validate session
     * 
     * @param string $session
     * @return boolean
     */
    private function validateSession($session)
    {
        $this->get('logger')->info('validate session');

        $result = $this->getDoctrine()
            ->getRepository('SkedAppCoreBundle:MobileSession')
            ->findOneBySession($session);

        if (!$result) {
            throw new \Exception("invalid session used to query api");
        }

        return true;
    }

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
     * @param string $session user session id
     * 
     * @return json response
     */
    public function getCategoriesAction($session)
    {
        $this->get('logger')->info('get categories');
        $isValid = true;
        $error = '';

        $results = array();

        try {
            $this->validateSession($session);

            $em = $this->getDoctrine()->getEntityManager();
            $categories = $em->getRepository('SkedAppCoreBundle:Category')->findAll();


            if ($categories) {
                foreach ($categories as $category) {
                    $results[] = array('id' => $category->getId(), 'name' => $category->getName());
                }
            }
        } catch (\Exception $e) {
            $isValid = false;
            $error = $e->getMessage();
        }

        $response = new \stdClass();
        $response->status = $isValid;
        $response->error = $error;
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
     * @param $session string
     * @param $category integer
     *
     * @return json response
     */
    public function getServicesAction($session, $category = 1)
    {
        $this->get('logger')->info('get services by id:' . $category);
        $isValid = true;
        $error = '';

        try {
            $this->validateSession($session);

            $em = $this->getDoctrine()->getEntityManager();
            $services = $em->getRepository('SkedAppCoreBundle:Service')->findByCategory($category);
            $results = array();

            if ($services) {
                foreach ($services as $service) {
                    $results[] = array('id' => $service->getId(), 'name' => $service->getName());
                }
            }
        } catch (\Exception $e) {
            $isValid = false;
            $error = $e->getMessage();
        }

        $response = new \stdClass();
        $response->status = $isValid;
        $response->error = $error;
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
     * Authenticate user
     * 
     * @param string $session
     * @param string $password
     * @param string $email
     * 
     * @return json response
     */
    public function loginAction($session, $password, $email)
    {
        $this->get('logger')->info('authenticate user');
        $isValid = true;
        $error = '';

        $code = 1;
        $member = array();

        try {
            $this->validateSession($session);

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
        } catch (\Exception $e) {
            $isValid = false;
            $error = $e->getMessage();
        }

        $response = new \stdClass();
        $response->status = $isValid;
        $response->error = $error;
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
     * Register user
     * 
     * @param string $session
     * @param string $firstName
     * @param string $lastName
     * @param string $mobile
     * @param string $password
     * @param string $email
     * 
     * @return json response
     */
    public function registerCustomerAction($session, $firstName, $lastName, $mobile, $password, $email)
    {
        $this->get('logger')->info('customer login');
        $isValid = true;
        $error = '';
        $code = 1;

        try {
            $this->validateSession($session);

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
        } catch (\Exception $e) {
            $isValid = false;
            $error = $e->getMessage();
        }

        $response = new \stdClass();
        $response->status = $isValid;
        $response->error = $error;
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
    public function searchConsultantAction($session, $category, $service, $date = null, $lat = null, $lng = null, $page = 1)
    {
        $this->get('logger')->info('search for consultant by service id and location');
        $isValid = true;
        $error = '';

        $consultantsList = array();

        try {
            $this->validateSession($session);

            $sort = $this->get('request')->query->get('sort');
            $direction = $this->get('request')->query->get('direction', 'desc');

            $options = array('sort' => $sort,
                'direction' => $direction
            );

            $options['lat'] = $lat;
            $options['lng'] = $lng;
            $options['radius'] = 20;
            $options['category'] = $category;
            $options['service'] = $this->get('service.manager')->getById($service);

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
                    'distance' => round($consultant->getDistanceFromPosition($lat, $lng)),
                    'date' => $strdate->format('l') . ', ' . $strdate->format('j') . ' ' . $strdate->format('M'),
                    'slots' => $slots
                );

                if ($slots['time_slots']) {
                    $consultantsList[] = $tmp;
                }
            }
        } catch (\Exception $e) {
            $isValid = false;
            $error = $e->getMessage();
        }

        $response = new \stdClass();
        $response->status = $isValid;
        $response->error = $error;
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
     * Get consultant by slug
     *
     * @param string $session 
     * @param string $slug
     * 
     * @return json response
     */
    public function getConsultantAction($session, $slug)
    {
        $this->get('logger')->info('get consultant by slug');
        $isValid = true;
        $error = '';

        $results = array();

        try {
            $this->validateSession($session);

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
            $isValid = false;
            $error = $e->getMessage();
        }

        $response = new \stdClass();
        $response->status = $isValid;
        $response->error = $error;
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
     * Make a booking
     *
     * @return json response
     */
    public function bookAction($consultantId, $serviceId, $bookingDateTime, $customerId)
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
