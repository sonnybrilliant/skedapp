<?php

namespace SkedApp\CoreBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;

/**
 * Site manager 
 * 
 * @author Ronald Conco <ronald.conco@kaizania.com>
 * @package SkedAppCoreBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class SiteController extends Controller
{

    public function indexAction()
    {
        
        return $this->render('SkedAppCoreBundle:Site:index.html.twig');
    }

}
