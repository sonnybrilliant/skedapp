<?php

namespace SkedApp\MemberBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use SkedApp\MemberBundle\Form\MemberCreateType;
use SkedApp\MemberBundle\Form\MemberUpdateType;
use SkedApp\MemberBundle\Form\MemberShowType;
use SkedApp\CoreBundle\Entity\Member;

/**
 * Member controller
 *
 * @author Mfana Ronald Conco <ronald.conco@creativecloud.co.za>
 * @package SkedAppMemberBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class MemberController extends Controller
{

    /**
     * List all available members
     * 
     * @param Integer $page
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function listAction($page = 1)
    {
        $this->get('logger')->info('list all members');

        $isDirectionSet = $this->get('request')->query->get('direction', false);
        $searchText = $this->get('request')->query->get('searchText');
        $sort = $this->get('request')->query->get('sort', 'm.id');
        $direction = $this->get('request')->query->get('direction', 'asc');

        $options = array('searchText' => $searchText,
            'sort' => $sort,
            'direction' => $direction,
        );

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $this->container->get('member.manager')->listAll($options), $this->getRequest()->query->get('page', $page), 10);

        
        return $this->render('SkedAppMemberBundle:Member:list.html.twig', array(
                'pagination' => $pagination,
                'direction' => $direction,
                'isDirectionSet' => $isDirectionSet
            ));
    }

    /**
     * Create a new member
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function newAction()
    {
        $this->get('logger')->info('add a new member');

        $member = new Member();
        $form = $this->createForm(new MemberCreateType(), $member);
        return $this->render('SkedAppMemberBundle:Member:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * Create a new member
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function createAction()
    {

        $this->get('logger')->info('add a new member');

        $member = new Member();
        $form = $this->createForm(new MemberCreateType(), $member);

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {

                //Check: super admin account must only be associated with skedapp service provider
                $group = $member->getGroup();
                $isValid = true;

                if ($group->getId() == 1) {
                    $serviceProvider = $member->getCompany();
                    if ($serviceProvider->getId() != 1) {
                        $this->getRequest()->getSession()->setFlash(
                            'error', 'Super admin account can only be associated with the Skedapp service provider');
                        $isValid = false;
                    }
                }

                if ($isValid) {
                    $this->get('member.manager')->createNewMember($member);

                    $this->getRequest()->getSession()->setFlash(
                        'success', 'Created member successfully');
                    return $this->redirect($this->generateUrl('sked_app_member_list'));
                }
            } else {
                $this->getRequest()->getSession()->setFlash(
                    'error', 'Failed to create member');
            }
        }

        return $this->render('SkedAppMemberBundle:Member:create.html.twig', array(
                'form' => $form->createView(),
            ));
    }

    /**
     * Show member
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function showAction($id)
    {
        $this->get('logger')->info('edit member:' . $id);

        try {
            $member = $this->get('member.manager')->getById($id);
            $form = $this->createForm(new MemberShowType(), $member);
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request: ' . $e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_member_list'));
        }

        return $this->render('SkedAppMemberBundle:Member:show.html.twig', array(
                'form' => $form->createView(),
                'member' => $member
            ));
    }
    
    /**
     * Edit member
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function editAction($id)
    {
        $this->get('logger')->info('edit member:' . $id);

        try {
            $member = $this->get('member.manager')->getById($id);
            $form = $this->createForm(new MemberUpdateType(), $member);
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request: ' . $e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_member_list'));
        }

        return $this->render('SkedAppMemberBundle:Member:edit.html.twig', array(
                'form' => $form->createView(),
                'member' => $member
            ));
    }

    /**
     * Update member
     * 
     * @Secure(roles="ROLE_ADMIN")
     */
    public function updateAction($id)
    {

        $this->get('logger')->info('edit member:' . $id);

        try {
            $member = $this->get('member.manager')->getById($id);
            $form = $this->createForm(new MemberUpdateType(), $member);

            if ($this->getRequest()->getMethod() == 'POST') {
                $form->bindRequest($this->getRequest());

                if ($form->isValid()) {

                    //Check: super admin account must only be associated with skedapp service provider
                    $group = $member->getGroup();
                    $isValid = true;

                    if ($group->getId() == 1) {
                        $serviceProvider = $member->getCompany();
                        if ($serviceProvider->getId() != 1) {
                            $this->getRequest()->getSession()->setFlash(
                                'error', 'Super admin account can only be associated with the Skedapp service provider');
                            $isValid = false;
                        }
                    }

                    if ($isValid) {
                        $this->get('member.manager')->updateMember($member);

                        $this->getRequest()->getSession()->setFlash(
                            'success', 'Updated member successfully');
                        return $this->redirect($this->generateUrl('sked_app_member_list'));
                    }
                } else {
                    $this->getRequest()->getSession()->setFlash(
                        'error', 'Failed to update member');
                }
            }
        } catch (\Exception $e) {
            $this->getRequest()->getSession()->setFlash(
                'error', 'Invalid request: ' . $e->getMessage());
            return $this->redirect($this->generateUrl('sked_app_member_list'));
        }

        return $this->render('SkedAppMemberBundle:Member:edit.html.twig', array(
                'form' => $form->createView(),
                'member' => $member
            ));
    }

}
