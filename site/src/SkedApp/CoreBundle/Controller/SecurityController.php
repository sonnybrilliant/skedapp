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

        return $this->render('SkedAppCoreBundle:Security:login.html.twig', array(
          'last_username' => $this->getRequest()->getSession()->get(SecurityContext::LAST_USERNAME),
          'error' => $error,
          'csrf_token' => $csrf
        ));
    }

}
