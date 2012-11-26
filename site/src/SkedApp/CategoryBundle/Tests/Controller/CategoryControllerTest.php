<?php

namespace SkedApp\CategoryBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Category controller test 
 * 
 * @author Ronald Conco <ronald.conco@kaizania.co.za>
 * @package SkedAppCategoryBundle
 * @subpackage Tests/Controller
 * @version 0.0.1
 */
class CategoryControllerTest extends WebTestCase
{

    /**
     *  List view
     */
    public function testList()
    {
        $client = static::createClient();
        $client->followRedirects(true);


        $crawler = $client->request('GET', '/login');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

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
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are not available on the page
        $this->assertEquals(0, $crawler->filter('html:contains("Please login")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Having login trouble?")')->count());


        //go to list view page
        $crawler = $client->request('GET', '/category/list');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("List categories")')->count());


        //test edit screen
        $crawler = $client->request('GET', '/category/edit/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     *  Create category
     */
    public function testCreate()
    {
        $client = static::createClient();
        $client->followRedirects(true);


        $crawler = $client->request('GET', '/login');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

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
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are not available on the page
        $this->assertEquals(0, $crawler->filter('html:contains("Please login")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Having login trouble?")')->count());


        //go to list view page
        $crawler = $client->request('GET', '/category/new');

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Add a new category")')->count());

        // select the add new service form
//        $form = $crawler->selectButton('submit')->form();
//
//        $photo = array(
//            'tmp_name' => __DIR__.'/../../../../../web/test/test_upload.jpg',
//            'name' => 'test_upload.jpg',
//            'type' => 'image/jpeg',
//            'size' => 31861,
//            'error' => UPLOAD_ERR_OK
//        );
//
//       
//        
//        // submit the form with valid credentials
//        $crawler = $client->submit(
//            $form, array(
//            'Category[name]' => 'functional test',
//            'Category[description]' => 'this is a description',
//            'Category[picture]' => $photo,
//            )
//        );
//
//        // response should be success
//        $this->assertEquals(200, $client->getResponse()->getStatusCode());
//        $this->assertTrue($client->getResponse()->isSuccessful());
//
//        //we are at the list view page
//        $this->assertEquals(1, $crawler->filter('title:contains("List categories")')->count());
    }

    /**
     *  Edit category
     */
    public function testEdit()
    {
        $client = static::createClient();
        $client->followRedirects(true);


        $crawler = $client->request('GET', '/login');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

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
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are not available on the page
        $this->assertEquals(0, $crawler->filter('html:contains("Please login")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Having login trouble?")')->count());


        //go to list view page
        $crawler = $client->request('GET', '/category/edit/1');

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Edit category")')->count());

        // select the add new service form
        $form = $crawler->selectButton('submit')->form();



        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            'Category[name]' => 'functional test',
            'Category[description]' => 'this is a description',
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("List categories")')->count());
    }

    /**
     *  Delete category
     */
    public function testDelete()
    {
        $client = static::createClient();
        $client->followRedirects(true);


        $crawler = $client->request('GET', '/login');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

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
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //check if words are not available on the page
        $this->assertEquals(0, $crawler->filter('html:contains("Please login")')->count());
        $this->assertEquals(0, $crawler->filter('html:contains("Having login trouble?")')->count());


        //go to list view page
        $crawler = $client->request('GET', '/category/delete/1');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("List categories")')->count());
    }

}
