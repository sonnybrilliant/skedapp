<?php

namespace SkedApp\ServiceBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Service controller test 
 * 
 * @author Ronald Conco <ronald.conco@kaizania.co.za>
 * @package SkedAppServiceBundle
 * @subpackage Tests/Controller
 * @version 0.0.1
 */
class ServiceControllerTest extends WebTestCase
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
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please login")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Having login trouble?")')->count());

        // select the login form
        $form = $crawler->selectButton('submit')->form();

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            '_username' => 'ronald.conco@kaizania.co.za',
            '_password' => '654321',
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are not available on the page
        $this->assertEquals(0, $crawler->filter('html:contains("Please login")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Having login trouble?")')->count());


        //go to list view page
        $crawler = $client->request('GET', '/service/list');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("List services")')->count());


        //test edit screen
        $crawler = $client->request('GET', '/service/edit/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //test delete screen
        //$crawler = $client->request('GET', '/service/delete/1');
        //$this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Add new service
     */
    public function testCreate()
    {

        $client = static::createClient();
        $client->followRedirects(true);


        $crawler = $client->request('GET', '/login');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //check if words are available on the page
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please login")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Having login trouble?")')->count());

        // select the login form
        $form = $crawler->selectButton('submit')->form();

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            '_username' => 'ronald.conco@kaizania.co.za',
            '_password' => '654321',
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are not available on the page
        $this->assertEquals(0, $crawler->filter('html:contains("Please login")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Having login trouble?")')->count());


        //go to list view page
        $crawler = $client->request('GET', '/service/new');

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Add a new service")')->count());

        // select the add new service form
        $form = $crawler->selectButton('submit')->form();

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            'Service[name]' => 'functional test',
            'Service[description]' => 'this is a description',
            'Service[category]' => '1',
            'Service[appointmentDuration]' => '1',
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("List services")')->count());
    }

    /**
     * Update service
     */
    public function testUpdate()
    {

        $client = static::createClient();
        $client->followRedirects(true);


        $crawler = $client->request('GET', '/login');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //check if words are available on the page
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please login")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Having login trouble?")')->count());

        // select the login form
        $form = $crawler->selectButton('submit')->form();

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            '_username' => 'ronald.conco@kaizania.co.za',
            '_password' => '654321',
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are not available on the page
        $this->assertEquals(0, $crawler->filter('html:contains("Please login")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Having login trouble?")')->count());


        //Edit service
        $crawler = $client->request('GET', '/service/edit/1');

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Edit service")')->count());

        // select the add new service form
        $form = $crawler->selectButton('submit')->form();

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            'Service[name]' => 'update functional test',
            'Service[description]' => 'this is an update description',
            'Service[category]' => '1',
            'Service[appointmentDuration]' => '1',
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("List services")')->count());
    }

    /**
     * Delete service
     */
    public function testDelete()
    {

        $client = static::createClient();
        $client->followRedirects(true);


        $crawler = $client->request('GET', '/login');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //check if words are available on the page
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please login")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Having login trouble?")')->count());

        // select the login form
        $form = $crawler->selectButton('submit')->form();

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            '_username' => 'ronald.conco@kaizania.co.za',
            '_password' => '654321',
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are not available on the page
        $this->assertEquals(0, $crawler->filter('html:contains("Please login")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Having login trouble?")')->count());


        //delete service
        $crawler = $client->request('GET', '/service/delete/12');
        
        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("List services")')->count());
    }

}
