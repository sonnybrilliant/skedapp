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

    /**
     * Test the search page and the search results
     */
    public function testIndex()
    {

        $client = static::createClient();
        $client->followRedirects(true);

        //open search results page
        $crawler = $client->request('GET', '/search/results');

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

    /**
     * Test the click a time slot to make a booking
     * @runInSeparateProcess
     */
    public function testMake()
    {

        $client = static::createClient();
        $client->followRedirects(true);

        //open search results page
        $crawler = $client->request('GET', '/booking/make/2/2/' . date('d-m-Y', (time() + (60 * 60 * 24))) . '/12:00/1');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //check if make booking required login
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please login")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Having login trouble?")')->count());

        // select the login form
        $form = $crawler->selectButton('submit')->form();

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            '_username' => 'otto@saayman.net',
            '_password' => 'gertygert',
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are not available on the page
        $this->assertEquals(0, $crawler->filter('html:contains("Please login")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Having login trouble?")')->count());

        //open search results page
        $crawler = $client->request('GET', '/booking/make/2/2/' . date('d-m-Y', (time() + (60 * 60 * 24))) . '/12:00/1');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //check if make booking form is displayed
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Appointment Date")')->count());

        // select the search form
        $form = $crawler->selectButton('submit')->form();

        // submit the form which should be pre-populated
        $crawler = $client->submit(
            $form, array()
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        echo $client->getRequest()->getContent();

        //we found the expected consultant
        $this->assertEquals(1, $crawler->filter('html:contains("location")')->count());

    }

}
