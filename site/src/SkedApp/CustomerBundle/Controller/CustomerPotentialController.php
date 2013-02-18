<?php

namespace SkedApp\CustomerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use SkedApp\CoreBundle\Entity\Customer;
use SkedApp\CoreBundle\Entity\Service;
use SkedApp\CustomerBundle\Form\CustomerCreateType;
use SkedApp\CustomerBundle\Form\CustomerShowType;

/**
 * SkedApp\ConsultantBundle\Controller\CustomerController
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCustomerBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class CustomerPotentialController extends Controller
{

    /**
     * Create or update a Potential Customer
     *
     * @return Response
     */
    public function updateAction()
    {
        $this->get('logger')->info('add/ edit potential customer');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('list consultants, access denied.');
            throw new AccessDeniedException();
        }

        $customerPotentialArray = $this->getRequest()->get('customerPotential');

        if ($customerPotentialArray['id'] > 0) {
            $customer = $this->container->get('customer.potential.manager')->find($customerPotentialArray['id']);
        } else {
            $customer = new Customer();
        }
        $form = $this->createForm(new CustomerCreateType(), $customer);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $token = $this->container->get('token.generator')->generateToken();
                $customer->setConfirmationToken($token);

                $this->get('customer.manager')->createCustomer($customer);

                //TODO send email
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

                return $this->redirect($this->generateUrl('sked_app_customer_register_success', array('email' => $customer->getEmail())));
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed to create account, please fix form errors.');
            }
        }

        return $this->render('SkedAppCustomerBundle:Customer:register.account.html.twig', array('form' => $form->createView()));
    }
}
