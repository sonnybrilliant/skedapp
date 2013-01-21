<?php

namespace SkedApp\ApiBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;

/**
 * Notifications manager
 *
 * @author Otto Saayman <otto.saayman@kaizania.co.za>
 * @version 1.0
 * @package SkedAppApiBundle
 * @subpackage Services
 */
final class GeoEncodeManager
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
     * Return an array containing the correct encoded address from Google maps, or an error message
     *
     * @return array
     */
    public function getGeoEncodedAddress($arrConf = array())
    {
        $this->logger->info("geo encode an address");

        $arrOut = array(
            'error_message' => null,
            'address_string' => '',
            'locality' => '',
            'administrative_area_level_1' => '',
            'administrative_area_level_2' => '',
            'country' => '',
            'lat' => '',
            'lng' => '',
        );

        $geocoder = $this->container->get('ivory_google_map.geocoder');

        $response = $geocoder->geocode($arrConf['address']);

        if (!is_object ($response)) {
            $arrOut['error_message'] = 'Unable to get latitude and longitude from this address';
        }

        $results = $response->getResults();

        if (count($results) > 1) {
          $arrOut['error_message'] = 'We found more than one location for the specified address. Please type your address in more detail.';
        }

        foreach($results as $result) {

            // Get the formatted address
            $location = $result->getGeometry()->getLocation();
            $arrOut['lat'] = $location->getLatitude();
            $arrOut['lng'] = $location->getLongitude();

            $arrOut['address_string'] = $result->getFormattedAddress();

            foreach($result->getAddressComponents() as $addressComponent)
                {
                    $longName = $addressComponent->getLongName();
                    $shortName = $addressComponent->getShortName();
                    $types = $addressComponent->getTypes();
                    $arrOut[$types[0]] = $longName;

                } //foreach

        }

        return $arrOut;
    }

}