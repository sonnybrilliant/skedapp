<?php

namespace SkedApp\CustomerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use SkedApp\CoreBundle\Entity\Customer;
use SkedApp\CustomerBundle\Form\CustomerCreateType;

/**
 * SkedApp\ConsultantBundle\Controller\CustomerController
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppCustomerBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class CustomerController extends Controller
{

    /**
     * Register an account
     * 
     * @return Response
     */
    public function registerAction()
    {
        $this->get('logger')->info('new customer registration');

        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('_welcome'));
        }
        
        $customer = new Customer();
        $form = $this->createForm(new CustomerCreateType(), $customer);

        return $this->render('SkedAppCustomerBundle:Customer:register.account.html.twig', array('form' => $form->createView()));
    }

    /**
     * Register an account
     * 
     * @return Response
     */
    public function registerAccountAction()
    {
        $this->get('logger')->info('new customer registration');

        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('_welcome'));
        }
        
        $customer = new Customer();
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

    /**
     * Register an account
     * 
     * @return Response
     */
    public function registerSuccessAction($email)
    {
        $this->get('logger')->info('Customer was successfully created');
        
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('_welcome'));
        }
        
        return $this->render('SkedAppCustomerBundle:Customer:register.account.successful.html.twig');
    }

    /**
     * Account activate
     * 
     * @return Response
     */
    public function accountActivateAction($token)
    {
        $this->get('logger')->info('account activate token' . $token);

        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('_welcome'));
        }

        $customer = $this->container->get('customer.manager')->getByToken($token);

        if ($customer) {
            $customer->setIsActive(true);
            $customer->setEnabled(true);
            $customer->setConfirmationToken('');
            $this->container->get('customer.manager')->update($customer);
            
            return $this->render("SkedAppCustomerBundle:Customer:register.account.activated.html.twig");
        }

        return $this->render('SkedAppMemberBundle:Reset:reset.invalid.tokent.html.twig');
    }

}
