<?php

namespace SkedApp\MemberBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Annotation\Secure;
use SkedApp\MemberBundle\Form\MemberCreateType;
use SkedApp\CoreBundle\Entity\Member;

/**
 * Member controller
 *
 * @author Ronald Conco <ronald.conco@gmail.com>
 * @package SkedAppMemberBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class MemberController extends Controller
{

    /**
     * List all available members
     * 
     * @param integer $page
     * @return Response
     * @throws AccessDeniedException
     * 
     */
    public function listAction($page = 1)
    {
        $this->get('logger')->info('list all members');

        //check permission
        $this->permissionCheck();            
        
        $searchText = $this->get('request')->query->get('search_text');
        $sort = $this->get('request')->query->get('sort');
        $direction = $this->get('request')->query->get('direction');
        $filterBy = $this->get('request')->query->get('filter_by');

        $options = array('searchText' => $searchText,
            'sort' => $sort,
            'direction' => $direction);

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
            $this->container->get('member.manager')->listAll($options), $this->getRequest()->query->get('page', $page), 10);


        return $this->render('SkedAppMemberBundle:Member:list.html.twig', array('pagination' => $pagination));
    }
    
    /**
     * Create a new member
     * 
     * @return Render
     */
    public function newAction()
    {
       $this->get('logger')->info('add a new member');
       
       $member = new Member();
       
       $form = $this->createForm(new MemberCreateType(),$member);
       
       return $this->render('SkedAppMemberBundle:Member:new.html.twig',array('form'=>$form->createView()));
    }
    
    private function permissionCheck()
    {
       if ((!$this->get('security.context')->isGranted('ROLE_ADMIN')) ||  (!$this->get('security.context')->isGranted('ROLE_CONSULTANT_ADMIN'))) {
           return $this->redirect($this->generateUrl("_welcome")); 
        } 
    }

}
