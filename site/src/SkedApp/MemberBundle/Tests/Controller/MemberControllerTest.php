<?php

namespace SkedApp\MemberBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Service provider controller test
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppCompanyBundle
 * @subpackage Tests/Controller
 * @version 0.0.1
 */
class MemberControllerTest extends WebTestCase
{

    /**
     * Test member list page
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

        //go to list view page
        $crawler = $client->request('GET', '/member/list.html');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are not available on the page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage members")')->count()); 
        return;
    }
    

    /**
     * Test member list page
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
        $crawler = $client->request('GET', '/member/list.html');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are not available on the page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage members")')->count()); 
        return;
    }    

}
