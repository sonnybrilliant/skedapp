<?php

namespace SkedApp\CustomerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        //Test if register form is displayed
        $crawler = $client->request('GET', '/customer/register');

        $this->assertTrue($crawler->filter('html:contains("Register account")')->count() > 0);

        //Test if registration works

        // select the register form
        $form = $crawler->selectButton('submit')->form();

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            'Customer[firstName]' => 'Test Name',
            'Customer[lastName]' => 'Test Surname',
            'Customer[mobileNumber]' => 'Sandton',
            'Customer[administrative_area_level_2]' => '',
            'Customer[administrative_area_level_1]' => '',
            'Customer[country]' => 'South Africa',
            'Customer[lat]' => '-26.1075261',
            'Customer[lng]' => '28.056656699999962',
            'Customer[booking_date]' => date('d-m-Y'),
            )
        );

        //Need to get info on how to disable captcha during unit tests

    }
}
