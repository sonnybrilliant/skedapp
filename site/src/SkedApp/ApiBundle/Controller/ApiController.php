<?php

namespace SkedApp\ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * SkedApp\ApiBundle\Controller\ApiController
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppApiBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class ApiController extends Controller
{

    /**
     * Get categories
     * 
     * @return json response
     */
    public function getCategoriesAction()
    {
        $this->get('logger')->info('get categories');

        $em = $this->getDoctrine()->getEntityManager();
        $categories = $em->getRepository('SkedAppCoreBundle:Category')->findAll();
        $results = array();

        if ($categories) {
            foreach ($categories as $category) {
                $results[] = array('id' => $category->getId(), 'name' => $category->getName());
            }
        }

        return $this->respond($results);
    }

    /**
     * Get service
     * 
     * @return json response
     */
    public function getServicesAction($id = 1)
    {
        $this->get('logger')->info('get services');
        $em = $this->getDoctrine()->getEntityManager();
        $services = $em->getRepository('SkedAppCoreBundle:Service')->findByCategory($id);
        $results = array();

        if ($services) {
            foreach ($services as $service) {
                $results[] = array('id' => $service->getId(), 'name' => $service->getName());
            }
        }
        return $this->respond($results);
    }

   /**
     * Get consultant by Id
     * 
     * @return json response
     */
    public function getConsultantAction($id = 1)
    {
        $this->get('logger')->info('get consultant by id');
        $em = $this->getDoctrine()->getEntityManager();
        $consultant = $em->getRepository('SkedAppCoreBundle:Consultant')->find($id);
        $results = array();
        
        if($consultant){
            $results[] = $this->buildConsultant($consultant);            
        }
        
        return $this->respond($results);
    }    
    
    /**
     * Get consultant by service Id
     * 
     * @return json response
     */
    public function getConsultantsAction($serviceId = 1)
    {
        $this->get('logger')->info('get consultant by service id');
        $em = $this->getDoctrine()->getEntityManager();
        $consultants = $em->getRepository('SkedAppCoreBundle:Consultant')->getByService($serviceId);
        $results = array();
        
        if($consultants){
            foreach($consultants as $consultant){
                $results[] = $this->buildConsultant($consultant);
            }
        }
        
        return $this->respond($results);
    }
    
    /**
     * Build consultant response
     * @param object $consultant
     * @return array
     */
    private function buildConsultant($consultant)
    {
        return array('firstName' => $consultant->getFirstName(),
                     'lastName'  => $consultant->getLastName(),
                     'gender'    => $consultant->getGender()->getName(),
                     'speciality' =>  $consultant->getSpeciality(),
                     'professionalStatement' => $consultant->getProfessionalStatement(), 
                      
            
        );
    }
    
    /**
     * Create a json response object
     * @param array $results
     */
    private function respond($results = array())
    {
        $return = new \stdClass();
        $return->status = 'success';
        $return->count = sizeof($results);
        $return->results = $results;

        $response = new Response(json_encode($return));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

}
