<?php

namespace SkedApp\ServiceBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Service controller test 
 * 
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppServiceBundle
 * @subpackage Tests/Controller
 * @version 0.0.1
 */
class ServiceControllerTest extends WebTestCase
{

    /**
     * List services
     * 
     * @return void
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
            '_username' => 'ronald.conco@creativecloud.co.za',
            '_password' => '654321',
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are not available on the page
        $this->assertEquals(0, $crawler->filter('title:contains("Welcome, please login")')->count());

        //go to list view page
        $crawler = $client->request('GET', '/service/list.html');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //check if user landed on the service provider list pager
        $this->assertEquals(1, $crawler->filter('title:contains("Manage services")')->count());

        return;
    }

    /**
     * create services
     * 
     * @return void
     */
    public function testCreate()
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
            '_username' => 'ronald.conco@creativecloud.co.za',
            '_password' => '654321',
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are not available on the page
        $this->assertEquals(0, $crawler->filter('title:contains("Welcome, please login")')->count());

        //go to list view page
        $crawler = $client->request('GET', '/service/create.html');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //check if user landed on the service provider list pager
        $this->assertEquals(1, $crawler->filter('title:contains("Add service")')->count());
        
        // select the add new company form
        $form = $crawler->selectButton('submit')->form();

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            'Service[name]' => 'service-' . rand(1, 2000),
            'Service[description]' => 'Service description',
            'Service[category]' => '1',
            'Service[appointmentDuration]' => '2',
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage services")')->count());
        return;
    }

    
    /**
     * Edit services
     * 
     * @return void
     */
    public function testEdit()
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
            '_username' => 'ronald.conco@creativecloud.co.za',
            '_password' => '654321',
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are not available on the page
        $this->assertEquals(0, $crawler->filter('title:contains("Welcome, please login")')->count());

        //go to list view page
        $crawler = $client->request('GET', '/service/edit/5.html');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //check if user landed on the service provider list pager
        $this->assertEquals(1, $crawler->filter('title:contains("Edit service")')->count());
        
        // select the add new company form
        $form = $crawler->selectButton('submit')->form();

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            'Service[name]' => 'service-' . rand(1, 2000),
            'Service[description]' => 'Service description',
            'Service[category]' => '1',
            'Service[appointmentDuration]' => '2',
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage services")')->count());
        return;
    }

    /**
     * Delete services
     * 
     * @return void
     */
    public function testDelete()
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
            '_username' => 'ronald.conco@creativecloud.co.za',
            '_password' => '654321',
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are not available on the page
        $this->assertEquals(0, $crawler->filter('title:contains("Welcome, please login")')->count());

        //go to list view page
        $crawler = $client->request('GET', '/service/delete/8.html');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage services")')->count());
        return;
    }    
}
