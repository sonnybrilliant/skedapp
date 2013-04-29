<?php

namespace SkedApp\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use SkedApp\SearchBundle\Form\SearchType;
use SkedApp\CoreBundle\Form\ContactUsType;
use Ivory\GoogleMap\Overlays\Animation;
use Ivory\GoogleMap\MapTypeId;
use Ivory\GoogleMap\Events\MouseEvent;

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

        $arrOptions = array('form' => $form->createView());

        if ($this->get('security.context')->isGranted('ROLE_CONSULTANT_USER'))
            $arrOptions['logged_in_consultant'] = $user = $this->get('consultant.manager')->getLoggedInUser();

        if ($this->get('security.context')->isGranted('ROLE_SITE_USER'))
            $arrOptions['logged_in_customer'] = $user = $this->get('customer.manager')->getLoggedInUser();

        return $this->render('SkedAppCoreBundle:Site:index.html.twig', $arrOptions);
    }

    public function privacyAction()
    {
        return $this->render('SkedAppCoreBundle:Site:privacy.html.twig');
    }

    public function termsAction()
    {
        return $this->render('SkedAppCoreBundle:Site:terms.html.twig');
    }

    public function contactUsAction()
    {
        $this->get('logger')->info('contact us page');
        $company = $this->get('company.manager')->getById(1);
        
        $form = $this->createForm(new ContactUsType());

        if ($this->getRequest()->getMethod() == 'POST') {
            $form->bindRequest($this->getRequest());

            if ($form->isValid()) {
                $data = $form->getData();
                $options = array(
                  'fullName' => $data['fullName'],  
                  'fromEmail' => $data['emailaddress'],  
                  'message' => strip_tags($data['message']), 
                  'toName' => 'Support',
                  'toEmail' => 'ryan@skedapp.co.za'  
                );
                
                 $this->get("notification.manager")->sendContactUs($options);
                 
                 $this->getRequest()->getSession()->setFlash(
                        'success', "Your message was sent successfully");
                 return $this->redirect($this->generateUrl('_welcome'));
                 
            }else{
               $this->getRequest()->getSession()->setFlash(
                        'error', "All fields are required");  
            }
            
        }     
        
        $infoWindow = $this->get('ivory_google_map.info_window');

        // Configure your info window options
        $infoWindow->setPrefixJavascriptVariable('info_window_');
        $infoWindow->setPosition(0, 0, true);
        $infoWindow->setPixelOffset(1.1, 2.1, 'px', 'pt');
        $infoWindow->setContent('<p>' . $company->getName() . '<br/><small>Telphone: '.$company->getContactNumber().' </p>');
        $infoWindow->setOpen(false);
        $infoWindow->setAutoOpen(true);
        $infoWindow->setOpenEvent(MouseEvent::CLICK);
        $infoWindow->setAutoClose(false);
        $infoWindow->setOption('disableAutoPan', true);
        $infoWindow->setOption('zIndex', 10);
        $infoWindow->setOptions(array(
            'disableAutoPan' => true,
            'zIndex' => 10
        ));



        $marker = $this->get('ivory_google_map.marker');


        // Configure your marker options
        $marker->setPrefixJavascriptVariable('marker_');
        $marker->setPosition($company->getLat(), $company->getLng(), true);
        $marker->setAnimation(Animation::DROP);
        $marker->setOptions(array(
            'clickable' => true,
            'flat' => true
        ));
        $marker->setIcon('/img/assets/icons/skedapp-map-icon.png');
        $marker->setShadow('/img/assets/icons/skedapp-map-icon.png');

        $map = $this->get('ivory_google_map.map');
        // Configure your map options
        $map->setPrefixJavascriptVariable('map_');
        $map->setHtmlContainerId('map_canvas');

        $map->setAsync(false);

        $map->setAutoZoom(false);

        $map->setCenter($company->getLat(), $company->getLng(), true);
        $map->setMapOption('zoom', 14);

        $map->setBound(0, 0, 0, 0, false, false);

        // Sets your map type
        $map->setMapOption('mapTypeId', MapTypeId::ROADMAP);
        $map->setMapOption('mapTypeId', 'roadmap');

        $map->setMapOption('disableDefaultUI', false);
        $map->setMapOption('disableDoubleClickZoom', false);
        $map->setStylesheetOptions(array(
            'width' => '100%',
            'height' => '200px'
        ));

        $map->setLanguage('en');


        $map->addMarker($marker);
        $marker->setInfoWindow($infoWindow);

        return $this->render('SkedAppCoreBundle:Site:contactus.html.twig' , array(
            'map' => $map,
            'company' => $company,
            'form' => $form->createView()
        ));
    }
    
    public function aboutUsAction()
    {
        $this->get('logger')->info('about us page');
        
        return $this->render('SkedAppCoreBundle:Site:aboutus.html.twig' );
    }
}
