<?php

namespace SkedApp\ServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SkedApp\ServiceBundle\Form\ServiceCreateType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use SkedApp\CoreBundle\Entity\Service;


/**
 * Service manager 
 * 
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppServiceBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class ServiceController extends Controller
{

    /**
     * List services
     * 
     * @param Integer $page
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function listAction($page = 1)
    {
        $this->get('logger')->info('list services');

        $isDirectionSet = $this->get('request')->query->get('direction', false);
        $searchText = $this->get('request')->query->get('searchText');
        $sort = $this->get('request')->query->get('sort', 's.id');
        $direction = $this->get('request')->query->get('direction', 'asc');

        $options = array('searchText' => $searchText,
            'sort' => $sort,
            'direction' => $direction,
        );



        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $this->container->get('service.manager')->listAll($options), $this->getRequest()->query->get('page', $page), 10
        );

        return $this->render('SkedAppServiceBundle:Service:list.html.twig', array(
                'pagination' => $pagination,
                'direction' => $direction,
                'isDirectionSet' => $isDirectionSet
            ));
    }

    /**
     * Create a new service
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function newAction()
    {
        $this->get('logger')->info('create a new service');

        $service = new Service();
        $form = $this->createForm(new ServiceCreateType(), $service);

        return $this->render('SkedAppServiceBundle:Service:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * Create a new service
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function createAction()
    {
        $this->get('logger')->info('create a new service');

        $service = new Service();
        $form = $this->createForm(new ServiceCreateType(), $service);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $this->get('service.manager')->createAndUpdateService($service);
                $this->getRequest()->getSession()->setFlash(
                    'success', 'Created service successfully');
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
     * @Secure(roles="ROLE_ADMIN")
     */
    public function editAction($id)
    {
        $this->get('logger')->info('edit service');

        try {
            $service = $this->get('service.manager')->getById($id);
            $form = $this->createForm(new ServiceCreateType(), $service);
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request: ' . $e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_service_list') . 'html');
        }

        return $this->render('SkedAppServiceBundle:Service:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $service->getId(),
            ));
    }

    /**
     * Update service
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function updateAction($id)
    {
        $this->get('logger')->info('update service');

        try {
            $service = $this->get('service.manager')->getById($id);
            $form = $this->createForm(new ServiceCreateType(), $service);

            if ($this->getRequest()->getMethod() == 'POST') {
                $form->bindRequest($this->getRequest());

                if ($form->isValid()) {
                    $this->get('service.manager')->createAndUpdateService($service);
                    $this->getRequest()->getSession()->setFlash(
                        'success', 'Updated service successfully');
                    return $this->redirect($this->generateUrl('sked_app_service_list'));
                } else {
                    $this->getRequest()->getSession()->setFlash(
                        'error', 'Failed to update service');
                }
            }
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request: ' . $e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_service_list') . 'html');
        }

        return $this->render('SkedAppServiceBundle:Service:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $service->getId(),
            ));
    }

    /**
     * Delete service
     *  
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction($id)
    {
        $this->get('logger')->info('delete service');

        try {
            $service = $this->get('service.manager')->getById($id);
            $this->get('service.manager')->delete($service);
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request: ' . $e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_service_list') . 'html');
        }

        $this->getRequest()->getSession()->setFlash(
            'success', 'Service was successfully deleted');
        return $this->redirect($this->generateUrl('sked_app_service_list'));
    }

}
