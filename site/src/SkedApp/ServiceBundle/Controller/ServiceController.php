<?php

namespace SkedApp\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use SkedApp\ServiceBundle\Form\ServiceCreateType;
use SkedApp\CoreBundle\Entity\Service;
use Symfony\Component\HttpFoundation\Response;

/**
 * Service manager 
 * 
 * @author Ronald Conco <ronald.conco@kaizania.com>
 * @package SkedAppServiceBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class ServiceController extends Controller
{

    /**
     * List services
     * 
     * @param integer $page
     * @return Reponse
     */
    public function listAction($page = 1)
    {
        $this->get('logger')->info('list services');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('list services, access denied.');
            throw new AccessDeniedException();
        }

        $sort = $this->get('request')->query->get('sort');
        $direction = $this->get('request')->query->get('direction', 'desc');

        $options = array('sort' => $sort,
            'direction' => $direction
        );


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $this->container->get('service.manager')->listAll($options), $this->getRequest()->query->get('page', $page), 10
        );

        return $this->render('SkedAppServiceBundle:Service:list.html.twig', array(
                'pagination' => $pagination,
                'sort_img' => '/img/sort_' . $direction . '.png',
                'sort' => $direction,
            ));
    }

    /**
     * Create a new service
     * 
     * @return View
     * @throws AccessDeniedException
     */
    public function newAction()
    {
        $this->get('logger')->info('create a new service');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('create service, access denied.');
            throw new AccessDeniedException();
        }

        $service = new Service();
        $form = $this->createForm(new ServiceCreateType(), $service);

        return $this->render('SkedAppServiceBundle:Service:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * Create a new service
     * 
     * @return View
     * @throws AccessDeniedException
     */
    public function createAction()
    {
        $this->get('logger')->info('create a new service');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('create service, access denied.');
            throw new AccessDeniedException();
        }

        $service = new Service();
        $form = $this->createForm(new ServiceCreateType(), $service);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $this->get('service.manager')->createAndUpdateService($service);
                $this->getRequest()->getSession()->setFlash(
                    'success', 'Created service sucessfully');
                return $this->redirect($this->generateUrl('sked_app_service_list'));
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed to create service');
            }
        }

        return $this->render('SkedAppServiceBundle:Service:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * Edit service
     * 
     * @return View
     * @throws AccessDeniedException
     */
    public function editAction($id)
    {
        $this->get('logger')->info('edit service');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('edit service, access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $service = $em->getRepository('SkedAppCoreBundle:Service')->find($id);

        if (!$service) {
            $this->createNotFoundException('Service does not exist');
        }

        $form = $this->createForm(new ServiceCreateType(), $service);

        return $this->render('SkedAppServiceBundle:Service:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $service->getId(),
            ));
    }

    /**
     * Update service
     * 
     * @return View
     * @throws AccessDeniedException
     */
    public function updateAction($id)
    {
        $this->get('logger')->info('update service');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('update service, access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $service = $em->getRepository('SkedAppCoreBundle:Service')->find($id);

        if (!$service) {
            $this->createNotFoundException('Service does not exist');
        }

        $form = $this->createForm(new ServiceCreateType(), $service);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $this->get('service.manager')->createAndUpdateService($service);
                $this->getRequest()->getSession()->setFlash(
                    'success', 'Updated service sucessfully');
                return $this->redirect($this->generateUrl('sked_app_service_list'));
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed to update service');
            }
        }

        return $this->render('SkedAppServiceBundle:Service:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $service->getId(),
            ));
    }

    /**
     * Delete service
     *  
     * @return Response
     * @throws AccessDeniedException 
     */
    public function deleteAction($id)
    {
        $this->get('logger')->info('delete service');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('view agency, access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $service = $em->getRepository('SkedAppCoreBundle:Service')->find($id);


        $service->setIsDeleted(true);
        $em->persist($service);
        $em->flush();


        $this->getRequest()->getSession()->setFlash(
            'success', 'Service was sucessfully deleted');
        return $this->redirect($this->generateUrl('sked_app_service_list'));
    }

}
