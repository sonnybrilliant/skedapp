<?php

namespace SkedApp\CategoryBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use SkedApp\CoreBundle\Entity\Category;
use SkedApp\CategoryBundle\Form\CategoryCreateType;
use SkedApp\CategoryBundle\Form\CategoryUpdateType;

/**
 * Catergory manager
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppCategoryBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class CategoryController extends Controller
{

    /**
     * List category
     *
     * @param Integer $page paginator
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function listAction($page = 1)
    {
        $this->get('logger')->info('list categories');

        $isDirectionSet = $this->get('request')->query->get('direction', false);
        $searchText = $this->get('request')->query->get('searchText');
        $sort = $this->get('request')->query->get('sort', 'c.id');
        $direction = $this->get('request')->query->get('direction', 'asc');

        $options = array('searchText' => $searchText,
            'sort' => $sort,
            'direction' => $direction,
        );


        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $this->container->get('category.manager')->listAll($options), $this->getRequest()->query->get('page', $page), 10
        );

        return $this->render('SkedAppCategoryBundle:Category:list.html.twig', array(
                'pagination' => $pagination,
                'direction' => $direction,
                'isDirectionSet' => $isDirectionSet
            ));
    }

    /**
     * Create a new category
     *
     * @Secure(roles="ROLE_ADMIN")
     */
    public function newAction()
    {
        $this->get('logger')->info('create a new category');

        $category = new Category();
        $form = $this->createForm(new CategoryCreateType(), $category);

        return $this->render('SkedAppCategoryBundle:Category:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * Create a new category
     *
     * @Secure(roles="ROLE_ADMIN")
     */
    public function createAction()
    {
        $this->get('logger')->info('create a new category');

        $category = new Category();
        $form = $this->createForm(new CategoryCreateType(), $category);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $this->get('category.manager')->createAndUpdateCategory($category);
                $this->getRequest()->getSession()->setFlash(
                    'success', 'Created category successfully');
                return $this->redirect($this->generateUrl('sked_app_category_list'));
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed to create category');
            }
        }

        return $this->render('SkedAppCategoryBundle:Category:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * Edit category
     *
     * @param Integer $id
     *  
     * @Secure(roles="ROLE_ADMIN")
     */
    public function editAction($id)
    {
        $this->get('logger')->info('edit category');

        try {
            $category = $this->get('category.manager')->getById($id);
            $form = $this->createForm(new CategoryUpdateType(), $category);
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request: ' . $e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_category_list') . '.html');
        }

        return $this->render('SkedAppCategoryBundle:Category:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $category->getId(),
            ));
    }

    /**
     * Update a category
     *
     * @param Integer $id
     *  
     * @Secure(roles="ROLE_ADMIN")
     */
    public function updateAction($id)
    {
        $this->get('logger')->info('update category');

        try {
            $category = $this->get('category.manager')->getById($id);
            $form = $this->createForm(new CategoryUpdateType(), $category);

            if ($this->getRequest()->getMethod() == 'POST') {
                $form->bindRequest($this->getRequest());

                if ($form->isValid()) {
                    $this->get('category.manager')->createAndUpdateCategory($category);
                    $this->getRequest()->getSession()->setFlash(
                        'success', 'Update category successfully');
                    return $this->redirect($this->generateUrl('sked_app_category_list'));
                } else {
                    $this->getRequest()->getSession()->setFlash(
                        'error', 'Failed to update category');
                }
            }
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request: ' . $e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_category_list') . '.html');
        }

        return $this->render('SkedAppCategoryBundle:Category:edit.html.twig', array('form' => $form->createView()));
    }

    /**
     * Delete category
     *
     * @param Integer $id
     *  
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction($id)
    {
        $this->get('logger')->info('delete category');

        try {
            $category = $this->get('category.manager')->getById($id);
            $this->get('service.manager')->deleteServicesByCategory($category);
            $this->get('category.manager')->delete($category);
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request: ' . $e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_category_list') . '.html');
        }

        $this->getRequest()->getSession()->setFlash(
            'success', 'Category was successfully deleted');
        return $this->redirect($this->generateUrl('sked_app_category_list'));
    }

    /**
     * Show category
     *
     *  
     * @Secure(roles="ROLE_ADMIN")
     */
    public function showAction($id)
    {

        $this->get('logger')->info('view category');

        try {
            $category = $this->get('category.manager')->getById($id);
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request: ' . $e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_category_list') . '.html');
        }

        return $this->render('SkedAppCategoryBundle:Category:show.html.twig', array('category' => $category));
    }

}

