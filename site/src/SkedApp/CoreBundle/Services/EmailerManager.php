<?php

namespace SkedApp\CoreBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;

/**
 * Emailer manager
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SuleCoreBundle
 * @subpackage Services
 */
final class EmailerManager
{

    /**
     * Service Container
     * @var object
     */
    private $container = null;

    /**
     * Monolog logger
     * @var object
     */
    private $logger = null;

    /**
     * Entity manager
     * @var object
     */
    private $em;

    /**
     * Template engine
     * @var object 
     */
    private $template;

    /**
     * Class construct
     *
     * @param ContainerInterface $container
     * @param Logger $logger
     * @return void
     */
    public function __construct(
    ContainerInterface $container, Logger $logger)
    {
        $this->setContainer($container);
        $this->setLogger($logger);
        $this->setEm($container->get('doctrine')->getEntityManager('default'));
        $this->setTemplate($container->get('templating'));
        return;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getLogger()
    {
        return $this->logger;
    }

    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    public function getEm()
    {
        return $this->em;
    }

    public function setEm($em)
    {
        $this->em = $em;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Send simple email
     *
     * @param array $params
     * @return void
     */
    public function sendMail($params)
    {
        $this->logger->info('sending mail to:' . $params['email']);
        $message = \Swift_Message::newInstance()
            ->setSubject($params['subject'])
            ->setFrom(array(
                $this->container->getParameter('mailer_from_mail')
                => $this->container->getParameter('mailer_from_name'))
            )
            ->setTo(array($params['email'] => $params['fullName']))
            ->setBody($params['bodyHTML'], 'text/html')
            ->addPart($params['bodyTEXT'], 'text/plain');

        ;

        $this->container->get('mailer')->send($message);
        return;
    }

    /**
     * Send registration email
     *
     * @param array $params
     * @return void
     */
    public function memberRegistration($params)
    {
        $this->logger->info('sending registration email to:' . $params['email']);
        $params['subject'] = "Welcome to SkedApp";
        $this->sendMail($params);
        return;
    }

    /**
     * Send forgot password email
     *
     * @param array $params
     * @return void
     */
    public function memberForgotPassword($params)
    {
        $this->logger->info('sending registration email to:' . $params['email']);
        $params['subject'] = "Reset Your SkedApp Password";
        $this->sendMail($params);
        return;
    }

    /**
     * Send booking created e-mail to company
     *
     * @param array $params
     * @return void
     */
    public function companyBookingCreated($params)
    {
        $this->logger->info('sending new booking for company');
        $options['subject'] = "New Booking Created";

        $admins = $this->container->get("member.manager")
            ->getServiceProviderAdmin($params['booking']->getConsultant()->getCompany()->getId());

        if ($admins) {
            foreach ($admins as $admin) {
                $tmp = array(
                    'fullName' => $admin->getFirstName() . ' ' . $admin->getLastName(),
                    'consultantName' => $params['booking']->getConsultant()->getFirstName() . ' ' . $params['booking']->getConsultant()->getLastName(),
                    'link' => $params['link']
                );


                $emailBodyHtml = $this->template->render(
                    'SkedAppCoreBundle:EmailTemplates:booking.created.company.html.twig', $tmp
                );

                $emailBodyTxt = $this->template->render(
                    'SkedAppCoreBundle:EmailTemplates:booking.created.company.txt.twig', $tmp
                );

                $options['bodyHTML'] = $emailBodyHtml;
                $options['bodyTEXT'] = $emailBodyTxt;
                $options['bodyTEXT'] = 'hello';
                $options['email'] = $admin->getEmail();
                $options['fullName'] = $tmp['fullName'];

                $this->sendMail($options);
            }
        }

        return;
    }
    
    /**
     * Send booking confirmation to customers
     * 
     * @param array $params
     * @return void
     */
    public function bookingConfirmationCustomer($params)
    {
        $this->logger->info("send booking confimation to customer");
        $options['subject'] = "Your SkedApp booking confirmation";

        $booking = $params['booking'];
        
        $tmp = array(
            'fullName' => $booking->getCustomer()->getFirstName() . ' ' . $booking->getCustomer()->getLastName(),
            'consultant' => $booking->getConsultant()->getFirstName() . ' ' . $booking->getConsultant()->getLastName(),
            'service' => $booking->getService()->getName(),
            'date' => $booking->getHiddenAppointmentStartTime()->format("r"),
        );


        $emailBodyHtml = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:booking.created.customer.html.twig', $tmp
        );

        $emailBodyTxt = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:booking.created.customer.txt.twig', $tmp
        );

        $options['bodyHTML'] = $emailBodyHtml;
        $options['bodyTEXT'] = $emailBodyTxt;
        $options['bodyTEXT'] = 'hello';
        $options['email'] = $booking->getCustomer()->getEmail();
        $options['fullName'] = $tmp['fullName'];

        $this->sendMail($options);
        return;
    }

    /**
     * Send customer account verification after an account register
     *
     * @param array $params
     * @return void
     */
    public function verifyCustomerAccount($params)
    {
        $this->logger->info('sending customer account verification');
        $params['subject'] = "You have successfully created your account";
        $this->sendMail($params);
        return;
    }

}