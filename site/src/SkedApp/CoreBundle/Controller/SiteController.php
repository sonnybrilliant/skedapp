<?php

namespace SkedApp\CoreBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;

class SiteController extends Controller
{

    public function indexAction()
    {
        
        return $this->render('SkedAppCoreBundle:Site:index.html.twig');
    }

}
