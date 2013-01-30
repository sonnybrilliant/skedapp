<?php

namespace SkedApp\ApiBundle\Services;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Logger;

/**
 * Geo Encode manager
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
     * Search for consultants using specified address.
     * 
     * @param string $address
     * @return array
     */
    public function getGeoEncodedAddress($address)
    {
        $this->logger->info("geo encode an address");

        $output = array(
            'errorMessage' => null,
            'isValid' => true,
            'addressString' => '',
            'locality' => '',
            'administrativeAreaLevel1' => '',
            'administrativeAreaLevel2' => '',
            'country' => '',
            'lat' => '',
            'long' => '',
        );

        $geocoder = $this->container->get('ivory_google_map.geocoder');

        $response = $geocoder->geocode($address);

        if (!is_object($response)) {
            $output['errorMessage'] = 'Unable to get latitude and longitude from this address';
            $output['isValid'] = false;
        } else {
            $results = $response->getResults();

            if (count($results) > 1) {
                $output['errorMessage'] = 'We found more than one location for the specified address. Please type your address in more detail.';
            }

            foreach ($results as $result) {
                // Get the formatted address
                $location = $result->getGeometry()->getLocation();
                $output['lat'] = $location->getLatitude();
                $output['long'] = $location->getLongitude();

                $output['addressString'] = $result->getFormattedAddress();

                foreach ($result->getAddressComponents() as $addressComponent) {
                    $longName = $addressComponent->getLongName();
                    //$shortName = $addressComponent->getShortName();
                    $types = $addressComponent->getTypes();
                    $output[$types[0]] = $longName;
                } //foreach
            }
        }
        return $output;
    }

}