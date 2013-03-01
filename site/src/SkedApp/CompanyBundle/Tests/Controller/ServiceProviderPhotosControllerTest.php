<?php

namespace SkedApp\CompanyBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Service provider photo controller test
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppCompanyBundle
 * @subpackage Tests/Controller
 * @version 0.0.1
 */
class ServiceProviderPhotosControllerTest extends WebTestCase
{

    /**
     * Add service provider photo
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
        $crawler = $client->request('GET', '/service_provider/add/photo/1.html');

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Add service provider photo")')->count());

        // select the add new company form
        $form = $crawler->selectButton('submit')->form();

        $photo = new UploadedFile(
                __DIR__ . '/../../../../../web/test/ServiceProvider/xara_layout.jpg',
                'xara_layout.jpg',
                'image/jpeg',
                156336
        );

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            'CompanyPhotos[caption]' => 'Xara layout-' . rand(1, 2000),
            'CompanyPhotos[picture]' => $photo,
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('html:contains("Service Provider Profile")')->count());
        return;
    }   

}
