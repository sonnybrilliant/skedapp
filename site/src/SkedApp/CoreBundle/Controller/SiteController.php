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

        $arrOptions = array ('form' => $form->createView ());

        if ($this->get('security.context')->isGranted('ROLE_CONSULTANT_USER'))
          $arrOptions['logged_in_consultant'] = $user = $this->get('consultant.manager')->getLoggedInUser();

        if ($this->get('security.context')->isGranted('ROLE_SITE_USER'))
          $arrOptions['logged_in_customer'] = $user = $this->get('customer.manager')->getLoggedInUser();

        return $this->render('SkedAppCoreBundle:Site:index.html.twig', $arrOptions);
    }

}
