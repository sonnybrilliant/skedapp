<?php

namespace SkedApp\SearchBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SkedApp\CoreBundle\Entity\company;
use SkedApp\CoreBundle\Entity\Category;
use SkedApp\CoreBundle\Entity\Consultant;
use SkedApp\CoreBundle\Entity\Service;
use SkedApp\SearchBundle\Form\SearchType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;

/**
 * SkedApp\ConsultantBundle\Controller\ConsultantController
 *
 * @author Otto Saayman <otto.saayman@kaizania.co.za>
 * @package SkedAppSearchBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class SearchController extends Controller
{

    /**
     * ssearch results
     *
     * @return View
     * @throws AccessDeniedException
     */
    public function resultsAction($page = 1)
    {

        $this->get('logger')->info('search results');

        $sort = $this->get('request')->query->get('sort');
        $direction = $this->get('request')->query->get('direction', 'desc');

        $options = array('sort' => $sort,
            'direction' => $direction
        );

        //Instantiate search form
        $formData = $this->getRequest()->get('Search');

        if (!isset($formData['booking_date'])){
          $formData['booking_date'] = $this->getRequest()->get('date', '');
        }

        if (!isset($formData['lat'])){
          $formData['lat'] = $this->getRequest()->get('pos_lat', null);
        }

        if (!isset($formData['lng']))
          $formData['lng'] = $this->getRequest()->get('pos_lng', null);

        if (!isset ($formData['consultantServices']))
            $formData['consultantServices'] = $this->getRequest()->get('service_ids', array());
        if (!is_array($formData['consultantServices']))
            $formData['consultantServices'] = explode(',', $formData['consultantServices']);

        if (!isset ($formData['category']))
            $formData['category'] = $this->getRequest()->get('category_id', 0);

        $form = $this->createForm(new SearchType($formData['category'], $formData['booking_date']));

        if ( (!is_null($formData['lat'])) && (!is_null($formData['lng'])) && ($formData['category'] > 0) ) {

            if ($this->getRequest()->getMethod() == 'POST')
                $form->bindRequest($this->getRequest());

            $options['lat'] = $formData['lat'];
            $options['lng'] = $formData['lng'];
            $options['radius'] = 5;
            $options['categoryId'] = $formData['category'];

            if ( (isset ($formData['consultantServices'])) && (count($formData['consultantServices']) > 0) )
              $options['consultantServices'] = $formData['consultantServices'];

        }

        $arrResults = $this->container->get('consultant.manager')->listAllWithinRadius($options);

        $variables = array();

        //Read form variables into an array
        foreach ($formData as $strKey => $strValue) {
            $variables['Search[' . $strKey . ']'] = $strValue;
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $arrResults['arrResult'], $this->getRequest()->query->get('page', $page), 10
        );

        $objDate = new \DateTime($formData['booking_date']);

        if ($objDate->getTimestamp() <= 0)
            $objDate = new \DateTime();

        $em = $this->getDoctrine()->getEntityManager();

        foreach ($pagination as $objConsultant) {
            $objDateSend = new \DateTime($formData['booking_date']);
            $objConsultant->setAvailableBookingSlots ($em->getRepository('SkedAppCoreBundle:Booking')->getBookingSlotsForConsultantSearch($objConsultant, $objDateSend));
        }

        return $this->render('SkedAppSearchBundle:Search:search.html.twig', array(
                'pagination' => $pagination,
                'sort_img' => '/img/sort_' . $direction . '.png',
                'sort' => $direction,
                'form' => $form->createView(),
                'intPositionLat' => $formData['lat'],
                'intPositionLong' => $formData['lng'],
                'objDate' => $objDate,
                'dateFull' => $objDate->format('d-m-Y'),
                'category_id' => $formData['category'],
                'serviceIds' => implode(',', $formData['consultantServices']),
                'paginatorVariables' => $variables
            ));
    }

    /**
     * Ajax call services by category
     *
     * @param integer $categoryId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     */
    public function ajaxGetServicesByCategoryAction($categoryId)
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->get('logger')->info('get services by category');
            $results = array();

            $em = $this->getDoctrine()->getEntityManager();
            $category = $em->getRepository('SkedAppCoreBundle:Category')->find($categoryId);

            if ($category) {
                $services = $this->get('service.manager')->getServicesByCategory($category);

                if ($services) {
                    foreach ($services as $service) {
                        $results[] = array(
                            'id' => $service->getId(),
                            'name' => $service->getName()
                        );
                    }
                }
            }

            $return = new \stdClass();
            $return->status = 'success';
            $return->count = sizeof($results);
            $return->results = $results;

            $response = new Response(json_encode($return));
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        } else {
            $this->get('logger')->warn('not a valid request, expected ajax call');
            throw new AccessDeniedException();
        }
    }

}

