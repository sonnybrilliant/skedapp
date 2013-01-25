<?php

namespace SkedApp\CoreBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;
use SkedApp\CoreBundle\Entity\MobileSession;

/**
 * Mobile Session Manager
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SuleCoreBundle
 * @subpackage Services
 */
final class MobileSessionManager
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
     * Initialize session
     * 
     * @return SkedAppCoreBundle:MobileSession
     */
    public function init()
    {
        $this->getLogger()->info('Initialize mobile session');

        $session = new MobileSession();

        $session->setSession(uniqid());
        $this->getEm()->persist($session);
        $this->getEm()->flush();
        return $session;
    }
    
    /**
     * Get by session
     * 
     * @param string $uniqueSession
     * @return SkedAppCoreBundle:MobileSession | boolean
     */
    public function getBySession($uniqueSession)
    {
        $sessions = $this->em->getRepository('SkedAppCoreBundle:MobileSession')
            ->findBySession($uniqueSession);

        if($sessions){
            return $sessions[0];
        }

        return $sessions;
    }

}