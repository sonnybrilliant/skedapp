<?php

namespace SkedApp\CoreBundle\Controller ;

use Symfony\Bundle\FrameworkBundle\Controller\Controller ;
use SkedApp\SearchBundle\Form\SearchType;

/**
 * Site manager
 *
 * @author Ronald Conco <ronald.conco@kaizania.co.za>
 * @package SkedAppCoreBundle
 * @subpackage Controller
 * @version 0.0.1
 */
class SiteController extends Controller
{

    public function indexAction()
    {

        //Instantiate search form

        $form = $this->createForm(new SearchType());

        return $this->render('SkedAppCoreBundle:Site:index.html.twig', array ('form' => $form->createView ()));
    }

}
