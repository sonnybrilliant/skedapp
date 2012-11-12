<?php

namespace SkedApp\ConsultantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SkedApp\CoreBundle\Entity\Consultant;
use SkedApp\ConsultantBundle\Form\ConsultantCreateType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * SkedApp\ConsultantBundle\Controller\ConsultantController
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppConsultantBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class ConsultantController extends Controller
{

    /**
     * list consultants
     * 
     * @return View
     * @throws AccessDeniedException
     */
    public function listAction($page = 1)
    {

        $this->get('logger')->info('list consultants');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('list consultants, access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $consultantsQuery = $em->getRepository('SkedAppCoreBundle:Consultant')->findAll();

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($consultantsQuery, $this->getRequest()->query->get('page', $page), 5);

        return $this->render('SkedAppConsultantBundle:Consultant:list.html.twig', array('pagination' => $pagination));
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

        $consultant = new Consultant();
        $form = $this->createForm(new ConsultantCreateType(), $consultant);

        return $this->render('SkedAppConsultantBundle:Consultant:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * Create a new consultant
     * 
     * @return View
     * @throws AccessDeniedException
     */
    public function createAction()
    {
        $this->get('logger')->info('create a new consultant');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('create consultant, access denied.');
            throw new AccessDeniedException();
        }

        $consultant = new Consultant();
        $form = $this->createForm(new ConsultantCreateType(), $consultant);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($consultant);
                $em->flush();
                $this->getRequest()->getSession()->setFlash(
                    'success', 'Created consultant sucessfully');
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed to create consultant');
            }
        }

        return $this->render('SkedAppConsultantBundle:Consultant:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * Show consultant
     * 
     * @return View
     * @throws AccessDeniedException
     */
    public function showAction($id)
    {
        
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

