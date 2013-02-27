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
     * @return View
     * @throws AccessDeniedException
     */
    public function newAction()
    {
        $this->get('logger')->info('create a new category');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('create category, access denied.');
            throw new AccessDeniedException();
        }

        $category = new Category();
        $form = $this->createForm(new CategoryCreateType(), $category);

        return $this->render('SkedAppCategoryBundle:Category:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * Create a new category
     *
     * @return View
     * @throws AccessDeniedException
     */
    public function createAction()
    {
        $this->get('logger')->info('create a new category');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('create category, access denied.');
            throw new AccessDeniedException();
        }

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
     * @return View
     * @throws AccessDeniedException
     */
    public function editAction($id)
    {
        $this->get('logger')->info('edit category');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('edit category, access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $category = $em->getRepository('SkedAppCoreBundle:Category')->find($id);

        if (!$category) {
            $this->createNotFoundException('Category does not exist');
        }

        $form = $this->createForm(new CategoryUpdateType(), $category);

        return $this->render('SkedAppCategoryBundle:Category:edit.html.twig', array(
                'form' => $form->createView(),
                'id' => $category->getId(),
            ));
    }

    /**
     * Update a category
     *
     * @return View
     * @throws AccessDeniedException
     */
    public function updateAction($id)
    {
        $this->get('logger')->info('update category');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('update category, access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $category = $em->getRepository('SkedAppCoreBundle:Category')->find($id);

        if (!$category) {
            $this->createNotFoundException('Category does not exist');
        }

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

        return $this->render('SkedAppCategoryBundle:Category:edit.html.twig', array('form' => $form->createView()));
    }

    /**
     * Delete category
     *
     * @return Response
     * @throws AccessDeniedException
     */
    public function deleteAction($id)
    {
        $this->get('logger')->info('delete category');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('delete category, access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $category = $em->getRepository('SkedAppCoreBundle:Category')->find($id);

        $this->get('service.manager')->deleteServicesByCategory($category);

        $category->setIsDeleted(true);
        $em->persist($category);
        $em->flush();

        $this->getRequest()->getSession()->setFlash(
            'success', 'Category was successfully deleted');
        return $this->redirect($this->generateUrl('sked_app_category_list'));
    }

    /**
     * Show category
     *
     * @return View
     * @throws AccessDeniedException
     */
    public function showAction ($id) {

        $this->get('logger')->info('view category');

        if (!$this->get('security.context')->isGranted('ROLE_ADMIN')) {
            $this->get('logger')->warn('view category, access denied.');
            throw new AccessDeniedException();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $category = $em->getRepository('SkedAppCoreBundle:Category')->find($id);

        if (!$category) {
            $this->get('logger')->warn("category not found $id");
            return $this->createNotFoundException();
        }

        return $this->render('SkedAppCategoryBundle:Category:show.html.twig', array('category' => $category));

    }

}

