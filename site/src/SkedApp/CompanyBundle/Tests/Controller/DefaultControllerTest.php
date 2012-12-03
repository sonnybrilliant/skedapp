<?php

namespace SkedApp\CompanyBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/company/list');

        $this->assertTrue($crawler->filter('html:contains("Please")')->count() > 0);
    }
}
