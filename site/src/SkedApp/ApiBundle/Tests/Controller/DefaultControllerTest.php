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
        $crawler = $client->request('GET', '/api/get/consultants/1?Search[address]=Sandton, South Africa&Search[category]=1&page=1');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //Check if no error messages were found in the geocoding of the address
        $this->assertRegExp('/\"fullAddress\":{\"error_message\":null/',$client->getResponse()->getContent());

        //Check if the consultants were found
        $this->assertRegExp('/\"consultantsFound\":\[{\"id\":/',$client->getResponse()->getContent());

        //open address geo encode
        $crawler = $client->request('GET', '/api/geocode/address?Search[address]=Sandton, South Africa');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //Check if no error messages were found in the geocoding of the address
        $this->assertRegExp('/\"error_message\":null/',$client->getResponse()->getContent());

        //Test if API register call works
        $crawler = $client->request('GET', '/api/geocode/address?Search[address]=Sandton, South Africa');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //Check if no error messages were found in the geocoding of the address
        $this->assertRegExp('/\"error_message\":null/',$client->getResponse()->getContent());

    }
}
