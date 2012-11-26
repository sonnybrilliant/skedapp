<?php

namespace SkedApp\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Security manager 
 * 
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SuleCoreBundle
 * @subpackage Tests/Controller
 * @version 0.0.1
 */
class SecurityControllerTest extends WebTestCase
{

    /**
     * Test successful login action
     */
    public function testLoginSuccessfulAction()
    {
        $client = static::createClient();
        $client->followRedirects(true) ;
        
        
        $crawler = $client->request('GET', '/login');

        // response should be success
        $this->assertEquals(200 , $client->getResponse()->getStatusCode()) ;
        
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
        $this->assertEquals(200 , $client->getResponse()->getStatusCode()) ;
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are not available on the page
        $this->assertEquals(0, $crawler->filter('html:contains("Please login")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Having login trouble?")')->count());
    }
    
   /**
     * Test failed login action
     */
    public function testLoginFailedAction()
    {
        $client = static::createClient();
        $client->followRedirects(true) ;
        
        
        $crawler = $client->request('GET', '/login');

        // response should be success
        $this->assertEquals(200 , $client->getResponse()->getStatusCode()) ;
        
        //check if words are available on the page
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Please login")')->count());
        $this->assertGreaterThan(0, $crawler->filter('html:contains("Having login trouble?")')->count());

        // select the login form
        $form = $crawler->selectButton('submit')->form();

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            '_username' => 'ronald.conco@sulehosting.co.za',
            '_password' => '1234567',
            )
        );

        // response should be success
        $this->assertEquals(200 , $client->getResponse()->getStatusCode()) ;
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are available on the page
        $this->assertEquals(1, $crawler->filter('html:contains("Warning!")')->count());
        $this->assertEquals(1, $crawler->filter('html:contains("Your username and password are invalid, please try again or contact support")')->count());
    }    

}
