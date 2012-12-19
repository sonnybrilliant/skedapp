<?php

namespace SkedApp\CustomerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use SkedApp\CoreBundle\Entity\Customer;
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
class CustomerController extends Controller
{

    /**
     * list customers
     *
     * @return View
     * @throws AccessDeniedException
     *
     * @Secure(roles="ROLE_ADMIN")
     */
    public function listAction($page = 1)
    {

        $this->get('logger')->info('list customers');

        $sort = $this->get('request')->query->get('sort');
        $direction = $this->get('request')->query->get('direction', 'desc');

        $options = array('sort' => $sort,
            'direction' => $direction
        );


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $this->container->get('customer.manager')->listAll($options), $this->getRequest()->query->get('page', $page), 10
        );

        return $this->render('SkedAppCustomerBundle:Customer:list.html.twig', array(
                'pagination' => $pagination,
                'sort_img' => '/img/sort_' . $direction . '.png',
                'sort' => $direction,
            ));
    }

    /**
     * show customer
     *
     * @return View
     * @throws createNotFoundException
     *
     * @Secure(roles="ROLE_ADMIN")
     */
    public function showAction($id)
    {
        $this->get('logger')->info('show customers');

        try {
            $customer = $this->get("customer.manager")->getById($id);
            $form = $this->createForm(new CustomerShowType(), $customer);
        } catch (\Exception $e) {
            return $this->createNotFoundException($e->getMessage());
        }

        return $this->render('SkedAppCustomerBundle:Customer:show.html.twig', array('form' => $form->createView()));
    }

    /**
     * show customer bookings
     *
     * @return View
     * @throws createNotFoundException
     *
     * @Secure(roles="ROLE_ADMIN,ROLE_SITE_USER")
     */
    public function listBookingsAction($id,$page=1)
    {

        $bookingParameters = unserialize($_SESSION['booking_params']);

        if ( (is_array($bookingParameters)) && ($bookingParameters['booking_attempt'] == 1) ) {
            $_SESSION['booking_params'] = null;
            return $this->redirect($this->generateUrl('sked_app_booking_make', array(
                    'companyId' => $bookingParameters['company_id'],
                    'consultantId' => $bookingParameters['consultant_id'],
                    'date' => $bookingParameters['booking_date'],
                    'timeSlotStart' => $bookingParameters['timeslot_start'],
                    'serviceIds' => $bookingParameters['service_ids'],
                )
                    ));
        }

        $this->get('logger')->info('show customers');

        try {

            $customer = $this->get("customer.manager")->getById($id);

            $sort = $this->get('request')->query->get('sort');
            $direction = $this->get('request')->query->get('direction', 'desc');

            $options = array('sort' => $sort,
                'direction' => $direction,
                'customer' => $customer
            );

            $paginator = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $this->container->get('booking.manager')->getAllCustomerBookings($options), $this->getRequest()->query->get('page', $page), 10
            );
        } catch (\Exception $e) {
            return $this->createNotFoundException($e->getMessage());
        }

        return $this->render('SkedAppCustomerBundle:Customer:bookings.list.html.twig', array(
                'pagination' => $pagination,
                'sort_img' => '/img/sort_' . $direction . '.png',
                'sort' => $direction,
                'customer' => $customer
            ));
    }

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

    /**
     * Delete customer
     *
     * @return View
     * @throws AccessDeniedException
     *
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction($id)
    {
        $this->get('logger')->info('delete customer id:' . $id);

        try {
            $customer = $this->get("customer.manager")->getById($id);
            $this->get("customer.manager")->delete($customer);
        } catch (\Exception $e) {
            return $this->createNotFoundException($e->getMessage());
        }

        $this->getRequest()->getSession()->setFlash(
            'success', 'Deleted customer sucessfully');
        return $this->redirect($this->generateUrl('sked_app_customer_list'));
    }

}
