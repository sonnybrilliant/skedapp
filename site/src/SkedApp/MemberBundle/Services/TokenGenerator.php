<?php

namespace SkedApp\MemberBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;

/**
 * Token generator
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 0.0.1
 * @package SkedAppMemberBundle
 * @subpackage Services
 */
final class TokenGenerator
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
     * Class construct
     *
     * @param ContainerInterface $container
     * @param Logger $logger
     * @return void
     */
    public function __construct(
    ContainerInterface $container , Logger $logger)
    {
        $this->setContainer($container);
        $this->setLogger($logger);
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

    public function generateToken()
    {
        return base_convert(bin2hex($this->getRandomNumber()) , 16 , 36);
    }

    private function getRandomNumber()
    {
        return hash('sha256' , uniqid(mt_rand() , true) , true);
    }
}
