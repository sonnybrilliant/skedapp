<?php

namespace SkedApp\SearchBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Search controller test
 *
 * @author Otto Saayman <otto.saayman@kaizania.co.za>
 * @package SkedAppSearchBundle
 * @subpackage Tests/Controller
 * @version 0.0.1
 */
class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {

        $client = static::createClient();
        $client->followRedirects(true);

        //open search results page
        $crawler = $client->request('GET', '/search/index');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

    }

}
