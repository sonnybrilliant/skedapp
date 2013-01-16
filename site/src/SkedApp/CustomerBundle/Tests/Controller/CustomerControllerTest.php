<?php

namespace SkedApp\CustomerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Customer controller test 
 * 
 * @author Ronald Conco <ronald.conco@kaizania.co.za>
 * @package SkedAppCustomerBundle
 * @subpackage Tests/Controller
 * @version 0.0.1
 */
class CustomerControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        //Test if register form is displayed
        $crawler = $client->request('GET', '/customer/register');

        $this->assertTrue($crawler->filter('html:contains("Register account")')->count() > 0);

        //Test if registration works

        // select the register form
//        $form = $crawler->selectButton('submit')->form();
//
//        // submit the form with valid credentials
//        $crawler = $client->submit(
//            $form, array(
//            'Customer[firstName]' => 'FirstName',
//            'Customer[lastName]' => 'lastName',
//            'Customer[mobileNumber]' => '27713264128',
//            'Customer[landLineNumber]' => '0129989536',
//            'Customer[email][first]' => 'john.smith@example.com',
//            'Customer[email][second]' => 'john.smith@example.com',
//            'Customer[password][first]' => '654321',
//            'Customer[password][second]' => '654321',
//            )
//        );

        #NB:Need to get info on how to disable captcha during unit tests

    }
}
