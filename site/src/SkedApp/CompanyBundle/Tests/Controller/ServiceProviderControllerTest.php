<?php

namespace SkedApp\CompanyBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Service provider controller test
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppCompanyBundle
 * @subpackage Tests/Controller
 * @version 0.0.1
 */
class ServiceProviderControllerTest extends WebTestCase
{

    /**
     * Test service provider list page
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


        //check if user landed on the service provider list pager
        $this->assertEquals(1, $crawler->filter('title:contains("Manage service providers")')->count());

        return;
    }

    /**
     * Add service provider
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


        //check if user landed on the service provider list pager
        $this->assertEquals(1, $crawler->filter('title:contains("Manage service providers")')->count());


        //go to list view page
        $crawler = $client->request('GET', '/service_provider/new');

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Add service provider")')->count());

        // select the add new company form
        $form = $crawler->selectButton('submit')->form();

        $photo = new UploadedFile(
                __DIR__ . '/../../../../../web/test/ServiceProvider/xara_logo.jpg',
                'xara_logo.jpg',
                'image/jpeg',
                57440
        );

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            'Company[name]' => 'Xara Studio-' . rand(1, 2000),
            'Company[contactNumber]' => '0129986745',
            'Company[description]' => 'Xara studio based in menlyn office park',
            'Company[address]' => 'Menlyn Office Park, Menlyn Park Shopping Mall, Gobie, Pretoria 0181, South Africa',
            'Company[locality]' => 'Pretoria',
            'Company[country]' => 'South Africa',
            'Company[lat]' => '-25.7849599',
            'Company[lng]' => '28.273249999999962',
            'Company[picture]' => $photo,
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage service providers")')->count());
        return;
    }

    /**
     * Edit service provider
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


        //check if user landed on the service provider list pager
        $this->assertEquals(1, $crawler->filter('title:contains("Manage service providers")')->count());


        //go to list view page
        $crawler = $client->request('GET', '/service_provider/edit/1.html');

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Edit service provider")')->count());

        // select the add new company form
        $form = $crawler->selectButton('submit')->form();

        $photo = new UploadedFile(
                __DIR__ . '/../../../../../web/test/ServiceProvider/skedapp.jpg',
                'skedapp.jpg',
                'image/jpeg',
                24364
        );

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            'Company[name]' => 'Skedapp -' . rand(1, 2000),
            'Company[contactNumber]' => '0129986766',
            'Company[description]' => 'Skedapp pty based in menlyn office park',
            'Company[address]' => 'Menlyn Office Park, Menlyn Park Shopping Mall, Gobie, Pretoria 0181, South Africa',
            'Company[locality]' => 'Pretoria',
            'Company[country]' => 'South Africa',
            'Company[lat]' => '-25.7849599',
            'Company[lng]' => '28.273249999999962',
            'Company[picture]' => $photo,
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage service providers")')->count());
        return;
    }
    
    /**
     * Delete service provider
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


        //check if user landed on the service provider list pager
        $this->assertEquals(1, $crawler->filter('title:contains("Manage service providers")')->count());


        //go to list view page
        $crawler = $client->request('GET', '/service_provider/delete/3.html');

                // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage service providers")')->count());
        return;
    }
    

}
