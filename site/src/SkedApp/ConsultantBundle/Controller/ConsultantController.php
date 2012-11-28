<?php

namespace SkedApp\ConsultantBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SkedApp\CoreBundle\Entity\Consultant;
use SkedApp\ConsultantBundle\Form\ConsultantCreateType;
use SkedApp\ConsultantBundle\Form\ConsultantUpdateType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;

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

        $sort = $this->get('request')->query->get('sort');
        $direction = $this->get('request')->query->get('direction', 'desc');

        $options = array('sort' => $sort,
            'direction' => $direction
        );


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $this->container->get('consultant.manager')->listAll($options), $this->getRequest()->query->get('page', $page), 10
        );

        return $this->render('SkedAppConsultantBundle:Consultant:list.html.twig', array(
                'pagination' => $pagination,
                'sort_img' => '/img/sort_' . $direction . '.png',
                'sort' => $direction,
            ));
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
                return $this->redirect($this->generateUrl('sked_app_consultant_list'));
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
        $this->get('logger')->info('view consultant');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('view consultant, access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $consultant = $em->getRepository('SkedAppCoreBundle:Consultant')->find($id);

        if (!$consultant) {
            $this->get('logger')->warn("consultant not found $id");
            return $this->createNotFoundException();
        }

        return $this->render('SkedAppConsultantBundle:Consultant:show.html.twig', array('consultant' => $consultant));
    }

    /**
     * Edit consultant
     * 
     * @return View
     * @throws AccessDeniedException
     */
    public function editAction($id)
    {
        $this->get('logger')->info('edit consultant id:' . $id);

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('edit consultant id:' . $id . ', access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $consultant = $em->getRepository('SkedAppCoreBundle:Consultant')->find($id);

        if (!$consultant) {
            $this->get('logger')->warn("consultant not found $id");
            return $this->createNotFoundException();
        }

        $form = $this->createForm(new ConsultantUpdateType(), $consultant);

        return $this->render('SkedAppConsultantBundle:Consultant:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $consultant->getId()
            ));
    }

    /**
     * Update consultant
     * 
     * @return View
     * @throws AccessDeniedException
     */
    public function updateAction($id)
    {
        $this->get('logger')->info('update consultant id:' . $id);

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('update consultant id:' . $id . ', access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $consultant = $em->getRepository('SkedAppCoreBundle:Consultant')->find($id);

        if (!$consultant) {
            $this->get('logger')->warn("consultant not found $id");
            return $this->createNotFoundException();
        }

        $form = $this->createForm(new ConsultantUpdateType(), $consultant);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($consultant);
                $em->flush();
                $this->getRequest()->getSession()->setFlash(
                    'success', 'Updated consultant sucessfully');
                return $this->redirect($this->generateUrl('sked_app_consultant_list'));
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed to update consultant');
            }
        }

        return $this->render('SkedAppConsultantBundle:Consultant:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $consultant->getId()
            ));
    }

    /**
     * Delete consultant
     * 
     * @return View
     * @throws AccessDeniedException
     */
    public function deleteAction($id)
    {
        $this->get('logger')->info('delete consultant id:' . $id);

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('delete consultant id:' . $id . ', access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $consultant = $em->getRepository('SkedAppCoreBundle:Consultant')->find($id);

        if (!$consultant) {
            $this->get('logger')->warn("consultant not found $id");
            return $this->createNotFoundException();
        }

        $this->container->get('consultant.manager')->delete($consultant);
        $this->getRequest()->getSession()->setFlash(
            'success', 'Deleted consultant sucessfully');
        return $this->redirect($this->generateUrl('sked_app_consultant_list'));
    }

    /**
     * Ajax call services by category
     * 
     * @param integer $categoryId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws AccessDeniedException
     */
    public function ajaxGetByCategoryAction($categoryId)
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->get('logger')->info('get services by category');
            $results = array();

            if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
                $this->get('logger')->warn('view agency, access denied.');
                throw new AccessDeniedException();
            }

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

