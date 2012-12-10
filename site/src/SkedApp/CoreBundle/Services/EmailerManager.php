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
     * Send booking created e-mail to consultant
     *
     * @param array $params
     * @return void
     */
    public function consultantBookingCreated($params)
    {
        $this->logger->info('sending new booking for consultant mail to:' . $params['email']);
        $params['subject'] = "New Booking Created";
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
        $this->logger->info('sending new booking for company mail to:' . $params['email']);
        $params['subject'] = "New Booking Created";
        $this->sendMail($params);
        return;
    }

}