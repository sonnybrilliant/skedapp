<?php

namespace SkedApp\CompanyBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;

/**
 * Service provider manager
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppServiceBundle
 * @subpackage Company
 * @version 0.0.1
 */
final class CompanyManager
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
     * Get service provider by id
     *
     * @param integer $id
     * @return SkedAppCoreBundle:Company
     * @throws \Exception
     */
    public function getById($id)
    {
        $company = $this->em->getRepository('SkedAppCoreBundle:Company')
            ->find($id);

        if (!$company) {
            throw new \Exception(' Service Provider not found for id:' . $id);
            $this->logger->err(' Failed to find Service Provider by id:' . $id);
        }

        return $company;
    }

    /**
     * Create new service provider
     *
     * @param type $company
     * @return void
     */
    public function create($company)
    {
        $this->logger->info('Create new service provider');
        $this->em->persist($company);
        $this->em->flush();
        return;
    }

    /**
     * update company
     *
     * @param type $company
     * @return void
     */
    public function update($company)
    {
        $this->logger->info('Save Service Provider');
        $this->em->persist($company);
        $this->em->flush();
        return;
    }

    /**
     * update company
     *
     * @param type $company
     * @return void
     */
    public function delete($company)
    {
        $this->logger->info('delete Service Provider');

        $company->setIsDeleted(true);
        $company->setIsActive(false);
        $company->setIsLocked(true);

        $this->em->persist($company);
        $this->em->flush();
        return;
    }

    /**
     * Get all companys query
     *
     * @param array $options
     * @return query
     */
    public function listAll($options = array())
    {
        return $this->em
                ->getRepository('SkedAppCoreBundle:Company')
                ->getAllActiveCompaniesQuery($options);
    }

}
