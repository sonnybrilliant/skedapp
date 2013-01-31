<?php

namespace SkedApp\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testGetConsultantsJson()
    {
        $client = static::createClient();
        $client->followRedirects(true);

        //open search results api json code
        $crawler = $client->request('GET', '/api/get/consultants/1/1/1/Sandton, South Africa/null/null/null/1');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //Check if the consultants were found
        $this->assertRegExp('/status\":/',$client->getResponse()->getContent());

        //open address geo encode
        $crawler = $client->request('GET', '/api/geocode/address?Search[address]=Sandton, South Africa');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //Check if no error messages were found in the geocoding of the address
        $this->assertRegExp('/status\":true/',$client->getResponse()->getContent());

        //Test if API register call works
        $crawler = $client->request('GET', '/api/register/customer?Customer[firstName]=Testing&Customer[lastName]=Testlastname&Customer[email]=testing' . time() . '@igotafrica.com&Customer[password]=123456'
                . '&Customer[mobileNumber]=0123456789');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //Check if no error messages were found in the geocoding of the address
        $this->assertRegExp('/status\":true/',$client->getResponse()->getContent());

        //Test if API register call works
        $crawler = $client->request('GET', '/api/make/booking/1/1/' . date('Y-m-d 15:00', (time() + (60 * 60 * 24))) . '/1');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //Check if no error messages were found in the geocoding of the address
        $this->assertRegExp('/status\":true/',$client->getResponse()->getContent());

    }
}
