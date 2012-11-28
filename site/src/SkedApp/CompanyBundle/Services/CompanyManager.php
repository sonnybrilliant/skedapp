<?php

namespace SkedApp\CompanyBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;

/**
 * Company manager
 *
 * @author Ronald Conco <ronald.conco@kaizania.com>
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
     * update company
     *
     * @param type $company
     * @return void
     */
    public function update($company)
    {
        $this->logger->info('Save company');
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
        $this->logger->info('delete company');

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
