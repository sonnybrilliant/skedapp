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

        if ( (isset($params['attachments_data'])) && (count($params['attachments_data']) > 0) ) {
          //Need to attach files using data
          foreach ($params['attachments_data'] as $file) {
            $attachment = \Swift_Attachment::newInstance($file['file_data'], $file['file_name'], $file['file_mime']);
            $message->attach($attachment);
          }
        }

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
     * Send booking created to customers
     *
     * @param array $params
     * @return void
     */
    public function bookingCreatedCustomer($params)
    {
        $this->logger->info("send booking scheduled notification to customer");
        $options['subject'] = "Your SkedApp booking created and awaiting confirmation";

        $booking = $params['booking'];

        $tmp = array(
            'user' => $booking->getCustomer(),
            'consultant' => $booking->getConsultant(),
            'provider' => $booking->getConsultant()->getCompany(),
            'service' => $booking->getService(),
            'date' => $booking->getHiddenAppointmentStartTime()->format("Y-m-d H:i"),
            'company' => $booking->getConsultant()->getCompany()
        );

        $emailBodyHtml = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:booking.created.customer.html.twig', $tmp
        );

        $emailBodyTxt = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:booking.created.customer.txt.twig', $tmp
        );

        $options['bodyHTML'] = $emailBodyHtml;
        $options['bodyTEXT'] = $emailBodyTxt;
        $options['email'] = $booking->getCustomer()->getEmail();
        $options['fullName'] = $tmp['user']->getFullName();

        $this->sendMail($options);
        return;
    }

    /**
     * Send booking created e-mail to company
     *
     * @param array $params
     * @return void
     */
    public function bookingConfirmationCompany($params)
    {
        $this->logger->info('sending new booking for company');
        $options['subject'] = "New Booking Created - Confirmation Needed";

        $admins = $this->container->get("member.manager")
            ->getServiceProviderAdmin($params['booking']->getConsultant()->getCompany()->getId());

        $booking = $params['booking'];

        if ($admins) {
            foreach ($admins as $user) {
                $tmp = array(
                    'user' => $user,
                    'consultant' => $booking->getConsultant(),
                    'link' => $params['link'],
                    'service' => $booking->getService(),
                    'customer' => $booking->getCustomer(),
                    'fullName' => $booking->getCustomer()->getFullName(),
                    'date' => $booking->getHiddenAppointmentStartTime()->format("Y-m-d H:i")
                );


                $emailBodyHtml = $this->template->render(
                    'SkedAppCoreBundle:EmailTemplates:booking.created.company.html.twig', $tmp
                );

                $emailBodyTxt = $this->template->render(
                    'SkedAppCoreBundle:EmailTemplates:booking.created.company.txt.twig', $tmp
                );

                $options['bodyHTML'] = $emailBodyHtml;
                $options['bodyTEXT'] = $emailBodyTxt;
                $options['email'] = $user->getEmail();
                $options['fullName'] = $user->getFullName();

                $this->sendMail($options);
            }
        }

        return;
    }

    /**
     * Send booking confirmation to consultants
     *
     * @param array $params
     * @return void
     */
    public function bookingConfirmationConsultant($params)
    {

        $this->logger->info('sending new booking for consultant');
        $options['subject'] = "New Booking Created";

        $booking = $params['booking'];

        $tmp = array(
            'user' => $booking->getConsultant(),
            'consultant' => $booking->getConsultant(),
            'link' => $params['link'],
            'service' => $booking->getService(),
            'customer' => $booking->getCustomer(),
            'fullName' => $booking->getCustomer()->getFullName(),
            'date' => $booking->getHiddenAppointmentStartTime()->format("Y-m-d H:i")
        );


        $emailBodyHtml = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:booking.created.consultant.html.twig', $tmp
        );

        $emailBodyTxt = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:booking.created.consultant.txt.twig', $tmp
        );

        $options['bodyHTML'] = $emailBodyHtml;
        $options['bodyTEXT'] = $emailBodyTxt;
        $options['email'] = $booking->getConsultant()->getEmail();
        $options['fullName'] = $booking->getConsultant()->getFullName();

        $this->sendMail($options);

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
            'user' => $booking->getCustomer(),
            'consultant' => $booking->getConsultant(),
            'provider' => $booking->getConsultant()->getCompany(),
            'service' => $booking->getService(),
            'date' => $booking->getHiddenAppointmentStartTime()->format("Y-m-d H:i"),
            'company' => $booking->getConsultant()->getCompany()
        );

        $emailBodyHtml = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:booking.confirmed.customer.html.twig', $tmp
        );

        $emailBodyTxt = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:booking.confirmed.customer.txt.twig', $tmp
        );

        $options['bodyHTML'] = $emailBodyHtml;
        $options['bodyTEXT'] = $emailBodyTxt;
        $options['email'] = $booking->getCustomer()->getEmail();
        $options['fullName'] = $tmp['user']->getFullName();

        if ( (isset($params['attachments_data'])) && (count($params['attachments_data']) > 0) )
          $options['attachments_data'] = $params['attachments_data'];

        $this->sendMail($options);
        return;
    }

    /**
     * Send booking message to customers
     *
     * @param array $params
     * @return void
     */
    public function bookingMessageFromCompany($params)
    {
        $this->logger->info("send booking message to customer");
        $options['subject'] = "Message regarding your SkedApp booking";

        $booking = $params['booking'];

        if (!isset ($params['messageText']))
            $params['messageText'] = '';

        if (strlen ($params['messageText']) > 0)
            $params['messageText'] = 'This is the message from ' . $booking->getConsultant()->getCompany()->getName() . ': ' . $params['messageText'];
        else
            $params['messageText'] = 'Please take note of your booking details.';

        $tmp = array(
            'user' => $booking->getCustomer(),
            'consultant' => $booking->getConsultant(),
            'provider' => $booking->getConsultant()->getCompany(),
            'service' => $booking->getService(),
            'date' => $booking->getHiddenAppointmentStartTime()->format("Y-m-d H:i"),
            'company' => $booking->getConsultant()->getCompany(),
            'messageText' => $params['messageText'],
        );

        $emailBodyHtml = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:booking.message.customer.html.twig', $tmp
        );

        $emailBodyTxt = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:booking.message.customer.txt.twig', $tmp
        );

        $options['bodyHTML'] = $emailBodyHtml;
        $options['bodyTEXT'] = $emailBodyTxt;
        $options['email'] = $booking->getCustomer()->getEmail();
        $options['fullName'] = $tmp['user']->getFullName();

        if ( (isset($params['attachments_data'])) && (count($params['attachments_data']) > 0) )
          $options['attachments_data'] = $params['attachments_data'];

        $this->sendMail($options);
        return;
    }

    /**
     * Send booking message to customers
     *
     * @param array $params
     * @return void
     */
    public function bookingMessageCancelFromCompany($params)
    {
        $this->logger->info("send booking message with cancellation to customer");
        $options['subject'] = "Your SkedApp booking has been cancelled";

        $booking = $params['booking'];

        if (!isset ($params['messageText']))
            $params['messageText'] = '';

        if (strlen ($params['messageText']) > 0)
            $params['messageText'] = 'This is the message from ' . $booking->getConsultant()->getCompany()->getName() . ': ' . $params['messageText'];
        else
            $params['messageText'] = 'Please take note of your cancelled booking.';

        $tmp = array(
            'user' => $booking->getCustomer(),
            'consultant' => $booking->getConsultant(),
            'provider' => $booking->getConsultant()->getCompany(),
            'service' => $booking->getService(),
            'date' => $booking->getHiddenAppointmentStartTime()->format("Y-m-d H:i"),
            'company' => $booking->getConsultant()->getCompany(),
            'messageText' => $params['messageText'],
            'booking' => $booking,
        );

        $emailBodyHtml = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:booking.message.cancel.customer.html.twig', $tmp
        );

        $emailBodyTxt = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:booking.message.cancel.customer.txt.twig', $tmp
        );

        $options['bodyHTML'] = $emailBodyHtml;
        $options['bodyTEXT'] = $emailBodyTxt;
        $options['email'] = $booking->getCustomer()->getEmail();
        $options['fullName'] = $tmp['user']->getFullName();

        if ( (isset($params['attachments_data'])) && (count($params['attachments_data']) > 0) )
          $options['attachments_data'] = $params['attachments_data'];

        $this->sendMail($options);
        return;
    }

    /**
     * Send booking reminder to customers
     *
     * @param array $params
     * @return void
     */
    public function bookingReminderCustomer($params)
    {
        $this->logger->info("send booking reminder to customer");
        $options['subject'] = "Your SkedApp booking reminder for day";

        $booking = $params['booking'];

        $tmp = array(
            'fullName' => $booking->getCustomer()->getFirstName() . ' ' . $booking->getCustomer()->getLastName(),
            'consultant' => $booking->getConsultant(),
            'provider' => $booking->getConsultant()->getCompany(),
            'service' => $booking->getService(),
            'date' => $booking->getHiddenAppointmentStartTime()->format("Y-m-d H:i"),
            'company' => $booking->getConsultant()->getCompany()
        );


        $emailBodyHtml = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:booking.reminder.customer.html.twig', $tmp
        );

        $emailBodyTxt = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:booking.reminder.customer.txt.twig', $tmp
        );

        $options['bodyHTML'] = $emailBodyHtml;
        $options['bodyTEXT'] = $emailBodyTxt;
        $options['email'] = $booking->getCustomer()->getEmail();
        $options['fullName'] = $tmp['fullName'];

        $this->sendMail($options);
        return;
    }

    /**
     * Send booking cancellation to customers
     *
     * @param array $params
     * @return void
     */
    public function bookingCancellationCustomer($params)
    {
        $this->logger->info("send booking cancellation confimation to customer");
        $options['subject'] = "Your SkedApp booking cancellation confirmed";

        $booking = $params['booking'];

        $tmp = array(
            'fullName' => $booking->getCustomer()->getFirstName() . ' ' . $booking->getCustomer()->getLastName(),
            'consultant' => $booking->getConsultant()->getFirstName() . ' ' . $booking->getConsultant()->getLastName(),
            'service' => $booking->getService()->getName(),
            'date' => $booking->getHiddenAppointmentStartTime()->format("r"),
        );


        $emailBodyHtml = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:booking.cancel.customer.html.twig', $tmp
        );

        $emailBodyTxt = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:booking.cancel.customer.txt.twig', $tmp
        );

        $options['bodyHTML'] = $emailBodyHtml;
        $options['bodyTEXT'] = $emailBodyTxt;
        $options['email'] = $booking->getCustomer()->getEmail();
        $options['fullName'] = $tmp['fullName'];

        $this->sendMail($options);
        return;
    }

     /**
     * Send booking created e-mail to company
     *
     * @param array $params
     * @return void
     */
    public function bookingCancellationCompany($params)
    {
        $this->logger->info('sending new booking for company');
        $options['subject'] = "Your SkedApp customer booking cancellation confirmed";

        $booking = $params['booking'];

        $admins = $this->container->get("member.manager")
            ->getServiceProviderAdmin($params['booking']->getConsultant()->getCompany()->getId());

        if ($admins) {
            foreach ($admins as $admin) {
                $tmp = array(
                    'fullName' => $admin->getFirstName() . ' ' . $admin->getLastName(),
                    'consultant' => $booking->getConsultant()->getFirstName() . ' ' . $booking->getConsultant()->getLastName(),
                    'service' => $booking->getService()->getName(),
                    'date' => $booking->getHiddenAppointmentStartTime()->format("r"),
                );


                $emailBodyHtml = $this->template->render(
                    'SkedAppCoreBundle:EmailTemplates:booking.cancel.company.html.twig', $tmp
                );

                $emailBodyTxt = $this->template->render(
                    'SkedAppCoreBundle:EmailTemplates:booking.cancel.company.txt.twig', $tmp
                );

                $options['bodyHTML'] = $emailBodyHtml;
                $options['bodyTEXT'] = $emailBodyTxt;
                $options['email'] = $admin->getEmail();
                $options['fullName'] = $tmp['fullName'];

                $this->sendMail($options);
            }
        }

        return;
    }

     /**
     * Send booking cancellation to customers
     *
     * @param array $params
     * @return void
     */
    public function bookingCancellationConsultant($params)
    {
        $this->logger->info("send booking cancellation confimation to customer");
        $options['subject'] = "Your SkedApp customer booking cancellation confirmed";

        $booking = $params['booking'];

        $tmp = array(
            'fullName' => $booking->getConsultant()->getFirstName() . ' ' . $booking->getConsultant()->getLastName(),
            'service' => $booking->getService()->getName(),
            'date' => $booking->getHiddenAppointmentStartTime()->format("r"),
        );


        $emailBodyHtml = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:booking.cancel.consultant.html.twig', $tmp
        );

        $emailBodyTxt = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:booking.cancel.consultant.txt.twig', $tmp
        );

        $options['bodyHTML'] = $emailBodyHtml;
        $options['bodyTEXT'] = $emailBodyTxt;
        $options['email'] = $booking->getCustomer()->getEmail();
        $options['fullName'] = $tmp['fullName'];

        $this->sendMail($options);
        return;
    }

    public function inviteFriendLoggedin($params)
    {
        $this->logger->info("send invite friend to logged in");
        $options['subject'] = "Your SkedApp friend invite";

        $tmp = array(
            'fullName' => $params['friendName'],
            'senderName' => $params['senderName'],
            'link' => $params['link'],
        );


        $emailBodyHtml = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:invite.friend.loggedin.html.twig', $tmp
        );

        $emailBodyTxt = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:invite.friend.loggedin.txt.twig', $tmp
        );

        $options['bodyHTML'] = $emailBodyHtml;
        $options['bodyTEXT'] = $emailBodyTxt;
        $options['email'] = $params['email'];
        $options['fullName'] = $tmp['fullName'];

        $this->sendMail($options);
        return;
    }

    public function inviteFriendConsultant($params)
    {
        $this->logger->info("send invite friend to consultant");
        $options['subject'] = "Your SkedApp friend invite";

        $tmp = array(
            'fullName' => $params['friendName'],
            'senderName' => $params['senderName'],
            'link' => $params['link'],
            'consultantName' => $params['consultantName']
        );


        $emailBodyHtml = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:invite.friend.consultant.html.twig', $tmp
        );

        $emailBodyTxt = $this->template->render(
            'SkedAppCoreBundle:EmailTemplates:invite.friend.consultant.txt.twig', $tmp
        );

        $options['bodyHTML'] = $emailBodyHtml;
        $options['bodyTEXT'] = $emailBodyTxt;
        $options['email'] = $params['email'];
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