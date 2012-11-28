<?php

namespace SkedApp\ConsultantBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;

/**
 * Consultant manager 
 * 
 * @author Ronald Conco <ronald.conco@kaizania.com>
 * @package SkedAppServiceBundle
 * @subpackage Consultant
 * @version 0.0.1
 */
final class ConsultantManager
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
     * @param  ContainerInterface $container
     * @param  Logger             $logger
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
     * Get consultant by id
     * @param integer $id
     * @return SkedAppCoreBundle:Consultant
     * @throws \Exception 
     */
    public function getById($id)
    {
        $consultant = $this->em->getRepository('SkedAppCoreBundle:Consultant')
            ->find($id);

        if (!$consultant) {
            throw new \Exception('Consultant not found for id:' . $id);
            $this->logger->err('Failed to find Consultant by id:' . $id);
        }

        return $consultant;
    }    

    /**
     * update consultant
     * 
     * @param type $consultant
     * @return void
     */
    public function update($consultant)
    {
        $this->logger->info('Save consultant');
        $this->em->persist($consultant);
        $this->em->flush();
        return;
    }

    /**
     * update consultant
     * 
     * @param type $consultant
     * @return void
     */
    public function delete($consultant)
    {
        $this->logger->info('delete consultant');

        $consultant->setIsDeleted(true);
        $consultant->setIsActive(false);
        $consultant->setIsLocked(true);

        $this->em->persist($consultant);
        $this->em->flush();
        return;
    }

    /**
     * Get all consultants query
     * 
     * @param array $options
     * @return query
     */
    public function listAll($options = array())
    {
        return $this->em
                ->getRepository('SkedAppCoreBundle:Consultant')
                ->getAllActiveConsultantsQuery($options);
    }

}
