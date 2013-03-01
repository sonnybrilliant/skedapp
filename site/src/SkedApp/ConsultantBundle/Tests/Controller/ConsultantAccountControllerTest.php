<?php

namespace SkedApp\ConsultantBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Consultant Account controller test
 *
 * @author Ronald Conco <ronald.conco@kaizania.co.za>
 * @package SkedAppServiceBundle
 * @subpackage Tests/Controller
 * @version 0.0.1
 */
class ConsultantAccountControllerTest extends WebTestCase
{

    /**
     * Show list view
     */
    public function testList()
    {
        $client = static::createClient();
        $client->followRedirects(true);


        $crawler = $client->request('GET', '/login');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //check if words are available on the page
        $this->assertGreaterThan(0, $crawler->filter('title:contains("Welcome, please login")')->count());

        // select the login form
        $form = $crawler->selectButton('submit')->form();

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            '_username' => 'qa2@creativecloud.co.za',
            '_password' => '654321',
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are not available on the page
        $this->assertEquals(0, $crawler->filter('title:contains("Welcome, please login")')->count());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage consultants")')->count());

        return;
    }
}
