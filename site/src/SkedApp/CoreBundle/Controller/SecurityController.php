<?php

namespace SkedApp\CoreBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use Symfony\Component\Security\Core\SecurityContext ;
use Symfony\Component\Form\Extension\Csrf\CsrfProvider\DefaultCsrfProvider;

/**
 * Sercurity manager
 *
 * @author Ronald Conco <ronald.conco@kaizania.co.za>
 * @package SkedAppCoreBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class SecurityController extends Controller
{

    /**
     * login action
     *
     * @return Reponse
     */
    public function loginAction()
    {
        $error = null;

        if ($this->getRequest()->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
           $error = $this->getRequest()->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $this->getRequest()->getSession()->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        $token = new DefaultCsrfProvider($this->container->getParameter('secret'));
        $csrf = $token->generateCsrfToken(md5(time()));

        $bookingAttempt = $this->getRequest()->get('booking_attempt', 0);
        $companyId = $this->getRequest()->get('company_id', 0);
        $consultantId = $this->getRequest()->get('consultant_id', 0);
        $date = $this->getRequest()->get('booking_date', 'nodate');
        $timeSlotStart = $this->getRequest()->get('timeslot_start', 0);
        $serviceIds = $this->getRequest()->get('service_ids', 0);

        $bookingParameters = array ('booking_attempt' => $bookingAttempt,
            'company_id' => $companyId,
            'consultant_id' => $consultantId,
            'booking_date' => $date,
            'timeslot_start' => $timeSlotStart,
            'service_ids' => $serviceIds);

        $_SESSION['booking_params'] = serialize($bookingParameters);

        return $this->render('SkedAppCoreBundle:Security:login.html.twig', array(
          'last_username' => $this->getRequest()->getSession()->get(SecurityContext::LAST_USERNAME),
          'error' => $error,
          'csrf_token' => $csrf,
        ));
    }
    
    /**
     * Show permission denied screen
     * 
     * @return View
     */
    public function accessDeniedAction()
    {
        return $this->render('SkedAppCoreBundle:Security:permission.html.twig');
    }

}
