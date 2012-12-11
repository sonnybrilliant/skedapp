<?php

namespace SkedApp\CoreBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * SkedApp\CoreBundle\EventListener\SecurityListener
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SkedAppCoreBundle
 * @subpackage EventListener
 */
class SecurityListener
{

    /**
     * @var Router $router
     */
    private $router;

    /**
     * @var SecurityContext $security
     */
    private $security;

    /**
     * @var boolean $redirectToAdmin
     */
    private $redirectToAdmin = false;

    /**
     *
     * @var boolean $isLoggedIn
     */
    private $isLoggedIn = false;

    /**
     * Constructs a new instance of SecurityListener.
     *
     * @param Router          $router   The router
     * @param SecurityContext $security The security context
     */
    public function __construct(Router $router, SecurityContext $security)
    {
        $this->router = $router;
        $this->security = $security;
    }

    /**
     * Invoked after a successful login.
     *
     * @param InteractiveLoginEvent $event The event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        if ($this->security->getToken()->getUser()) {
            $this->isLoggedIn = true;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $this->redirectToAdmin = true;
        }
    }

    /**
     * Invoked after the response has been created.
     *
     * @param FilterResponseEvent $event The event
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($this->redirectToAdmin) {
            $event->setResponse(new RedirectResponse($this->router->generate('sked_app_company_list')));
        } else {
            if ($this->isLoggedIn) {
                $user = $this->security->getToken()->getUser();
                if ($user) {

                    if ("SkedApp\CoreBundle\Entity\Customer" == get_class($user)) {
                       $event->setResponse(new RedirectResponse($this->router->generate('_welcome'))); 
                    } else {
                        $event->setResponse(new RedirectResponse($this->router->generate('sked_app_consultant_booking_show', array(
                                    'id' => $this->security->getToken()->getUser()->getId()))));
                    }
                }
            }
        }
    }

}
