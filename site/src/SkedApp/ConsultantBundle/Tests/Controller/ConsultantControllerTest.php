<?php

namespace SkedApp\ConsultantBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Consultant controller test 
 * 
 * @author Ronald Conco <ronald.conco@kaizania.co.za>
 * @package SkedAppServiceBundle
 * @subpackage Tests/Controller
 * @version 0.0.1
 */
class ConsultantControllerTest extends WebTestCase
{

    /**
     * Test names for conslutants
     * 
     * @var array
     */
    public $tmpName = array("Jersey","Sally","Jimmy","Sarah","Tommy","Gugu" ,"Nelly");

    /**
     * Test surname for conslutants
     * 
     * @var array
     */
    public $tmpSurname = array("Gordon","Van der walt","Jamson","Dunhill","Sceepers","Conco" ,"Furtado");

    /**
     * Show list view
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
        $crawler = $client->request('GET', '/consultant/list');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("List consultants")')->count());


    }

    /**
     * Create new consultant
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
        $crawler = $client->request('GET', '/consultant/new');

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Add a new consultant")')->count());

        $photo = new UploadedFile(
                __DIR__ . '/../../../../../web/test/consultant.jpg',
                'consultant.jpg',
                'image/jpeg',
                31861
        );
        
        // select the add new service form
        $form = $crawler->selectButton('submit')->form();
        
                // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            'Consultant[company]' => 1,
            'Consultant[firstName]' => $this->tmpName[rand(0,6)],
            'Consultant[lastName]' => $this->tmpSurname[rand(0,6)],
            'Consultant[gender]' => '1',
            'Consultant[professionalStatement]' => '<p>Hello world, fucntional testing</p>',
            'Consultant[speciality]' => '<p>Hello world, fucntional testing</p>',
            'Consultant[category]' => 1,
            'Consultant[picture]' => $photo,
            'Consultant[consultantServices]' => array(4,5),
            'Consultant[monday]' => 1,    
            'Consultant[tuesday]' => 1,    
            'Consultant[wednesday]' => 1,    
            'Consultant[thursday]' => 1,    
            'Consultant[friday]' => 1,  
            'Consultant[startTimeslot]' => 9,  
            'Consultant[endTimeslot]' => 18,  
            'Consultant[appointmentDuration]' => 4,                  
            )
        );
        
        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("List consultants")')->count());

        
    }

    /**
     * Update consultant
     */
    public function testUpdate()
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


        //Edit service
        $crawler = $client->request('GET', '/consultant/edit/1');

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Edit consultant")')->count());

        // select the add new service form
        $form = $crawler->selectButton('submit')->form();

                // submit the form with valid credentials
        $crawler = $client->submit(
            $form, array(
            'Consultant[company]' => 1,
            'Consultant[firstName]' => $this->tmpName[rand(0,6)],
            'Consultant[lastName]' => $this->tmpSurname[rand(0,6)],
            'Consultant[gender]' => '1',
            'Consultant[professionalStatement]' => '<p>Hello world, fucntional testing</p>',
            'Consultant[speciality]' => '<p>Hello world, fucntional testing</p>',
            'Consultant[category]' => 1,
            'Consultant[consultantServices]' => array(4,5),
            'Consultant[monday]' => 1,    
            'Consultant[tuesday]' => 1,    
            'Consultant[wednesday]' => 1,    
            'Consultant[friday]' => 1,  
            'Consultant[startTimeslot]' => 9,  
            'Consultant[endTimeslot]' => 18,  
            'Consultant[appointmentDuration]' => 4,            
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("List consultants")')->count());
    }

    /**
     * Delete service
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


        //delete service
        $crawler = $client->request('GET', '/consultant/delete/1');
        
        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("List consultants")')->count());
    }

}
