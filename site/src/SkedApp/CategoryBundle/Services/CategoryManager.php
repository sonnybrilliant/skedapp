<?php

namespace SkedApp\CategoryBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;


/**
 * Category manager 
 * 
 * @author Ronald Conco <ronald.conco@kaizania.com>
 * @package SkedAppCategoryBundle
 * @subpackage Services
 * @version 0.0.1
 */
final class CategoryManager
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
     * Get service by id
     *
     * @param integer $id
     * @return SkedAppCoreBundle:Category
     * @throws \Exception
     */
    public function getById($id)
    {
        $category = $this->em->getRepository('SkedAppCoreBundle:Category')
            ->find($id);

        if (!$category) {
            throw new \Exception('Category not found for id:' . $id);
            $this->logger->err('Failed to find Service by id:' . $id);
        }

        return $category;
    }   
    
    /**
     * Create and update category
     * 
     * @param SkedApp\CoreBundle\Entity\Category $category
     * @return void
     */
    public function createAndUpdateCategory($category)
    {
        $this->logger->info('Save category');
        $this->em->persist($category);
        $this->em->flush();
        return;
    }


    
    /**
     * Get all category query
     * 
     * @param array $options
     * @return query
     */
    public function listAll($options = array())
    {
        return $this->em
                    ->getRepository('SkedAppCoreBundle:Category')
                    ->getAllActiveCategoryQuery($options);
    }

}
