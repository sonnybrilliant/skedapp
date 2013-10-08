<?php

namespace SkedApp\CoreBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;

/**
 * Days of the week manager
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @version 1.0
 * @package SuleCoreBundle
 * @subpackage Services
 */
final class DaysOfTheWeekManager
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
    ContainerInterface $container , Logger $logger)
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
     * Get day of the week by name
     * 
     * @param type $dayName
     * @return SkedAppCoreBundle:DaysOfTheWeek 
     * 
     * @throws \Exception 
     */
    public function getDayByName($dayName)
    {
        $this->logger->info('get day of the week by name ' . $dayName);

        $dayOfTheWeek = $this->em
                ->getRepository('SkedAppCoreBundle:DaysOfTheWeek')
                ->getName($dayName);

        if (!$dayOfTheWeek) {
            $this->logger->err('Failed to get ' . $dayName . ' day of the week');
            throw new \Exception('Logical exception, no ' . $dayName . ' day of the week found');
        }

        return $dayOfTheWeek;
    }

    /**
     * get day of the week monday
     * @return SkedAppCoreBundle:DaysOfTheWeek 
     */
    public function monday()
    {
        $this->logger->info('get day of the week monday ');
        return $this->getDayByName('Monday');
    }    

    /**
     * get day of the week tuesday
     * @return SkedAppCoreBundle:DaysOfTheWeek 
     */
    public function tuesday()
    {
        $this->logger->info('get day of the week tuesday ');
        return $this->getDayByName('Tuesday');
    }    

    /**
     * get day of the week wednesday
     * @return SkedAppCoreBundle:DaysOfTheWeek 
     */
    public function wednesday()
    {
        $this->logger->info('get day of the week wednesday ');
        return $this->getDayByName('Wednesday');
    }    

    /**
     * get day of the week thursday
     * @return SkedAppCoreBundle:DaysOfTheWeek 
     */
    public function thursday()
    {
        $this->logger->info('get day of the week thursday ');
        return $this->getDayByName('Thursday');
    }    

    /**
     * get day of the week friday
     * @return SkedAppCoreBundle:DaysOfTheWeek 
     */
    public function friday()
    {
        $this->logger->info('get day of the week friday ');
        return $this->getDayByName('Friday');
    }    

    /**
     * get day of the week saturday
     * @return SkedAppCoreBundle:DaysOfTheWeek 
     */
    public function saturday()
    {
        $this->logger->info('get day of the week saturday ');
        return $this->getDayByName('Saturday');
    }    

    /**
     * get day of the week sunday
     * @return SkedAppCoreBundle:DaysOfTheWeek 
     */
    public function sunday()
    {
        $this->logger->info('get day of the week sunday ');
        return $this->getDayByName('Sunday');
    }  
    
    /**
     * Get day by id
     * 
     * @param integer $dayId
     * @return SkedAppCoreBundle:DaysOfTheWeek|boolean
     */
    public function getById($dayId)
    {
        $this->logger->info('get day of the week by id:'.$dayId);
        
        if(1 == $dayId)
        {
            return $this->monday();
        }else if(2 == $dayId){
            return $this->tuesday();
        }else if(3 == $dayId){
            return $this->wednesday();
        }else if(4 == $dayId){
            return $this->thursday();
        }else if(5 == $dayId){
            return $this->friday();
        }else if(6 == $dayId){
            return $this->saturday();
        }else if(7 == $dayId){
            return $this->sunday();
        }
        return false;
    }
    
}