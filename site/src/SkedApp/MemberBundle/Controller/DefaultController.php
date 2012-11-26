<?php

namespace SkedApp\MemberBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SkedAppMemberBundle:Default:index.html.twig', array('name' => $name));
    }
}
