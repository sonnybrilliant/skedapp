<?php

namespace SkedApp\CompanyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SkedApp\CoreBundle\Entity\Company;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use SkedApp\CompanyBundle\Form\CompanyCreateType;
use SkedApp\CompanyBundle\Form\CompanyUpdateType;

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

        $this->get('logger')->info('list service providers');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('list service providers, access denied.');
            throw new AccessDeniedException();
        }

        $sort = $this->get('request')->query->get('sort');
        $direction = $this->get('request')->query->get('direction', 'desc');

        $options = array('sort' => $sort,
            'direction' => $direction
        );


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $this->container->get('company.manager')->listAll($options), $this->getRequest()->query->get('page', $page), 10
        );

        return $this->render('SkedAppCompanyBundle:Company:list.html.twig', array(
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
       return $this->createAction ();
    }

    /**
     * Create a new consultant
     *
     * @return View
     * @throws AccessDeniedException
     */
    public function createAction()
    {

        $this->get('logger')->info('create a new service provider');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('create service provider, access denied.');
            throw new AccessDeniedException();
        }

        $company = new Company();
        $form = $this->createForm(new CompanyCreateType(), $company);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($company);
                $em->flush();
                $this->getRequest()->getSession()->setFlash(
                    'success', 'Created service provider sucessfully');
                return $this->redirect($this->generateUrl('sked_app_company_list'));
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed to create service provider');
            }
        }

        return $this->render('SkedAppCompanyBundle:Company:create.html.twig', array('form' => $form->createView()));

    }

    /**
     * Show consultant
     *
     * @return View
     * @throws AccessDeniedException
     */
    public function showAction ($id) {

        $this->get('logger')->info('view service provider');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('view service provider, access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $company = $em->getRepository('SkedAppCoreBundle:Company')->find($id);

        if (!$company) {
            $this->get('logger')->warn("service provider not found $id");
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
        $this->get('logger')->info('edit service provider id:' . $id);

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('edit service provider id:' . $id . ', access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $company = $em->getRepository('SkedAppCoreBundle:Company')->find($id);

        if (!$company) {
            $this->get('logger')->warn("service provider not found $id");
            return $this->createNotFoundException();
        }

        $form = $this->createForm(new CompanyUpdateType(), $company);

        return $this->render('SkedAppCompanyBundle:Company:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $company->getId()
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
        $this->get('logger')->info('update service provider id:' . $id);

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('update service provider id:' . $id . ', access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $company = $em->getRepository('SkedAppCoreBundle:Company')->find($id);

        if (!$company) {
            $this->get('logger')->warn("service provider not found $id");
            return $this->createNotFoundException();
        }

        $form = $this->createForm(new CompanyUpdateType(), $company);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($company);
                $em->flush();
                $this->getRequest()->getSession()->setFlash(
                    'success', 'Updated service provider sucessfully');
                return $this->redirect($this->generateUrl('sked_app_company_list'));
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed to update service provider');
            }
        }

        return $this->render('SkedAppCompanyBundle:Company:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $company->getId()
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
        $this->get('logger')->info('delete service provider id:' . $id);

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('delete  service provider id:' . $id . ', access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $company = $em->getRepository('SkedAppCoreBundle:Company')->find($id);

        if (!$company) {
            $this->get('logger')->warn("service provider not found $id");
            return $this->createNotFoundException();
        }

        $this->container->get('company.manager')->delete($company);
        $this->getRequest()->getSession()->setFlash(
            'success', 'Deleted service provider sucessfully');
        return $this->redirect($this->generateUrl('sked_app_company_list'));
    }

}
