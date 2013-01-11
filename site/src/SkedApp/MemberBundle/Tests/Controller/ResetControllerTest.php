<?php

namespace SkedApp\MemberBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Reset controller tester
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SuleCoreBundle
 * @subpackage Tests/Controller
 * @version 0.0.1
 */
class ResetControllerTest extends WebTestCase
{

    /**
     * reset password page
     */
    public function testResetPassword()
    {
        $client = static::createClient();
        $client->followRedirects(true);


        $crawler = $client->request('GET', '/reset/password');

        
        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are available on the page
        $this->assertEquals(1, $crawler->filter('title:contains("Reset password")')->count());
    }

    /**
     * test reset password invalid post
     */
    public function testInvalidSubmit()
    {
        $client = static::createClient();
        $client->followRedirects(true);


        $crawler = $client->request('GET', '/reset/password');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are available on the page
        $this->assertEquals(1, $crawler->filter('title:contains("Reset password")')->count());

        // select the login form
        $form = $crawler->selectButton('submit')->form();

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            'ResetPassword[email]' => 'ronald.conco@sulehosting.co.za',
            )
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertEquals(1, $crawler->filter('html:contains("Unable to check the captcha from the server")')->count());
    }

    /**
     * test invalid token submit
     */
    public function testInvalidToken()
    {
        $client = static::createClient();
        $client->followRedirects(true);


        $crawler = $client->request('GET', '/reset/token/dadasdqweqw');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are available on the page
        $this->assertEquals(1, $crawler->filter('title:contains("Please check your email")')->count());
    }

}
