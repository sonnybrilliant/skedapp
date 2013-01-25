<?php

namespace SkedApp\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use SkedApp\CoreBundle\Entity\Customer;
use SkedApp\CustomerBundle\Form\CustomerCreateApiType;

/**
 * SkedApp\ApiBundle\Controller\ApiTestController
 *
 * @author Otto Saayman <otto.saayman@creativecloud.co.za>
 * @package SkedAppApiBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class ApiTestController extends Controller
{

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
