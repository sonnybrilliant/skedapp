<?php

namespace SkedApp\CompanyBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Service controller test
 *
 * @author Otto Saayman <otto.saayman@kaizania.co.za>
 * @package SkedAppCompanyBundle
 * @subpackage Tests/Controller
 * @version 0.0.1
 */
class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
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
            '_username' => 'otto.saayman@kaizania.co.za',
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
        $crawler = $client->request('GET', '/company/list');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage service providers")')->count());

        //test add screen
        $crawler = $client->request('GET', '/company/new');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //test edit screen
        $crawler = $client->request('GET', '/company/edit/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //test show screen
        $crawler = $client->request('GET', '/company/show/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //test delete screen
        $crawler = $client->request('GET', '/company/delete/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Add new company
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
        $crawler = $client->request('GET', '/company/new');

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Add a new company")')->count());

        // select the add new company form
        $form = $crawler->selectButton('submit')->form();

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            'company[name]' => 'functional test',
            'company[description]' => 'this is a description',
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage service providers")')->count());
    }

    /**
     * Update company
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


        //Edit company
        $crawler = $client->request('GET', '/company/edit/1');

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Edit service provider")')->count());

        // select the add new company form
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
        $this->assertEquals(1, $crawler->filter('title:contains("List service providers")')->count());
    }

    /**
     * Delete company
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


         //go to list view page
        $crawler = $client->request('GET', '/company/new');

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Add a new service provider")')->count());

        // select the add new company form
        $form = $crawler->selectButton('submit')->form();

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            'Service[name]' => 'functional for deleting',
            'Service[description]' => 'this is a description',
            'Service[category]' => '1',
            'Service[appointmentDuration]' => '1',
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("List service providers")')->count());

    }

}
