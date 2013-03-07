<?php

namespace SkedApp\ConsultantBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Consultant controller test
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
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
    public $tmpName = array("Jersey", "Sally", "Jimmy", "Sarah", "Tommy", "Gugu", "Nelly");

    /**
     * Test surname for conslutants
     *
     * @var array
     */
    public $tmpSurname = array("Gordon", "Van der walt", "Jamson", "Dunhill", "Sceepers", "Conco", "Furtado");
    
    /**
     *
     * @var string
     */
    public $consultantFullName = null;

    /**
     * List Consultant
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
        $crawler = $client->request('GET', '/consultant/list.html');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage consultants")')->count());

        return;
    }

    /**
     * Create Consultant
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

        //go to list view page
        $crawler = $client->request('GET', '/consultant/list.html');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage consultants")')->count());

        //create a new consultant
        $crawler = $client->request('GET', '/consultant/new.html');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //we are at the add new consultant
        $this->assertEquals(1, $crawler->filter('title:contains("Add a new consultant")')->count());

        $photo = new UploadedFile(
                __DIR__ . '/../../../../../web/test/Consultant/male_tony_bruce.jpg',
                'male_tony_bruce.jpg',
                'image/jpeg',
                28591
        );

        // select the add new service form
        $form = $crawler->selectButton('submit')->form();

                // submit the form with valid credentials
        $firstName = $this->tmpName[rand(0,6)];
        $lastName = $this->tmpSurname[rand(0,6)];
        
        $this->consultantFullName = $firstName.'-'.$lastName.'-1';
        
        $crawler = $client->submit(
            $form, array(
            'Consultant[company]' => 1,
            'Consultant[firstName]' => $firstName,
            'Consultant[lastName]' => $lastName,
            'Consultant[gender]' => '1',
            'Consultant[email][first]' => $firstName.'.'.$lastName.'@sulehosting.co.za',
            'Consultant[email][second]' => $firstName.'.'.$lastName.'@sulehosting.co.za',
            'Consultant[professionalStatement]' => '<p>I love what I do, fucntional testing</p>',
            'Consultant[speciality]' => '<p>I love cutting hair</p>',
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
            'Consultant[appointmentDuration]' => 3,
            )
        );

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage consultants")')->count());
        
        $this->show($this->consultantFullName);
        $this->delete($this->consultantFullName);
        
        return;
    }
    
    /**
     * Show Consultant
     * 
     * @return void
     */
    public function show($name)
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
        $crawler = $client->request('GET', '/consultant/list.html');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage consultants")')->count());

        //create a new consultant
        $crawler = $client->request('GET', '/consultant/show/details/'.strtolower($name).'.html');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Consultant Profile")')->count());
        
        return;
    }
    
     /**
     * Delete Consultant
     * 
     * @return void
     */
    public function delete($name)
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
        $crawler = $client->request('GET', '/consultant/list.html');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage consultants")')->count());

        //create a new consultant
        $crawler = $client->request('GET', '/consultant/delete/'.strtolower($name).'.html');

        // response should be success
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertTrue($client->getResponse()->isSuccessful());
        
        //we are at the list view page
        $this->assertEquals(1, $crawler->filter('title:contains("Manage consultants")')->count());
        
        return;
    }   

}
