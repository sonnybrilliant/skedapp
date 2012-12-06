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
     * list consultants
     *
     * @return View
     * @throws AccessDeniedException
     */
    public function indexAction($page = 1)
    {

        $this->get('logger')->info('search results');

        $sort = $this->get('request')->query->get('sort');
        $direction = $this->get('request')->query->get('direction', 'desc');

        $options = array('sort' => $sort,
            'direction' => $direction
        );


        //Instantiate search form

        $form = $this->createForm(new SearchType());

        $arrFormValues = $this->getRequest()->get('Search');

        if ($this->getRequest()->getMethod() == 'POST') {

            $form->bindRequest($this->getRequest());

            $options['lat'] = $arrFormValues['lat'];
            $options['lng'] = $arrFormValues['lng'];
            $options['radius'] = 5;

        }

        $arrResults = $this->container->get('consultant.manager')->listAllWithinRadius($options);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $arrResults['arrResult'], $this->getRequest()->query->get('page', $page), 10
        );

        $objDate = new \DateTime($arrFormValues['booking_date']);

        if ($objDate->getTimestamp() <= 0)
            $objDate = new \DateTime();

        $em = $this->getDoctrine()->getEntityManager();

        foreach ($pagination as $objConsultant) {
            $objDateSend = new \DateTime($arrFormValues['booking_date']);
            $objConsultant->setAvailableBookingSlots ($em->getRepository('SkedAppCoreBundle:Booking')->getBookingSlotsForConsultantSearch($objConsultant, $objDateSend));
        }

        return $this->render('SkedAppSearchBundle:Search:index.html.twig', array(
                'pagination' => $pagination,
                'sort_img' => '/img/sort_' . $direction . '.png',
                'sort' => $direction,
                'form' => $form->createView (),
                'intPositionLat' => $arrFormValues['lat'],
                'intPositionLong' => $arrFormValues['lng'],
                'objDate' => $objDate,
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

