<?php

namespace SkedApp\CompanyBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;

/**
 * Service provider photos manager
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppServiceBundle
 * @subpackage CompanyPhotos
 * @version 0.0.1
 */
final class CompanyPhotosManager
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
     * upload service provider photo
     *
     * @param SkedAppCoreBundle:CompanyPhotos $serviceProviderPhoto
     * @param Integer $serviceProviderId Service provider id
     * @return void
     */
    public function upload($serviceProviderPhoto, $serviceProviderId)
    {
        $this->logger->info('Upload service provider photo');

        try {
            $company = $this->getContainer()->get('company.manager')->getById($serviceProviderId);
            $serviceProviderPhoto->setCompany($company);
            $this->em->persist($serviceProviderPhoto);
            $this->em->flush();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        
        return;
    }

    /**
     * update service provider photo
     *
     * @param type $company_photo
     * @return void
     */
    public function update($company_photo)
    {
        $this->logger->info('Save service provider photo');
        $this->em->persist($company_photo);
        $this->em->flush();
        return;
    }

    /**
     * update service provider photo
     *
     * @param type $company_photo
     * @return void
     */
    public function delete($company_photo)
    {
        $this->logger->info('delete service provider photo');

        $company_photo->setIsDeleted(true);
        $company_photo->setIsActive(false);
        $company_photo->setIsLocked(true);

        $this->em->persist($company_photo);
        $this->em->flush();
        return;
    }

}
