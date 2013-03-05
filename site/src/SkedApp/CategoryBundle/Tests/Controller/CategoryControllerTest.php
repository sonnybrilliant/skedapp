<?php

namespace SkedApp\CategoryBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Category controller test 
 * 
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppCategoryBundle
 * @subpackage Tests/Controller
 * @version 0.0.1
 */
class CategoryControllerTest extends WebTestCase
{

    /**
     * List categories
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

        //go to list view page
        $crawler = $client->request('GET', '/category/list.html');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage categories")')->count());

        return;
    }

    /**
     * Create a category
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


        //create a category
        $crawler = $client->request('GET', '/category/new.html');

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Add a new category")')->count());

        // select the add new company form
        $form = $crawler->selectButton('submit')->form();

        $photo = new UploadedFile(
                __DIR__ . '/../../../../../web/test/ServiceProvider/skedapp.jpg',
                'skedapp.jpg',
                'image/jpeg',
                24364
        );
        

        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            'Category[name]' => 'category-'.rand(1, 2000),
            'Category[picture]' => $photo,
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage categories")')->count());
        return;
    }

    /**
     *  Edit category
     * 
     *  @return void
     */
    public function testEdit()
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


        //create a category
        $crawler = $client->request('GET', '/category/edit/2.html');

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Edit category")')->count());

        // select the add new company form
        $form = $crawler->selectButton('submit')->form();


        // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            'Category[name]' => 'category-'.rand(1, 2000),
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage categories")')->count());
        return;
    }

    /**
     *  Delete category
     * 
     *  @return void
     */
    public function testDelete()
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


        //create a category
        $crawler = $client->request('GET', '/category/delete/6.html');
        
                // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage categories")')->count());
        return;
    }

}
