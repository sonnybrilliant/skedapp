<?php

namespace SkedApp\SearchBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Search controller test
 *
 * @author Otto Saayman <otto.saayman@kaizania.co.za>
 * @package SkedAppSearchBundle
 * @subpackage Tests/Controller
 * @version 0.0.1
 */
class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {

        $client = static::createClient();
        $client->followRedirects(true);

        //open search results page
        $crawler = $client->request('GET', '/search/index');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // select the search form
        $form = $crawler->selectButton('submit')->form();

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            'Search[category]' => 1,
            'Search[address]' => 'Sandton, South Africa',
            'Search[locality]' => 'Sandton',
            'Search[administrative_area_level_2]' => '',
            'Search[administrative_area_level_1]' => '',
            'Search[country]' => 'South Africa',
            'Search[lat]' => '-26.1075261',
            'Search[lng]' => '28.056656699999962',
            'Search[booking_date]' => date('d-m-Y'),
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we found the expected consultant
        $this->assertEquals(1, $crawler->filter('html:contains("View details")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Sonny")')->count());

    }

}
