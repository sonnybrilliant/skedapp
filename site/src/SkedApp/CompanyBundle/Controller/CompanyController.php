<?php

namespace SkedApp\CompanyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SkedApp\CoreBundle\Entity\Company;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CompanyController extends Controller
{
        /**
     * list consultants
     * 
     * @return View
     * @throws AccessDeniedException
     */
    public function listAction($page = 1)
    {

        $this->get('logger')->info('list companies');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('list companies, access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $consultantsQuery = $em->getRepository('SkedAppCoreBundle:Company')->findAll();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($consultantsQuery, $this->getRequest()->query->get('page', $page), 5);

        return $this->render('SkedAppCompanyBundle:Company:list.html.twig', array('pagination' => $pagination));
    }

    /**
     * Create a new consultant
     * 
     * @return View
     * @throws AccessDeniedException
     */
    public function newAction()
    {
        $this->get('logger')->info('create a new consultant');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('create consultant, access denied.');
            throw new AccessDeniedException();
        }


    }

    /**
     * Create a new consultant
     * 
     * @return View
     * @throws AccessDeniedException
     */
    public function createAction()
    {

    }

    /**
     * Show consultant
     * 
     * @return View
     * @throws AccessDeniedException
     */
    public function showAction($id)
    {
        $this->get('logger')->info('view consultant');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('view consultant, access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $company = $em->getRepository('SkedAppCoreBundle:Company')->find($id);

        if (!$company) {
            $this->get('logger')->warn("consultant not found $id");
            return $this->createNotFoundException();
        }

        return $this->render('SkedAppCompanyBundle:Company:show.html.twig', array('company' => $company));
    }

    /**
     * Edit consultant
     * 
     * @return View
     * @throws AccessDeniedException
     */
    public function editAction($id)
    {
        
    }

    /**
     * Update consultant
     * 
     * @return View
     * @throws AccessDeniedException
     */
    public function updateAction($id)
    {
        
    }

    /**
     * Delete consultant
     * 
     * @return View
     * @throws AccessDeniedException
     */
    public function deleteAction($id)
    {
        
    }

}
